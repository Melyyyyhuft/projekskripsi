@extends('layouts.kepala-sekolah')
@section('title', 'Laporan Seleksi PPDB')

@section('content')
<style>
/* ═══════════════════════════════════════════════
   LAPORAN PAGE STYLES
═══════════════════════════════════════════════ */
.lp-wrapper {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
    padding-bottom: 3rem;
    animation: fadeInUp .45s cubic-bezier(.16,1,.3,1);
}
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(18px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* ── Header Card ─────────────────────────────── */
.lp-hero {
    background: linear-gradient(135deg, #1e1b4b 0%, #312e81 55%, #4338ca 100%);
    border-radius: 24px;
    padding: 2rem 2.25rem;
    color: white;
    position: relative;
    overflow: hidden;
    box-shadow: 0 16px 40px rgba(49,46,129,.25);
}
.lp-hero::before {
    content:'';
    position:absolute;
    top:-80px; right:-80px;
    width:280px; height:280px;
    border-radius:50%;
    background:rgba(255,255,255,.07);
    filter:blur(30px);
}
.lp-hero::after {
    content:'';
    position:absolute;
    bottom:-60px; left:30px;
    width:200px; height:200px;
    border-radius:50%;
    background:rgba(255,255,255,.05);
    filter:blur(20px);
}
.lp-hero-badge {
    display:inline-flex;
    align-items:center; gap:.4rem;
    background:rgba(255,255,255,.15);
    border:1px solid rgba(255,255,255,.25);
    padding:.3rem .9rem;
    border-radius:999px;
    font-size:.72rem; font-weight:800;
    letter-spacing:.07em; text-transform:uppercase;
    margin-bottom:.7rem;
    backdrop-filter:blur(8px);
}
.lp-hero h1 {
    font-size:1.7rem; font-weight:900;
    margin:0 0 .35rem; letter-spacing:-.02em;
}
.lp-hero p { margin:0; opacity:.9; font-size:.9rem; }
.lp-hero-meta {
    margin-top:1.25rem;
    padding-top:1rem;
    border-top:1px solid rgba(255,255,255,.15);
    display:flex; align-items:center; justify-content:space-between;
    flex-wrap:wrap; gap:.75rem; font-size:.82rem;
}

/* ── Tombol aksi header ──────────────────────── */
.btn-action {
    display:inline-flex; align-items:center; gap:.45rem;
    padding:.6rem 1.2rem;
    border-radius:12px;
    font-size:.83rem; font-weight:800;
    cursor:pointer;
    border:none;
    transition:all .2s ease;
    text-decoration:none;
}
.btn-print  { background:#fff; color:#312e81; box-shadow:0 4px 14px rgba(0,0,0,.15); }
.btn-pdf    { background:#ef4444; color:#fff; box-shadow:0 4px 14px rgba(239,68,68,.3); }
.btn-excel  { background:#10b981; color:#fff; box-shadow:0 4px 14px rgba(16,185,129,.3); }
.btn-action:hover { transform:translateY(-2px); filter:brightness(1.05); }

/* ── Filter Card ─────────────────────────────── */
.lp-filter-card {
    background:var(--white);
    border:1px solid var(--border-color);
    border-radius:20px;
    padding:1.5rem 1.75rem;
    box-shadow:var(--shadow-sm);
}
.lp-filter-card h3 {
    font-size:1rem; font-weight:800; color:var(--dark);
    margin:0 0 1.1rem; display:flex; align-items:center; gap:.5rem;
}
.filter-grid {
    display:grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap:.85rem;
    align-items:end;
}
.filter-group label {
    display:block;
    font-size:.75rem; font-weight:700; color:var(--gray-text);
    text-transform:uppercase; letter-spacing:.04em;
    margin-bottom:.35rem;
}
.filter-group select,
.filter-group input {
    width:100%; padding:.6rem .85rem;
    border:1px solid var(--border-color);
    border-radius:10px;
    font-size:.85rem; color:var(--dark);
    background:var(--light-bg);
    transition:border-color .2s;
}
.filter-group select:focus,
.filter-group input:focus {
    outline:none; border-color:#6366f1;
    box-shadow:0 0 0 3px rgba(99,102,241,.1);
}
.filter-actions { display:flex; gap:.6rem; }
.btn-filter-apply {
    flex:1; padding:.65rem 1.1rem;
    background:linear-gradient(135deg,#4f46e5,#7c3aed);
    color:#fff; border:none; border-radius:10px;
    font-size:.85rem; font-weight:800;
    cursor:pointer; transition:all .2s;
}
.btn-filter-apply:hover { transform:translateY(-1px); box-shadow:0 6px 15px rgba(79,70,229,.3); }
.btn-filter-reset {
    padding:.65rem 1rem;
    background:var(--light-bg); color:var(--gray-text);
    border:1px solid var(--border-color); border-radius:10px;
    font-size:.85rem; font-weight:700;
    cursor:pointer; text-decoration:none;
    display:inline-flex; align-items:center; gap:.35rem;
    transition:all .2s;
}
.btn-filter-reset:hover { background:var(--hover-bg); }

/* ── KPI Cards ───────────────────────────────── */
.kpi-grid {
    display:grid;
    grid-template-columns:repeat(auto-fit, minmax(200px,1fr));
    gap:1.1rem;
}
.kpi-card {
    background:var(--white);
    border:1px solid var(--border-color);
    border-radius:20px; padding:1.4rem;
    box-shadow:var(--shadow-sm);
    display:flex; align-items:center; gap:1.1rem;
    transition:transform .2s, box-shadow .2s;
}
.kpi-card:hover { transform:translateY(-3px); box-shadow:var(--shadow-md); }
.kpi-icon {
    width:52px; height:52px; border-radius:15px;
    display:flex; align-items:center; justify-content:center;
    font-size:1.4rem; flex-shrink:0;
}
.kpi-label {
    font-size:.72rem; font-weight:700;
    color:var(--gray-text); text-transform:uppercase;
    letter-spacing:.05em; margin-bottom:.2rem;
}
.kpi-value {
    font-size:1.7rem; font-weight:900;
    color:var(--dark); line-height:1;
}
.kpi-sub { font-size:.7rem; color:var(--gray-text); margin-top:.2rem; }

/* ── Section Header ──────────────────────────── */
.section-hd {
    display:flex; align-items:flex-start;
    justify-content:space-between; gap:1rem;
    flex-wrap:wrap; margin-bottom:1.25rem;
}
.section-hd h3 {
    font-size:1.05rem; font-weight:800; color:var(--dark);
    margin:0 0 .2rem; display:flex; align-items:center; gap:.5rem;
}
.section-hd p { margin:0; font-size:.82rem; color:var(--gray-text); }

/* ── Card universal ──────────────────────────── */
.lp-card {
    background:var(--white);
    border:1px solid var(--border-color);
    border-radius:20px; padding:1.6rem;
    box-shadow:var(--shadow-sm);
}

/* ── Jurusan Cards ───────────────────────────── */
.jurusan-grid {
    display:grid;
    grid-template-columns:repeat(auto-fit, minmax(280px,1fr));
    gap:1.1rem;
}
.jurusan-card {
    background:var(--white);
    border:1px solid var(--border-color);
    border-radius:18px; padding:1.35rem;
    box-shadow:var(--shadow-sm);
    transition:transform .2s, box-shadow .2s;
}
.jurusan-card:hover { transform:translateY(-2px); box-shadow:var(--shadow-md); }
.jurusan-stats {
    display:grid; grid-template-columns:repeat(4,1fr);
    gap:.6rem; background:var(--light-bg);
    padding:.8rem; border-radius:12px; margin:.85rem 0;
    text-align:center;
}
.jurusan-stat-label { font-size:.65rem; font-weight:700; color:var(--gray-text); text-transform:uppercase; }
.jurusan-stat-val   { font-size:1.15rem; font-weight:900; color:var(--dark); }

/* ── Badges ──────────────────────────────────── */
.badge-status {
    display:inline-flex; align-items:center; gap:.3rem;
    padding:.3rem .75rem; border-radius:999px;
    font-size:.72rem; font-weight:800;
}
.bs-diterima  { background:#dcfce7; color:#15803d; border:1px solid #86efac; }
.bs-tolak     { background:#fee2e2; color:#b91c1c; border:1px solid #fca5a5; }
.bs-nocbt     { background:#f1f5f9; color:#475569; border:1px solid #cbd5e1; }
.bs-lunas     { background:#dcfce7; color:#15803d; border:1px solid #86efac; }
.bs-belumbayar{ background:#fef3c7; color:#b45309; border:1px solid #fde68a; }
.bs-pending   { background:#e0f2fe; color:#0369a1; border:1px solid #bae6fd; }
.bs-gagal     { background:#fee2e2; color:#b91c1c; border:1px solid #fca5a5; }
.bs-kedaluwarsa { background:#f1f5f9; color:#475569; border:1px solid #cbd5e1; }

/* ── Table ───────────────────────────────────── */
.lp-table { width:100%; border-collapse:collapse; font-size:.84rem; }
.lp-table thead tr { background:var(--light-bg); border-bottom:2px solid var(--border-color); }
.lp-table thead th { padding:.8rem 1rem; font-weight:800; color:var(--dark); text-align:left; }
.lp-table thead th.center { text-align:center; }
.lp-table tbody tr { border-bottom:1px solid var(--border-color); transition:background .15s; }
.lp-table tbody tr:hover { background:var(--light-bg); }
.lp-table tbody td { padding:.82rem 1rem; vertical-align:middle; }
.lp-table tbody td.center { text-align:center; }
.lp-table tfoot tr { background:var(--light-bg); border-top:2px solid var(--border-color); }
.lp-table tfoot td { padding:.82rem 1rem; font-weight:900; }

/* ── Rekap Pembayaran Stats ──────────────────── */
.bayar-stats-grid {
    display:grid;
    grid-template-columns:repeat(auto-fit, minmax(160px,1fr));
    gap:1rem; margin-bottom:1.5rem;
}
.bayar-stat-card {
    text-align:center;
    background:var(--light-bg);
    border:1px solid var(--border-color);
    border-radius:14px; padding:1.1rem .8rem;
}
.bayar-stat-label { font-size:.72rem; font-weight:700; color:var(--gray-text); text-transform:uppercase; letter-spacing:.04em; margin-bottom:.3rem; }
.bayar-stat-val   { font-size:1.5rem; font-weight:900; color:var(--dark); }

/* ── Charts ──────────────────────────────────── */
.charts-grid {
    display:grid; grid-template-columns:1.8fr 1fr; gap:1.25rem;
}
.chart-card {
    background:var(--white);
    border:1px solid var(--border-color);
    border-radius:20px; padding:1.6rem;
    box-shadow:var(--shadow-sm);
}
.chart-card h4 {
    font-size:.9rem; font-weight:800; color:var(--dark);
    margin:0 0 1.1rem; display:flex; align-items:center; gap:.4rem;
}

/* ── Progress Bar ────────────────────────────── */
.progress-wrap { margin-top:.5rem; }
.progress-label { display:flex; justify-content:space-between; font-size:.72rem; font-weight:700; margin-bottom:.3rem; }
.progress-track { width:100%; height:7px; background:var(--border-color); border-radius:999px; overflow:hidden; }
.progress-fill  { height:100%; border-radius:999px; transition:width .5s ease; }

/* ── Print CSS ───────────────────────────────── */
@media print {
    @page { size: A4 portrait; margin: 15mm 15mm 20mm; }

    body { background:white !important; color:black !important; }
    .sidebar, .main-content > header,
    .no-print, .lp-filter-card, .charts-grid,
    .pagination-wrapper { display:none !important; }

    .main-content {
        margin:0 !important; padding:0 !important; width:100% !important;
        max-width:100% !important;
    }
    .lp-hero {
        background:#1e3a8a !important; print-color-adjust:exact;
        -webkit-print-color-adjust:exact;
    }
    .kpi-card, .jurusan-card, .lp-card, .chart-card {
        box-shadow:none !important; border:1px solid #cbd5e0 !important;
        break-inside:avoid;
    }
    .lp-table tbody tr:hover { background:transparent !important; }
    .print-signature {
        display:block !important;
        page-break-before:always;
    }
    .lp-hero { page-break-after:avoid; }
    .kpi-grid { page-break-after:avoid; }
}

/* ── Signature Print Block ───────────────────── */
.print-signature {
    display:none;
    margin-top:3rem; text-align:right; padding-right:3rem;
}
.print-signature p { font-size:.9rem; margin:.2rem 0; }
.print-signature-line {
    margin-top:4rem; border-top:1px solid #000;
    display:inline-block; min-width:200px;
    text-align:center; padding-top:.3rem;
    font-weight:800;
}

/* ── Responsive ──────────────────────────────── */
@media (max-width: 900px) {
    .charts-grid { grid-template-columns:1fr; }
    .jurusan-stats { grid-template-columns:repeat(2,1fr); }
}
@media (max-width: 640px) {
    .kpi-grid { grid-template-columns:repeat(2,1fr); }
    .filter-grid { grid-template-columns:1fr; }
}
</style>

<div class="lp-wrapper">

{{-- ─── HERO HEADER ──────────────────────────────────────────────────── --}}
<div class="lp-hero">
    <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:1.25rem; position:relative; z-index:1;">
        <div>
            <div class="lp-hero-badge">
                <i class="fa-solid fa-graduation-cap"></i> Laporan Eksekutif Kepala Sekolah
            </div>
            <h1>Laporan Seleksi PPDB</h1>
            <p>Ringkasan dan hasil penerimaan peserta didik baru {{ $settings['nama_sekolah'] ?? 'SMK Mitra Bintaro' }}.</p>
        </div>
        <div style="display:flex; align-items:center; gap:.7rem; flex-wrap:wrap;" class="no-print">
            <button onclick="window.print()" class="btn-action btn-print">
                <i class="fa-solid fa-print"></i> Cetak Laporan
            </button>
            <a href="{{ route('kepala_sekolah.laporan.pdf', request()->query()) }}"
               class="btn-action btn-pdf" target="_blank">
                <i class="fa-solid fa-file-pdf"></i> Export PDF
            </a>
            <a href="{{ route('kepala_sekolah.laporan.excel', request()->query()) }}"
               class="btn-action btn-excel" target="_blank">
                <i class="fa-solid fa-file-excel"></i> Export Excel
            </a>
        </div>
    </div>
    <div class="lp-hero-meta" style="position:relative; z-index:1;">
        <div style="display:flex; align-items:center; gap:.5rem;">
            <span style="width:9px; height:9px; border-radius:50%; background:{{ $totalDiterima > 0 ? '#4ade80' : '#fbbf24' }}; display:inline-block;"></span>
            <span>Tahun Ajaran: <strong>{{ $settings['tahun_ajaran'] ?? '2026/2027' }}</strong> &bull; Status Seleksi:
                <strong>{{ $totalDiterima > 0 ? 'Sudah Difinalisasi ('.$totalPublished.' Siswa)' : 'Belum Ada Data Terfinalisasi' }}</strong>
            </span>
        </div>
        <div style="opacity:.8;">
            <i class="fa-regular fa-clock"></i>
            Diperbarui: {{ now()->translatedFormat('d F Y, H:i') }} WIB
        </div>
    </div>
</div>

{{-- ─── FILTER CARD ─────────────────────────────────────────────────── --}}
<div class="lp-filter-card no-print">
    <h3>
        <i class="fa-solid fa-sliders" style="color:#6366f1;"></i> Filter Laporan
    </h3>
    <form method="GET" action="{{ route('kepala_sekolah.laporan') }}">
        <div class="filter-grid">
            <div class="filter-group">
                <label>Tahun Ajaran</label>
                <select name="tahun_ajaran" disabled>
                    <option>{{ $settings['tahun_ajaran'] ?? '2026/2027' }}</option>
                </select>
            </div>
            <div class="filter-group">
                <label>Jurusan</label>
                <select name="jurusan_id">
                    <option value="">Semua Jurusan</option>
                    @foreach($jurusans as $j)
                        <option value="{{ $j->id }}" {{ $filterJurusan == $j->id ? 'selected' : '' }}>{{ $j->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group">
                <label>Status Seleksi</label>
                <select name="status_seleksi">
                    <option value="">Semua Status</option>
                    <option value="DITERIMA" {{ $filterStatusSeleksi === 'DITERIMA' ? 'selected' : '' }}>Diterima</option>
                    <option value="TIDAK DITERIMA" {{ $filterStatusSeleksi === 'TIDAK DITERIMA' ? 'selected' : '' }}>Tidak Diterima</option>
                    <option value="TIDAK HADIR CBT" {{ $filterStatusSeleksi === 'TIDAK HADIR CBT' ? 'selected' : '' }}>Tidak Hadir CBT</option>
                </select>
            </div>
            <div class="filter-group">
                <label>Status Pembayaran</label>
                <select name="status_bayar">
                    <option value="">Semua Status</option>
                    <option value="lunas" {{ $filterStatusBayar === 'lunas' ? 'selected' : '' }}>Lunas</option>
                    <option value="belum_bayar" {{ $filterStatusBayar === 'belum_bayar' ? 'selected' : '' }}>Belum Bayar</option>
                    <option value="pending" {{ $filterStatusBayar === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="gagal" {{ $filterStatusBayar === 'gagal' ? 'selected' : '' }}>Gagal</option>
                    <option value="kedaluwarsa" {{ $filterStatusBayar === 'kedaluwarsa' ? 'selected' : '' }}>Kedaluwarsa</option>
                </select>
            </div>
            <div class="filter-group">
                <label>Cari Siswa</label>
                <input type="text" name="q" value="{{ $search }}" placeholder="Nama / No. Pendaftaran…">
            </div>
            <div class="filter-group">
                <label style="visibility:hidden;">Aksi</label>
                <div class="filter-actions">
                    <button type="submit" class="btn-filter-apply">
                        <i class="fa-solid fa-magnifying-glass"></i> Terapkan Filter
                    </button>
                    @if(!empty($search) || !empty($filterJurusan) || !empty($filterStatusSeleksi) || !empty($filterStatusBayar))
                        <a href="{{ route('kepala_sekolah.laporan') }}" class="btn-filter-reset">
                            <i class="fa-solid fa-rotate-left"></i> Reset
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </form>
</div>

{{-- ─── KPI CARDS ───────────────────────────────────────────────────── --}}
<div class="kpi-grid">
    <div class="kpi-card">
        <div class="kpi-icon" style="background:#eff6ff; color:#3b82f6;">
            <i class="fa-solid fa-users"></i>
        </div>
        <div>
            <div class="kpi-label">Total Pendaftar</div>
            <div class="kpi-value">{{ number_format($totalPendaftarKeseluruhan) }}</div>
            <div class="kpi-sub">Seluruh pendaftar</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon" style="background:#dcfce7; color:#16a34a;">
            <i class="fa-solid fa-user-check"></i>
        </div>
        <div>
            <div class="kpi-label">Total Diterima</div>
            <div class="kpi-value" style="color:#16a34a;">{{ number_format($totalDiterima) }}</div>
            <div class="kpi-sub">Lolos seleksi final</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon" style="background:#fee2e2; color:#dc2626;">
            <i class="fa-solid fa-user-xmark"></i>
        </div>
        <div>
            <div class="kpi-label">Tidak Diterima</div>
            <div class="kpi-value" style="color:#dc2626;">{{ number_format($totalTidakDiterima) }}</div>
            <div class="kpi-sub">Tidak lolos seleksi</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon" style="background:#fef9c3; color:#ca8a04;">
            <i class="fa-solid fa-ban"></i>
        </div>
        <div>
            <div class="kpi-label">Tidak Ikut CBT</div>
            <div class="kpi-value" style="color:#ca8a04;">{{ number_format($totalTidakHadirCBT) }}</div>
            <div class="kpi-sub">Tidak mengikuti ujian</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon" style="background:#f3e8ff; color:#9333ea;">
            <i class="fa-solid fa-chart-line"></i>
        </div>
        <div>
            <div class="kpi-label">Rata-rata Skor</div>
            <div class="kpi-value" style="color:#9333ea;">{{ number_format($avgSkorKeseluruhan, 2) }}</div>
            <div class="kpi-sub">Skor akhir seluruh siswa</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon" style="background:#fff7ed; color:#ea580c;">
            <i class="fa-solid fa-door-open"></i>
        </div>
        <div>
            <div class="kpi-label">Total Kuota</div>
            <div class="kpi-value">{{ number_format($totalKuota) }}</div>
            <div class="kpi-sub">Kapasitas seluruh jurusan</div>
        </div>
    </div>
</div>

{{-- ─── GRAFIK RINGKASAN ────────────────────────────────────────────── --}}
<div class="charts-grid no-print">
    <div class="chart-card">
        <h4>
            <i class="fa-solid fa-chart-bar" style="color:#6366f1;"></i>
            Jumlah Pendaftar dan Diterima Per Jurusan
        </h4>
        <canvas id="chartJurusan" height="240"></canvas>
    </div>
    <div class="chart-card">
        <h4>
            <i class="fa-solid fa-chart-pie" style="color:#10b981;"></i>
            Status Hasil Seleksi
        </h4>
        <canvas id="chartStatus" height="240"></canvas>
    </div>
</div>

{{-- ─── REKAP PER JURUSAN ───────────────────────────────────────────── --}}
<div>
    <div class="section-hd">
        <div>
            <h3><i class="fa-solid fa-layer-group" style="color:var(--primary);"></i> Rekap Pendaftar & Keterisian Kuota Per Jurusan</h3>
            <p>Perbandingan jumlah kuota, pendaftar, dan calon siswa yang diterima.</p>
        </div>
    </div>
    <div class="jurusan-grid">
        @foreach($rekapJurusan as $rk)
        @php
            $pct   = $rk['persen_keterisian'];
            $bar   = $pct >= 100 ? '#10b981' : ($pct >= 60 ? '#3b82f6' : ($pct >= 30 ? '#f59e0b' : '#ef4444'));
        @endphp
        <div class="jurusan-card">
            <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                <div>
                    <div style="font-size:1rem; font-weight:800; color:var(--dark);">{{ $rk['jurusan']->nama }}</div>
                    <div style="font-size:.72rem; color:var(--gray-text); font-weight:600; margin-top:.15rem;">
                        Kode: {{ $rk['jurusan']->kode ?? 'JUR-'.$rk['jurusan']->id }}
                    </div>
                </div>
                <span style="background:var(--light-bg); border:1px solid var(--border-color); padding:.22rem .7rem; border-radius:8px; font-weight:800; font-size:.75rem; white-space:nowrap;">
                    Kuota: {{ $rk['kuota'] }}
                </span>
            </div>
            <div class="jurusan-stats">
                <div>
                    <div class="jurusan-stat-label">Pendaftar</div>
                    <div class="jurusan-stat-val">{{ $rk['total_pendaftar'] }}</div>
                </div>
                <div>
                    <div class="jurusan-stat-label" style="color:#16a34a;">Diterima</div>
                    <div class="jurusan-stat-val" style="color:#16a34a;">{{ $rk['diterima'] }}</div>
                </div>
                <div>
                    <div class="jurusan-stat-label" style="color:#dc2626;">Ditolak</div>
                    <div class="jurusan-stat-val" style="color:#dc2626;">{{ $rk['tidak_diterima'] }}</div>
                </div>
                <div>
                    <div class="jurusan-stat-label">Sisa Kuota</div>
                    <div class="jurusan-stat-val" style="color:{{ $rk['sisa_kuota'] > 0 ? '#d97706' : '#64748b' }};">{{ $rk['sisa_kuota'] }}</div>
                </div>
            </div>
            <div class="progress-wrap">
                <div class="progress-label">
                    <span style="color:var(--gray-text);">Keterisian Kuota</span>
                    <span style="color:{{ $bar }}; font-weight:900;">{{ $pct }}%</span>
                </div>
                <div class="progress-track">
                    <div class="progress-fill" style="width:{{ min(100, $pct) }}%; background:{{ $bar }};"></div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- ─── TABEL REKAP HASIL SELEKSI PER JURUSAN ──────────────────────── --}}
<div class="lp-card">
    <div class="section-hd">
        <div>
            <h3><i class="fa-solid fa-chart-column" style="color:#6366f1;"></i> Rekap Status Kelulusan Per Jurusan</h3>
            <p>Data dari hasil seleksi yang telah difinalisasi.</p>
        </div>
    </div>
    <div style="overflow-x:auto;">
        <table class="lp-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Jurusan</th>
                    <th class="center">Kuota</th>
                    <th class="center">Pendaftar</th>
                    <th class="center">Diterima</th>
                    <th class="center">Tdk Diterima</th>
                    <th class="center">Tdk Hadir CBT</th>
                    <th class="center">Sisa Kuota</th>
                    <th class="center">% Keterisian</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rekapJurusan as $idx => $rk)
                <tr>
                    <td style="font-weight:700;">{{ $idx + 1 }}</td>
                    <td style="font-weight:800; color:var(--dark);">{{ $rk['jurusan']->nama }}</td>
                    <td class="center">{{ $rk['kuota'] }}</td>
                    <td class="center">{{ $rk['total_pendaftar'] }}</td>
                    <td class="center" style="font-weight:900; color:#16a34a;">{{ $rk['diterima'] }}</td>
                    <td class="center" style="color:#dc2626;">{{ $rk['tidak_diterima'] }}</td>
                    <td class="center" style="color:#64748b;">{{ $rk['tidak_hadir_cbt'] }}</td>
                    <td class="center">{{ $rk['sisa_kuota'] }}</td>
                    <td class="center" style="font-weight:900; color:{{ $rk['persen_keterisian'] >= 100 ? '#10b981' : '#3b82f6' }};">
                        {{ $rk['persen_keterisian'] }}%
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2">TOTAL KESELURUHAN</td>
                    <td class="center">{{ $totalKuota }}</td>
                    <td class="center">{{ $totalPendaftarKeseluruhan }}</td>
                    <td class="center" style="color:#16a34a;">{{ $totalDiterima }}</td>
                    <td class="center" style="color:#dc2626;">{{ $totalTidakDiterima }}</td>
                    <td class="center" style="color:#64748b;">{{ $totalTidakHadirCBT }}</td>
                    <td class="center">{{ max(0, $totalKuota - $totalDiterima) }}</td>
                    <td class="center" style="color:#3b82f6;">
                        {{ $totalKuota > 0 ? round(($totalDiterima / $totalKuota) * 100, 1) : 0 }}%
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

{{-- ─── TABEL REKAP HASIL SELEKSI SISWA ────────────────────────────── --}}
<div class="lp-card">
    <div class="section-hd">
        <div>
            <h3><i class="fa-solid fa-list-check" style="color:var(--primary);"></i> Rekap Hasil Seleksi Siswa</h3>
            <p>Daftar lengkap siswa dari hasil seleksi yang telah dipublish secara resmi.</p>
        </div>
    </div>

    {{-- Filter Bar (hanya untuk tabel siswa) --}}
    <form method="GET" action="{{ route('kepala_sekolah.laporan') }}" class="no-print"
          style="background:var(--light-bg); padding:.85rem 1rem; border-radius:12px; margin-bottom:1.25rem; display:flex; gap:.8rem; align-items:center; flex-wrap:wrap;">
        <input type="text" name="q" value="{{ $search }}"
               placeholder="🔍 Cari nama siswa / no. pendaftaran…"
               style="flex:1; min-width:200px; padding:.55rem .9rem; border:1px solid var(--border-color); border-radius:9px; font-size:.84rem;">
        <select name="jurusan_id" style="padding:.55rem .85rem; border:1px solid var(--border-color); border-radius:9px; font-size:.84rem; min-width:160px;">
            <option value="">Semua Jurusan</option>
            @foreach($jurusans as $j)
                <option value="{{ $j->id }}" {{ $filterJurusan == $j->id ? 'selected' : '' }}>{{ $j->nama }}</option>
            @endforeach
        </select>
        <select name="status_seleksi" style="padding:.55rem .85rem; border:1px solid var(--border-color); border-radius:9px; font-size:.84rem; min-width:150px;">
            <option value="">Semua Status</option>
            <option value="DITERIMA" {{ $filterStatusSeleksi === 'DITERIMA' ? 'selected' : '' }}>✅ Diterima</option>
            <option value="TIDAK DITERIMA" {{ $filterStatusSeleksi === 'TIDAK DITERIMA' ? 'selected' : '' }}>❌ Tidak Diterima</option>
            <option value="TIDAK HADIR CBT" {{ $filterStatusSeleksi === 'TIDAK HADIR CBT' ? 'selected' : '' }}>⚪ Tdk Hadir CBT</option>
        </select>
        <select name="status_bayar" style="padding:.55rem .85rem; border:1px solid var(--border-color); border-radius:9px; font-size:.84rem; min-width:150px;">
            <option value="">Semua Pembayaran</option>
            <option value="lunas" {{ $filterStatusBayar === 'lunas' ? 'selected' : '' }}>💚 Lunas</option>
            <option value="belum_bayar" {{ $filterStatusBayar === 'belum_bayar' ? 'selected' : '' }}>🟡 Belum Bayar</option>
            <option value="pending" {{ $filterStatusBayar === 'pending' ? 'selected' : '' }}>🔵 Pending</option>
            <option value="gagal" {{ $filterStatusBayar === 'gagal' ? 'selected' : '' }}>🔴 Gagal</option>
        </select>
        <button type="submit" class="btn-filter-apply" style="flex:none; padding:.55rem 1.1rem;">
            <i class="fa-solid fa-filter"></i> Filter
        </button>
        @if(!empty($search) || !empty($filterJurusan) || !empty($filterStatusSeleksi) || !empty($filterStatusBayar))
            <a href="{{ route('kepala_sekolah.laporan') }}" class="btn-filter-reset">
                <i class="fa-solid fa-rotate-left"></i> Reset
            </a>
        @endif
    </form>

    <div style="overflow-x:auto;">
        <table class="lp-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>No. Pendaftaran</th>
                    <th>Nama Siswa</th>
                    <th>Jurusan</th>
                    <th class="center">Nilai Rapor</th>
                    <th class="center">Nilai CBT</th>
                    <th class="center">Skor Akhir</th>
                    <th class="center">Status Seleksi</th>
                    <th class="center">Status Daftar Ulang</th>
                </tr>
            </thead>
            <tbody>
                @forelse($daftarHasil as $idx => $hs)
                @php
                    $p     = $hs->pendaftaran;
                    $bayar = $p->pembayaranDaftarUlang ?? null;
                    $bayarStatus = match($bayar?->status ?? '') {
                        'lunas'       => ['label' => 'Lunas',       'cls' => 'bs-lunas'],
                        'pending'     => ['label' => 'Pending',     'cls' => 'bs-pending'],
                        'gagal'       => ['label' => 'Gagal',       'cls' => 'bs-gagal'],
                        'kedaluwarsa' => ['label' => 'Kedaluwarsa', 'cls' => 'bs-kedaluwarsa'],
                        'belum_bayar' => ['label' => 'Belum Bayar', 'cls' => 'bs-belumbayar'],
                        default       => ['label' => '—',           'cls' => 'bs-nocbt'],
                    };
                @endphp
                <tr>
                    <td style="font-weight:700; color:var(--gray-text);">
                        {{ ($daftarHasil->currentPage() - 1) * $daftarHasil->perPage() + $idx + 1 }}
                    </td>
                    <td style="font-family:monospace; font-weight:800; color:var(--primary); font-size:.82rem;">
                        {{ $p->nomor_pendaftaran ?? '-' }}
                    </td>
                    <td>
                        <div style="font-weight:800; color:var(--dark);">{{ $p->nama_lengkap ?? ($p->user->name ?? '-') }}</div>
                        <div style="font-size:.72rem; color:var(--gray-text);">{{ $p->user->email ?? '' }}</div>
                    </td>
                    <td style="font-weight:700;">{{ $p->jurusan->nama ?? '-' }}</td>
                    <td class="center" style="font-weight:700;">{{ number_format($p->nilai_rapor ?? 0, 1) }}</td>
                    <td class="center" style="font-weight:700;">{{ number_format($p->hasilUjian->skor ?? 0, 1) }}</td>
                    <td class="center" style="font-weight:900; color:var(--primary); font-size:.95rem;">
                        {{ number_format($hs->skor_akhir, 2) }}
                    </td>
                    <td class="center">
                        @if($hs->kategori_kelulusan === 'DITERIMA')
                            <span class="badge-status bs-diterima"><i class="fa-solid fa-check"></i> DITERIMA</span>
                        @elseif($hs->kategori_kelulusan === 'TIDAK HADIR CBT')
                            <span class="badge-status bs-nocbt"><i class="fa-solid fa-clock"></i> TDK HADIR CBT</span>
                        @else
                            <span class="badge-status bs-tolak"><i class="fa-solid fa-xmark"></i> TDK DITERIMA</span>
                        @endif
                    </td>
                    <td class="center">
                        <span class="badge-status {{ $bayarStatus['cls'] }}">{{ $bayarStatus['label'] }}</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="text-align:center; padding:3rem; color:var(--gray-text);">
                        <i class="fa-solid fa-folder-open" style="font-size:2rem; display:block; margin-bottom:.5rem; opacity:.4;"></i>
                        <strong>Tidak ada data hasil seleksi yang sesuai filter.</strong>
                        <p style="margin:.3rem 0 0; font-size:.8rem;">Pastikan admin telah mempublish hasil seleksi peserta.</p>
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

{{-- ─── REKAP PEMBAYARAN DAFTAR ULANG ──────────────────────────────── --}}
<div class="lp-card">
    <div class="section-hd" style="margin-bottom:1rem;">
        <div>
            <h3><i class="fa-solid fa-receipt" style="color:#10b981;"></i> Rekap Pembayaran Daftar Ulang</h3>
            <p>Data pembayaran daftar ulang calon siswa yang diterima.</p>
        </div>
    </div>

    {{-- Statistik Pembayaran --}}
    <div class="bayar-stats-grid">
        <div class="bayar-stat-card">
            <div class="bayar-stat-label">Total Tagihan</div>
            <div class="bayar-stat-val">{{ $totalTagihan }}</div>
            <div style="font-size:.7rem; color:var(--gray-text);">siswa</div>
        </div>
        <div class="bayar-stat-card">
            <div class="bayar-stat-label">Sudah Dibayar</div>
            <div class="bayar-stat-val" style="color:#16a34a;">{{ $totalSudahBayar }}</div>
            <div style="font-size:.7rem; color:var(--gray-text);">siswa lunas</div>
        </div>
        <div class="bayar-stat-card">
            <div class="bayar-stat-label">Belum Dibayar</div>
            <div class="bayar-stat-val" style="color:#dc2626;">{{ $totalBelumBayar }}</div>
            <div style="font-size:.7rem; color:var(--gray-text);">siswa belum bayar</div>
        </div>
        <div class="bayar-stat-card">
            <div class="bayar-stat-label">Total Nominal Masuk</div>
            <div class="bayar-stat-val" style="color:#7c3aed; font-size:1.2rem;">
                Rp {{ number_format($totalNominalMasuk, 0, ',', '.') }}
            </div>
            <div style="font-size:.7rem; color:var(--gray-text);">total terbayar</div>
        </div>
    </div>

    <div style="overflow-x:auto;">
        <table class="lp-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>No. Pendaftaran</th>
                    <th>Nama Siswa</th>
                    <th>Jurusan</th>
                    <th class="center">Jumlah Tagihan</th>
                    <th class="center">Status Pembayaran</th>
                    <th class="center">Metode</th>
                    <th class="center">Tanggal Bayar</th>
                </tr>
            </thead>
            <tbody>
                @forelse($daftarPembayaran as $idx => $pb)
                @php
                    $p2 = $pb->pendaftaran;
                    $pbStatus = match($pb->status ?? '') {
                        'lunas'       => ['label' => 'Lunas',       'cls' => 'bs-lunas'],
                        'pending'     => ['label' => 'Pending',     'cls' => 'bs-pending'],
                        'gagal'       => ['label' => 'Gagal',       'cls' => 'bs-gagal'],
                        'kedaluwarsa' => ['label' => 'Kedaluwarsa', 'cls' => 'bs-kedaluwarsa'],
                        'belum_bayar' => ['label' => 'Belum Bayar', 'cls' => 'bs-belumbayar'],
                        default       => ['label' => ucfirst($pb->status), 'cls' => 'bs-nocbt'],
                    };
                @endphp
                <tr>
                    <td style="font-weight:700; color:var(--gray-text);">{{ $idx + 1 }}</td>
                    <td style="font-family:monospace; font-weight:800; color:var(--primary); font-size:.82rem;">
                        {{ $p2->nomor_pendaftaran ?? '-' }}
                    </td>
                    <td style="font-weight:800; color:var(--dark);">{{ $p2->nama_lengkap ?? ($p2->user->name ?? '-') }}</td>
                    <td style="font-weight:700;">{{ $p2->jurusan->nama ?? '-' }}</td>
                    <td class="center" style="font-weight:900; color:var(--dark);">
                        Rp {{ number_format($pb->jumlah, 0, ',', '.') }}
                    </td>
                    <td class="center">
                        <span class="badge-status {{ $pbStatus['cls'] }}">{{ $pbStatus['label'] }}</span>
                    </td>
                    <td class="center" style="font-weight:700; color:var(--gray-text);">
                        {{ $pb->metode_pembayaran ?? '—' }}
                    </td>
                    <td class="center" style="font-weight:700;">
                        {{ $pb->paid_at ? $pb->paid_at->translatedFormat('d F Y') : '—' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center; padding:2.5rem; color:var(--gray-text);">
                        <i class="fa-solid fa-wallet" style="font-size:1.8rem; display:block; margin-bottom:.5rem; opacity:.35;"></i>
                        <strong>Belum ada data pembayaran daftar ulang.</strong>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ─── TANDA TANGAN (hanya saat cetak) ───────────────────────────── --}}
<div class="print-signature">
    <p>{{ $settings['nama_sekolah'] ?? 'SMK Mitra Bintaro' }}, {{ now()->translatedFormat('d F Y') }}</p>
    <p>Mengetahui,</p>
    <p>Kepala Sekolah</p>
    <div style="margin-top:4rem;">
        <div class="print-signature-line">
            (________________________________)
        </div>
    </div>
    <p style="font-weight:800;">Kepala {{ $settings['nama_sekolah'] ?? 'SMK Mitra Bintaro' }}</p>
</div>

</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// ─── Data dari PHP ──────────────────────────────────────────────────────────
const chartJurusanLabels    = @json($chartJurusanLabels);
const chartJurusanPendaftar = @json($chartJurusanPendaftar);
const chartJurusanDiterima  = @json($chartJurusanDiterima);

const totalDiterima       = {{ $totalDiterima }};
const totalTidakDiterima  = {{ $totalTidakDiterima }};
const totalTidakHadirCBT  = {{ $totalTidakHadirCBT }};

// ─── Chart 1: Bar Chart Jurusan ─────────────────────────────────────────────
const ctxBar = document.getElementById('chartJurusan').getContext('2d');
new Chart(ctxBar, {
    type: 'bar',
    data: {
        labels: chartJurusanLabels,
        datasets: [
            {
                label: 'Pendaftar',
                data: chartJurusanPendaftar,
                backgroundColor: 'rgba(99, 102, 241, 0.75)',
                borderColor: '#6366f1',
                borderWidth: 1.5,
                borderRadius: 6,
            },
            {
                label: 'Diterima',
                data: chartJurusanDiterima,
                backgroundColor: 'rgba(16, 185, 129, 0.75)',
                borderColor: '#10b981',
                borderWidth: 1.5,
                borderRadius: 6,
            },
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'top', labels: { font: { weight: '700', size: 12 } } },
        },
        scales: {
            x: { grid: { display: false }, ticks: { font: { size: 11 } } },
            y: {
                beginAtZero: true,
                grid: { color: 'rgba(0,0,0,.06)' },
                ticks: { stepSize: 1, font: { size: 11 } }
            }
        }
    }
});

// ─── Chart 2: Doughnut Status Seleksi ──────────────────────────────────────
const ctxDoughnut = document.getElementById('chartStatus').getContext('2d');
new Chart(ctxDoughnut, {
    type: 'doughnut',
    data: {
        labels: ['Diterima', 'Tidak Diterima', 'Tidak Hadir CBT'],
        datasets: [{
            data: [totalDiterima, totalTidakDiterima, totalTidakHadirCBT],
            backgroundColor: [
                'rgba(16, 185, 129, 0.85)',
                'rgba(239, 68, 68, 0.85)',
                'rgba(148, 163, 184, 0.85)',
            ],
            borderColor: ['#10b981', '#ef4444', '#94a3b8'],
            borderWidth: 2,
            hoverOffset: 8,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '62%',
        plugins: {
            legend: {
                position: 'bottom',
                labels: { padding: 16, font: { weight: '700', size: 12 } }
            },
        }
    }
});
</script>
@endsection
