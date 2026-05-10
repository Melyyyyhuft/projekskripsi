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

        // Pengecekan Periode CBT Global dari Pengaturan
        $settings = \App\Models\Pengaturan::pluck('value', 'key')->all();
        $tglMulaiGlobal = $settings['tgl_mulai_cbt'] ?? null;
        $durasiGlobal = $settings['durasi_cbt'] ?? 0;
        $now = now();
        
        if ($tglMulaiGlobal) {
            $start = \Carbon\Carbon::parse($tglMulaiGlobal);
            $end = (clone $start)->addDays($durasiGlobal)->endOfDay();
            
            if ($now->lt($start)) {
                $pesan = 'Ujian belum dimulai. Periode ujian CBT: ' . $start->format('d M Y') . ' s/d ' . $end->format('d M Y');
                return view('siswa.ujian_info', compact('pendaftaran', 'pesan', 'hasilUjian', 'ujianAktif'));
            }
            if ($now->gt($end)) {
                $pesan = 'Periode ujian CBT telah berakhir pada ' . $end->format('d M Y') . '.';
                return view('siswa.ujian_info', compact('pendaftaran', 'pesan', 'hasilUjian', 'ujianAktif'));
            }
        }

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
            $ujian = $ujianAktif;
            return view('siswa.ujian_info', compact('pendaftaran', 'pesan', 'hasilUjian', 'ujian'));
        }

        // Ambil ujian aktif (belum ditutup)
        $ujian = $ujianAktif;

        if (!$ujian) {
            $pesan = 'Ujian belum tersedia. Silakan tunggu informasi dari panitia.';
            return view('siswa.ujian_info', compact('pendaftaran', 'pesan', 'hasilUjian', 'ujian'));
        }

        // Cek periode jadwal jika diset
        $now = now();
        if ($ujian->jadwal_mulai && $now->lt(\Carbon\Carbon::parse($ujian->jadwal_mulai))) {
            $pesan = 'Ujian belum dimulai. Jadwal mulai: ' . \Carbon\Carbon::parse($ujian->jadwal_mulai)->format('d M Y H:i');
            return view('siswa.ujian_info', compact('pendaftaran', 'pesan', 'hasilUjian', 'ujian'));
        }
        if ($ujian->jadwal_selesai && $now->gt(\Carbon\Carbon::parse($ujian->jadwal_selesai))) {
            $pesan = 'Periode ujian telah berakhir pada ' . \Carbon\Carbon::parse($ujian->jadwal_selesai)->format('d M Y H:i') . '.';
            return view('siswa.ujian_info', compact('pendaftaran', 'pesan', 'hasilUjian', 'ujian'));
        }

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

        return view('siswa.ujian', compact('ujian', 'soals'));
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
        $pendaftaran->update(['status' => 'sudah_ujian']);

        return redirect()->route('siswa.dashboard')->with('success', 'Ujian selesai! Skor Anda telah disimpan dan Anda siap masuk ke tahap seleksi perangkingan.');
    }
}
