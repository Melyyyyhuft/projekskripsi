<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Pendaftaran;
use App\Models\HasilUjian;
use App\Models\HasilSeleksi;

class SeleksiController extends Controller
{
    /**
     * Bobot tetap: 60% Ujian, 40% Rapor
     */
    const BOBOT_UJIAN = 0.6;
    const BOBOT_RAPOR = 0.4;

    /**
     * Ambang batas kategori kelulusan berdasarkan Skor Akhir
     */
    const AMBANG_UNGGULAN = 85;
    const AMBANG_REGULER  = 70;

    public function index()
    {
        // Ambil semua siswa yang relevan untuk seleksi
        $siswaDaftarSeleksi = Pendaftaran::with(['user', 'jurusan'])
            ->whereIn('status', [
                'lolos_admin',
                'sudah_ujian',
                'tidak_mengikuti_ujian',
                'siap_finalisasi',
                'siap_diumumkan',
            ])
            ->orderBy('updated_at', 'desc')
            ->get();

        // Tambahkan data nilai ujian ke setiap record
        $siswaDaftarSeleksi->each(function ($p) {
            $p->hasil_ujian = HasilUjian::where('user_id', $p->user_id)->first();
        });

        // --- Validasi kelayakan tombol "Proses Seleksi" ---
        // Harus ada, semua yang eligible sudah ujian atau sudah tidak_mengikuti_ujian
        $eligible = $siswaDaftarSeleksi->whereIn('status', ['sudah_ujian', 'tidak_mengikuti_ujian']);
        $adaYangBelumUjian = $siswaDaftarSeleksi->where('status', 'lolos_admin')->count() > 0;

        $bolehProsesSeleksi = $eligible->isNotEmpty()
            && !$adaYangBelumUjian
            && $eligible->where('status', 'sudah_ujian')->every(
                fn($p) => $p->hasil_ujian !== null && $p->hasil_ujian->skor !== null
            );

        $pesanTidakBoleh = null;
        if ($siswaDaftarSeleksi->isEmpty()) {
            $pesanTidakBoleh = 'Belum ada siswa yang lolos administrasi.';
        } elseif ($adaYangBelumUjian) {
            $pesanTidakBoleh = 'Masih ada siswa berstatus "Lolos Administrasi" yang belum mengikuti ujian. Tutup ujian terlebih dahulu.';
        } elseif (!$bolehProsesSeleksi) {
            $pesanTidakBoleh = 'Masih terdapat data atau nilai yang belum lengkap.';
        }

        $adaHasilSeleksi   = Pendaftaran::where('status', 'siap_finalisasi')->exists();
        $sudahDifinalisasi = Pendaftaran::where('status', 'siap_diumumkan')->exists();

        $hasil = HasilSeleksi::with(['pendaftaran.user', 'pendaftaran.jurusan'])
            ->orderBy('skor_akhir', 'desc')
            ->get();

        return view('admin.seleksi.index', compact(
            'siswaDaftarSeleksi',
            'bolehProsesSeleksi',
            'pesanTidakBoleh',
            'adaHasilSeleksi',
            'sudahDifinalisasi',
            'hasil'
        ));
    }

    public function jalankanSeleksi()
    {
        // Validasi: tidak boleh ada yang masih lolos_admin (belum tutup ujian)
        if (Pendaftaran::where('status', 'lolos_admin')->exists()) {
            return redirect()->route('admin.seleksi.index')
                ->with('error', 'Masih ada siswa yang belum mengikuti ujian. Tutup ujian terlebih dahulu sebelum proses seleksi.');
        }

        // Ambil siswa yang sudah ujian (yang belum mengikuti = Gugur, tidak dihitung)
        $pendaftarans = Pendaftaran::with('user')->where('status', 'sudah_ujian')->get();

        if ($pendaftarans->isEmpty()) {
            return redirect()->route('admin.seleksi.index')
                ->with('error', 'Tidak ada siswa yang memenuhi syarat untuk diseleksi (sudah mengikuti ujian).');
        }

        // Hapus hasil seleksi lama yang belum difinalisasi
        HasilSeleksi::where('is_finalisasi', false)->delete();

        $hasilList = [];
        foreach ($pendaftarans as $p) {
            $ujian     = HasilUjian::where('user_id', $p->user_id)->first();
            $skorUjian = $ujian ? $ujian->skor : 0;
            $skorAkhir = round((self::BOBOT_UJIAN * $skorUjian) + (self::BOBOT_RAPOR * $p->nilai_rapor), 2);

            $hasilList[] = [
                'pendaftaran_id' => $p->id,
                'skor_akhir'     => $skorAkhir,
            ];
        }

        // Urutkan dari skor tertinggi
        usort($hasilList, fn($a, $b) => $b['skor_akhir'] <=> $a['skor_akhir']);

        $rank = 1;
        foreach ($hasilList as $item) {
            if ($item['skor_akhir'] >= self::AMBANG_UNGGULAN) {
                $kategori        = 'Unggulan';
                $statusKelulusan = true;
            } elseif ($item['skor_akhir'] >= self::AMBANG_REGULER) {
                $kategori        = 'Reguler';
                $statusKelulusan = true;
            } else {
                $kategori        = 'Tidak Lulus';
                $statusKelulusan = false;
            }

            HasilSeleksi::create([
                'pendaftaran_id'    => $item['pendaftaran_id'],
                'skor_akhir'        => $item['skor_akhir'],
                'ranking'           => $rank,
                'status_kelulusan'  => $statusKelulusan,
                'kategori_kelulusan'=> $kategori,
                'is_finalisasi'     => false,
            ]);

            Pendaftaran::where('id', $item['pendaftaran_id'])
                ->update(['status' => 'siap_finalisasi']);

            $rank++;
        }

        // Siswa tidak_mengikuti_ujian → langsung siap_finalisasi juga (status Gugur)
        // Mereka tidak masuk hasil_seleksi, tapi status pendaftaran ditandai
        Pendaftaran::where('status', 'tidak_mengikuti_ujian')
            ->update(['status' => 'gugur']);

        return redirect()->route('admin.seleksi.index')
            ->with('success', 'Proses seleksi berhasil! Semua siswa telah dikelompokkan. Lakukan Finalisasi untuk mengumumkan hasilnya.');
    }

    public function finalisasi()
    {
        $hasilList = HasilSeleksi::with('pendaftaran')
            ->where('is_finalisasi', false)
            ->get();

        if ($hasilList->isEmpty()) {
            return redirect()->route('admin.seleksi.index')
                ->with('error', 'Tidak ada hasil seleksi yang siap difinalisasi. Jalankan Proses Seleksi terlebih dahulu.');
        }

        foreach ($hasilList as $hasil) {
            $hasil->update(['is_finalisasi' => true]);
            if ($hasil->pendaftaran) {
                $hasil->pendaftaran->update(['status' => 'siap_diumumkan']);
            }
        }

        return redirect()->route('admin.seleksi.index')
            ->with('success', 'Finalisasi berhasil! Hasil seleksi telah dikunci dan siswa sudah dapat melihat pengumuman.');
    }
}
