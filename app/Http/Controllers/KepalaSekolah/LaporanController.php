<?php

namespace App\Http\Controllers\KepalaSekolah;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pendaftaran;
use App\Models\Jurusan;
use App\Models\HasilSeleksi;
use App\Models\HasilUjian;
use App\Models\Pengaturan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $settings = Pengaturan::pluck('value', 'key')->all();
        $jurusans = Jurusan::all();

        // Total registrations overall
        $totalPendaftarKeseluruhan = Pendaftaran::count();
        $totalVerifikasi = Pendaftaran::whereNotIn('status', ['draft', 'menunggu_verifikasi', 'revisi'])->count();

        // Published results query
        $publishedQuery = HasilSeleksi::where('is_finalisasi', true)
            ->with(['pendaftaran.user', 'pendaftaran.jurusan', 'pendaftaran.hasilUjian']);

        $totalPublished     = (clone $publishedQuery)->count();
        $totalDiterima      = (clone $publishedQuery)->where('kategori_kelulusan', 'DITERIMA')->count();
        $totalTidakDiterima = (clone $publishedQuery)->where('kategori_kelulusan', 'TIDAK DITERIMA')->count();
        $totalTidakHadirCBT = (clone $publishedQuery)->where('kategori_kelulusan', 'TIDAK HADIR CBT')->count();
        $totalKuota         = $jurusans->sum('kuota');

        // Rekap per jurusan
        $rekapJurusan = [];
        foreach ($jurusans as $j) {
            $pendaftarJurusanCount = Pendaftaran::where('jurusan_id', $j->id)->count();

            // Published hasil seleksi for this jurusan
            $hasilJurusan = HasilSeleksi::where('is_finalisasi', true)
                ->whereHas('pendaftaran', function($q) use ($j) {
                    $q->where('jurusan_id', $j->id);
                })
                ->with(['pendaftaran.hasilUjian'])
                ->get();

            $publishedCount     = $hasilJurusan->count();
            $diterimaCount      = $hasilJurusan->where('kategori_kelulusan', 'DITERIMA')->count();
            $tidakDiterimaCount = $hasilJurusan->where('kategori_kelulusan', 'TIDAK DITERIMA')->count();
            $tidakHadirCBTCount = $hasilJurusan->where('kategori_kelulusan', 'TIDAK HADIR CBT')->count();

            // Calculate averages from published students
            $avgRapor     = 0;
            $avgCBT       = 0;
            $avgSkorAkhir = 0;
            $maxSkor      = 0;
            $minSkor      = 0;

            if ($publishedCount > 0) {
                $avgSkorAkhir = round($hasilJurusan->avg('skor_akhir') ?? 0, 2);
                $maxSkor      = round($hasilJurusan->max('skor_akhir') ?? 0, 2);
                $minSkor      = round($hasilJurusan->min('skor_akhir') ?? 0, 2);

                $raporValues = $hasilJurusan->map(fn($h) => (float) ($h->pendaftaran->nilai_rapor ?? 0))->filter(fn($v) => $v > 0);
                $avgRapor    = $raporValues->isNotEmpty() ? round($raporValues->avg(), 2) : 0;

                $cbtValues = $hasilJurusan->map(fn($h) => (float) ($h->pendaftaran->hasilUjian->skor ?? 0))->filter(fn($v) => $v > 0);
                $avgCBT    = $cbtValues->isNotEmpty() ? round($cbtValues->avg(), 2) : 0;
            }

            $persenKeterisian = $j->kuota > 0 ? round(($diterimaCount / $j->kuota) * 100, 1) : 0;
            $sisaKuota        = max(0, $j->kuota - $diterimaCount);

            $rekapJurusan[] = [
                'jurusan'            => $j,
                'kuota'              => $j->kuota,
                'total_pendaftar'    => $pendaftarJurusanCount,
                'total_published'    => $publishedCount,
                'diterima'           => $diterimaCount,
                'tidak_diterima'     => $tidakDiterimaCount,
                'tidak_hadir_cbt'    => $tidakHadirCBTCount,
                'persen_keterisian'  => $persenKeterisian,
                'sisa_kuota'         => $sisaKuota,
                'avg_rapor'          => $avgRapor,
                'avg_cbt'            => $avgCBT,
                'avg_skor_akhir'     => $avgSkorAkhir,
                'max_skor'           => $maxSkor,
                'min_skor'           => $minSkor,
            ];
        }

        // Overall averages
        $allPublished        = (clone $publishedQuery)->get();
        $avgSkorKeseluruhan  = $allPublished->isNotEmpty() ? round($allPublished->avg('skor_akhir') ?? 0, 2) : 0;
        $avgRaporKeseluruhan = $allPublished->isNotEmpty() ? round($allPublished->map(fn($h) => (float) ($h->pendaftaran->nilai_rapor ?? 0))->filter(fn($v) => $v > 0)->avg() ?? 0, 2) : 0;
        $avgCbtKeseluruhan   = $allPublished->isNotEmpty() ? round($allPublished->map(fn($h) => (float) ($h->pendaftaran->hasilUjian->skor ?? 0))->filter(fn($v) => $v > 0)->avg() ?? 0, 2) : 0;

        // Detail list with filters
        $filterJurusan = $request->query('jurusan_id');
        $filterStatus  = $request->query('status');
        $search        = $request->query('q');

        $listQuery = HasilSeleksi::where('is_finalisasi', true)
            ->with(['pendaftaran.user', 'pendaftaran.jurusan', 'pendaftaran.hasilUjian'])
            ->orderBy('skor_akhir', 'desc');

        if (!empty($filterJurusan)) {
            $listQuery->whereHas('pendaftaran', function($q) use ($filterJurusan) {
                $q->where('jurusan_id', $filterJurusan);
            });
        }

        if (!empty($filterStatus)) {
            $listQuery->where('kategori_kelulusan', $filterStatus);
        }

        if (!empty($search)) {
            $listQuery->whereHas('pendaftaran', function($q) use ($search) {
                $q->where('nomor_pendaftaran', 'like', "%{$search}%")
                  ->orWhere('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('asal_sekolah', 'like', "%{$search}%")
                  ->orWhereHas('user', function($qu) use ($search) {
                      $qu->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $daftarHasil = $listQuery->paginate(20)->withQueryString();

        return view('kepala_sekolah.laporan', compact(
            'settings',
            'jurusans',
            'totalPendaftarKeseluruhan',
            'totalVerifikasi',
            'totalPublished',
            'totalDiterima',
            'totalTidakDiterima',
            'totalTidakHadirCBT',
            'totalKuota',
            'rekapJurusan',
            'avgSkorKeseluruhan',
            'avgRaporKeseluruhan',
            'avgCbtKeseluruhan',
            'daftarHasil',
            'filterJurusan',
            'filterStatus',
            'search'
        ));
    }

    public function profile()
    {
        $user     = Auth::user();
        $settings = Pengaturan::pluck('value', 'key')->all();
        return view('kepala_sekolah.profile', compact('user', 'settings'));
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, Auth::user()->password)) {
            return back()->with('error', 'Password saat ini tidak cocok.');
        }

        Auth::user()->update([
            'password' => Hash::make($request->password)
        ]);

        return back()->with('success', 'Password berhasil diperbarui.');
    }
}
