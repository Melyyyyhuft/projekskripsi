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
        return view('siswa.pendaftaran', compact('jurusans', 'pendaftaran'));
    }

    public function store(Request $request)
    {
        // Validasi Relasional dan File Upload
        $request->validate([
            'jurusan_id' => 'required|exists:jurusans,id',
            'nisn' => 'required|numeric',
            'asal_sekolah' => 'required|string|max:255',
            'no_hp' => 'required|string|max:20',
            'nilai_rapor' => 'required|numeric|min:0|max:100',
            'skl' => 'required|mimes:pdf,jpg,jpeg,png|max:2048',
            'rapor' => 'required|mimes:pdf|max:2048',
            'pasfoto' => 'required|mimes:jpg,jpeg,png|max:2048',
            'sertifikat.*' => 'nullable|mimes:pdf,jpg,jpeg,png|max:2048'
        ]);

        $existingPendaftaran = Pendaftaran::where('user_id', Auth::id())->first();

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

        // Hapus berkas lama jika upload ulang (opsional, tapi disarankan)
        Berkas::where('pendaftaran_id', $pendaftaran->id)->delete();

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

        // Simpan Sertifikat
        if($request->hasFile('sertifikat')) {
            foreach($request->file('sertifikat') as $file) {
                $path = $file->store('berkas_pendaftaran/sertifikat', 'public');
                Berkas::create([
                    'pendaftaran_id' => $pendaftaran->id,
                    'jenis_berkas' => 'sertifikat',
                    'file_path' => $path,
                    'nama_file' => $file->getClientOriginalName(),
                    'file_type' => $file->getClientOriginalExtension(),
                    'status_verifikasi' => 'pending'
                ]);
            }
        }

        // Kirim Notifikasi ke Admin
        $admins = \App\Models\User::where('role', 'admin')->get();
        \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\PendaftaranBaruNotification($pendaftaran));

        return redirect()->route('siswa.dashboard')->with('success', 'Pendaftaran berhasil disubmit dan menunggu verifikasi admin.');
    }
}
