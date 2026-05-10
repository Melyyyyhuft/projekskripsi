<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Pendaftaran;
use App\Models\HasilUjian;
use App\Models\HasilSeleksi;

class SeleksiController extends Controller
{
    // Ambang batas kategori kelulusan (bisa diatur lewat DB nantinya)
    const AMBANG_UNGGULAN = 80;

    // ──────────────────────────────────────────
    // INDEX — tampilkan halaman seleksi lengkap
    // ──────────────────────────────────────────
    public function index()
    {
        // Sudah difinalisasi? Kunci semua aksi
        $sudahDifinalisasi = Pendaftaran::where('status', 'siap_diumumkan')->exists()
            || HasilSeleksi::where('is_finalisasi', true)->exists();

        // Ambil semua siswa yang relevan untuk seleksi
        $semua = Pendaftaran::with(['user', 'jurusan', 'hasilSeleksi'])
            ->whereIn('status', [
                'lolos_admin',
                'sudah_ujian',
                'tidak_mengikuti_ujian',
                'siap_finalisasi',
                'siap_diumumkan',
                'gugur',
                'revisi'
            ])
            ->orderBy('updated_at', 'desc')
            ->get();

        // Lampirkan hasil ujian ke setiap record
        $semua->each(function ($p) {
            $p->hasil_ujian = HasilUjian::where('user_id', $p->user_id)->first();
        });

        // ── Pengelompokan Status ──
        $belumUjian          = $semua->where('status', 'lolos_admin');
        $sudahUjian          = $semua->whereIn('status', ['sudah_ujian', 'siap_finalisasi', 'siap_diumumkan']);
        $tidakMengikutiUjian = $semua->whereIn('status', ['tidak_mengikuti_ujian', 'gugur']);

        // Sudah diseleksi = punya hasil seleksi (draft maupun dikunci)
        $sudahDiSeleksi   = $sudahUjian->filter(fn($p) => $p->hasilSeleksi !== null);
        $belumDiSeleksi   = $sudahUjian->filter(fn($p) => $p->hasilSeleksi === null && !$p->ditunda_seleksi);
        $ditundaSeleksi   = $semua->where('ditunda_seleksi', true);

        // Summary badge untuk panel atas
        $statusSummary = [
            'belum_ujian'           => $belumUjian->count(),
            'sudah_ujian'           => $sudahUjian->count(),
            'sudah_diseleksi'       => $sudahDiSeleksi->count(),
            'belum_diseleksi'       => $belumDiSeleksi->count(),
            'ditunda'               => $ditundaSeleksi->count(),
            'tidak_mengikuti_ujian' => $tidakMengikutiUjian->count(),
        ];

        // Apakah ada yang bisa diseleksi saat ini?
        $adaYangBisaSeleksi = $semua
            ->whereIn('status', ['sudah_ujian'])
            ->where('ditunda_seleksi', false)
            ->isNotEmpty();

        // Apakah ada hasil seleksi (draft)?
        $adaHasilSeleksi = HasilSeleksi::where('is_finalisasi', false)->exists();

        // Hasil seleksi untuk tabel bawah
        $hasil = HasilSeleksi::with(['pendaftaran.user', 'pendaftaran.jurusan'])
            ->orderBy('skor_akhir', 'desc')
            ->get();

        $settings = \App\Models\Pengaturan::pluck('value', 'key')->all();

        return view('admin.seleksi.index', compact(
            'semua',
            'statusSummary',
            'adaYangBisaSeleksi',
            'adaHasilSeleksi',
            'sudahDifinalisasi',
            'hasil',
            'settings'
        ));
    }

