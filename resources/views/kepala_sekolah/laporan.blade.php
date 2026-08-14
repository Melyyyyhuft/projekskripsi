@extends('layouts.kepala-sekolah')
@section('title', 'Laporan Hasil Seleksi PPDB')

@section('content')
<style>
    .report-header-card {
        background: linear-gradient(135deg, #1e1b4b 0%, #312e81 50%, #4338ca 100%);
        border-radius: 24px;
        padding: 2.25rem 2.5rem;
        color: white;
        margin-bottom: 2rem;
        box-shadow: 0 15px 35px rgba(49, 46, 129, 0.2);
        position: relative;
        overflow: hidden;
    }
    .report-header-card::after {
        content: "";
        position: absolute;
        top: -60px;
        right: -60px;
        width: 240px;
        height: 240px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.08);
        filter: blur(40px);
    }
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1.25rem;
        margin-bottom: 2rem;
    }
    .kpi-card {
        background: var(--white);
        border: 1px solid var(--border-color);
        border-radius: 20px;
        padding: 1.5rem;
        box-shadow: var(--shadow-sm);
        display: flex;
        align-items: center;
        gap: 1.25rem;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .kpi-card:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-md);
    }
    .kpi-icon {
        width: 54px;
        height: 54px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
    }
    .kpi-label {
        font-size: 0.78rem;
        font-weight: 700;
        color: var(--gray-text);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.25rem;
    }
    .kpi-value {
        font-size: 1.75rem;
        font-weight: 900;
        color: var(--dark);
        line-height: 1;
    }
    .jurusan-card-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 1.25rem;
        margin-bottom: 2rem;
    }
    .jurusan-card {
        background: var(--white);
        border: 1px solid var(--border-color);
        border-radius: 20px;
        padding: 1.5rem;
        box-shadow: var(--shadow-sm);
    }
    .table-container {
        background: var(--white);
        border: 1px solid var(--border-color);
        border-radius: 20px;
        padding: 1.75rem;
        margin-bottom: 2rem;
        box-shadow: var(--shadow-sm);
    }
    .badge-status {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.3rem 0.75rem;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 800;
        letter-spacing: 0.03em;
    }
    .badge-accepted {
        background: #dcfce7;
        color: #15803d;
        border: 1px solid #86efac;
    }
    .badge-rejected {
        background: #fee2e2;
        color: #b91c1c;
        border: 1px solid #fca5a5;
    }
    .badge-nocbt {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #cbd5e1;
    }
    .btn-print {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: white;
        color: #312e81;
        padding: 0.65rem 1.4rem;
        border-radius: 12px;
        font-weight: 800;
        font-size: 0.875rem;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .btn-print:hover {
        transform: translateY(-2px);
        background: #f8fafc;
        box-shadow: 0 6px 16px rgba(0,0,0,0.2);
    }

    @media print {
        body {
            background: white !important;
            color: black !important;
        }
        .sidebar, header, .btn-print, .no-print, .pagination-wrapper {
            display: none !important;
        }
        .main-content {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
        }
        .report-header-card {
            background: #f1f5f9 !important;
            color: black !important;
            box-shadow: none !important;
            border: 1px solid #cbd5e1 !important;
            padding: 1.5rem !important;
        }
        .report-header-card p, .report-header-card span, .report-header-card h1 {
            color: black !important;
        }
        .table-container, .kpi-card, .jurusan-card {
            box-shadow: none !important;
            border: 1px solid #cbd5e1 !important;
            break-inside: avoid;
        }
    }
</style>

{{-- Header Laporan Eksekutif --}}
<div class="report-header-card">
    <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:1.25rem;">
        <div>
            <div style="display:inline-flex; align-items:center; gap:0.5rem; background:rgba(255,255,255,0.15); padding:0.35rem 0.85rem; border-radius:999px; font-size:0.75rem; font-weight:800; letter-spacing:0.08em; text-transform:uppercase; margin-bottom:0.75rem; backdrop-filter:blur(10px);">
                <i class="fa-solid fa-graduation-cap"></i> LAPORAN EKSEKUTIF KEPALA SEKOLAH
            </div>
            <h1 style="font-size:1.75rem; font-weight:900; margin:0 0 0.4rem; letter-spacing:-0.02em;">
                Hasil Seleksi Penerimaan Peserta Didik Baru (PPDB)
            </h1>
            <p style="margin:0; opacity:0.9; font-size:0.95rem; max-width:680px; line-height:1.5;">
                {{ $settings['nama_sekolah'] ?? 'SMK MITRA BINTARO' }} — Tahun Ajaran {{ $settings['tahun_ajaran'] ?? '2026/2027' }}
            </p>
        </div>

        <div style="display:flex; align-items:center; gap:0.75rem; flex-wrap:wrap;" class="no-print">
            <button onclick="window.print()" class="btn-print">
                <i class="fa-solid fa-print"></i> Cetak / Ekspor Laporan
            </button>
        </div>
    </div>

    {{-- Status publikasi banner --}}
    <div style="margin-top:1.5rem; padding-top:1.25rem; border-top:1px solid rgba(255,255,255,0.15); display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:1rem; font-size:0.85rem;">
        <div style="display:flex; align-items:center; gap:0.6rem;">
            <span style="width:10px; height:10px; border-radius:50%; background:{{ $totalPublished > 0 ? '#4ade80' : '#fbbf24' }}; display:inline-block;"></span>
            <span>
                Status Publikasi Seleksi: <strong>{{ $totalPublished > 0 ? 'Sudah Dipublish Admin (' . $totalPublished . ' Siswa)' : 'Menunggu Publikasi Admin' }}</strong>
            </span>
        </div>
        <div style="opacity:0.85;">
            <i class="fa-regular fa-clock"></i> Data diperbarui per: {{ now()->translatedFormat('l, d F Y - H:i') }} WIB
        </div>
    </div>
</div>

{{-- KPI Cards Keseluruhan --}}
<div class="kpi-grid">
    <div class="kpi-card">
        <div class="kpi-icon" style="background:#eff6ff; color:#3b82f6;">
            <i class="fa-solid fa-users"></i>
        </div>
        <div>
            <div class="kpi-label">Total Pendaftar</div>
            <div class="kpi-value">{{ number_format($totalPendaftarKeseluruhan) }}</div>
            <span style="font-size:0.72rem; color:var(--gray-text);">Semua berkas masuk</span>
        </div>
    </div>

    <div class="kpi-card">
        <div class="kpi-icon" style="background:#dcfce7; color:#16a34a;">
            <i class="fa-solid fa-user-check"></i>
        </div>
        <div>
            <div class="kpi-label">Total Diterima</div>
            <div class="kpi-value" style="color:#16a34a;">{{ number_format($totalDiterima) }}</div>
            <span style="font-size:0.72rem; color:var(--gray-text);">Lolos seleksi final</span>
        </div>
    </div>

    <div class="kpi-card">
        <div class="kpi-icon" style="background:#fee2e2; color:#dc2626;">
            <i class="fa-solid fa-user-xmark"></i>
        </div>
        <div>
            <div class="kpi-label">Tidak Diterima</div>
            <div class="kpi-value" style="color:#dc2626;">{{ number_format($totalTidakDiterima + $totalTidakHadirCBT) }}</div>
            <span style="font-size:0.72rem; color:var(--gray-text);">{{ $totalTidakHadirCBT }} tdk hadir CBT</span>
        </div>
    </div>

    <div class="kpi-card">
        <div class="kpi-icon" style="background:#f3e8ff; color:#9333ea;">
            <i class="fa-solid fa-chart-line"></i>
        </div>
        <div>
            <div class="kpi-label">Rata-rata Skor</div>
            <div class="kpi-value" style="color:#9333ea;">{{ number_format($avgSkorKeseluruhan, 2) }}</div>
            <span style="font-size:0.72rem; color:var(--gray-text);">Skor akhir seluruh siswa</span>
        </div>
    </div>

    <div class="kpi-card">
        <div class="kpi-icon" style="background:#fffbeb; color:#d97706;">
            <i class="fa-solid fa-door-open"></i>
        </div>
        <div>
            <div class="kpi-label">Total Kuota</div>
            <div class="kpi-value">{{ number_format($totalKuota) }}</div>
            <span style="font-size:0.72rem; color:var(--gray-text);">Kapasitas semua jurusan</span>
        </div>
    </div>
</div>

{{-- BAGIAN 1: Keterisian Kuota Per Jurusan --}}
<div style="margin-bottom:1.5rem;">
    <h3 style="font-size:1.15rem; font-weight:800; color:var(--dark); margin:0 0 0.4rem; display:flex; align-items:center; gap:0.5rem;">
        <i class="fa-solid fa-layer-group" style="color:var(--primary);"></i> Rekap Pendaftar &amp; Keterisian Kuota Per Jurusan
    </h3>
    <p style="margin:0; font-size:0.85rem; color:var(--gray-text);">Perbandingan jumlah kuota, pendaftar terdaftar, dan calon siswa yang diterima.</p>
</div>

<div class="jurusan-card-grid">
    @foreach($rekapJurusan as $rk)
        @php
            $isFull = $rk['persen_keterisian'] >= 100;
            $barColor = $isFull ? '#10b981' : ($rk['persen_keterisian'] >= 70 ? '#3b82f6' : '#f59e0b');
        @endphp
        <div class="jurusan-card">
            <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:1rem;">
                <div>
                    <h4 style="margin:0 0 0.25rem; font-size:1.05rem; font-weight:800; color:var(--dark);">
                        {{ $rk['jurusan']->nama }}
                    </h4>
                    <span style="font-size:0.75rem; font-weight:700; color:var(--gray-text);">
                        Kode: {{ $rk['jurusan']->kode ?? 'JUR-'.$rk['jurusan']->id }}
                    </span>
                </div>
                <span style="background:var(--light-bg); border:1px solid var(--border-color); padding:0.25rem 0.65rem; border-radius:10px; font-weight:800; font-size:0.75rem; color:var(--dark);">
                    Kuota: {{ $rk['kuota'] }}
                </span>
            </div>

            <div style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap:0.75rem; background:var(--light-bg); padding:0.85rem; border-radius:14px; margin-bottom:1rem; text-align:center;">
                <div>
                    <div style="font-size:0.68rem; font-weight:700; color:var(--gray-text); text-transform:uppercase;">Pendaftar</div>
                    <div style="font-size:1.15rem; font-weight:900; color:var(--dark);">{{ $rk['total_pendaftar'] }}</div>
                </div>
                <div>
                    <div style="font-size:0.68rem; font-weight:700; color:#16a34a; text-transform:uppercase;">Diterima</div>
                    <div style="font-size:1.15rem; font-weight:900; color:#16a34a;">{{ $rk['diterima'] }}</div>
                </div>
                <div>
                    <div style="font-size:0.68rem; font-weight:700; color:var(--gray-text); text-transform:uppercase;">Sisa Kuota</div>
                    <div style="font-size:1.15rem; font-weight:900; color:{{ $rk['sisa_kuota'] > 0 ? '#d97706' : '#64748b' }};">{{ $rk['sisa_kuota'] }}</div>
                </div>
            </div>

            {{-- Progress Bar --}}
            <div>
                <div style="display:flex; justify-content:space-between; font-size:0.75rem; font-weight:700; margin-bottom:0.35rem;">
                    <span style="color:var(--gray-text);">Keterisian Kuota</span>
                    <span style="color:{{ $barColor }};">{{ $rk['persen_keterisian'] }}%</span>
                </div>
                <div style="width:100%; height:8px; background:var(--border-color); border-radius:999px; overflow:hidden;">
                    <div style="width:{{ min(100, $rk['persen_keterisian']) }}%; height:100%; background:{{ $barColor }}; border-radius:999px; transition:width 0.5s ease;"></div>
                </div>
            </div>
        </div>
    @endforeach
</div>

{{-- BAGIAN 2: Tabel Rekap Kelulusan & Nilai Per Jurusan --}}
<div class="table-container">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.25rem; flex-wrap:wrap; gap:1rem;">
        <div>
            <h3 style="font-size:1.1rem; font-weight:800; color:var(--dark); margin:0 0 0.25rem;">
                <i class="fa-solid fa-chart-column" style="color:var(--primary);"></i> Rekap Status Kelulusan Per Jurusan
            </h3>
            <p style="margin:0; font-size:0.8rem; color:var(--gray-text);">Data diambil dari hasil seleksi yang telah difinalisasi / dipublish.</p>
        </div>
    </div>

    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; font-size:0.85rem; text-align:left;">
            <thead>
                <tr style="background:var(--light-bg); color:var(--dark); border-bottom:2px solid var(--border-color);">
                    <th style="padding:0.85rem 1rem; font-weight:800;">No</th>
                    <th style="padding:0.85rem 1rem; font-weight:800;">Jurusan</th>
                    <th style="padding:0.85rem 1rem; font-weight:800; text-align:center;">Kuota</th>
                    <th style="padding:0.85rem 1rem; font-weight:800; text-align:center;">Pendaftar</th>
                    <th style="padding:0.85rem 1rem; font-weight:800; text-align:center;">Dipublish</th>
                    <th style="padding:0.85rem 1rem; font-weight:800; text-align:center; color:#16a34a;">Diterima</th>
                    <th style="padding:0.85rem 1rem; font-weight:800; text-align:center; color:#dc2626;">Tidak Diterima</th>
                    <th style="padding:0.85rem 1rem; font-weight:800; text-align:center; color:#64748b;">Tdk Hadir CBT</th>
                    <th style="padding:0.85rem 1rem; font-weight:800; text-align:center;">% Keterisian</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rekapJurusan as $idx => $rk)
                    <tr style="border-bottom:1px solid var(--border-color);">
                        <td style="padding:0.85rem 1rem; font-weight:700;">{{ $idx + 1 }}</td>
                        <td style="padding:0.85rem 1rem; font-weight:800; color:var(--dark);">{{ $rk['jurusan']->nama }}</td>
                        <td style="padding:0.85rem 1rem; text-align:center; font-weight:700;">{{ $rk['kuota'] }}</td>
                        <td style="padding:0.85rem 1rem; text-align:center; font-weight:700;">{{ $rk['total_pendaftar'] }}</td>
                        <td style="padding:0.85rem 1rem; text-align:center; font-weight:700;">{{ $rk['total_published'] }}</td>
                        <td style="padding:0.85rem 1rem; text-align:center; font-weight:900; color:#16a34a; background:rgba(34,197,94,0.04);">
                            {{ $rk['diterima'] }}
                        </td>
                        <td style="padding:0.85rem 1rem; text-align:center; font-weight:800; color:#dc2626;">
                            {{ $rk['tidak_diterima'] }}
                        </td>
                        <td style="padding:0.85rem 1rem; text-align:center; font-weight:700; color:#64748b;">
                            {{ $rk['tidak_hadir_cbt'] }}
                        </td>
                        <td style="padding:0.85rem 1rem; text-align:center;">
                            <span style="font-weight:900; color:{{ $rk['persen_keterisian'] >= 100 ? '#10b981' : '#3b82f6' }};">
                                {{ $rk['persen_keterisian'] }}%
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background:var(--light-bg); font-weight:900; border-top:2px solid var(--border-color);">
                    <td colspan="2" style="padding:0.85rem 1rem;">TOTAL KESELURUHAN</td>
                    <td style="padding:0.85rem 1rem; text-align:center;">{{ $totalKuota }}</td>
                    <td style="padding:0.85rem 1rem; text-align:center;">{{ $totalPendaftarKeseluruhan }}</td>
                    <td style="padding:0.85rem 1rem; text-align:center;">{{ $totalPublished }}</td>
                    <td style="padding:0.85rem 1rem; text-align:center; color:#16a34a;">{{ $totalDiterima }}</td>
                    <td style="padding:0.85rem 1rem; text-align:center; color:#dc2626;">{{ $totalTidakDiterima }}</td>
                    <td style="padding:0.85rem 1rem; text-align:center; color:#64748b;">{{ $totalTidakHadirCBT }}</td>
                    <td style="padding:0.85rem 1rem; text-align:center; color:#3b82f6;">
                        {{ $totalKuota > 0 ? round(($totalDiterima / $totalKuota) * 100, 1) : 0 }}%
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

