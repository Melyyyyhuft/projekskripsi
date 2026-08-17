<?php

namespace App\Http\Controllers\KepalaSekolah;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pendaftaran;
use App\Models\Jurusan;
use App\Models\HasilSeleksi;
use App\Models\HasilUjian;
use App\Models\Pengaturan;
use App\Models\PembayaranDaftarUlang;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class LaporanController extends Controller
{
    /**
     * Membangun data laporan (digunakan oleh index() dan exportPdf())
     */
    private function buildLaporanData(Request $request): array
    {
        $settings  = Pengaturan::pluck('value', 'key')->all();
        $jurusans  = Jurusan::orderBy('nama')->get();

        // ── Filter params ──────────────────────────────────────────────
        $filterJurusan        = $request->query('jurusan_id');
        $filterStatusSeleksi  = $request->query('status_seleksi');
        $filterStatusBayar    = $request->query('status_bayar');
        $search               = $request->query('q');

        // ── Base query untuk HasilSeleksi yang sudah difinalisasi ───────
        $baseQuery = HasilSeleksi::where('is_finalisasi', true)
            ->with([
                'pendaftaran.user',
                'pendaftaran.jurusan',
                'pendaftaran.hasilUjian',
                'pendaftaran.pembayaranDaftarUlang',
            ]);

        // Filter Jurusan
        if (!empty($filterJurusan)) {
            $baseQuery->whereHas('pendaftaran', function ($q) use ($filterJurusan) {
                $q->where('jurusan_id', $filterJurusan);
            });
        }

        // Filter Status Seleksi
        if (!empty($filterStatusSeleksi)) {
            $baseQuery->where('kategori_kelulusan', $filterStatusSeleksi);
        }

        // Filter Status Pembayaran Daftar Ulang
        if (!empty($filterStatusBayar)) {
            $baseQuery->whereHas('pendaftaran.pembayaranDaftarUlang', function ($q) use ($filterStatusBayar) {
                $q->where('status', $filterStatusBayar);
            });
        }

        // Filter Pencarian
        if (!empty($search)) {
            $baseQuery->whereHas('pendaftaran', function ($q) use ($search) {
                $q->where('nomor_pendaftaran', 'like', "%{$search}%")
                  ->orWhere('nama_lengkap', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($qu) use ($search) {
                      $qu->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $allPublished = (clone $baseQuery)->get();

        // ── KPI Keseluruhan ─────────────────────────────────────────────
        $totalPublished      = $allPublished->count();
        $totalDiterima       = $allPublished->where('kategori_kelulusan', 'DITERIMA')->count();
        $totalTidakDiterima  = $allPublished->where('kategori_kelulusan', 'TIDAK DITERIMA')->count();
        $totalTidakHadirCBT  = $allPublished->where('kategori_kelulusan', 'TIDAK HADIR CBT')->count();
        $totalKuota          = $jurusans->sum('kuota');
        $avgSkorKeseluruhan  = $allPublished->isNotEmpty()
            ? round($allPublished->avg('skor_akhir') ?? 0, 2) : 0;
        $totalPendaftarKeseluruhan = Pendaftaran::when(!empty($filterJurusan), fn($q) => $q->where('jurusan_id', $filterJurusan))->count();

        // ── Rekap Per Jurusan ────────────────────────────────────────────
        $rekapJurusan = [];
        foreach ($jurusans as $j) {
            $pendaftarJurusanCount = Pendaftaran::where('jurusan_id', $j->id)->count();

            $hasilJurusan = HasilSeleksi::where('is_finalisasi', true)
                ->whereHas('pendaftaran', fn($q) => $q->where('jurusan_id', $j->id))
                ->with(['pendaftaran.hasilUjian'])
                ->get();

            $publishedCount     = $hasilJurusan->count();
            $diterimaCount      = $hasilJurusan->where('kategori_kelulusan', 'DITERIMA')->count();
            $tidakDiterimaCount = $hasilJurusan->where('kategori_kelulusan', 'TIDAK DITERIMA')->count();
            $tidakHadirCBTCount = $hasilJurusan->where('kategori_kelulusan', 'TIDAK HADIR CBT')->count();

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
                'jurusan'           => $j,
                'kuota'             => $j->kuota,
                'total_pendaftar'   => $pendaftarJurusanCount,
                'total_published'   => $publishedCount,
                'diterima'          => $diterimaCount,
                'tidak_diterima'    => $tidakDiterimaCount,
                'tidak_hadir_cbt'   => $tidakHadirCBTCount,
                'persen_keterisian' => $persenKeterisian,
                'sisa_kuota'        => $sisaKuota,
                'avg_rapor'         => $avgRapor,
                'avg_cbt'           => $avgCBT,
                'avg_skor_akhir'    => $avgSkorAkhir,
                'max_skor'          => $maxSkor,
                'min_skor'          => $minSkor,
            ];
        }

        // ── Rekap Pembayaran Daftar Ulang ──────────────────────────────
        $bayarQuery = PembayaranDaftarUlang::with([
            'pendaftaran.user',
            'pendaftaran.jurusan',
        ])->when(!empty($filterJurusan), function ($q) use ($filterJurusan) {
            $q->whereHas('pendaftaran', fn($sq) => $sq->where('jurusan_id', $filterJurusan));
        })->when(!empty($filterStatusBayar), function ($q) use ($filterStatusBayar) {
            $q->where('status', $filterStatusBayar);
        });

        $semuaBayar           = (clone $bayarQuery)->get();
        $totalTagihan         = $semuaBayar->count();
        $totalSudahBayar      = $semuaBayar->where('status', 'lunas')->count();
        $totalBelumBayar      = $semuaBayar->whereIn('status', ['belum_bayar', 'pending'])->count();
        $totalNominalMasuk    = $semuaBayar->where('status', 'lunas')->sum('jumlah');

        $daftarPembayaran = $semuaBayar->sortByDesc('paid_at');

        // ── Data Rinci Hasil Seleksi (paginated) ───────────────────────
        $listQuery = (clone $baseQuery)->orderBy('skor_akhir', 'desc');
        $daftarHasil = $listQuery->paginate(15)->withQueryString();

        // ── Chart Data (JSON) ──────────────────────────────────────────
        $chartJurusanLabels    = [];
        $chartJurusanPendaftar = [];
        $chartJurusanDiterima  = [];

        foreach ($rekapJurusan as $rk) {
            // Nama jurusan singkat untuk chart
            $namaJurusan = $rk['jurusan']->nama;
            if (strlen($namaJurusan) > 20) {
                $namaJurusan = substr($namaJurusan, 0, 20) . '…';
            }
            $chartJurusanLabels[]    = $namaJurusan;
            $chartJurusanPendaftar[] = $rk['total_pendaftar'];
            $chartJurusanDiterima[]  = $rk['diterima'];
        }

        return compact(
            'settings',
            'jurusans',
            'filterJurusan',
            'filterStatusSeleksi',
            'filterStatusBayar',
            'search',
            'totalPendaftarKeseluruhan',
            'totalPublished',
            'totalDiterima',
            'totalTidakDiterima',
            'totalTidakHadirCBT',
            'totalKuota',
            'avgSkorKeseluruhan',
            'rekapJurusan',
            'totalTagihan',
            'totalSudahBayar',
            'totalBelumBayar',
            'totalNominalMasuk',
            'daftarPembayaran',
            'daftarHasil',
            'allPublished',
            'chartJurusanLabels',
            'chartJurusanPendaftar',
            'chartJurusanDiterima'
        );
    }

    /**
     * Halaman Laporan PPDB
     */
    public function index(Request $request)
    {
        $data = $this->buildLaporanData($request);
        return view('kepala_sekolah.laporan', $data);
    }

    /**
     * Export PDF Laporan
     */
    public function exportPdf(Request $request)
    {
        $data = $this->buildLaporanData($request);
        $pdf  = Pdf::loadView('kepala_sekolah.laporan_pdf', $data)
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => true,
                'defaultFont'          => 'sans-serif',
            ]);

        $namaFile = 'Laporan_PPDB_' . str_replace('/', '-', $data['settings']['tahun_ajaran'] ?? date('Y')) . '.pdf';

        return $pdf->download($namaFile);
    }

    /**
     * Export Excel Laporan (multi-sheet)
     */
    public function exportExcel(Request $request)
    {
        $data = $this->buildLaporanData($request);

        $spreadsheet = new Spreadsheet();

        // Style Helpers
        $titleStyle = [
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E3A8A']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
        ];

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2D3748']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ];

        $borderStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CBD5E0'],
                ],
            ],
        ];

        // ─── SHEET 1: RINGKASAN ──────────────────────────────────────
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Ringkasan');

        $sheet1->mergeCells('A1:B1');
        $sheet1->setCellValue('A1', 'RINGKASAN LAPORAN PPDB');
        $sheet1->getStyle('A1:B1')->applyFromArray($titleStyle);
        $sheet1->getRowDimension(1)->setRowHeight(30);

        $sheet1->setCellValue('A3', 'Parameter');
        $sheet1->setCellValue('B3', 'Jumlah / Nilai');
        $sheet1->getStyle('A3:B3')->applyFromArray($headerStyle);
        $sheet1->getRowDimension(3)->setRowHeight(20);

        $sheet1->setCellValue('A4', 'Tahun Ajaran');
        $sheet1->setCellValue('B4', $data['settings']['tahun_ajaran'] ?? '2026/2027');
        $sheet1->setCellValue('A5', 'Total Pendaftar');
        $sheet1->setCellValue('B5', $data['totalPendaftarKeseluruhan']);
        $sheet1->setCellValue('A6', 'Total Diterima (Lolos Seleksi)');
        $sheet1->setCellValue('B6', $data['totalDiterima']);
        $sheet1->setCellValue('A7', 'Tidak Diterima');
        $sheet1->setCellValue('B7', $data['totalTidakDiterima']);
        $sheet1->setCellValue('A8', 'Tidak Mengikuti CBT');
        $sheet1->setCellValue('B8', $data['totalTidakHadirCBT']);
        $sheet1->setCellValue('A9', 'Rata-rata Skor');
        $sheet1->setCellValue('B9', $data['avgSkorKeseluruhan']);
        $sheet1->setCellValue('A10', 'Total Kuota');
        $sheet1->setCellValue('B10', $data['totalKuota']);

        $sheet1->getStyle('A4:A10')->getFont()->setBold(true);
        $sheet1->getStyle('A3:B10')->applyFromArray($borderStyle);
        $sheet1->getStyle('B4:B10')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet1->getColumnDimension('A')->setAutoSize(true);
        $sheet1->getColumnDimension('B')->setAutoSize(true);


        // ─── SHEET 2: REKAP JURUSAN ──────────────────────────────────
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Rekap Jurusan');

        $sheet2->setCellValue('A1', 'No');
        $sheet2->setCellValue('B1', 'Program Keahlian / Jurusan');
        $sheet2->setCellValue('C1', 'Kuota');
        $sheet2->setCellValue('D1', 'Pendaftar');
        $sheet2->setCellValue('E1', 'Diterima');
        $sheet2->setCellValue('F1', 'Tidak Diterima');
        $sheet2->setCellValue('G1', 'Tdk Hadir CBT');
        $sheet2->setCellValue('H1', 'Sisa Kuota');
        $sheet2->setCellValue('I1', '% Keterisian');

        $sheet2->getStyle('A1:I1')->applyFromArray($headerStyle);
        $sheet2->getRowDimension(1)->setRowHeight(25);

        $row = 2;
        foreach ($data['rekapJurusan'] as $idx => $rk) {
            $sheet2->setCellValue('A' . $row, $idx + 1);
            $sheet2->setCellValue('B' . $row, $rk['jurusan']->nama);
            $sheet2->setCellValue('C' . $row, $rk['kuota']);
            $sheet2->setCellValue('D' . $row, $rk['total_pendaftar']);
            $sheet2->setCellValue('E' . $row, $rk['diterima']);
            $sheet2->setCellValue('F' . $row, $rk['tidak_diterima']);
            $sheet2->setCellValue('G' . $row, $rk['tidak_hadir_cbt']);
            $sheet2->setCellValue('H' . $row, $rk['sisa_kuota']);
            $sheet2->setCellValue('I' . $row, $rk['persen_keterisian'] . '%');

            $sheet2->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet2->getStyle("C{$row}:I{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            if ($row % 2 === 1) {
                $sheet2->getStyle("A{$row}:I{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F7FAFC');
            }
            $row++;
        }

        // Total Row
        $sheet2->setCellValue('A' . $row, 'TOTAL');
        $sheet2->mergeCells("A{$row}:B{$row}");
        $sheet2->setCellValue('C' . $row, $data['totalKuota']);
        $sheet2->setCellValue('D' . $row, $data['totalPendaftarKeseluruhan']);
        $sheet2->setCellValue('E' . $row, $data['totalDiterima']);
        $sheet2->setCellValue('F' . $row, $data['totalTidakDiterima']);
        $sheet2->setCellValue('G' . $row, $data['totalTidakHadirCBT']);
        $sheet2->setCellValue('H' . $row, max(0, $data['totalKuota'] - $data['totalDiterima']));
        $totalPct = $data['totalKuota'] > 0 ? round(($data['totalDiterima'] / $data['totalKuota']) * 100, 1) : 0;
        $sheet2->setCellValue('I' . $row, $totalPct . '%');

        $sheet2->getStyle("A{$row}:I{$row}")->getFont()->setBold(true);
        $sheet2->getStyle("A{$row}:I{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('EDF2F7');
        $sheet2->getStyle("C{$row}:I{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet2->getStyle("A1:I{$row}")->applyFromArray($borderStyle);
        foreach (range('A', 'I') as $col) {
            $sheet2->getColumnDimension($col)->setAutoSize(true);
        }


        // ─── SHEET 3: HASIL SELEKSI ──────────────────────────────────
        $sheet3 = $spreadsheet->createSheet();
        $sheet3->setTitle('Hasil Seleksi');

        $sheet3->setCellValue('A1', 'No');
        $sheet3->setCellValue('B1', 'No. Pendaftaran');
        $sheet3->setCellValue('C1', 'Nama Siswa');
        $sheet3->setCellValue('D1', 'Jurusan');
        $sheet3->setCellValue('E1', 'Nilai Rapor');
        $sheet3->setCellValue('F1', 'Nilai CBT');
        $sheet3->setCellValue('G1', 'Skor Akhir');
        $sheet3->setCellValue('H1', 'Kategori');
        $sheet3->setCellValue('I1', 'Status Seleksi');
        $sheet3->setCellValue('J1', 'Status Daftar Ulang');

        $sheet3->getStyle('A1:J1')->applyFromArray($headerStyle);
        $sheet3->getRowDimension(1)->setRowHeight(25);

        $row = 2;
        foreach ($data['allPublished'] as $idx => $hs) {
            $p = $hs->pendaftaran;
            $bayar = $p->pembayaranDaftarUlang ?? null;
            $bayarStatus = match($bayar?->status ?? '') {
                'lunas'       => 'Lunas',
                'pending'     => 'Pending',
                'gagal'       => 'Gagal',
                'kedaluwarsa' => 'Kedaluwarsa',
                'belum_bayar' => 'Belum Bayar',
                default       => $hs->kategori_kelulusan === 'DITERIMA' ? 'Belum Bayar' : '—',
            };

            $sheet3->setCellValue('A' . $row, $idx + 1);
            $sheet3->setCellValue('B' . $row, $p->nomor_pendaftaran ?? '-');
            $sheet3->setCellValue('C' . $row, $p->nama_lengkap ?? ($p->user->name ?? '-'));
            $sheet3->setCellValue('D' . $row, $p->jurusan->nama ?? '-');
            $sheet3->setCellValue('E' . $row, $p->nilai_rapor ?? 0);
            $sheet3->setCellValue('F' . $row, $p->hasilUjian->skor ?? 0);
            $sheet3->setCellValue('G' . $row, $hs->skor_akhir);
            $sheet3->setCellValue('H' . $row, 'Reguler');
            $sheet3->setCellValue('I' . $row, $hs->kategori_kelulusan);
            $sheet3->setCellValue('J' . $row, $bayarStatus);

            $sheet3->getStyle("A{$row}:B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet3->getStyle("E{$row}:G{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet3->getStyle("H{$row}:J{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            if ($row % 2 === 1) {
                $sheet3->getStyle("A{$row}:J{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F7FAFC');
            }
            $row++;
        }

        if ($row === 2) {
            $sheet3->setCellValue('A2', 'Tidak ada data hasil seleksi.');
            $sheet3->mergeCells('A2:J2');
            $sheet3->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet3->getStyle('A2:J2')->applyFromArray($borderStyle);
            $row++;
        } else {
            $sheet3->getStyle("A1:J" . ($row - 1))->applyFromArray($borderStyle);
        }

        foreach (range('A', 'J') as $col) {
            $sheet3->getColumnDimension($col)->setAutoSize(true);
        }


        // ─── SHEET 4: PEMBAYARAN DAFTAR ULANG ───────────────────────
        $sheet4 = $spreadsheet->createSheet();
        $sheet4->setTitle('Pembayaran Daftar Ulang');

        $sheet4->setCellValue('A1', 'No');
        $sheet4->setCellValue('B1', 'No. Pendaftaran');
        $sheet4->setCellValue('C1', 'Nama Siswa');
        $sheet4->setCellValue('D1', 'Jurusan');
        $sheet4->setCellValue('E1', 'Jumlah Tagihan');
        $sheet4->setCellValue('F1', 'Status Pembayaran');
        $sheet4->setCellValue('G1', 'Metode Pembayaran');
        $sheet4->setCellValue('H1', 'Tanggal Pembayaran');

        $sheet4->getStyle('A1:H1')->applyFromArray($headerStyle);
        $sheet4->getRowDimension(1)->setRowHeight(25);

        $row = 2;
        foreach ($data['daftarPembayaran'] as $idx => $pb) {
            $p2 = $pb->pendaftaran;
            $statusBayarLabel = match($pb->status ?? '') {
                'lunas'       => 'Lunas',
                'pending'     => 'Pending',
                'gagal'       => 'Gagal',
                'kedaluwarsa' => 'Kedaluwarsa',
                'belum_bayar' => 'Belum Bayar',
                default       => ucfirst($pb->status),
            };

            $sheet4->setCellValue('A' . $row, $idx + 1);
            $sheet4->setCellValue('B' . $row, $p2->nomor_pendaftaran ?? '-');
            $sheet4->setCellValue('C' . $row, $p2->nama_lengkap ?? ($p2->user->name ?? '-'));
            $sheet4->setCellValue('D' . $row, $p2->jurusan->nama ?? '-');
            $sheet4->setCellValue('E' . $row, (double) $pb->jumlah);
            $sheet4->setCellValue('F' . $row, $statusBayarLabel);
            $sheet4->setCellValue('G' . $row, $pb->metode_pembayaran ?? '—');
            $sheet4->setCellValue('H' . $row, $pb->paid_at ? $pb->paid_at->translatedFormat('d F Y') : '—');

            // Format Currency
            $sheet4->getStyle('E' . $row)->getNumberFormat()->setFormatCode('"Rp"#,##0');

            $sheet4->getStyle("A{$row}:B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet4->getStyle("E{$row}:H{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            if ($row % 2 === 1) {
                $sheet4->getStyle("A{$row}:H{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F7FAFC');
            }
            $row++;
        }

        // Total Row for Pembayaran
        if ($row > 2) {
            $sheet4->setCellValue('A' . $row, 'TOTAL NOMINAL MASUK');
            $sheet4->mergeCells("A{$row}:D{$row}");
            $sheet4->setCellValue('E' . $row, (double) $data['totalNominalMasuk']);
            $sheet4->getStyle('E' . $row)->getNumberFormat()->setFormatCode('"Rp"#,##0');
            $sheet4->getStyle("A{$row}:H{$row}")->getFont()->setBold(true);
            $sheet4->getStyle("A{$row}:H{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('EDF2F7');
            $sheet4->getStyle("E{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $row++;
        }

        if ($row === 2) {
            $sheet4->setCellValue('A2', 'Tidak ada data pembayaran daftar ulang.');
            $sheet4->mergeCells('A2:H2');
            $sheet4->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet4->getStyle('A2:H2')->applyFromArray($borderStyle);
            $row++;
        } else {
            $sheet4->getStyle("A1:H" . ($row - 1))->applyFromArray($borderStyle);
        }

        foreach (range('A', 'H') as $col) {
            $sheet4->getColumnDimension($col)->setAutoSize(true);
        }


        // ─── STREAM RESPONSE DOWNLOAD ──────────────────────────────────
        $namaFile = 'Laporan_PPDB_' . str_replace('/', '_', $data['settings']['tahun_ajaran'] ?? date('Y')) . '.xlsx';

        return response()->streamDownload(function() use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $namaFile, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Profil & Ganti Password
     */
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
