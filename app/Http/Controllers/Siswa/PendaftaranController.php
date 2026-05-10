<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use App\Models\Jurusan;
use App\Models\Pendaftaran;
use App\Models\Berkas;

class PendaftaranController extends Controller
{
    public function create()
    {
        // Menghitung jumlah diterima secara efisien
        $jurusans = Jurusan::withCount(['pendaftarans as diterima_count' => function ($query) {
            $query->where('status', 'diterima');
        }])->get();
        
        $pendaftaran = Pendaftaran::where('user_id', Auth::id())->first();
        
        $berkasAktif = [];
        $riwayatBerkas = [];
        if ($pendaftaran) {
            // Group by jenis_berkas, getting the latest one as active
            $allBerkas = Berkas::where('pendaftaran_id', $pendaftaran->id)
                               ->latest()
                               ->get();
            
            // Separate into active and history (except sertifikat which can have multiple active)
            foreach($allBerkas as $b) {
                if ($b->jenis_berkas == 'sertifikat') {
                    // For sertifikat, we consider all of them active for now, unless replaced. 
                    // To keep it simple, we just put them in berkasAktif
                    $berkasAktif['sertifikat_' . $b->id] = $b;
                } else {
                    if (!isset($berkasAktif[$b->jenis_berkas])) {
                        $berkasAktif[$b->jenis_berkas] = $b;
                    } else {
                        $riwayatBerkas[] = $b;
                    }
                }
            }
        }

        return view('siswa.pendaftaran', compact('jurusans', 'pendaftaran', 'berkasAktif', 'riwayatBerkas'));
    }

