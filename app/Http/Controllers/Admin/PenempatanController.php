<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pendaftaran;
use App\Models\HasilUjian;
use App\Models\HasilSeleksi;
use App\Models\Jurusan;
use App\Models\Berkas;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class PenempatanController extends Controller
{


    public function index(Request $request)
    {
        $jurusans = Jurusan::orderBy('nama')->get();

        $fJurusan      = $request->get('jurusan_id');
        $fStatusHasil  = $request->get('status_hasil');
        $fStatusProses = $request->get('status_proses');
        $search        = $request->get('search');

        $statusRelevan = [
            'lolos_admin', 'sudah_ujian', 'siap_finalisasi',
            'siap_diumumkan', 'gugur', 'tidak_mengikuti_ujian',
        ];

        $query = Pendaftaran::with(['user', 'user.hasilUjian', 'jurusan', 'berkas', 'hasilSeleksi']);

        if ($fJurusan) $query->where('jurusan_id', $fJurusan);
        if ($search) {
            $query->whereHas('user', fn($q) => $q->where('name', 'like', "%{$search}%"));
        }
        if ($fStatusHasil) {
            $query->whereHas('hasilSeleksi', fn($q) => $q->where('kategori_kelulusan', $fStatusHasil));
        }
        if ($fStatusProses) {
            if ($fStatusProses === 'Belum Dihitung') {
                $query->whereDoesntHave('hasilSeleksi');
            } elseif ($fStatusProses === 'Sudah Dihitung') {
                $query->whereHas('hasilSeleksi', fn($q) => $q->where('is_finalisasi', false)->where(function($qq) {
                    $qq->whereNull('status_proses')->orWhere('status_proses', '!=', 'Perlu Review');
                }));
            } elseif ($fStatusProses === 'Perlu Review') {
                $query->whereHas('hasilSeleksi', fn($q) => $q->where('status_proses', 'Perlu Review'));
            } elseif ($fStatusProses === 'Sudah Dipublish') {
                $query->whereHas('hasilSeleksi', fn($q) => $q->where('is_finalisasi', true));
            }
        }

        // Get all items for scrolling
        $pendaftarans = $query->orderBy('created_at', 'desc')->get();

        // Stats
        $allData = Pendaftaran::with('hasilSeleksi')->get();
        $hasHasil = $allData->filter(fn($p) => $p->hasilSeleksi !== null);

        $stats = [
            'total'          => $allData->count(),
            'dihitung'       => $hasHasil->count(),
            'belum_publish'  => $hasHasil->filter(fn($p) => !$p->hasilSeleksi->is_finalisasi)->count(),
            'diterima'       => $hasHasil->filter(fn($p) => $p->hasilSeleksi->kategori_kelulusan === 'DITERIMA')->count(),
            'tidak_diterima' => $hasHasil->filter(fn($p) => $p->hasilSeleksi->kategori_kelulusan === 'TIDAK DITERIMA')->count(),
        ];

        // Get weights
        $settings = \App\Models\Pengaturan::pluck('value', 'key')->all();
        $bobotRapor = (float) ($settings['bobot_rapor'] ?? 70);
        $bobotUjian = (float) ($settings['bobot_ujian'] ?? 30);

        return view('admin.penempatan.index', compact(
            'pendaftarans', 'jurusans', 'stats',
            'fJurusan', 'fStatusHasil', 'fStatusProses', 'search',
            'bobotRapor', 'bobotUjian'
        ));
    }

    /**
     * Preview Hitung — returns JSON calculation without saving.
     */
    public function previewHitung(Request $request)
    {
        $ids   = $request->input('selected_ids', []);
        $isAll = $request->input('mode') === 'all';

        $query = Pendaftaran::with(['user', 'jurusan', 'berkas', 'hasilSeleksi'])
            ->whereIn('status', ['lolos_admin', 'sudah_ujian', 'siap_finalisasi', 'siap_diumumkan']);

        if (!$isAll) {
            if (empty($ids)) {
                return response()->json(['error' => 'Pilih minimal satu siswa.'], 422);
            }
            $query->whereIn('id', $ids);
        }

        $students = $query->get();

        if ($students->isEmpty()) {
            return response()->json(['error' => 'Tidak ada siswa yang memenuhi syarat.'], 422);
        }

        $previews = [];
        foreach ($students as $p) {
            $previews[] = $this->calculateStudent($p);
        }

        return response()->json(['data' => $previews]);
    }

    /**
     * Pure calculation logic — returns array, does NOT save.
     */
    private function calculateStudent(Pendaftaran $p): array
    {
        $hs = $p->calculateSelectionResult();
        
        $settings   = \App\Models\Pengaturan::pluck('value', 'key')->all();
        $bobotRapor = (float) ($settings['bobot_rapor'] ?? 70);
        $bobotUjian = (float) ($settings['bobot_ujian'] ?? 30);

        $hasilUjian = HasilUjian::where('user_id', $p->user_id)->first();
        $nilaiCBT   = $hasilUjian ? (float) $hasilUjian->skor : null;
        $nilaiRapor = (float) $p->nilai_rapor;

        // For display in the index table and modals
        return [
            'pendaftaran_id' => $p->id,
            'nama'           => $p->user->name,
            'nisn'           => $p->nisn,
            'jurusan'        => $p->jurusan->nama ?? '-',
            'rapor'          => $nilaiRapor,
            'cbt'            => $nilaiCBT,
            'has_cbt'        => $nilaiCBT !== null,
            'bonus'          => 0,
            'formula'        => $nilaiCBT !== null
                ? "(({$bobotRapor}% × {$nilaiRapor}) + ({$bobotUjian}% × {$nilaiCBT})) = {$hs->skor_akhir}"
                : 'Tidak ada data CBT',
            'skor_akhir'     => (float) $hs->skor_akhir,
            'penempatan'     => '-',
            'kategori'       => $hs->kategori_kelulusan,
            'sudah_dihitung' => true,
            'sudah_publish'  => (bool) $hs->is_finalisasi,
            'is_override'    => (bool) $hs->is_manual_override,
        ];
    }

    /**
     * Hitung Seleksi — actually saves results.
     */
    public function hitungSeleksi(Request $request)
    {
        $ids   = $request->input('selected_ids', []);
        $isAll = $request->input('mode') === 'all';

        // Manual override fields (optional)
        $manualOverrides = $request->input('overrides', []);
        // overrides = [ pendaftaran_id => [ bonus, kategori, penempatan, catatan ] ]

        $query = Pendaftaran::with(['user', 'berkas', 'hasilSeleksi'])
            ->whereIn('status', ['lolos_admin', 'sudah_ujian', 'siap_finalisasi', 'siap_diumumkan']);

        if (!$isAll) {
            if (empty($ids)) return back()->with('error', 'Pilih minimal satu siswa.');
            $query->whereIn('id', $ids);
        }

        $students = $query->get();
        if ($students->isEmpty()) return back()->with('error', 'Tidak ada siswa yang memenuhi syarat.');

        $count = 0;
        DB::transaction(function () use ($students, $manualOverrides, $request, &$count) {
            foreach ($students as $p) {
                $calc = $this->calculateStudent($p);

                // Check for manual overrides for this student
                $override = $manualOverrides[$p->id] ?? null;
                $isManual = false;

                $skorAkhir  = $calc['skor_akhir'];
                $bonusVal   = $calc['bonus'];
                $kategori   = $calc['kategori'];
                $penempatan = $calc['penempatan'];
                $catatan    = null;

                if ($override) {
                    if (isset($override['kategori']) && $override['kategori'] !== '') {
                        $kategori = $override['kategori'];
                        $isManual = true;
                    }
                    if (isset($override['penempatan']) && $override['penempatan'] !== '') {
                        $penempatan = $override['penempatan'];
                        $isManual = true;
                    }
                    if (isset($override['catatan']) && $override['catatan'] !== '') {
                        $catatan = $override['catatan'];
                    }

                    // Recalculate score (Bonus removed)
                    if ($calc['has_cbt']) {
                        $settings = \App\Models\Pengaturan::pluck('value', 'key')->all();
                        $bR = (float) ($settings['bobot_rapor'] ?? 70) / 100;
                        $bU = (float) ($settings['bobot_ujian'] ?? 30) / 100;
                        $skorAkhir = round(($bR * $calc['rapor']) + ($bU * $calc['cbt']), 2);
                    }
                }

                $statusProses = $isManual ? 'Sudah Dihitung' : 'Sudah Dihitung';

                // Check review status from request
                if ($request->input('mark_review') && in_array($p->id, (array) $request->input('review_ids', []))) {
                    $statusProses = 'Perlu Review';
                }

                HasilSeleksi::updateOrCreate(
                    ['pendaftaran_id' => $p->id],
                    [
                        'skor_sistem'        => $calc['skor_akhir'],
                        'bonus_sistem'       => $calc['bonus'],
                        'penempatan_sistem'  => $calc['penempatan'],
                        'kategori_sistem'    => $calc['kategori'],
                        
                        'skor_akhir'         => $skorAkhir,
                        'bonus_sertifikat'   => $bonusVal,
                        'penempatan_kelas'   => $penempatan,
                        'ranking'            => 0,
                        'status_kelulusan'   => $kategori === 'DITERIMA',
                        'kategori_kelulusan' => $kategori,
                        'alasan_penolakan'   => $catatan,
                        'is_finalisasi'      => false,
                        'is_manual_override' => $isManual,
                        'overridden_by'      => $isManual ? Auth::user()->name : null,
                        'overridden_at'      => $isManual ? now() : null,
                        'status_proses'      => $statusProses,
                    ]
                );

                $p->update(['status' => 'siap_finalisasi']);
                $count++;
            }
        });

        return back()->with('success', "Perhitungan berhasil disimpan untuk {$count} siswa.");
    }

    /**
     * Publish Hasil
     */
    public function publishHasil(Request $request)
    {
        $ids   = $request->input('selected_ids', []);
        $isAll = $request->input('mode') === 'all';

        $query = HasilSeleksi::where('is_finalisasi', false)
            ->where(function($q) {
                $q->whereNull('status_proses')->orWhere('status_proses', '!=', 'Perlu Review');
            });

        if (!$isAll) {
            if (empty($ids)) return back()->with('error', 'Pilih minimal satu siswa.');
            $query->whereIn('pendaftaran_id', $ids);
        }

        $drafts = $query->with('pendaftaran')->get();

        if ($drafts->isEmpty()) return back()->with('error', 'Tidak ada hasil yang siap dipublish.');

        DB::transaction(function () use ($drafts) {
            foreach ($drafts as $h) {
                $h->update([
                    'is_finalisasi' => true,
                    'status_proses' => 'Sudah Dipublish',
                ]);

                $newStatus = match ($h->kategori_kelulusan) {
                    'TIDAK HADIR CBT' => 'tidak_mengikuti_ujian',
                    'DITERIMA'        => 'diterima',
                    default           => 'tidak_diterima',
                };

                $h->pendaftaran->update(['status' => $newStatus]);
            }
        });

        return back()->with('success', "Hasil berhasil dipublish untuk {$drafts->count()} siswa.");
    }

    /**
     * Get detail data for modal
     */
    public function getDetail($id)
    {
        $p          = Pendaftaran::with(['user', 'jurusan', 'berkas', 'hasilSeleksi'])->findOrFail($id);
        $hasilUjian = HasilUjian::where('user_id', $p->user_id)->first();
        $calc       = $this->calculateStudent($p);

        return response()->json([
            'pendaftaran_id' => $p->id,
            'nama'           => $p->user->name,
            'nisn'           => $p->nisn,
            'jurusan'        => $p->jurusan->nama,
            'rapor'          => $calc['rapor'],
            'cbt'            => $calc['cbt'],
            'has_cbt'        => $calc['has_cbt'],
            'formula'        => $calc['formula'],
            'sertifikat'     => null,
            'calc_skor'      => $calc['skor_akhir'],
            'calc_kategori'  => $calc['kategori'],
            'calc_penempatan'=> $calc['penempatan'],
            'hasil'          => $p->hasilSeleksi ? [
                'skor'        => (float) $p->hasilSeleksi->skor_akhir,
                'bonus'       => 0,
                'penempatan'  => $p->hasilSeleksi->penempatan_kelas,
                'kategori'    => $p->hasilSeleksi->kategori_kelulusan,
                
                'skor_sistem' => (float) $p->hasilSeleksi->skor_sistem,
                'bonus_sistem'=> 0,
                'penempatan_sistem' => $p->hasilSeleksi->penempatan_sistem,
                'kategori_sistem'   => $p->hasilSeleksi->kategori_sistem,

                'is_publish'  => (bool) $p->hasilSeleksi->is_finalisasi,
                'is_override' => (bool) $p->hasilSeleksi->is_manual_override,
                'override_by' => $p->hasilSeleksi->overridden_by,
                'override_at' => $p->hasilSeleksi->overridden_at,
                'catatan'     => $p->hasilSeleksi->alasan_penolakan,
                'status_proses' => $p->hasilSeleksi->status_proses,
            ] : null,
        ]);
    }

    /**
     * Update individual selection result (Manual Override / Final Decision)
     */
    public function updateHasil(Request $request, $id)
    {
        $p = Pendaftaran::with('hasilSeleksi')->findOrFail($id);
        $hs = $p->hasilSeleksi;

        if (!$hs) {
            return response()->json(['error' => 'Data hasil belum dihitung.'], 422);
        }

        $isOverride = $request->boolean('is_manual_override');
        $catatan = $request->input('catatan');

        if ($isOverride) {
            $bonus      = 0;
            $penempatan = $request->input('penempatan_manual', $hs->penempatan_kelas);
            $kategori   = $request->input('kategori_manual', $hs->kategori_kelulusan);

            // Recalculate score with manual bonus
            $nilaiRapor = (float) $p->nilai_rapor;
            $hasilUjian = HasilUjian::where('user_id', $p->user_id)->first();
            $nilaiCBT   = $hasilUjian ? (float) $hasilUjian->skor : null;

            $skorAkhir = $hs->skor_sistem;
            if ($nilaiCBT !== null) {
                $settings  = \App\Models\Pengaturan::pluck('value', 'key')->all();
                $bR        = (float) ($settings['bobot_rapor'] ?? 70) / 100;
                $bU        = (float) ($settings['bobot_ujian'] ?? 30) / 100;
                $raporPart = round($bR * $nilaiRapor, 2);
                $cbtPart   = round($bU * $nilaiCBT, 2);
                $skorAkhir = round($raporPart + $cbtPart, 2);
            }

            $hs->update([
                'skor_akhir'         => $skorAkhir,
                'bonus_sertifikat'   => $bonus,
                'penempatan_kelas'   => $penempatan,
                'kategori_kelulusan' => $kategori,
                'status_kelulusan'   => $kategori === 'DITERIMA',
                'alasan_penolakan'   => $catatan,
                'is_manual_override' => true,
                'overridden_by'      => Auth::user()->name,
                'overridden_at'      => now(),
                'status_proses'      => 'Sudah Dihitung', // Reset from Perlu Review if it was
            ]);
        } else {
            // Revert to system values or just update comments/status
            $hs->update([
                'skor_akhir'         => $hs->skor_sistem,
                'bonus_sertifikat'   => $hs->bonus_sistem,
                'penempatan_kelas'   => $hs->penempatan_sistem,
                'kategori_kelulusan' => $hs->kategori_sistem,
                'status_kelulusan'   => $hs->kategori_sistem === 'DITERIMA',
                'alasan_penolakan'   => $catatan,
                'is_manual_override' => false,
                'overridden_by'      => null,
                'overridden_at'      => null,
                'status_proses'      => 'Sudah Dihitung',
            ]);
        }

        return response()->json(['success' => 'Keputusan final berhasil disimpan.']);
    }

    /**
     * Generate PDF
     */
    public function generatePDF($id)
    {
        $p = Pendaftaran::with(['user', 'jurusan', 'hasilSeleksi', 'user.hasilUjian'])->findOrFail($id);
        if (!$p->hasilSeleksi || !$p->hasilSeleksi->is_finalisasi) {
            return back()->with('error', 'Hasil belum tersedia atau belum dipublish.');
        }

        $hasil    = $p->hasilSeleksi;
        $settings = \App\Models\Pengaturan::pluck('value', 'key')->all();

        $pdf = Pdf::loadView('siswa.hasil_pdf', [
            'pendaftaran' => $p,
            'hasil'       => $hasil,
            'settings'    => $settings,
        ]);

        return $pdf->download('Hasil_Seleksi_' . $p->user->name . '.pdf');
    }
}
