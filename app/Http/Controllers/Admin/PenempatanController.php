<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pendaftaran;
use App\Models\HasilUjian;
use App\Models\HasilSeleksi;
use App\Models\Jurusan;

class PenempatanController extends Controller
{
    // Ambang batas penempatan kelas
    const AMBANG_UNGGULAN = 85;

    // Bonus sertifikat (hanya sertifikat terbaik yang dihitung)
    const BONUS_SERTIFIKAT = [
        'Internasional'  => 3,
        'Nasional'       => 2,
        'Provinsi'       => 1,
        'Kabupaten/Kota' => 0.5,
        'Kecamatan'      => 0,
        'Sekolah'        => 0,
    ];

    /**
     * Tampilkan halaman Seleksi & Penempatan
     */
    public function index(Request $request)
    {
        $jurusans      = Jurusan::orderBy('nama')->get();
        $filterJurusan = $request->get('jurusan_id');

        // Status yang relevan untuk halaman ini
        $statusRelevan = [
            'lolos_admin',
            'sudah_ujian',
            'tidak_mengikuti_ujian',
            'siap_finalisasi',
            'siap_diumumkan',
            'gugur',
        ];

        $query = Pendaftaran::with(['user', 'jurusan', 'berkas', 'hasilSeleksi'])
            ->whereIn('status', $statusRelevan);

        if ($filterJurusan) {
            $query->where('jurusan_id', $filterJurusan);
        }

        $pendaftarans = $query->orderBy('jurusan_id')->orderBy('created_at')->get();

        // Bangun data rows untuk view
        $rows = $pendaftarans->map(function ($p) {
            $hasilUjian      = HasilUjian::where('user_id', $p->user_id)->first();
            $nilaiCBT        = $hasilUjian ? (float) $hasilUjian->skor : null;
            $nilaiRapor      = (float) $p->nilai_rapor;

            // Cari sertifikat terbaik yang valid
            $sertifikatTerbaik = null;
            $bonusSertifikat   = 0;
            $sertifikats = $p->berkas
                ->where('jenis_berkas', 'sertifikat')
                ->where('status_verifikasi', 'valid');

            foreach ($sertifikats as $sert) {
                $val = self::BONUS_SERTIFIKAT[$sert->tingkat_prestasi] ?? 0;
                if ($val > $bonusSertifikat) {
                    $bonusSertifikat   = $val;
                    $sertifikatTerbaik = $sert->tingkat_prestasi;
                }
            }

            // Hitung skor akhir hanya jika sudah CBT
            $skorAkhir  = null;
            $penempatan = null;
            if ($nilaiCBT !== null) {
                $skorAkhir  = round((0.7 * $nilaiRapor) + (0.3 * $nilaiCBT) + $bonusSertifikat, 2);
                $penempatan = $skorAkhir >= self::AMBANG_UNGGULAN ? 'Unggulan' : 'Reguler';
            }

            // Status tampilan
            $statusLabel = $this->resolveStatusLabel($p, $nilaiCBT);

            return [
                'pendaftaran'       => $p,
                'nama'              => $p->user->name,
                'jurusan'           => $p->jurusan->nama,
                'nilai_rapor'       => $nilaiRapor,
                'nilai_cbt'         => $nilaiCBT,
                'sertifikat_level'  => $sertifikatTerbaik,
                'bonus_sertifikat'  => $bonusSertifikat,
                'skor_akhir'        => $skorAkhir,
                'penempatan'        => $penempatan,
                'status'            => $statusLabel,
                'hasil_seleksi'     => $p->hasilSeleksi,
                'sudah_publish'     => $p->hasilSeleksi && $p->hasilSeleksi->is_finalisasi,
            ];
        });

        // Status keseluruhan sistem
        $sudahPublish     = HasilSeleksi::where('is_finalisasi', true)->exists();
        $adaDraft         = HasilSeleksi::where('is_finalisasi', false)->exists();

        // Statistik
        $totalSiswa      = $rows->count();
        $totalDiterima   = $rows->where('status', 'Diterima')->count();
        $totalTidakHadir = $rows->where('status', 'Tidak Hadir CBT')->count();
        $totalTidakDiterima = $rows->where('status', 'Tidak Diterima')->count();
        $sudahDihitung   = $rows->filter(fn($r) => $r['skor_akhir'] !== null && $r['hasil_seleksi'])->count();

        $settings = \App\Models\Pengaturan::pluck('value', 'key')->all();

        return view('admin.penempatan.index', compact(
            'rows',
            'jurusans',
            'filterJurusan',
            'totalSiswa',
            'totalDiterima',
            'totalTidakHadir',
            'totalTidakDiterima',
            'sudahDihitung',
            'sudahPublish',
            'adaDraft',
            'settings'
        ));
    }

