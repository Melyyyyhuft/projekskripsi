<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Pendaftaran;
use App\Models\HasilUjian;
use App\Models\Pengaturan;
use App\Models\Jurusan;
use App\Models\HasilSeleksi;

class SeleksiController extends Controller
{
    public function index()
    {
        $hasil = HasilSeleksi::with(['pendaftaran.user', 'pendaftaran.jurusan'])
            ->orderBy('skor_akhir', 'desc')
            ->get();
            
        return view('admin.seleksi.index', compact('hasil'));
    }

    public function jalankanSeleksi()
    {
        // 1. Ambil bobot dari tabel pengaturan (hindari kesalahan operator precedence)
        $bobotRapor = Pengaturan::where('key', 'bobot_rapor')->value('value') / 100;
        $bobotUjian = Pengaturan::where('key', 'bobot_ujian')->value('value') / 100;

        // 2. Kosongkan HasilSeleksi sebelumnya
        HasilSeleksi::truncate();

        $jurusans = Jurusan::all();

        foreach ($jurusans as $jurusan) {
            // Ambil semua pendaftar jurusan yang sudah ujian
            $pendaftars = Pendaftaran::where('jurusan_id', $jurusan->id)
                ->where('status', 'sudah_ujian')
                ->get();

            $hasilList = [];

            foreach ($pendaftars as $p) {
                // Cari hasil ujiannya
                $ujian = HasilUjian::where('user_id', $p->user_id)->first();
                $skorUjian = $ujian ? $ujian->skor : 0;

                // Hitung skor akhir dengan rumus: (bobot_ujian × nilai_ujian) + (bobot_rapor × nilai_rapor)
                $skorAkhir = ($skorUjian * $bobotUjian) + ($p->nilai_rapor * $bobotRapor);

                $hasilList[] = [
                    'pendaftaran_id' => $p->id,
                    'skor_akhir'     => round($skorAkhir, 2)
                ];
            }

            // 3. Sorting berdasarkan skor_akhir tertinggi (Ranking)
            usort($hasilList, function ($a, $b) {
                return $b['skor_akhir'] <=> $a['skor_akhir'];
            });

            // 4. Masukkan ke database hasil seleksi dan update status sesuai kuota
            $kuota = $jurusan->kuota;
            $rank  = 1;

            foreach ($hasilList as $hasil) {
                $statusKelulusan = ($rank <= $kuota);

                HasilSeleksi::create([
                    'pendaftaran_id'  => $hasil['pendaftaran_id'],
                    'skor_akhir'      => $hasil['skor_akhir'],
                    'ranking'         => $rank,
                    'status_kelulusan' => $statusKelulusan
                ]);

                // Update status di tabel utama pendaftaran
                $pObj = Pendaftaran::find($hasil['pendaftaran_id']);
                $pObj->status = $statusKelulusan ? 'diterima' : 'tidak_diterima';
                $pObj->save();

                $rank++;
            }
        }

        // Redirect ke halaman Hasil Seleksi (bukan back() yang tidak stabil)
        return redirect()->route('admin.seleksi.index')
            ->with('success', 'Algoritma seleksi berhasil dijalankan! Semua pendaftar telah diperingkat berdasarkan skor akhir dan kuota jurusan.');
    }
}
