<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Pendaftaran;
use App\Models\HasilUjian;
use App\Models\HasilSeleksi;

class SeleksiController extends Controller
{
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

        // Hasil seleksi untuk tabel bawah (diurutkan per jurusan lalu ranking)
        $hasil = HasilSeleksi::with(['pendaftaran.user', 'pendaftaran.jurusan'])
            ->join('pendaftarans', 'hasil_seleksis.pendaftaran_id', '=', 'pendaftarans.id')
            ->orderBy('pendaftarans.jurusan_id')
            ->orderBy('hasil_seleksis.ranking')
            ->select('hasil_seleksis.*')
            ->get();

        // Hitung total pendaftar yang masuk seleksi per jurusan
        $totalPerJurusan = Pendaftaran::whereIn('status', [
                'sudah_ujian', 
                'siap_finalisasi', 
                'siap_diumumkan', 
                'gugur', 
                'tidak_mengikuti_ujian'
            ])
            ->select('jurusan_id', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
            ->groupBy('jurusan_id')
            ->pluck('total', 'jurusan_id')
            ->all();

        $settings = \App\Models\Pengaturan::pluck('value', 'key')->all();

        return view('admin.seleksi.index', compact(
            'semua',
            'statusSummary',
            'adaYangBisaSeleksi',
            'adaHasilSeleksi',
            'sudahDifinalisasi',
            'hasil',
            'totalPerJurusan',
            'settings'
        ));
    }

    // ──────────────────────────────────────────
    // JALANKAN SELEKSI (Per Jurusan + Bonus Sertifikat)
    // ──────────────────────────────────────────
    public function jalankanSeleksi(Request $request)
    {
        // Blokir jika sudah difinalisasi
        if (HasilSeleksi::where('is_finalisasi', true)->exists()) {
            return redirect()->route('admin.seleksi.index')
                ->with('error', 'Hasil seleksi sudah difinalisasi dan tidak dapat diubah lagi.');
        }

        // Get weights from settings
        $settings   = \App\Models\Pengaturan::pluck('value', 'key')->all();
        $bobotRapor = (float) ($settings['bobot_rapor'] ?? 70) / 100;
        $bobotUjian = (float) ($settings['bobot_ujian'] ?? 30) / 100;

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

        // ── Hitung skor & Kelompokkan per Jurusan ──
        $dataPerJurusan = [];
        foreach ($pendaftarans as $p) {
            $ujian     = HasilUjian::where('user_id', $p->user_id)->first();
            $skorUjian = $ujian ? $ujian->skor : 0;
            
            // Rumus: (60% × Nilai Rapor) + (40% × Nilai Ujian CBT)
            $skorAkhir = round(($bobotRapor * $p->nilai_rapor) + ($bobotUjian * $skorUjian), 2);

            $dataPerJurusan[$p->jurusan_id][] = [
                'pendaftaran_id' => $p->id,
                'skor_akhir'     => $skorAkhir,
                'skor_ujian'     => $skorUjian,
                'nilai_rapor'    => $p->nilai_rapor,
                'waktu_daftar'   => $p->created_at->timestamp,
                'jurusan_id'     => $p->jurusan_id
            ];
        }

            // ── Proses Penentuan Kelulusan Berdasarkan Skor (Threshold 60) ──
        $jumlahProses = 0;
        foreach ($dataPerJurusan as $jurusanId => $siswas) {
            foreach ($siswas as $item) {
                // Tentukan Kelulusan Berdasarkan Skor (Bukan Quota)
                $isLulus = ($item['skor_akhir'] >= 60);
                $statusKelulusan = $isLulus ? 'DITERIMA' : 'TIDAK DITERIMA';

                HasilSeleksi::updateOrCreate(
                    ['pendaftaran_id' => $item['pendaftaran_id']],
                    [
                        'skor_akhir'         => $item['skor_akhir'],
                        'ranking'            => 0, // Ranking diabaikan dalam mode threshold
                        'status_kelulusan'   => $isLulus, 
                        'kategori_kelulusan' => $statusKelulusan,
                        'is_finalisasi'      => false,
                    ]
                );

                Pendaftaran::where('id', $item['pendaftaran_id'])
                    ->update(['status' => 'siap_finalisasi']);

                $jumlahProses++;
            }
        }

        return redirect()->route('admin.seleksi.index')
            ->with('success', "Proses seleksi berhasil! {$jumlahProses} siswa telah diproses berdasarkan ambang batas skor 60.");
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
        // Ambil semua hasil draft
        $hasilList = HasilSeleksi::where('is_finalisasi', false)->get();

        // Tandai siswa tidak ikut ujian sebagai GUGUR
        $tidakIkutUjian = Pendaftaran::where('status', 'tidak_mengikuti_ujian')->get();
        foreach ($tidakIkutUjian as $p) {
            $p->update(['status' => 'gugur']);
            
            // Masukkan ke hasil seleksi sebagai GUGUR jika belum ada
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

        if ($hasilList->isEmpty() && $tidakIkutUjian->isEmpty()) {
            return redirect()->route('admin.seleksi.index')->with('error', 'Tidak ada data untuk difinalisasi.');
        }

        foreach ($hasilList as $hasil) {
            $hasil->update(['is_finalisasi' => true]);
            $hasil->pendaftaran->update(['status' => 'siap_diumumkan']);
        }

        return redirect()->route('admin.seleksi.index')->with('success', 'Finalisasi berhasil! Data telah dikunci dan status kelulusan diumumkan.');
    }

}
