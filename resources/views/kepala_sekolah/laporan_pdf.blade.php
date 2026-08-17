<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Laporan Hasil Seleksi PPDB</title>
<style>
    * { box-sizing: border-box; }
    body {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 11px;
        color: #1a202c;
        margin: 0;
        padding: 0;
        line-height: 1.4;
    }
    .page { padding: 20px 28px; }

    /* Header */
    .header-wrap {
        text-align: center;
        border-bottom: 3px solid #1e3a8a;
        padding-bottom: 12px;
        margin-bottom: 16px;
    }
    .header-wrap h1 {
        font-size: 15px;
        font-weight: bold;
        margin: 0 0 2px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .header-wrap h2 {
        font-size: 13px;
        font-weight: bold;
        margin: 0 0 2px;
        text-transform: uppercase;
    }
    .header-wrap p {
        font-size: 11px;
        margin: 0;
        color: #4a5568;
    }

    /* Section title */
    .section-title {
        font-size: 11px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        background: #1e3a8a;
        color: white;
        padding: 5px 10px;
        margin: 14px 0 6px;
    }

    /* KPI Grid */
    .kpi-row { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
    .kpi-row td { text-align: center; border: 1px solid #cbd5e0; padding: 8px 4px; }
    .kpi-label { font-size: 9px; color: #4a5568; text-transform: uppercase; font-weight: bold; }
    .kpi-val { font-size: 18px; font-weight: bold; color: #1e3a8a; }
    .kpi-sub { font-size: 9px; color: #718096; }

    /* Tables */
    table.data-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 10px;
    }
    table.data-table thead tr {
        background: #2d3748;
        color: white;
    }
    table.data-table thead th {
        padding: 6px 5px;
        text-align: left;
        font-size: 9px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    table.data-table thead th.center { text-align: center; }
    table.data-table tbody tr { border-bottom: 1px solid #e2e8f0; }
    table.data-table tbody tr:nth-child(even) { background: #f7fafc; }
    table.data-table tbody td { padding: 5px 5px; vertical-align: middle; }
    table.data-table tbody td.center { text-align: center; }
    table.data-table tfoot tr { background: #edf2f7; font-weight: bold; }
    table.data-table tfoot td { padding: 6px 5px; }

    /* Badges */
    .badge {
        display: inline-block;
        padding: 2px 6px;
        border-radius: 3px;
        font-size: 9px;
        font-weight: bold;
    }
    .badge-hijau { background: #c6f6d5; color: #276749; }
    .badge-merah { background: #fed7d7; color: #9b2c2c; }
    .badge-abu   { background: #e2e8f0; color: #4a5568; }
    .badge-kuning{ background: #fefcbf; color: #7b6a00; }
    .badge-orange{ background: #feebc8; color: #7b4a00; }

    /* Signature */
    .signature-area {
        margin-top: 28px;
        text-align: right;
        padding-right: 40px;
    }
    .signature-area p { margin: 2px 0; font-size: 11px; }
    .signature-line {
        margin-top: 60px;
        border-top: 1px solid #1a202c;
        display: inline-block;
        min-width: 200px;
        font-size: 11px;
        font-weight: bold;
        text-align: center;
        padding-top: 3px;
    }

    .info-tanggal {
        text-align: right;
        font-size: 10px;
        color: #4a5568;
        margin-bottom: 4px;
    }

    .rekap-bayar-row {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 10px;
    }
    .rekap-bayar-row td {
        border: 1px solid #cbd5e0;
        padding: 7px 10px;
        text-align: center;
    }
    .rb-label { font-size: 9px; color: #718096; font-weight: bold; text-transform: uppercase; }
    .rb-val { font-size: 14px; font-weight: bold; color: #1e3a8a; }

    .page-break { page-break-after: always; }
</style>
</head>
<body>
<div class="page">

    {{-- ─── HEADER ─────────────────────────────────────────────────────── --}}
    <div class="header-wrap">
        <h1>{{ $settings['nama_sekolah'] ?? 'SMK MITRA BINTARO' }}</h1>
        <h2>LAPORAN HASIL SELEKSI PENERIMAAN PESERTA DIDIK BARU</h2>
        <p>Tahun Ajaran {{ $settings['tahun_ajaran'] ?? '2026/2027' }} &bull; Dicetak pada: {{ now()->translatedFormat('d F Y, H:i') }} WIB</p>
    </div>

    <div class="info-tanggal">
        Tanggal Cetak: {{ now()->translatedFormat('d F Y') }}
    </div>

    {{-- ─── A. RINGKASAN PPDB ──────────────────────────────────────────── --}}
    <div class="section-title">A. Ringkasan PPDB</div>

    <table class="kpi-row">
        <tr>
            <td>
                <div class="kpi-label">Total Pendaftar</div>
                <div class="kpi-val">{{ $totalPendaftarKeseluruhan }}</div>
                <div class="kpi-sub">Seluruh pendaftar</div>
            </td>
            <td>
                <div class="kpi-label">Total Diterima</div>
                <div class="kpi-val" style="color:#276749;">{{ $totalDiterima }}</div>
                <div class="kpi-sub">Lolos seleksi final</div>
            </td>
            <td>
                <div class="kpi-label">Tidak Diterima</div>
                <div class="kpi-val" style="color:#9b2c2c;">{{ $totalTidakDiterima }}</div>
                <div class="kpi-sub">Tidak lolos seleksi</div>
            </td>
            <td>
                <div class="kpi-label">Tdk Ikut CBT</div>
                <div class="kpi-val" style="color:#7b4a00;">{{ $totalTidakHadirCBT }}</div>
                <div class="kpi-sub">Tidak hadir ujian</div>
            </td>
            <td>
                <div class="kpi-label">Rata-rata Skor</div>
                <div class="kpi-val" style="color:#553c9a;">{{ number_format($avgSkorKeseluruhan, 2) }}</div>
                <div class="kpi-sub">Skor akhir</div>
            </td>
            <td>
                <div class="kpi-label">Total Kuota</div>
                <div class="kpi-val">{{ $totalKuota }}</div>
                <div class="kpi-sub">Semua jurusan</div>
            </td>
        </tr>
    </table>

    {{-- ─── B. REKAP PER JURUSAN ───────────────────────────────────────── --}}
    <div class="section-title">B. Rekap Pendaftar & Keterisian Kuota Per Jurusan</div>

    <table class="data-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Jurusan</th>
                <th class="center">Kuota</th>
                <th class="center">Pendaftar</th>
                <th class="center">Diterima</th>
                <th class="center">Tidak Diterima</th>
                <th class="center">Tdk Hadir CBT</th>
                <th class="center">Sisa Kuota</th>
                <th class="center">% Keterisian</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rekapJurusan as $idx => $rk)
            <tr>
                <td class="center">{{ $idx + 1 }}</td>
                <td>{{ $rk['jurusan']->nama }}</td>
                <td class="center">{{ $rk['kuota'] }}</td>
                <td class="center">{{ $rk['total_pendaftar'] }}</td>
                <td class="center" style="color:#276749; font-weight:bold;">{{ $rk['diterima'] }}</td>
                <td class="center" style="color:#9b2c2c;">{{ $rk['tidak_diterima'] }}</td>
                <td class="center" style="color:#718096;">{{ $rk['tidak_hadir_cbt'] }}</td>
                <td class="center">{{ $rk['sisa_kuota'] }}</td>
                <td class="center" style="font-weight:bold;">{{ $rk['persen_keterisian'] }}%</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2">TOTAL KESELURUHAN</td>
                <td class="center">{{ $totalKuota }}</td>
                <td class="center">{{ $totalPendaftarKeseluruhan }}</td>
                <td class="center" style="color:#276749;">{{ $totalDiterima }}</td>
                <td class="center" style="color:#9b2c2c;">{{ $totalTidakDiterima }}</td>
                <td class="center">{{ $totalTidakHadirCBT }}</td>
                <td class="center">{{ $totalKuota > 0 ? max(0, $totalKuota - $totalDiterima) : 0 }}</td>
                <td class="center">{{ $totalKuota > 0 ? round(($totalDiterima / $totalKuota) * 100, 1) : 0 }}%</td>
            </tr>
        </tfoot>
    </table>

    <div class="page-break"></div>

    {{-- ─── C. HASIL SELEKSI SISWA ─────────────────────────────────────── --}}
    <div class="section-title">C. Hasil Seleksi Siswa</div>

    <table class="data-table">
        <thead>
            <tr>
                <th>No</th>
                <th>No. Pendaftaran</th>
                <th>Nama</th>
                <th>Jurusan</th>
                <th class="center">Nilai Rapor</th>
                <th class="center">Nilai CBT</th>
                <th class="center">Skor Akhir</th>
                <th class="center">Status</th>
                <th class="center">Daftar Ulang</th>
            </tr>
        </thead>
        <tbody>
            @forelse($daftarHasil as $idx => $hs)
            @php
                $p       = $hs->pendaftaran;
                $bayar   = $p->pembayaranDaftarUlang ?? null;
                $statusBayarLabel = match($bayar?->status ?? '') {
                    'lunas'       => ['label' => 'Lunas', 'class' => 'badge-hijau'],
                    'pending'     => ['label' => 'Pending', 'class' => 'badge-kuning'],
                    'gagal'       => ['label' => 'Gagal', 'class' => 'badge-merah'],
                    'kedaluwarsa' => ['label' => 'Kedaluwarsa', 'class' => 'badge-abu'],
                    'belum_bayar' => ['label' => 'Belum Bayar', 'class' => 'badge-orange'],
                    default       => ['label' => '-', 'class' => 'badge-abu'],
                };
            @endphp
            <tr>
                <td class="center">{{ ($daftarHasil->currentPage() - 1) * $daftarHasil->perPage() + $idx + 1 }}</td>
                <td style="font-family:monospace;">{{ $p->nomor_pendaftaran ?? '-' }}</td>
                <td>{{ $p->nama_lengkap ?? ($p->user->name ?? '-') }}</td>
                <td>{{ $p->jurusan->nama ?? '-' }}</td>
                <td class="center">{{ number_format($p->nilai_rapor ?? 0, 1) }}</td>
                <td class="center">{{ number_format($p->hasilUjian->skor ?? 0, 1) }}</td>
                <td class="center" style="font-weight:bold;">{{ number_format($hs->skor_akhir, 2) }}</td>
                <td class="center">
                    @if($hs->kategori_kelulusan === 'DITERIMA')
                        <span class="badge badge-hijau">DITERIMA</span>
                    @elseif($hs->kategori_kelulusan === 'TIDAK HADIR CBT')
                        <span class="badge badge-abu">TDK HADIR</span>
                    @else
                        <span class="badge badge-merah">TDK DITERIMA</span>
                    @endif
                </td>
                <td class="center">
                    <span class="badge {{ $statusBayarLabel['class'] }}">{{ $statusBayarLabel['label'] }}</span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="center" style="padding:16px; color:#718096;">Tidak ada data hasil seleksi yang tersedia.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="page-break"></div>

    {{-- ─── D. REKAP PEMBAYARAN DAFTAR ULANG ──────────────────────────── --}}
    <div class="section-title">D. Rekap Pembayaran Daftar Ulang</div>

    <table class="rekap-bayar-row">
        <tr>
            <td>
                <div class="rb-label">Total Tagihan</div>
                <div class="rb-val">{{ $totalTagihan }}</div>
            </td>
            <td>
                <div class="rb-label">Sudah Dibayar</div>
                <div class="rb-val" style="color:#276749;">{{ $totalSudahBayar }}</div>
            </td>
            <td>
                <div class="rb-label">Belum Dibayar</div>
                <div class="rb-val" style="color:#9b2c2c;">{{ $totalBelumBayar }}</div>
            </td>
            <td>
                <div class="rb-label">Total Nominal Masuk</div>
                <div class="rb-val">Rp {{ number_format($totalNominalMasuk, 0, ',', '.') }}</div>
            </td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th>No</th>
                <th>No. Pendaftaran</th>
                <th>Nama Siswa</th>
                <th>Jurusan</th>
                <th class="center">Jumlah Tagihan</th>
                <th class="center">Status</th>
                <th class="center">Metode</th>
                <th class="center">Tanggal Bayar</th>
            </tr>
        </thead>
        <tbody>
            @forelse($daftarPembayaran as $idx => $pb)
            @php
                $p2 = $pb->pendaftaran;
                $statusBayarLabel2 = match($pb->status ?? '') {
                    'lunas'       => ['label' => 'Lunas', 'class' => 'badge-hijau'],
                    'pending'     => ['label' => 'Pending', 'class' => 'badge-kuning'],
                    'gagal'       => ['label' => 'Gagal', 'class' => 'badge-merah'],
                    'kedaluwarsa' => ['label' => 'Kedaluwarsa', 'class' => 'badge-abu'],
                    'belum_bayar' => ['label' => 'Belum Bayar', 'class' => 'badge-orange'],
                    default       => ['label' => ucfirst($pb->status), 'class' => 'badge-abu'],
                };
            @endphp
            <tr>
                <td class="center">{{ $idx + 1 }}</td>
                <td style="font-family:monospace;">{{ $p2->nomor_pendaftaran ?? '-' }}</td>
                <td>{{ $p2->nama_lengkap ?? ($p2->user->name ?? '-') }}</td>
                <td>{{ $p2->jurusan->nama ?? '-' }}</td>
                <td class="center">Rp {{ number_format($pb->jumlah, 0, ',', '.') }}</td>
                <td class="center">
                    <span class="badge {{ $statusBayarLabel2['class'] }}">{{ $statusBayarLabel2['label'] }}</span>
                </td>
                <td class="center">{{ $pb->metode_pembayaran ?? '-' }}</td>
                <td class="center">{{ $pb->paid_at ? $pb->paid_at->translatedFormat('d F Y') : '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="center" style="padding:12px; color:#718096;">Belum ada data pembayaran daftar ulang.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- ─── TANDA TANGAN ───────────────────────────────────────────────── --}}
    <div class="signature-area">
        <p>{{ $settings['nama_sekolah'] ?? 'SMK Mitra Bintaro' }}, {{ now()->translatedFormat('d F Y') }}</p>
        <p>Kepala Sekolah</p>
        <div style="margin-top: 55px;">
            <div class="signature-line">
                (________________________________)
            </div>
        </div>
        <p style="margin-top: 4px; font-weight: bold;">Kepala {{ $settings['nama_sekolah'] ?? 'SMK Mitra Bintaro' }}</p>
    </div>

</div>
</body>
</html>
