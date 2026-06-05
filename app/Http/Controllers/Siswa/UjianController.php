<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use App\Models\Pendaftaran;
use App\Models\Ujian;
use App\Models\Soal;
use App\Models\Jawaban;
use App\Models\HasilUjian;

class UjianController extends Controller
{
    public function index()
    {
        $user_id     = Auth::id();
        $pendaftaran = Pendaftaran::where('user_id', $user_id)->first();
        $hasilUjian  = $pendaftaran
            ? HasilUjian::where('user_id', $user_id)->first()
            : null;

        // Jika sudah pernah mengikuti ujian → tampilkan halaman nilai
        if ($hasilUjian) {
            $ujian = Ujian::find($hasilUjian->ujian_id);
            return view('siswa.ujian_selesai', compact('hasilUjian', 'ujian', 'pendaftaran'));
        }

        // Jika status tidak lolos_admin (belum verifikasi, ditolak, dll)
        $statusBisaUjian = ['lolos_admin'];
        // Ambil ujian aktif untuk ditampilkan di halaman info
        $ujianAktif = Ujian::where('is_active', true)->where('is_tutup', false)->first();

        // Pengecekan Periode CBT Global dari Pengaturan Baru
        $settings = \App\Models\Pengaturan::pluck('value', 'key')->all();
        $start = \Carbon\Carbon::parse($settings['cbt_tgl_mulai'] ?? now());
        $end   = \Carbon\Carbon::parse($settings['cbt_tgl_selesai'] ?? now()->addDays(3));
        $statusCbt = $settings['cbt_status'] ?? 'aktif';
        $now = now();
        
        // 1. Cek Status Global
        if ($statusCbt !== 'aktif') {
            $pesan = 'Sistem ujian CBT saat ini sedang ditutup oleh panitia.';
            $ujian = null;
            return view('siswa.ujian_info', compact('pendaftaran', 'pesan', 'hasilUjian', 'ujian', 'settings'));
        }

        // 2. Cek Periode Global
        if ($now->lt($start)) {
            $pesan = 'Ujian belum dimulai. Periode CBT: ' . $start->format('d M Y, H:i') . ' s/d ' . $end->format('d M Y, H:i');
            $ujian = null;
            return view('siswa.ujian_info', compact('pendaftaran', 'pesan', 'hasilUjian', 'ujian', 'settings'));
        }
        if ($now->gt($end)) {
            $pesan = 'Masa ujian CBT telah berakhir pada ' . $end->format('d M Y, H:i') . '.';
            $ujian = null;
            return view('siswa.ujian_info', compact('pendaftaran', 'pesan', 'hasilUjian', 'ujian', 'settings'));
        }

        // 3. Cek Syarat Pendaftaran
        if (!$pendaftaran || !in_array($pendaftaran->status, $statusBisaUjian)) {
            $pesan = 'Anda belum memenuhi syarat untuk mengikuti ujian.';
            if ($pendaftaran) {
                $statusPesan = [
                    'draft'                 => 'Pendaftaran Anda belum disubmit.',
                    'menunggu_verifikasi'   => 'Berkas Anda sedang diverifikasi oleh panitia.',
                    'ditolak_admin'         => 'Pendaftaran Anda ditolak oleh admin.',
                    'sudah_ujian'           => 'Anda sudah menyelesaikan ujian.',
                    'tidak_mengikuti_ujian' => 'Anda ditandai tidak mengikuti ujian.',
                    'siap_finalisasi'       => 'Ujian selesai, hasil sedang diproses.',
                    'siap_diumumkan'        => 'Hasil seleksi sudah diumumkan.',
                    'gugur'                 => 'Anda dinyatakan gugur.',
                ];
                $pesan = $statusPesan[$pendaftaran->status] ?? 'Status pendaftaran: ' . $pendaftaran->status;
            }
            $ujian = null;
            return view('siswa.ujian_info', compact('pendaftaran', 'pesan', 'hasilUjian', 'ujian', 'settings'));
        }

        // 4. Ambil Ujian Sesuai Jurusan Siswa
        $ujian = Ujian::where('jurusan_id', $pendaftaran->jurusan_id)
                      ->where('is_active', true)
                      ->first();

        if (!$ujian) {
            $pesan = 'Modul ujian untuk jurusan ' . ($pendaftaran->jurusan->nama ?? 'Anda') . ' belum tersedia atau dinonaktifkan.';
            return view('siswa.ujian_info', compact('pendaftaran', 'pesan', 'hasilUjian', 'ujian', 'settings'));
        }

        // Cek apakah siswa sudah klik "Mulai Ujian"
        if (!$pendaftaran->waktu_mulai_ujian) {
            return view('siswa.ujian_mulai', compact('ujian', 'pendaftaran', 'settings'));
        }

        // Hitung sisa waktu
        $waktuMulai = \Carbon\Carbon::parse($pendaftaran->waktu_mulai_ujian);
        $waktuSelesai = (clone $waktuMulai)->addMinutes($ujian->durasi_menit);
        
        if ($now->gt($waktuSelesai)) {
            return $this->autoSubmitHabisWaktu($ujian->id);
        }

        $sisaDetik = $now->diffInSeconds($waktuSelesai);

        // Ambil soal dari relasi ujian
        $soalsQuery = $ujian->soals();
        if ($ujian->acak_soal) {
            $soalsQuery->inRandomOrder();
        }
        $soals = $soalsQuery->get();

        foreach ($soals as $soal) {
            $opsi = ['A' => $soal->opsi_a, 'B' => $soal->opsi_b, 'C' => $soal->opsi_c, 'D' => $soal->opsi_d];
            if ($ujian->acak_jawaban) {
                $keys = array_keys($opsi);
                shuffle($keys);
                $shuffledOpsi = [];
                foreach ($keys as $key) { $shuffledOpsi[$key] = $opsi[$key]; }
                $soal->shuffled_opsi = $shuffledOpsi;
            } else {
                $soal->shuffled_opsi = $opsi;
            }
        }

        return view('siswa.ujian', compact('ujian', 'soals', 'sisaDetik'));
    }

