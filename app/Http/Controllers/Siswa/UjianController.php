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
        $user_id = Auth::id();
        $pendaftaran = Pendaftaran::where('user_id', $user_id)->first();

        if (!$pendaftaran || $pendaftaran->status != 'lolos_admin') {
            return redirect()->route('siswa.dashboard')->with('error', 'Anda belum diverifikasi atau pendaftaran belum lengkap untuk mengikuti ujian.');
        }

        // Cek apakah sudah ujian
        $hasil = HasilUjian::where('user_id', $user_id)->first();
        if ($hasil) {
            return redirect()->route('siswa.dashboard')->with('success', 'Anda sudah mengikuti ujian. Silakan tunggu pengumuman hasil seleksi.');
        }

        $ujian = Ujian::where('is_active', true)->first();
        if (!$ujian) {
            return redirect()->route('siswa.dashboard')->with('error', 'Ujian belum tersedia.');
        }

        $soals = Soal::where('ujian_id', $ujian->id)->inRandomOrder()->get();
        return view('siswa.ujian', compact('ujian', 'soals'));
    }

    public function submit(Request $request)
    {
        $user_id = Auth::id();
        $ujian_id = $request->ujian_id;
        $jawabanSiswa = $request->jawaban; // array: soal_id => opsi

        $skor = 0;
        $totalSoal = Soal::where('ujian_id', $ujian_id)->count();

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