    // ──────────────────────────────────────────
    // JALANKAN SELEKSI (Per Jurusan + Bonus Sertifikat)
    // ──────────────────────────────────────────
    public function jalankanSeleksi(Request $request)
    {
        // Ambil Bobot dari Pengaturan
        $settings = \App\Models\Pengaturan::pluck('value', 'key')->all();
        $bobotUjian = ($settings['bobot_ujian'] ?? 60) / 100;
        $bobotRapor = ($settings['bobot_rapor'] ?? 40) / 100;

        // Blokir jika sudah difinalisasi
        if (HasilSeleksi::where('is_finalisasi', true)->exists()) {
            return redirect()->route('admin.seleksi.index')
                ->with('error', 'Hasil seleksi sudah difinalisasi dan tidak dapat diubah lagi.');
        }

        $mode = $request->input('mode', 'semua'); // 'semua' | 'terpilih'
        $terpilihIds = $request->input('pendaftaran_ids', []);

        // ── Tentukan target siswa yang akan diproses ──
        $query = Pendaftaran::with(['user', 'jurusan', 'berkas'])
            ->where('status', 'sudah_ujian')
            ->where('ditunda_seleksi', false);

        if ($mode === 'terpilih' && !empty($terpilihIds)) {
            $query->whereIn('id', $terpilihIds);
        }

        $pendaftarans = $query->get();

        if ($pendaftarans->isEmpty()) {
            return redirect()->route('admin.seleksi.index')
                ->with('error', 'Tidak ada siswa yang memenuhi syarat untuk diseleksi (sudah ujian & tidak ditunda).');
        }

        // ── Mapping Bonus Sertifikat ──
        $bonusMapping = [
            'Sekolah'         => 2,
            'Kecamatan'       => 3,
            'Kabupaten/Kota'  => 5,
            'Provinsi'        => 10,
            'Nasional'        => 15,
            'Internasional'   => 15,
        ];

        // ── Hitung skor & Kelompokkan per Jurusan ──
        $dataPerJurusan = [];
        foreach ($pendaftarans as $p) {
            $ujian     = HasilUjian::where('user_id', $p->user_id)->first();
            $skorUjian = $ujian ? $ujian->skor : 0;
            
            // Hitung Bonus Sertifikat (hanya yang valid)
            $bonusSertifikat = 0;
            $sertifikats = $p->berkas->where('jenis_berkas', 'sertifikat')->where('status_verifikasi', 'valid');
            foreach ($sertifikats as $sert) {
                $bonusSertifikat += $bonusMapping[$sert->tingkat_prestasi] ?? 0;
            }

            // Rumus: (Ujian * Bobot) + (Rapor * Bobot) + Bonus
            $skorAkhir = round(($bobotUjian * $skorUjian) + ($bobotRapor * $p->nilai_rapor) + $bonusSertifikat, 2);

            $dataPerJurusan[$p->jurusan_id][] = [
                'pendaftaran_id' => $p->id,
                'skor_akhir'     => $skorAkhir,
                'jurusan_id'     => $p->jurusan_id
            ];
        }

        // ── Proses Ranking per Jurusan ──
        $jumlahProses = 0;
        foreach ($dataPerJurusan as $jurusanId => $siswas) {
            // Urutkan dari skor tertinggi
            usort($siswas, fn($a, $b) => $b['skor_akhir'] <=> $a['skor_akhir']);

            $rank = 1;
            foreach ($siswas as $item) {
                // Kategori berdasarkan skor akhir
                $kategori = $item['skor_akhir'] >= self::AMBANG_UNGGULAN ? 'Unggulan' : 'Reguler';

                HasilSeleksi::updateOrCreate(
                    ['pendaftaran_id' => $item['pendaftaran_id']],
                    [
                        'skor_akhir'         => $item['skor_akhir'],
                        'ranking'            => $rank,
                        'status_kelulusan'   => true, // Default lulus karena sudah masuk kuota pendaftaran
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

        return redirect()->route('admin.seleksi.index')
            ->with('success', "Proses seleksi berhasil! {$jumlahProses} siswa telah dikalkulasi (Nilai + Bonus Sertifikat) dan diranking per jurusan.");
    }

    public function tundaSeleksi(Request $request)
    {
        if (HasilSeleksi::where('is_finalisasi', true)->exists()) {
            return redirect()->route('admin.seleksi.index')->with('error', 'Hasil seleksi sudah difinalisasi.');
        }

        $request->validate([
            'pendaftaran_id' => 'required|exists:pendaftarans,id',
            'aksi'           => 'required|in:tunda,aktifkan',
        ]);

        $p = Pendaftaran::findOrFail($request->pendaftaran_id);
        $p->update(['ditunda_seleksi' => ($request->aksi === 'tunda')]);

        return redirect()->route('admin.seleksi.index')->with('success', 'Status tunda berhasil diperbarui.');
    }

    public function tandaTidakIkutUjian(Request $request)
    {
        if (HasilSeleksi::where('is_finalisasi', true)->exists()) {
            return redirect()->route('admin.seleksi.index')->with('error', 'Hasil seleksi sudah difinalisasi.');
        }

        $request->validate(['pendaftaran_id' => 'required|exists:pendaftarans,id']);
        $p = Pendaftaran::findOrFail($request->pendaftaran_id);
        $p->update(['status' => 'tidak_mengikuti_ujian']);

        return redirect()->route('admin.seleksi.index')->with('success', 'Siswa ditandai tidak mengikuti ujian.');
    }

    public function finalisasi()
    {
        $hasilList = HasilSeleksi::where('is_finalisasi', false)->get();

        if ($hasilList->isEmpty()) {
            return redirect()->route('admin.seleksi.index')->with('error', 'Tidak ada hasil draft untuk difinalisasi.');
        }

        foreach ($hasilList as $hasil) {
            $hasil->update(['is_finalisasi' => true]);
            $hasil->pendaftaran->update(['status' => 'siap_diumumkan']);
        }

        Pendaftaran::where('status', 'tidak_mengikuti_ujian')->update(['status' => 'gugur']);

        return redirect()->route('admin.seleksi.index')->with('success', 'Finalisasi berhasil! Hasil telah diumumkan.');
    }
}