{{-- BAGIAN 3: Statistik Nilai Rata-rata Per Jurusan --}}
<div class="table-container">
    <div style="margin-bottom:1.25rem;">
        <h3 style="font-size:1.1rem; font-weight:800; color:var(--dark); margin:0 0 0.25rem;">
            <i class="fa-solid fa-calculator" style="color:#8b5cf6;"></i> Statistik Nilai Rata-Rata Per Jurusan
        </h3>
        <p style="margin:0; font-size:0.8rem; color:var(--gray-text);">Distribusi nilai rapor, nilai ujian CBT, dan skor akhir seleksi per jurusan.</p>
    </div>

    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; font-size:0.85rem; text-align:left;">
            <thead>
                <tr style="background:var(--light-bg); color:var(--dark); border-bottom:2px solid var(--border-color);">
                    <th style="padding:0.85rem 1rem; font-weight:800;">No</th>
                    <th style="padding:0.85rem 1rem; font-weight:800;">Jurusan</th>
                    <th style="padding:0.85rem 1rem; font-weight:800; text-align:center;">Rata-rata Rapor</th>
                    <th style="padding:0.85rem 1rem; font-weight:800; text-align:center;">Rata-rata Nilai CBT</th>
                    <th style="padding:0.85rem 1rem; font-weight:800; text-align:center; color:#6366f1;">Rata-rata Skor Akhir</th>
                    <th style="padding:0.85rem 1rem; font-weight:800; text-align:center; color:#16a34a;">Skor Tertinggi</th>
                    <th style="padding:0.85rem 1rem; font-weight:800; text-align:center; color:#ea580c;">Skor Terendah</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rekapJurusan as $idx => $rk)
                    <tr style="border-bottom:1px solid var(--border-color);">
                        <td style="padding:0.85rem 1rem; font-weight:700;">{{ $idx + 1 }}</td>
                        <td style="padding:0.85rem 1rem; font-weight:800; color:var(--dark);">{{ $rk['jurusan']->nama }}</td>
                        <td style="padding:0.85rem 1rem; text-align:center; font-weight:700;">{{ number_format($rk['avg_rapor'], 2) }}</td>
                        <td style="padding:0.85rem 1rem; text-align:center; font-weight:700;">{{ number_format($rk['avg_cbt'], 2) }}</td>
                        <td style="padding:0.85rem 1rem; text-align:center; font-weight:900; color:#6366f1; background:rgba(99,102,241,0.04);">
                            {{ number_format($rk['avg_skor_akhir'], 2) }}
                        </td>
                        <td style="padding:0.85rem 1rem; text-align:center; font-weight:800; color:#16a34a;">
                            {{ number_format($rk['max_skor'], 2) }}
                        </td>
                        <td style="padding:0.85rem 1rem; text-align:center; font-weight:800; color:#ea580c;">
                            {{ number_format($rk['min_skor'], 2) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background:var(--light-bg); font-weight:900; border-top:2px solid var(--border-color);">
                    <td colspan="2" style="padding:0.85rem 1rem;">RATA-RATA TOTAL SEKOLAH</td>
                    <td style="padding:0.85rem 1rem; text-align:center;">{{ number_format($avgRaporKeseluruhan, 2) }}</td>
                    <td style="padding:0.85rem 1rem; text-align:center;">{{ number_format($avgCbtKeseluruhan, 2) }}</td>
                    <td style="padding:0.85rem 1rem; text-align:center; color:#6366f1;">{{ number_format($avgSkorKeseluruhan, 2) }}</td>
                    <td style="padding:0.85rem 1rem; text-align:center; color:#16a34a;">
                        {{ number_format(collect($rekapJurusan)->max('max_skor'), 2) }}
                    </td>
                    <td style="padding:0.85rem 1rem; text-align:center; color:#ea580c;">
                        {{ number_format(collect($rekapJurusan)->where('min_skor', '>', 0)->min('min_skor') ?? 0, 2) }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

{{-- BAGIAN 4: Daftar Rinci Peserta Hasil Seleksi Terpublish --}}
<div class="table-container">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.25rem; flex-wrap:wrap; gap:1rem;">
        <div>
            <h3 style="font-size:1.1rem; font-weight:800; color:var(--dark); margin:0 0 0.25rem;">
                <i class="fa-solid fa-list-check" style="color:var(--primary);"></i> Data Rinci Siswa Hasil Seleksi
            </h3>
            <p style="margin:0; font-size:0.8rem; color:var(--gray-text);">Daftar siswa yang hasil kelulusannya telah dipublish secara resmi.</p>
        </div>
    </div>

    {{-- Filter Bar --}}
    <form method="GET" action="{{ route('kepala_sekolah.laporan') }}" class="no-print" style="background:var(--light-bg); padding:1rem 1.25rem; border-radius:14px; margin-bottom:1.5rem; display:flex; gap:1rem; align-items:center; flex-wrap:wrap;">
        <div style="flex:1; min-width:200px;">
            <input type="text" name="q" value="{{ $search }}" class="form-control" placeholder="Cari nama siswa / no. pendaftaran / asal sekolah..." style="margin-bottom:0; font-size:0.85rem;">
        </div>

        <div style="min-width:180px;">
            <select name="jurusan_id" class="form-control" style="margin-bottom:0; font-size:0.85rem;">
                <option value="">Semua Jurusan</option>
                @foreach($jurusans as $j)
                    <option value="{{ $j->id }}" {{ $filterJurusan == $j->id ? 'selected' : '' }}>{{ $j->nama }}</option>
                @endforeach
            </select>
        </div>

        <div style="min-width:160px;">
            <select name="status" class="form-control" style="margin-bottom:0; font-size:0.85rem;">
                <option value="">Semua Status</option>
                <option value="DITERIMA" {{ $filterStatus === 'DITERIMA' ? 'selected' : '' }}>🟢 DITERIMA</option>
                <option value="TIDAK DITERIMA" {{ $filterStatus === 'TIDAK DITERIMA' ? 'selected' : '' }}>🔴 TIDAK DITERIMA</option>
                <option value="TIDAK HADIR CBT" {{ $filterStatus === 'TIDAK HADIR CBT' ? 'selected' : '' }}>⚪ TIDAK HADIR CBT</option>
            </select>
        </div>

        <div style="display:flex; gap:0.5rem;">
            <button type="submit" class="btn-primary" style="padding:0.6rem 1.25rem; font-size:0.85rem;">
                <i class="fa-solid fa-filter"></i> Filter
            </button>
            @if(!empty($search) || !empty($filterJurusan) || !empty($filterStatus))
                <a href="{{ route('kepala_sekolah.laporan') }}" class="btn-outline" style="padding:0.6rem 1rem; font-size:0.85rem; text-decoration:none; display:inline-flex; align-items:center;">
                    Reset
                </a>
            @endif
        </div>
    </form>

    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; font-size:0.85rem; text-align:left;">
            <thead>
                <tr style="background:var(--light-bg); color:var(--dark); border-bottom:2px solid var(--border-color);">
                    <th style="padding:0.85rem 1rem; font-weight:800;">No</th>
                    <th style="padding:0.85rem 1rem; font-weight:800;">No. Pendaftaran</th>
                    <th style="padding:0.85rem 1rem; font-weight:800;">Nama Calon Siswa</th>
                    <th style="padding:0.85rem 1rem; font-weight:800;">Jurusan</th>
                    <th style="padding:0.85rem 1rem; font-weight:800;">Asal Sekolah</th>
                    <th style="padding:0.85rem 1rem; font-weight:800; text-align:center;">Rapor</th>
                    <th style="padding:0.85rem 1rem; font-weight:800; text-align:center;">CBT</th>
                    <th style="padding:0.85rem 1rem; font-weight:800; text-align:center;">Skor Akhir</th>
                    <th style="padding:0.85rem 1rem; font-weight:800; text-align:center;">Status Kelulusan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($daftarHasil as $idx => $hs)
                    @php
                        $p = $hs->pendaftaran;
                    @endphp
                    <tr style="border-bottom:1px solid var(--border-color);">
                        <td style="padding:0.85rem 1rem; font-weight:700;">
                            {{ ($daftarHasil->currentPage() - 1) * $daftarHasil->perPage() + $idx + 1 }}
                        </td>
                        <td style="padding:0.85rem 1rem; font-weight:800; font-family:monospace; color:var(--primary);">
                            {{ $p->nomor_pendaftaran ?? '-' }}
                        </td>
                        <td style="padding:0.85rem 1rem;">
                            <span style="font-weight:800; color:var(--dark); display:block;">
                                {{ $p->nama_lengkap ?? ($p->user->name ?? '-') }}
                            </span>
                            <span style="font-size:0.72rem; color:var(--gray-text);">{{ $p->user->email ?? '-' }}</span>
                        </td>
                        <td style="padding:0.85rem 1rem; font-weight:700;">
                            {{ $p->jurusan->nama ?? '-' }}
                        </td>
                        <td style="padding:0.85rem 1rem; color:var(--gray-text);">
                            {{ $p->asal_sekolah ?? '-' }}
                        </td>
                        <td style="padding:0.85rem 1rem; text-align:center; font-weight:700;">
                            {{ number_format($p->nilai_rapor ?? 0, 1) }}
                        </td>
                        <td style="padding:0.85rem 1rem; text-align:center; font-weight:700;">
                            {{ number_format($p->hasilUjian->skor ?? 0, 1) }}
                        </td>
                        <td style="padding:0.85rem 1rem; text-align:center; font-weight:900; color:var(--primary); font-size:0.95rem;">
                            {{ number_format($hs->skor_akhir, 2) }}
                        </td>
                        <td style="padding:0.85rem 1rem; text-align:center;">
                            @if($hs->kategori_kelulusan === 'DITERIMA')
                                <span class="badge-status badge-accepted">
                                    <i class="fa-solid fa-check"></i> DITERIMA
                                </span>
                            @elseif($hs->kategori_kelulusan === 'TIDAK HADIR CBT')
                                <span class="badge-status badge-nocbt">
                                    <i class="fa-solid fa-clock"></i> TDK HADIR CBT
                                </span>
                            @else
                                <span class="badge-status badge-rejected">
                                    <i class="fa-solid fa-xmark"></i> TIDAK DITERIMA
                                </span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="text-align:center; padding:3rem 1rem; color:var(--gray-text);">
                            <i class="fa-solid fa-folder-open" style="font-size:2.5rem; color:#cbd5e1; margin-bottom:0.75rem; display:block;"></i>
                            <strong>Tidak ada data hasil seleksi yang sesuai filter.</strong>
                            <p style="margin:0.25rem 0 0; font-size:0.8rem;">Pastikan panitia seleksi / admin telah mempublish hasil seleksi pendaftar.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($daftarHasil->hasPages())
        <div class="pagination-wrapper no-print" style="margin-top:1.5rem; display:flex; justify-content:center;">
            {{ $daftarHasil->links() }}
        </div>
    @endif
</div>
@endsection