    /**
     * Hitung Semua: hitung skor & penempatan, simpan ke hasil_seleksis sebagai draft
     */
    public function prosesSeleksi(Request $request)
    {
        if (HasilSeleksi::where('is_finalisasi', true)->exists()) {
            return redirect()->route('admin.penempatan.index')
                ->with('error', 'Pengumuman sudah dipublish dan tidak dapat dihitung ulang.');
        }

        // Ambil siswa yang sudah ujian CBT (status sudah_ujian atau siap_finalisasi)
        $pendaftarans = Pendaftaran::with(['user', 'jurusan', 'berkas'])
            ->whereIn('status', ['sudah_ujian', 'siap_finalisasi'])
            ->get();

        if ($pendaftarans->isEmpty()) {
            return redirect()->route('admin.penempatan.index')
                ->with('error', 'Belum ada siswa yang telah mengikuti CBT untuk dihitung.');
        }

        // Kelompokkan per jurusan untuk ranking berdasarkan kuota
        $dataPerJurusan = [];

        foreach ($pendaftarans as $p) {
            $hasilUjian = HasilUjian::where('user_id', $p->user_id)->first();
            if (!$hasilUjian) continue;

            $nilaiCBT   = (float) $hasilUjian->skor;
            $nilaiRapor = (float) $p->nilai_rapor;

            // Cari sertifikat terbaik
            $bonusSertifikat = 0;
            foreach ($p->berkas->where('jenis_berkas', 'sertifikat')->where('status_verifikasi', 'valid') as $sert) {
                $val = self::BONUS_SERTIFIKAT[$sert->tingkat_prestasi] ?? 0;
                if ($val > $bonusSertifikat) $bonusSertifikat = $val;
            }

            // Formula: (0.7 × Rapor) + (0.3 × CBT) + Bonus
            $skorAkhir = round((0.7 * $nilaiRapor) + (0.3 * $nilaiCBT) + $bonusSertifikat, 2);

            $dataPerJurusan[$p->jurusan_id][] = [
                'pendaftaran_id' => $p->id,
                'skor_akhir'     => $skorAkhir,
                'skor_cbt'       => $nilaiCBT,
                'nilai_rapor'    => $nilaiRapor,
                'waktu_daftar'   => $p->created_at->timestamp,
            ];
        }

        $jumlahProses = 0;

        foreach ($dataPerJurusan as $jurusanId => $siswas) {
            $jurusan = Jurusan::find($jurusanId);
            $kuota   = $jurusan->kuota ?? 0;

            // Urutkan: skor_akhir DESC → cbt DESC → rapor DESC → waktu_daftar ASC
            usort($siswas, function ($a, $b) {
                if ($b['skor_akhir'] != $a['skor_akhir']) return $b['skor_akhir'] <=> $a['skor_akhir'];
                if ($b['skor_cbt']   != $a['skor_cbt'])   return $b['skor_cbt']   <=> $a['skor_cbt'];
                if ($b['nilai_rapor']!= $a['nilai_rapor']) return $b['nilai_rapor'] <=> $a['nilai_rapor'];
                return $a['waktu_daftar'] <=> $b['waktu_daftar'];
            });

            $rank = 1;
            foreach ($siswas as $item) {
                $isLulus  = ($kuota > 0) ? ($rank <= $kuota) : true;
                $kategori = $isLulus ? 'DITERIMA' : 'TIDAK DITERIMA';

                HasilSeleksi::updateOrCreate(
                    ['pendaftaran_id' => $item['pendaftaran_id']],
                    [
                        'skor_akhir'         => $item['skor_akhir'],
                        'ranking'            => $rank,
                        'status_kelulusan'   => $isLulus,
                        'kategori_kelulusan' => $kategori,
                        'is_finalisasi'      => false,
                    ]
                );

                Pendaftaran::where('id', $item['pendaftaran_id'])
                    ->update(['status' => 'siap_finalisasi']);

                $rank++;
                $jumlahProses++;
            }
        }

        return redirect()->route('admin.penempatan.index')
            ->with('success', "✅ Berhasil menghitung {$jumlahProses} siswa. Periksa hasilnya sebelum dipublish.");
    }

    /**
     * Publish Hasil: finalisasi & tampilkan ke siswa
     */
    public function publishPengumuman()
    {
        $hasilDraft = HasilSeleksi::where('is_finalisasi', false)->get();

        // Tandai siswa tidak hadir CBT sebagai GUGUR
        $tidakIkut = Pendaftaran::where('status', 'tidak_mengikuti_ujian')->get();
        foreach ($tidakIkut as $p) {
            $p->update(['status' => 'gugur']);
            HasilSeleksi::updateOrCreate(
                ['pendaftaran_id' => $p->id],
                [
                    'skor_akhir'         => 0,
                    'ranking'            => 0,
                    'status_kelulusan'   => false,
                    'kategori_kelulusan' => 'GUGUR',
                    'is_finalisasi'      => true,
                ]
            );
        }

        if ($hasilDraft->isEmpty() && $tidakIkut->isEmpty()) {
            return redirect()->route('admin.penempatan.index')
                ->with('error', 'Belum ada hasil seleksi yang dapat dipublish. Klik "Hitung Semua" terlebih dahulu.');
        }

        foreach ($hasilDraft as $hasil) {
            $hasil->update(['is_finalisasi' => true]);
            $hasil->pendaftaran->update(['status' => 'siap_diumumkan']);
        }

        return redirect()->route('admin.penempatan.index')
            ->with('success', '🎉 Hasil seleksi berhasil dipublish! Siswa sekarang dapat melihat hasilnya.');
    }

    /**
     * Tentukan label status tampilan berdasarkan kondisi siswa
     */
    private function resolveStatusLabel(Pendaftaran $p, ?float $nilaiCBT): string
    {
        // Finalisasi → ambil dari hasil seleksi
        if ($p->hasilSeleksi && $p->hasilSeleksi->is_finalisasi) {
            return match($p->hasilSeleksi->kategori_kelulusan) {
                'DITERIMA'       => 'Diterima',
                'TIDAK DITERIMA' => 'Tidak Diterima',
                'GUGUR'          => 'Tidak Hadir CBT',
                default          => 'Tidak Diterima',
            };
        }

        // Draft hasil seleksi
        if ($p->hasilSeleksi) {
            return $p->hasilSeleksi->kategori_kelulusan === 'DITERIMA' ? 'Diterima' : 'Tidak Diterima';
        }

        // Tidak hadir CBT
        if (in_array($p->status, ['tidak_mengikuti_ujian', 'gugur'])) {
            return 'Tidak Hadir CBT';
        }

        // Belum CBT
        if ($nilaiCBT === null) {
            return 'Belum CBT';
        }

        return 'Belum Dihitung';
    }
}
