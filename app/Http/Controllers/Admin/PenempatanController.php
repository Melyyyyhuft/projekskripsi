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
    const AMBANG_UNGGULAN = 70;

    // Bonus sertifikat (hanya sertifikat terbaik yang dihitung)
    const BONUS_SERTIFIKAT = [
        'Internasional'  => 3,
        'Nasional'       => 2,
        'Provinsi'       => 1,
        'Kabupaten/Kota' => 0.5,
    ];

    /**
     * Tampilkan halaman Seleksi & Penempatan
     */
    public function index(Request $request)
    {
        $jurusans      = Jurusan::orderBy('nama')->get();
        $filterJurusan = $request->get('jurusan_id');

        // Target: Siswa yang lolos verifikasi (lolos_admin) atau sudah punya hasil
        $statusRelevan = [
            'lolos_admin',
            'sudah_ujian',
            'siap_finalisasi',
            'siap_diumumkan',
            'gugur',
            'tidak_mengikuti_ujian'
        ];

        $query = Pendaftaran::with(['user', 'jurusan', 'berkas', 'hasilSeleksi'])
            ->whereIn('status', $statusRelevan);

        if ($filterJurusan) {
            $query->where('jurusan_id', $filterJurusan);
        }

        $pendaftarans = $query->orderBy('jurusan_id')->orderBy('created_at')->get();

        $rows = $pendaftarans->map(function ($p) {
            $hasilUjian      = HasilUjian::where('user_id', $p->user_id)->first();
            $nilaiCBT        = $hasilUjian ? (float) $hasilUjian->skor : null;
            $nilaiRapor      = (float) $p->nilai_rapor;

            // Jika sudah dihitung (sudah ada di hasil_seleksis), gunakan data yang disimpan
            if ($p->hasilSeleksi) {
                return [
                    'pendaftaran'       => $p,
                    'nama'              => $p->user->name,
                    'jurusan'           => $p->jurusan->nama,
                    'nilai_rapor'       => $nilaiRapor,
                    'nilai_cbt'         => $nilaiCBT,
                    'bonus_sertifikat'  => (float) $p->hasilSeleksi->bonus_sertifikat,
                    'skor_akhir'        => (float) $p->hasilSeleksi->skor_akhir,
                    'penempatan'        => $p->hasilSeleksi->penempatan_kelas,
                    'status'            => $this->resolveStatusLabel($p, $nilaiCBT),
                    'hasil_seleksi'     => $p->hasilSeleksi,
                    'sudah_publish'     => $p->hasilSeleksi->is_finalisasi,
                ];
            }

            // Jika belum dihitung, hitung sementara untuk tampilan preview (opsional, tapi user minta review sebelum publish)
            // Namun Hitung Semua akan menyimpan draft. Jadi di sini tampilkan null jika belum proses.
            return [
                'pendaftaran'       => $p,
                'nama'              => $p->user->name,
                'jurusan'           => $p->jurusan->nama,
                'nilai_rapor'       => $nilaiRapor,
                'nilai_cbt'         => $nilaiCBT,
                'bonus_sertifikat'  => 0,
                'skor_akhir'        => null,
                'penempatan'        => null,
                'status'            => $this->resolveStatusLabel($p, $nilaiCBT),
                'hasil_seleksi'     => null,
                'sudah_publish'     => false,
            ];
        });

        // Dashboard Stats
        $totalSiswa         = $rows->count();
        $totalDiterima      = $rows->where('status', 'Diterima')->count();
        $totalTidakDiterima = $rows->where('status', 'Tidak Diterima')->count();
        $totalGugur         = $rows->where('status', 'Tidak Hadir CBT')->count();
        $sudahDihitung      = $rows->where('skor_akhir', '!==', null)->count();
        $sudahPublish       = HasilSeleksi::where('is_finalisasi', true)->exists();

        // Ambil settings untuk display formula
        $settings = \App\Models\Pengaturan::whereIn('key', ['bobot_ujian', 'bobot_rapor', 'ambang_unggulan'])->pluck('value', 'key');

        return view('admin.penempatan.index', compact(
            'rows', 'jurusans', 'filterJurusan', 'totalSiswa', 'totalDiterima', 
            'totalTidakDiterima', 'totalGugur', 'sudahDihitung', 'sudahPublish', 'settings'
        ));
    }

    /**
     * Hitung Semua: hitung skor & penempatan, simpan ke hasil_seleksis sebagai draft
     */
    public function prosesSeleksi(Request $request)
    {
        // Ambil pengaturan dinamis
        $weights = \App\Models\Pengaturan::whereIn('key', ['bobot_ujian', 'bobot_rapor', 'ambang_unggulan'])->pluck('value', 'key');
        $wUjian    = (float) ($weights['bobot_ujian'] ?? 30) / 100;
        $wRapor    = (float) ($weights['bobot_rapor'] ?? 70) / 100;
        $threshold = (float) ($weights['ambang_unggulan'] ?? 70);

        // Ambil pendaftar lolos admin untuk diseleksi
        $pendaftarans = Pendaftaran::with(['user', 'jurusan', 'berkas'])
            ->whereIn('status', ['lolos_admin', 'sudah_ujian', 'siap_finalisasi'])
            ->get();

        if ($pendaftarans->isEmpty()) {
            return back()->with('error', 'Belum ada pendaftar yang siap diseleksi.');
        }

        \DB::transaction(function() use ($pendaftarans, $wUjian, $wRapor, $threshold) {
            foreach ($pendaftarans as $p) {
                $hasilUjian = HasilUjian::where('user_id', $p->user_id)->first();
                $nilaiCBT   = $hasilUjian ? (float) $hasilUjian->skor : null;
                $nilaiRapor = (float) $p->nilai_rapor;

                // 1. Bonus Sertifikat (Terbaik)
                $bonusVal = 0;
                $sertifikats = $p->berkas->where('jenis_berkas', 'sertifikat')->where('status_verifikasi', 'valid');
                foreach ($sertifikats as $s) {
                    $bonus = self::BONUS_SERTIFIKAT[$s->tingkat_prestasi] ?? 0;
                    if ($bonus > $bonusVal) $bonusVal = $bonus;
                }

                // 2. Score Formula
                $skorAkhir = 0;
                $statusKelulusan = false;
                $kategori = 'TIDAK DITERIMA';
                $penempatan = null;

                if ($nilaiCBT !== null) {
                    $skorAkhir = round(($wRapor * $nilaiRapor) + ($wUjian * $nilaiCBT) + $bonusVal, 2);
                    $penempatan = $skorAkhir >= $threshold ? 'Unggulan' : 'Reguler';
                    $statusKelulusan = true;
                    $kategori = 'DITERIMA';
                } else {
                    $kategori = 'GUGUR'; // Tidak hadir CBT
                }

                HasilSeleksi::updateOrCreate(
                    ['pendaftaran_id' => $p->id],
                    [
                        'skor_akhir'         => $skorAkhir,
                        'bonus_sertifikat'   => $bonusVal,
                        'penempatan_kelas'   => $penempatan,
                        'ranking'            => 0,
                        'status_kelulusan'   => $statusKelulusan,
                        'kategori_kelulusan' => $kategori,
                        'is_finalisasi'      => false,
                    ]
                );

                $p->update(['status' => 'siap_finalisasi']);
            }
        });

        return back()->with('success', '✅ Perhitungan skor dan penempatan selesai menggunakan bobot '.($wRapor*100).'% Rapor & '.($wUjian*100).'% CBT.');
    }

    /**
     * Publish Hasil: finalisasi & tampilkan ke siswa
     */
    public function publishPengumuman()
    {
        $hasilDraft = HasilSeleksi::where('is_finalisasi', false)->get();

        if ($hasilDraft->isEmpty()) {
            return back()->with('error', 'Belum ada hasil yang dihitung atau sudah dipublish.');
        }

        \DB::transaction(function() use ($hasilDraft) {
            foreach ($hasilDraft as $hasil) {
                $hasil->update(['is_finalisasi' => true]);

                // Update status pendaftaran
                if ($hasil->kategori_kelulusan === 'GUGUR') {
                    $hasil->pendaftaran->update(['status' => 'gugur']);
                } else {
                    $hasil->pendaftaran->update(['status' => 'siap_diumumkan']);
                }
            }
        });

        return back()->with('success', '🎉 Hasil seleksi berhasil dipublish! Siswa sekarang dapat melihat hasil di dashboard mereka.');
    }

    /**
     * Tentukan label status tampilan berdasarkan kondisi siswa
     */
    private function resolveStatusLabel(Pendaftaran $p, ?float $nilaiCBT): string
    {
        $hs = $p->hasilSeleksi;

        // Jika sudah ada hasil tersimpan
        if ($hs) {
            return match($hs->kategori_kelulusan) {
                'DITERIMA'       => 'Diterima',
                'TIDAK DITERIMA' => 'Tidak Diterima',
                'GUGUR'          => 'Tidak Hadir CBT',
                default          => 'Terdaftar',
            };
        }

        // Cek status pendaftaran
        if ($p->status === 'tidak_mengikuti_ujian' || $p->status === 'gugur') {
            return 'Tidak Hadir CBT';
        }

        if ($nilaiCBT === null && !in_array($p->status, ['lolos_admin'])) {
            return 'Belum CBT';
        }

        return 'Belum Dihitung';
    }
}
