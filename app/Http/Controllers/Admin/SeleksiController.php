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
     * Ambang batas kategori kelulusan:
     * ≥ 85 = Lulus Jalur Unggulan
     * < 85 = Lulus Jalur Reguler
     * Tidak Mengikuti Ujian = Gugur
     */
    const AMBANG_UNGGULAN = 85;

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

        return view('admin.seleksi.index', compact(
            'semua',
            'statusSummary',
            'adaYangBisaSeleksi',
            'adaHasilSeleksi',
            'sudahDifinalisasi',
            'hasil'
        ));
    }

    // ──────────────────────────────────────────
    // JALANKAN SELEKSI (Fleksibel)
    // ──────────────────────────────────────────
    public function jalankanSeleksi(Request $request)
    {
        // Blokir jika sudah difinalisasi
        if (HasilSeleksi::where('is_finalisasi', true)->exists()) {
            return redirect()->route('admin.seleksi.index')
                ->with('error', 'Hasil seleksi sudah difinalisasi dan tidak dapat diubah lagi.');
        }

        $mode = $request->input('mode', 'semua'); // 'semua' | 'terpilih'
        $terpilihIds = $request->input('pendaftaran_ids', []);

        // ── Tentukan target siswa yang akan diproses ──
        if ($mode === 'terpilih' && !empty($terpilihIds)) {
            // Hanya proses siswa yang dipilih + sudah ujian + tidak ditunda
            $pendaftarans = Pendaftaran::with('user')
                ->whereIn('id', $terpilihIds)
                ->where('status', 'sudah_ujian')
                ->where('ditunda_seleksi', false)
                ->get();

            if ($pendaftarans->isEmpty()) {
                return redirect()->route('admin.seleksi.index')
                    ->with('error', 'Tidak ada siswa terpilih yang memenuhi syarat (sudah ujian & tidak ditunda).');
            }
        } else {
            // Proses semua yang sudah ujian & tidak ditunda
            $pendaftarans = Pendaftaran::with('user')
                ->where('status', 'sudah_ujian')
                ->where('ditunda_seleksi', false)
                ->get();

            if ($pendaftarans->isEmpty()) {
                return redirect()->route('admin.seleksi.index')
                    ->with('error', 'Tidak ada siswa yang memenuhi syarat untuk diseleksi (sudah ujian & tidak ditunda).');
            }
        }

        // ── Validasi nilai rapor ──
        $missingRapor = $pendaftarans->filter(fn($p) => $p->nilai_rapor === null);
        if ($missingRapor->isNotEmpty()) {
            $nama = $missingRapor->pluck('user.name')->implode(', ');
            return redirect()->route('admin.seleksi.index')
                ->with('error', "Siswa berikut belum memiliki nilai rapor: {$nama}");
        }

        // ── Hitung skor & simpan hasil ──
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
            // Kategori: ≥85 Unggulan, <85 Reguler
            if ($item['skor_akhir'] >= self::AMBANG_UNGGULAN) {
                $kategori        = 'Unggulan';
                $statusKelulusan = true;
            } else {
                $kategori        = 'Reguler';
                $statusKelulusan = true;
            }

            // Upsert: perbarui jika sudah ada (draft), buat baru jika belum
            HasilSeleksi::updateOrCreate(
                ['pendaftaran_id' => $item['pendaftaran_id']],
                [
                    'skor_akhir'         => $item['skor_akhir'],
                    'ranking'            => $rank,
                    'status_kelulusan'   => $statusKelulusan,
                    'kategori_kelulusan' => $kategori,
                    'is_finalisasi'      => false,
                ]
            );

            Pendaftaran::where('id', $item['pendaftaran_id'])
                ->update(['status' => 'siap_finalisasi']);

            $rank++;
        }

        $jumlah = count($hasilList);
        return redirect()->route('admin.seleksi.index')
            ->with('success', "Proses seleksi berhasil! {$jumlah} siswa telah dikelompokkan. Hasil masih dapat diubah sebelum finalisasi.");
    }

    // ──────────────────────────────────────────
    // TUNDA / AKTIFKAN KEMBALI SELEKSI SISWA
    // ──────────────────────────────────────────
    public function tundaSeleksi(Request $request)
    {
        // Blokir jika sudah difinalisasi
        if (HasilSeleksi::where('is_finalisasi', true)->exists()) {
            return redirect()->route('admin.seleksi.index')
                ->with('error', 'Hasil seleksi sudah difinalisasi. Tidak dapat mengubah status.');
        }

        $request->validate([
            'pendaftaran_id' => 'required|exists:pendaftarans,id',
            'aksi'           => 'required|in:tunda,aktifkan',
        ]);

        $p = Pendaftaran::findOrFail($request->pendaftaran_id);

        // Tidak bisa tunda siswa yang sudah difinalisasi
        if ($p->hasilSeleksi && $p->hasilSeleksi->is_finalisasi) {
            return redirect()->route('admin.seleksi.index')
                ->with('error', 'Siswa ini sudah difinalisasi dan tidak bisa ditunda.');
        }

        $ditunda = $request->aksi === 'tunda';
        $p->update(['ditunda_seleksi' => $ditunda]);

        $pesan = $ditunda
            ? "Siswa {$p->user->name} ditandai sebagai 'Ditunda Seleksi'."
            : "Siswa {$p->user->name} diaktifkan kembali untuk seleksi.";

        return redirect()->route('admin.seleksi.index')->with('success', $pesan);
    }

    // ──────────────────────────────────────────
    // TANDAI "TIDAK MENGIKUTI UJIAN" (tanpa tutup ujian)
    // ──────────────────────────────────────────
    public function tandaTidakIkutUjian(Request $request)
    {
        // Blokir jika sudah difinalisasi
        if (HasilSeleksi::where('is_finalisasi', true)->exists()) {
            return redirect()->route('admin.seleksi.index')
                ->with('error', 'Hasil seleksi sudah difinalisasi. Tidak dapat mengubah status ujian.');
        }

        $request->validate([
            'pendaftaran_id' => 'required|exists:pendaftarans,id',
        ]);

        $p = Pendaftaran::findOrFail($request->pendaftaran_id);

        // Hanya bisa ditandai jika masih lolos_admin (belum ujian)
        if ($p->status !== 'lolos_admin') {
            return redirect()->route('admin.seleksi.index')
                ->with('error', "Siswa {$p->user->name} tidak bisa ditandai (status saat ini: {$p->status}).");
        }

        $p->update(['status' => 'tidak_mengikuti_ujian']);

        return redirect()->route('admin.seleksi.index')
            ->with('success', "Siswa {$p->user->name} ditandai sebagai 'Tidak Mengikuti Ujian'. Akan dinyatakan Gugur saat seleksi dijalankan.");
    }

    // ──────────────────────────────────────────
    // FINALISASI — kunci semua hasil draft
    // ──────────────────────────────────────────
    public function finalisasi()
    {
        $hasilList = HasilSeleksi::with('pendaftaran')
            ->where('is_finalisasi', false)
            ->get();

        if ($hasilList->isEmpty()) {
            return redirect()->route('admin.seleksi.index')
                ->with('error', 'Tidak ada hasil seleksi yang siap difinalisasi. Jalankan Proses Seleksi terlebih dahulu.');
        }

        // Kunci semua hasil draft
        foreach ($hasilList as $hasil) {
            $hasil->update(['is_finalisasi' => true]);
            if ($hasil->pendaftaran) {
                $hasil->pendaftaran->update(['status' => 'siap_diumumkan']);
            }
        }

        // Siswa tidak_mengikuti_ujian → gugur (sudah final)
        Pendaftaran::where('status', 'tidak_mengikuti_ujian')
            ->update(['status' => 'gugur']);

        return redirect()->route('admin.seleksi.index')
            ->with('success', 'Finalisasi berhasil! Hasil seleksi telah dikunci permanen dan siswa sudah dapat melihat pengumuman.');
    }
}