    public function mulai(Request $request)
    {
        $user_id = Auth::id();
        $pendaftaran = Pendaftaran::where('user_id', $user_id)->first();
        
        if ($pendaftaran && !$pendaftaran->waktu_mulai_ujian) {
            $pendaftaran->update(['waktu_mulai_ujian' => now()]);
        }
        
        return redirect()->route('siswa.ujian');
    }

    private function autoSubmitHabisWaktu($ujian_id)
    {
        $user_id = Auth::id();
        
        // Cek apakah sudah ada hasil
        $hasil = HasilUjian::where('user_id', $user_id)->where('ujian_id', $ujian_id)->first();
        if (!$hasil) {
            HasilUjian::create([
                'user_id' => $user_id,
                'ujian_id' => $ujian_id,
                'skor' => 0
            ]);

            $pendaftaran = Pendaftaran::where('user_id', $user_id)->first();
            if ($pendaftaran) {
                $pendaftaran->update(['status' => 'sudah_ujian']);
                $pendaftaran->calculateSelectionResult();
            }
        }

        return redirect()->route('siswa.dashboard')->with('error', 'Waktu ujian telah habis. Jawaban kosong otomatis tersimpan.');
    }

    public function submit(Request $request)
    {
        $user_id = Auth::id();
        $ujian_id = $request->ujian_id;
        $jawabanSiswa = $request->jawaban; // array: soal_id => opsi

        $skor = 0;
        $ujian = Ujian::findOrFail($ujian_id);
        $totalSoal = $ujian->soals()->count();

        if ($totalSoal == 0) return back()->with('error', 'Soal Kosong.');

        if ($jawabanSiswa) {
            foreach ($jawabanSiswa as $soal_id => $opsi) {
                $soal = Soal::find($soal_id);
                // Simpan Jawaban
                Jawaban::create([
                    'user_id' => $user_id,
                    'soal_id' => $soal_id,
                    'opsi_dipilih' => $opsi
                ]);

                // Auto Koreksi
                if ($soal->jawaban_benar == $opsi) {
                    $skor++;
                }
            }
        }

        $nilaiAkhir = ($skor / $totalSoal) * 100;

        HasilUjian::create([
            'user_id' => $user_id,
            'ujian_id' => $ujian_id,
            'skor' => $nilaiAkhir
        ]);

        // Update status pendaftaran menjadi sudah_ujian
        $pendaftaran = Pendaftaran::where('user_id', $user_id)->first();
        if ($pendaftaran) {
            $pendaftaran->update(['status' => 'sudah_ujian']);
            // Trigger automatic selection calculation
            $pendaftaran->calculateSelectionResult();
        }

        return redirect()->route('siswa.dashboard')->with('success', 'Ujian selesai! Skor Anda telah disimpan dan Anda siap masuk ke tahap seleksi perangkingan.');
    }
}