    public function store(Request $request)
    {
        $existingPendaftaran = Pendaftaran::where('user_id', Auth::id())->first();

        // Validasi Relasional dan File Upload
        $request->validate([
            'jurusan_id' => 'required|exists:jurusans,id',
            'nisn' => 'required|numeric',
            'asal_sekolah' => 'required|string|max:255',
            'no_hp' => 'required|string|max:20',
            'nilai_rapor' => 'required|numeric|min:0|max:100',
            'skl' => ($existingPendaftaran ? 'nullable' : 'required') . '|mimes:pdf,jpg,jpeg,png|max:2048',
            'rapor' => ($existingPendaftaran ? 'nullable' : 'required') . '|mimes:pdf|max:2048',
            'pasfoto' => ($existingPendaftaran ? 'nullable' : 'required') . '|mimes:jpg,jpeg,png|max:2048',
            'sertifikat_file.*' => 'nullable|mimes:pdf,jpg,jpeg,png|max:2048',
            'sertifikat_jenis.*' => 'nullable|string',
            'sertifikat_tingkat.*' => 'nullable|string'
        ]);

        // Validasi double submit (mencegah edit jika status sudah diproses)
        if ($existingPendaftaran && in_array($existingPendaftaran->status, ['lolos_admin', 'sudah_ujian', 'diterima', 'tidak_diterima'])) {
            return redirect()->route('siswa.dashboard')->with('error', 'Data pendaftaran Anda sudah diproses dan tidak dapat diubah lagi.');
        }

        $jurusan = Jurusan::findOrFail($request->jurusan_id);

        // Pengecekan sisa kuota
        if ($jurusan->sisa_kuota <= 0) {
            // Boleh update data jika jurusan_id yang dipilih sama dengan yang sebelumnya (tidak pindah jurusan)
            if (!$existingPendaftaran || $existingPendaftaran->jurusan_id != $request->jurusan_id) {
                return back()->with('error', 'Mohon maaf, kuota jurusan ' . $jurusan->nama . ' sudah penuh.')->withInput();
            }
        }

        $pendaftaran = Pendaftaran::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'jurusan_id' => $request->jurusan_id,
                'nisn' => $request->nisn,
                'asal_sekolah' => $request->asal_sekolah,
                'no_hp' => $request->no_hp,
                'nilai_rapor' => $request->nilai_rapor,
                'status' => 'menunggu_verifikasi'
            ]
        );

        // Jangan hapus berkas lama, biarkan tersimpan sebagai riwayat
        // Berkas::where('pendaftaran_id', $pendaftaran->id)->delete();

        // Simpan SKL
        if($request->hasFile('skl')) {
            $file = $request->file('skl');
            $path = $file->store('berkas_pendaftaran/skl', 'public');
            Berkas::create([
                'pendaftaran_id' => $pendaftaran->id,
                'jenis_berkas' => 'skl',
                'file_path' => $path,
                'nama_file' => $file->getClientOriginalName(),
                'file_type' => $file->getClientOriginalExtension(),
                'status_verifikasi' => 'pending'
            ]);
        }

        // Simpan Rapor
        if($request->hasFile('rapor')) {
            $file = $request->file('rapor');
            $path = $file->store('berkas_pendaftaran/rapor', 'public');
            Berkas::create([
                'pendaftaran_id' => $pendaftaran->id,
                'jenis_berkas' => 'rapor',
                'file_path' => $path,
                'nama_file' => $file->getClientOriginalName(),
                'file_type' => $file->getClientOriginalExtension(),
                'status_verifikasi' => 'pending'
            ]);
        }

        // Simpan Pas Foto
        if($request->hasFile('pasfoto')) {
            $file = $request->file('pasfoto');
            $path = $file->store('berkas_pendaftaran/pasfoto', 'public');
            Berkas::create([
                'pendaftaran_id' => $pendaftaran->id,
                'jenis_berkas' => 'pasfoto',
                'file_path' => $path,
                'nama_file' => $file->getClientOriginalName(),
                'file_type' => $file->getClientOriginalExtension(),
                'status_verifikasi' => 'pending'
            ]);
        }

        // Simpan Sertifikat Prestasi (Multiple)
        if($request->hasFile('sertifikat_file')) {
            $files = $request->file('sertifikat_file');
            $jenis = $request->input('sertifikat_jenis');
            $tingkat = $request->input('sertifikat_tingkat');

            foreach($files as $index => $file) {
                if ($file) {
                    $path = $file->store('berkas_pendaftaran/sertifikat', 'public');
                    Berkas::create([
                        'pendaftaran_id' => $pendaftaran->id,
                        'jenis_berkas' => 'sertifikat',
                        'file_path' => $path,
                        'nama_file' => $file->getClientOriginalName(),
                        'file_type' => $file->getClientOriginalExtension(),
                        'status_verifikasi' => 'pending',
                        'jenis_prestasi' => $jenis[$index] ?? null,
                        'tingkat_prestasi' => $tingkat[$index] ?? null
                    ]);
                }
            }
        }

        // Kirim Notifikasi ke Admin
        $admins = \App\Models\User::where('role', 'admin')->get();
        \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\PendaftaranBaruNotification($pendaftaran));

        return redirect()->route('siswa.dashboard')->with('success', 'Pendaftaran dan berkas berhasil disimpan!');
    }

    public function reuploadBerkas(Request $request)
    {
        $pendaftaran = Pendaftaran::where('user_id', Auth::id())->firstOrFail();
        
        if (in_array($pendaftaran->status, ['lolos_admin', 'sudah_ujian', 'siap_finalisasi', 'siap_diumumkan', 'diterima', 'ditolak'])) {
            return back()->with('error', 'Pendaftaran sudah dikunci, tidak bisa re-upload berkas.');
        }

        $request->validate([
            'jenis_berkas' => 'required|string|in:kk,akta,skl,rapor,pasfoto,sertifikat',
            'file_reupload' => 'required|file|max:2048',
            'berkas_id_lama' => 'required|exists:berkas,id'
        ]);

        $file = $request->file('file_reupload');
        $jenis = $request->jenis_berkas;

        $path = $file->store('berkas_pendaftaran/'.$jenis, 'public');

        // Simpan sebagai baris baru untuk menjaga riwayat
        $berkasLama = Berkas::find($request->berkas_id_lama);

        Berkas::create([
            'pendaftaran_id' => $pendaftaran->id,
            'jenis_berkas' => $jenis,
            'file_path' => $path,
            'nama_file' => $file->getClientOriginalName(),
            'file_type' => $file->getClientOriginalExtension(),
            'status_verifikasi' => 'pending',
            'jenis_prestasi' => $berkasLama ? $berkasLama->jenis_prestasi : null,
            'tingkat_prestasi' => $berkasLama ? $berkasLama->tingkat_prestasi : null
        ]);

        // Otomatis set pendaftaran ke status revisi jika sebelumnya menunggu_verifikasi agar diproses admin
        $pendaftaran->update(['status' => 'revisi']);

        return back()->with('success', 'Berkas ' . strtoupper($jenis) . ' berhasil diupload ulang. Menunggu verifikasi admin.');
    }
}
