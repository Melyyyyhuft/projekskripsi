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
        if (!\App\Models\Pengaturan::isOpen()) {
            return redirect()->route('siswa.dashboard')->with('error', 'Mohon maaf, pengisian data pendaftaran PPDB saat ini sedang ditutup.');
        }

        // Menghitung jumlah pendaftar secara efisien
        $jurusans = Jurusan::get();
        
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
        if (!\App\Models\Pengaturan::isOpen()) {
            return redirect()->route('siswa.dashboard')->with('error', 'Pendaftaran gagal! Periode pendaftaran PPDB telah ditutup.');
        }

        $existingPendaftaran = Pendaftaran::where('user_id', Auth::id())->first();

        // Validasi Relasional dan File Upload
        $request->validate([
            'nama'                => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z\s]+$/'],
            'jurusan_id'          => 'required|exists:jurusans,id',
            'nisn'                => ['required', 'string', 'size:10', 'regex:/^[0-9]+$/'],
            'asal_sekolah'        => ['required', 'string', 'max:255', 'regex:/[a-zA-Z]/'],
            'no_hp'               => ['required', 'string', 'digits_between:10,13', 'regex:/^[0-9]+$/'],
            'nilai_rapor'         => 'required|numeric|min:0|max:100',
            'tempat_lahir'        => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z\s]+$/'],
            'tanggal_lahir'       => 'required|date|before:today',
            'alamat'              => ['required', 'string', 'max:500', 'regex:/[a-zA-Z]/'],
            'skl'     => ($existingPendaftaran ? 'nullable' : 'required') . '|mimes:pdf|max:2048',
            'rapor'   => ($existingPendaftaran ? 'nullable' : 'required') . '|mimes:pdf|max:2048',
            'pasfoto' => ($existingPendaftaran ? 'nullable' : 'required') . '|mimes:jpg,jpeg,png|max:2048',
            'sertifikat_file.*'   => 'nullable|mimes:pdf,jpg,jpeg,png|max:2048',
            'sertifikat_jenis.*'  => 'nullable|string',
            'sertifikat_tingkat.*' => 'nullable|string',
        ], [
            'nama.regex'                => 'Nama lengkap hanya boleh berisi huruf dan spasi.',
            'nisn.size'                 => 'NISN harus tepat 10 digit angka.',
            'nisn.regex'                => 'NISN hanya boleh berisi angka.',
            'no_hp.digits_between'      => 'Nomor HP harus 10–13 digit.',
            'no_hp.regex'               => 'Nomor HP hanya boleh berisi angka.',
            'nilai_rapor.max'           => 'Nilai rapor tidak boleh melebihi 100.',
            'nilai_rapor.numeric'       => 'Nilai rapor harus berupa angka (contoh: 90.00).',
            'asal_sekolah.regex'        => 'Asal sekolah wajib mengandung unsur huruf.',
            'tempat_lahir.regex'        => 'Tempat lahir hanya boleh berisi huruf dan spasi.',
            'tempat_lahir.required'     => 'Tempat lahir wajib diisi.',
            'tanggal_lahir.required'    => 'Tanggal lahir wajib diisi.',
            'tanggal_lahir.before'      => 'Tanggal lahir tidak valid (harus sebelum hari ini).',
            'alamat.regex'              => 'Alamat wajib mengandung unsur huruf.',
            'alamat.required'           => 'Alamat rumah wajib diisi.',
            'skl.mimes'                 => 'File SKL harus berformat PDF.',
            'skl.max'                   => 'File SKL maksimal 2 MB.',
            'rapor.mimes'               => 'File Rapor harus berformat PDF.',
            'rapor.max'                 => 'File Rapor maksimal 2 MB.',
            'pasfoto.mimes'             => 'Pas Foto harus berformat JPG atau PNG.',
            'pasfoto.max'               => 'Pas Foto maksimal 2 MB.',
        ]);

        // Simpan status lama untuk keperluan notifikasi
        $oldStatus = $existingPendaftaran ? $existingPendaftaran->status : null;

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

        // Update data user agar sinkron dengan profil
        Auth::user()->update([
            'name'   => $request->nama,
            'no_hp'  => $request->no_hp,
            'alamat' => $request->alamat,
        ]);

        $pendaftaranData = [
            'jurusan_id'    => $request->jurusan_id,
            'nisn'          => $request->nisn,
            'asal_sekolah'  => $request->asal_sekolah,
            'no_hp'         => $request->no_hp,
            'nilai_rapor'   => $request->nilai_rapor,
            'tempat_lahir'  => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'alamat'        => $request->alamat,
            'status'        => 'menunggu_verifikasi',
        ];

        // Generate nomor pendaftaran hanya jika data baru
        if (!$existingPendaftaran) {
            $year = date('Y');
            $latest = Pendaftaran::whereNotNull('nomor_pendaftaran')
                                ->where('nomor_pendaftaran', 'like', "PPDB-{$year}-%")
                                ->orderBy('nomor_pendaftaran', 'desc')
                                ->first();
            
            $nextNum = 1;
            if ($latest) {
                $lastNum = (int) substr($latest->nomor_pendaftaran, -4);
                $nextNum = $lastNum + 1;
            }
            
            $pendaftaranData['nomor_pendaftaran'] = "PPDB-{$year}-" . str_pad($nextNum, 4, '0', STR_PAD_LEFT);
        }

        $pendaftaran = Pendaftaran::updateOrCreate(
            ['user_id' => Auth::id()],
            $pendaftaranData
        );

        // Jangan hapus berkas lama, biarkan tersimpan sebagai riwayat
        // Berkas::where('pendaftaran_id', $pendaftaran->id)->delete();

        // Simpan SKL (Update jika sudah ada)
        if($request->hasFile('skl')) {
            $file = $request->file('skl');
            $path = $file->store('berkas_pendaftaran/skl', 'public');
            Berkas::updateOrCreate(
                ['pendaftaran_id' => $pendaftaran->id, 'jenis_berkas' => 'skl'],
                [
                    'file_path' => $path,
                    'nama_file' => $file->getClientOriginalName(),
                    'file_type' => $file->getClientOriginalExtension(),
                    'status_verifikasi' => 'pending'
                ]
            );
        }

        // Simpan Rapor (Update jika sudah ada)
        if($request->hasFile('rapor')) {
            $file = $request->file('rapor');
            $path = $file->store('berkas_pendaftaran/rapor', 'public');
            Berkas::updateOrCreate(
                ['pendaftaran_id' => $pendaftaran->id, 'jenis_berkas' => 'rapor'],
                [
                    'file_path' => $path,
                    'nama_file' => $file->getClientOriginalName(),
                    'file_type' => $file->getClientOriginalExtension(),
                    'status_verifikasi' => 'pending'
                ]
            );
        }

        // Simpan Pas Foto (Update jika sudah ada)
        if($request->hasFile('pasfoto')) {
            $file = $request->file('pasfoto');
            $path = $file->store('berkas_pendaftaran/pasfoto', 'public');

            Berkas::updateOrCreate(
                ['pendaftaran_id' => $pendaftaran->id, 'jenis_berkas' => 'pasfoto'],
                [
                    'file_path' => $path,
                    'nama_file' => $file->getClientOriginalName(),
                    'file_type' => $file->getClientOriginalExtension(),
                    'status_verifikasi' => 'pending'
                ]
            );
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

        // Tentukan jenis notifikasi berdasarkan status sebelumnya
        $isRevision = (isset($oldStatus) && $oldStatus == 'revisi');

        // Kirim Notifikasi ke Admin
        $admins = \App\Models\User::where('role', 'admin')->get();
        if ($isRevision) {
            \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\PendaftaranRevisiNotification($pendaftaran));
        } else {
            \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\PendaftaranBaruNotification($pendaftaran));
        }

        return redirect()->route('siswa.dashboard')->with('success', 'Pendaftaran dan berkas berhasil disimpan!');
    }

    public function reuploadMass(Request $request)
    {
        $pendaftaran = Pendaftaran::where('user_id', Auth::id())->firstOrFail();
        
        if (in_array($pendaftaran->status, ['lolos_admin', 'sudah_ujian', 'siap_finalisasi', 'siap_diumumkan', 'diterima', 'ditolak'])) {
            return back()->with('error', 'Pendaftaran sudah dikunci, tidak bisa memperbarui berkas.');
        }

        $request->validate([
            'skl'     => 'nullable|mimes:pdf|max:2048',
            'rapor'   => 'nullable|mimes:pdf|max:2048',
            'pasfoto' => 'nullable|mimes:jpg,jpeg,png|max:2048',
        ]);

        $filesUploaded = 0;
        $fields = ['skl', 'rapor', 'pasfoto'];

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            foreach ($fields as $field) {
                if ($request->hasFile($field)) {
                    $file = $request->file($field);
                    $path = $file->store('berkas_pendaftaran/' . $field, 'public');

                    Berkas::updateOrCreate(
                        ['pendaftaran_id' => $pendaftaran->id, 'jenis_berkas' => $field],
                        [
                            'file_path' => $path,
                            'nama_file' => $file->getClientOriginalName(),
                            'file_type' => $file->getClientOriginalExtension(),
                            'status_verifikasi' => 'pending'
                        ]
                    );
                    $filesUploaded++;
                }
            }

            if ($filesUploaded > 0) {
                // Mengembalikan status ke menunggu_verifikasi
                $pendaftaran->update(['status' => 'menunggu_verifikasi']);

                // Kirim Notifikasi ke Admin
                $admins = \App\Models\User::where('role', 'admin')->get();
                \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\PendaftaranRevisiNotification($pendaftaran));

                \Illuminate\Support\Facades\DB::commit();
                return redirect()->route('siswa.dashboard')->with('success', $filesUploaded . ' berkas berhasil diunggah ulang. Menunggu verifikasi ulang oleh admin.');
            }

            \Illuminate\Support\Facades\DB::rollBack();
            return back()->with('error', 'Tidak ada berkas yang dipilih untuk diunggah.');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->with('error', 'Gagal memproses unggahan: ' . $e->getMessage());
        }
    }
}
