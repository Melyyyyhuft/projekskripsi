@extends('layouts.admin')
@section('title', 'Hasil Seleksi')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
/* ─── Core Variables & Animations ─── */
:root { --primary:#3b82f6; --success:#10b981; --warning:#f59e0b; --danger:#ef4444; --dark:#0f172a; --gray:#64748b; }
@keyframes fadeIn { from{opacity:0;transform:translateY(10px)} to{opacity:1;transform:translateY(0)} }
@keyframes slideUp { from{opacity:0;transform:translateY(30px)} to{opacity:1;transform:translateY(0)} }
@keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.6} }

.dashboard-container { display:flex; flex-direction:column; gap:2rem; animation:fadeIn .5s ease-out; }

/* ─── Glass Cards ─── */
.premium-card {
    background:rgba(255,255,255,.75); backdrop-filter:blur(20px); -webkit-backdrop-filter:blur(20px);
    border:1px solid rgba(255,255,255,.5); border-radius:24px; padding:1.5rem;
    box-shadow:0 8px 32px rgba(0,0,0,.03); transition:all .3s ease;
}
.premium-card:hover { box-shadow:0 12px 40px rgba(0,0,0,.06); }

/* ─── Stats ─── */
.stats-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:1.25rem; }
.stat-card { padding:1.25rem; display:flex; flex-direction:column; gap:.4rem; text-decoration:none!important; cursor:pointer; }
.stat-card:hover { transform:translateY(-4px); }
.stat-icon { width:40px; height:40px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:1.1rem; margin-bottom:.4rem; }
.stat-val { font-size:1.75rem; font-weight:900; color:var(--dark); line-height:1; }
.stat-label { font-size:.7rem; font-weight:800; color:var(--gray); text-transform:uppercase; letter-spacing:.06em; }

/* ─── Table ─── */
.modern-table { width:100%; border-collapse:separate; border-spacing:0; }
.modern-table thead th { padding:.9rem 1rem; text-align:left; font-size:.65rem; font-weight:800; color:var(--gray); text-transform:uppercase; letter-spacing:.06em; border-bottom:2px solid #f1f5f9; background:rgba(248,250,252,.6); }
.modern-table tbody tr { transition:all .2s; }
.modern-table tbody tr:hover { background:rgba(59,130,246,.02)!important; }
.modern-table tbody td { padding:1rem; font-size:.8rem; vertical-align:middle; border-bottom:1px solid #f1f5f9; color:#1e293b; }

/* ─── Badges ─── */
.badge-m { padding:.3rem .6rem; border-radius:999px; font-size:.6rem; font-weight:800; display:inline-flex; align-items:center; gap:.3rem; text-transform:uppercase; white-space:nowrap; }
.b-diterima { background:#dcfce7; color:#166534; border:1px solid #86efac; }
.b-tidak { background:#fee2e2; color:#991b1b; border:1px solid #fecaca; }
.b-cbt { background:#fff1f2; color:#e11d48; border:1px solid #fecdd3; }

.b-proses { background:#f5f3ff; color:#5b21b6; border:1px solid #ddd6fe; }
.b-publish { background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; }
.b-review { background:#fff7ed; color:#c2410c; border:1px solid #fed7aa; }
.b-override { background:#fef3c7; color:#92400e; border:1px solid #fde68a; }

/* ─── Buttons ─── */
.btn-g { padding:.65rem 1.25rem; border-radius:14px; font-weight:800; font-size:.8rem; border:none; color:white; display:inline-flex; align-items:center; gap:.5rem; cursor:pointer; transition:all .25s; box-shadow:0 6px 16px rgba(0,0,0,.06); }
.btn-g:hover { transform:translateY(-2px); filter:brightness(1.08); }
.btn-g:disabled { opacity:.45; cursor:not-allowed; transform:none; }
.bg-primary { background:linear-gradient(135deg,#3b82f6,#2563eb); }
.bg-success { background:linear-gradient(135deg,#10b981,#059669); }
.bg-dark { background:linear-gradient(135deg,#1e293b,#0f172a); }

.btn-action { width:32px; height:32px; border-radius:10px; border:none; background:#f1f5f9; color:var(--gray); display:flex; align-items:center; justify-content:center; cursor:pointer; transition:all .2s; font-size:.8rem; }
.btn-action:hover { background:#eff6ff; color:var(--primary); transform:scale(1.1); }

/* ─── Toolbar ─── */
.search-wrap { position:relative; min-width:240px; }
.search-wrap i { position:absolute; left:.9rem; top:50%; transform:translateY(-50%); color:var(--gray); font-size:.8rem; }
.search-inp { width:100%; padding:.65rem .9rem .65rem 2.4rem; border-radius:12px; border:1px solid #e2e8f0; background:white; font-weight:600; color:#1e293b; outline:none; font-size:.85rem; transition:all .2s; }
.search-inp:focus { border-color:var(--primary); box-shadow:0 0 0 3px rgba(59,130,246,.1); }
.filter-sel { padding:.6rem 1rem; border-radius:12px; border:1px solid #e2e8f0; background:white; font-weight:700; color:#4b5563; font-size:.78rem; outline:none; cursor:pointer; }
.form-checkbox { width:16px; height:16px; border-radius:5px; accent-color:var(--primary); cursor:pointer; }

/* ─── Modal ─── */
.modal-overlay {
    position:fixed; top:0; left:0; width:100%; height:100%;
    background:rgba(15,23,42,.5); backdrop-filter:blur(10px);
    display:none; align-items:center; justify-content:center; z-index:1000;
    animation:fadeIn .25s ease-out;
}
.modal-glass {
    width:92%; max-width:960px; max-height:90vh; overflow-y:auto;
    background:rgba(255,255,255,.95); border:1px solid rgba(255,255,255,.6);
    border-radius:32px; padding:2.5rem; box-shadow:0 25px 60px rgba(0,0,0,.12);
    animation:slideUp .35s cubic-bezier(.16,1,.3,1);
}
.modal-glass::-webkit-scrollbar { width:6px; }
.modal-glass::-webkit-scrollbar-thumb { background:#cbd5e1; border-radius:8px; }

/* ─── Preview Table ─── */
.preview-table { width:100%; border-collapse:collapse; }
.preview-table th { padding:.7rem .8rem; background:#f8fafc; font-size:.6rem; font-weight:800; color:var(--gray); text-transform:uppercase; letter-spacing:.05em; border-bottom:2px solid #f1f5f9; text-align:left; }
.preview-table td { padding:.7rem .8rem; font-size:.78rem; border-bottom:1px solid #f1f5f9; vertical-align:middle; }
.preview-table tr:hover { background:#fafbff; }

/* ─── Formula Box ─── */
.formula-box {
    background:linear-gradient(135deg,#f0f9ff,#eff6ff); border:1px solid #bfdbfe;
    border-radius:16px; padding:1rem 1.5rem; margin:.75rem 0;
    font-family:'Courier New',monospace; font-size:.85rem; font-weight:700; color:#1e40af;
}

/* ─── Switch / Toggle ─── */
.switch { position:relative; display:inline-block; width:44px; height:24px; }
.switch input { opacity:0; width:0; height:0; }
.slider { position:absolute; cursor:pointer; top:0; left:0; right:0; bottom:0; background-color:#e2e8f0; transition:.3s; border-radius:24px; }
.slider:before { position:absolute; content:""; height:18px; width:18px; left:3px; bottom:3px; background-color:white; transition:.3s; border-radius:50%; }
input:checked + .slider { background-color:var(--primary); }
input:checked + .slider:before { transform:translateX(20px); }

/* ─── Mode Section ─── */
.mode-section { padding:1.25rem; border-radius:24px; border:1px solid #f1f5f9; background:rgba(255,255,255,.5); margin-bottom:1.5rem; transition:all .3s ease; }
.mode-section.active { border-color:var(--primary); box-shadow:0 10px 25px rgba(59,130,246,.05); background:white; }
.mode-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:1.25rem; }
.mode-title { font-size:.65rem; font-weight:900; color:var(--gray); text-transform:uppercase; letter-spacing:.1em; display:flex; align-items:center; gap:.5rem; }

.mode-field { display:flex; flex-direction:column; gap:.4rem; }
.mode-label { font-size:.7rem; font-weight:700; color:var(--gray); }
.mode-input { padding:.6rem .8rem; border-radius:12px; border:1px solid #e2e8f0; outline:none; font-size:.8rem; font-weight:600; color:var(--dark); background:#f8fafc; transition:all .2s; }
.mode-input:focus { border-color:var(--primary); background:white; box-shadow:0 0 0 3px rgba(59,130,246,.1); }
.mode-input:disabled { opacity:.6; cursor:not-allowed; }

/* ─── Responsive ─── */
@media(max-width:768px) {
    .stats-grid { grid-template-columns:repeat(2,1fr); }
    .modal-glass { padding:1.5rem; border-radius:20px; max-height:95vh; }
    .toolbar-flex { flex-direction:column!important; align-items:stretch!important; }
}
    /* ─── Scrolling Table Refinement ─── */
    .table-scrolling-container {
        border-radius: 20px;
        overflow-y: auto;
        max-height: 480px;
        position: relative;
        background: white;
    }
    .table-scrolling-container::-webkit-scrollbar { width: 8px; }
    .table-scrolling-container::-webkit-scrollbar-track { background: #f8fafc; }
    .table-scrolling-container::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .table-scrolling-container::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

    .modern-table thead { position: sticky; top: 0; z-index: 20; }
    .modern-table thead th { background: #f8fafc; border-bottom: 2px solid #eef2f6; }
</style>

<div class="dashboard-container">
    {{-- ─── Header ─── --}}
    <div style="display:flex; justify-content:space-between; align-items:flex-end; flex-wrap:wrap; gap:1rem;">
        <div>
            <h1 style="font-size:1.6rem; font-weight:900; color:var(--dark); margin:0 0 .2rem; letter-spacing:-.02em;">🏫 Hasil Seleksi</h1>
            <p style="color:var(--gray); font-size:.9rem; margin:0; font-weight:500;">Preview kalkulasi → Review → Publish hasil ke siswa.</p>
        </div>
    </div>

    {{-- ─── Criteria Info ─── --}}
    <div style="background: linear-gradient(135deg, #eff6ff, #dbeafe); border: 1px solid #bfdbfe; border-radius: 18px; padding: 1.25rem 1.5rem; display: flex; align-items: center; gap: 1.25rem; animation: fadeIn 0.5s ease-out;">
        <div style="width: 48px; height: 48px; background: white; border-radius: 14px; display: flex; align-items: center; justify-content: center; color: #3b82f6; font-size: 1.2rem; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.1);">
            <i class="fa-solid fa-circle-info"></i>
        </div>
        <div>
            <h4 style="margin: 0; font-size: 0.9rem; font-weight: 800; color: #1e3a8a;">Kriteria Kelulusan Utama</h4>
            <p style="margin: 0.1rem 0 0; font-size: 0.82rem; color: #1e40af; font-weight: 600;">
                Skor Akhir ≥ <span style="background: #3b82f6; color: white; padding: 2px 8px; border-radius: 6px; font-size: 0.75rem;">60</span> = <span style="color: #059669;">Diterima</span> 
                | &lt; 60 = <span style="color: #dc2626;">Tidak Diterima</span>
            </p>
        </div>
    </div>

    {{-- ─── Stats ─── --}}
    @php
        $activeTab = request('tab', 'total');
        if(!request()->has('tab') && request()->has('status_proses')) {
            if(request('status_proses') == 'Sudah Dihitung') $activeTab = 'dihitung';
            elseif(request('status_hasil') == 'DITERIMA') $activeTab = 'diterima';
            elseif(request('status_hasil') == 'TIDAK DITERIMA') $activeTab = 'tidak_diterima';
        }
    @endphp
    <div class="stats-grid">
        <a href="{{ route('admin.penempatan.index', array_merge(request()->query(), ['status_proses'=>'','status_hasil'=>'', 'tab'=>'total'])) }}" class="premium-card stat-card" style="{{ $activeTab == 'total' ? 'border-color:#fbbf24; border-width: 2px;' : '' }}">
            <div class="stat-icon" style="background:#f1f5f9; color:var(--dark);"><i class="fa-solid fa-users"></i></div>
            <div class="stat-val">{{ $stats['total'] }}</div>
            <div class="stat-label">Total Peserta</div>
        </a>
        <a href="{{ route('admin.penempatan.index', array_merge(request()->query(), ['status_proses'=>'Sudah Dihitung','status_hasil'=>'', 'tab'=>'dihitung'])) }}" class="premium-card stat-card" style="{{ $activeTab == 'dihitung' ? 'border-color:#fbbf24; border-width: 2px;' : '' }}">
            <div class="stat-icon" style="background:#f5f3ff; color:#5b21b6;"><i class="fa-solid fa-calculator"></i></div>
            <div class="stat-val">{{ $stats['dihitung'] }}</div>
            <div class="stat-label">Sudah Dihitung</div>
        </a>
        <a href="{{ route('admin.penempatan.index', array_merge(request()->query(), ['status_proses'=>'Sudah Dihitung','status_hasil'=>'', 'tab'=>'belum_publish'])) }}" class="premium-card stat-card" style="{{ $activeTab == 'belum_publish' ? 'border-color:#fbbf24; border-width: 2px;' : '' }}">
            <div class="stat-icon" style="background:#fff7ed; color:#c2410c;"><i class="fa-solid fa-clock"></i></div>
            <div class="stat-val">{{ $stats['belum_publish'] }}</div>
            <div class="stat-label">Belum Dipublish</div>
        </a>
        <a href="{{ route('admin.penempatan.index', array_merge(request()->query(), ['status_hasil'=>'DITERIMA','status_proses'=>'', 'tab'=>'diterima'])) }}" class="premium-card stat-card" style="{{ $activeTab == 'diterima' ? 'border-color:#fbbf24; border-width: 2px;' : '' }}">
            <div class="stat-icon" style="background:#f0fdf4; color:#166534;"><i class="fa-solid fa-user-check"></i></div>
            <div class="stat-val">{{ $stats['diterima'] }}</div>
            <div class="stat-label">Diterima</div>
        </a>
        <a href="{{ route('admin.penempatan.index', array_merge(request()->query(), ['status_hasil'=>'TIDAK DITERIMA','status_proses'=>'', 'tab'=>'tidak_diterima'])) }}" class="premium-card stat-card" style="{{ $activeTab == 'tidak_diterima' ? 'border-color:#fbbf24; border-width: 2px;' : '' }}">
            <div class="stat-icon" style="background:#fef2f2; color:#991b1b;"><i class="fa-solid fa-user-xmark"></i></div>
            <div class="stat-val">{{ $stats['tidak_diterima'] }}</div>
            <div class="stat-label">Tidak Diterima</div>
        </a>
    </div>

    {{-- ─── Action Bar ─── --}}
    <div class="premium-card" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem;" >
        <div style="display:flex; align-items:center; gap:.75rem; flex-wrap:wrap;">
            <button class="btn-g bg-primary" onclick="requestPreview('selected')" id="btnHitungPilih">
                <i class="fa-solid fa-bolt"></i> Hitung Terpilih
            </button>
            <button class="btn-g bg-dark" onclick="requestPreview('all')">
                <i class="fa-solid fa-microchip"></i> Hitung Semua
            </button>
            <div style="width:1px; height:28px; background:#e2e8f0; margin:0 .25rem;"></div>
            <button class="btn-g bg-success" onclick="handlePublish('all')" {{ $stats['belum_publish'] == 0 ? 'disabled' : '' }}>
                <i class="fa-solid fa-bullhorn"></i> Publish Semua
            </button>
        </div>

        <form action="{{ route('admin.penempatan.index') }}" method="GET" style="display:flex; gap:.6rem; flex-wrap:wrap; align-items:center;" class="toolbar-flex">
            <div class="search-wrap">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" name="search" class="search-inp" placeholder="Cari nama..." value="{{ $search }}">
            </div>
            <select name="jurusan_id" class="filter-sel" onchange="this.form.submit()">
                <option value="">Semua Jurusan</option>
                @foreach($jurusans as $j)
                    <option value="{{ $j->id }}" {{ $fJurusan == $j->id ? 'selected' : '' }}>{{ $j->nama }}</option>
                @endforeach
            </select>
            <select name="status_proses" class="filter-sel" onchange="this.form.submit()">
                <option value="">Status: Semua</option>
                <option value="Belum Dihitung" {{ $fStatusProses == 'Belum Dihitung' ? 'selected' : '' }}>Belum Dihitung</option>
                <option value="Sudah Dihitung" {{ $fStatusProses == 'Sudah Dihitung' ? 'selected' : '' }}>Sudah Dihitung</option>
                <option value="Perlu Review" {{ $fStatusProses == 'Perlu Review' ? 'selected' : '' }}>Perlu Review</option>
                <option value="Sudah Dipublish" {{ $fStatusProses == 'Sudah Dipublish' ? 'selected' : '' }}>Sudah Dipublish</option>
            </select>
            <button type="submit" class="btn-action" style="width:36px;height:36px;"><i class="fa-solid fa-sliders"></i></button>
        </form>
    </div>

    {{-- ─── Table ─── --}}
    <div class="premium-card" style="padding:0; overflow:hidden;">
        <div class="table-scrolling-container">
            <div style="overflow-x:auto;">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th style="width:40px; text-align:center;"><input type="checkbox" class="form-checkbox" id="selectAll"></th>
                        <th>Nama Siswa</th>
                        <th>Jurusan</th>
                        <th style="text-align:center;">Rapor</th>
                        <th style="text-align:center;">CBT</th>
                        <th>Sertifikat</th>
                        <th style="text-align:center;">Skor Akhir</th>
                        <th>Hasil</th>
                        <th>Proses</th>
                        <th style="text-align:right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendaftarans as $p)
                        @php
                            $hs = $p->hasilSeleksi;
                            $nilaiCBT = $p->user->hasilUjian->skor ?? null;
                            $sp = $hs ? ($hs->status_proses ?: ($hs->is_finalisasi ? 'Sudah Dipublish' : 'Sudah Dihitung')) : 'Belum Dihitung';
                            
                            // Ambil sertifikat untuk ditampilkan
                            $allCerts = $p->berkas->where('jenis_berkas','sertifikat');
                            $validCert = $allCerts->where('status_verifikasi','valid')->first();
                            
                            
                            // Sertifikat untuk ditampilkan di tabel (prioritas valid, lalu pending/lainnya)
                            $displayCert = $validCert ?: $allCerts->first();
                        @endphp
                        <tr>
                            <td style="text-align:center;"><input type="checkbox" class="form-checkbox student-cb" value="{{ $p->id }}"></td>
                            <td>
                                <div style="display:flex; align-items:center; gap:.6rem;">
                                    <div style="width:32px;height:32px;background:#f1f5f9;border-radius:9px;display:flex;align-items:center;justify-content:center;font-weight:900;color:var(--gray);font-size:.7rem;">{{ mb_substr($p->user->name,0,1) }}</div>
                                    <div>
                                        <div style="font-weight:700; color:var(--dark); font-size:.82rem;">{{ $p->user->name }}</div>
                                        <div style="font-size:.62rem; font-weight:600; color:var(--gray);">{{ $p->nisn }}</div>
                                    </div>
                                </div>
                            </td>
                            <td><span style="font-weight:700; color:#475569; font-size:.78rem;">{{ $p->jurusan->nama }}</span></td>
                            <td style="text-align:center; font-weight:800; font-size:.82rem;">{{ number_format($p->nilai_rapor,1) }}</td>
                            <td style="text-align:center; font-weight:800; color:var(--primary); font-size:.82rem;">{{ $nilaiCBT !== null ? number_format($nilaiCBT,1) : '-' }}</td>
                            <td>
                                @if($displayCert)
                                    @if($displayCert->status_verifikasi == 'valid')
                                        <span class="badge-m" style="background:#e0f2fe;color:#0369a1;border:1px solid #7dd3fc;" title="Terverifikasi: {{ $displayCert->tingkat_prestasi }}">{{ $displayCert->tingkat_prestasi }}</span>
                                    @else
                                        <span class="badge-m" style="background:#f1f5f9;color:#94a3b8;border:1px solid #e2e8f0;" title="Menunggu Verifikasi: {{ $displayCert->tingkat_prestasi }}">
                                            <i class="fa-solid fa-clock" style="font-size:.5rem;"></i> {{ $displayCert->tingkat_prestasi }}
                                        </span>
                                    @endif
                                @else <span style="color:#cbd5e1;">-</span> @endif
                            </td>
                            <td style="text-align:center;">
                                @if($hs) <span style="font-weight:900; color:var(--dark); font-size:.9rem;">{{ number_format($hs->skor_akhir,2) }}</span>
                                @else <span style="color:#cbd5e1;">-</span> @endif
                            </td>
                            <td>
                                @if($hs)
                                    @php $hk = $hs->kategori_kelulusan; @endphp
                                    <span class="badge-m {{ $hk=='DITERIMA' ? 'b-diterima' : ($hk=='TIDAK HADIR CBT' ? 'b-cbt' : 'b-tidak') }}">
                                        <i class="fa-solid {{ $hk=='DITERIMA' ? 'fa-circle-check' : 'fa-circle-xmark' }}" style="font-size:.5rem;"></i>
                                        {{ $hk }}
                                    </span>
                                    @if($hs->is_manual_override)
                                        <span class="badge-m b-override" style="margin-left:2px;" title="Diubah oleh {{ $hs->overridden_by }}"><i class="fa-solid fa-pen" style="font-size:.45rem;"></i> Override</span>
                                    @endif
                                @else <span class="badge-m" style="background:#f8fafc;color:#cbd5e1;border:1px solid #e2e8f0;">MENUNGGU</span> @endif
                            </td>
                            <td>
                                <span class="badge-m {{ $sp=='Sudah Dipublish'?'b-publish':($sp=='Perlu Review'?'b-review':($sp=='Sudah Dihitung'?'b-proses':'')) }}" style="{{ $sp=='Belum Dihitung'?'background:#f1f5f9;color:#94a3b8;':'' }}">
                                    <i class="fa-solid {{ $sp=='Sudah Dipublish'?'fa-check-double':($sp=='Perlu Review'?'fa-triangle-exclamation':($sp=='Sudah Dihitung'?'fa-calculator':'fa-hourglass-start')) }}" style="font-size:.5rem;"></i>
                                    {{ $sp }}
                                </span>
                            </td>
                            <td style="text-align:right;">
                                <div style="display:flex; gap:.3rem; justify-content:flex-end;">
                                    <button class="btn-action" title="Detail & Review" onclick="showDetail({{ $p->id }})"><i class="fa-solid fa-magnifying-glass-chart"></i></button>
                                    <button class="btn-action" title="Hitung Ulang" onclick="requestPreviewSingle({{ $p->id }})"><i class="fa-solid fa-rotate-right"></i></button>
                                    @if($hs && !$hs->is_finalisasi && $sp !== 'Perlu Review')
                                        <button class="btn-action" title="Publish" onclick="handlePublishSingle({{ $p->id }})" style="color:var(--success);"><i class="fa-solid fa-bullhorn"></i></button>
                                    @endif
                                    @if($hs && $hs->is_finalisasi)
                                        <a href="{{ route('admin.penempatan.pdf', $p->id) }}" class="btn-action" title="PDF" style="color:#ef4444;"><i class="fa-solid fa-file-pdf"></i></a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" style="padding:4rem 2rem; text-align:center;">
                                <div style="font-size:2.5rem; margin-bottom:1rem; color:#e2e8f0;"><i class="fa-solid fa-search"></i></div>
                                <h3 style="font-weight:900; color:var(--dark); margin:0 0 .4rem;">Tidak Ada Data</h3>
                                <p style="color:var(--gray); margin:0; font-size:.85rem;">Pastikan siswa sudah lolos verifikasi berkas dan CBT.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════ --}}
{{-- ─── PREVIEW MODAL ─── --}}
{{-- ═══════════════════════════════════════════════ --}}
<div class="modal-overlay" id="previewModal">
    <div class="modal-glass">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
            <div style="display:flex; align-items:center; gap:.75rem;">
                <div style="width:44px;height:44px;background:linear-gradient(135deg,#3b82f6,#7c3aed);border-radius:14px;display:flex;align-items:center;justify-content:center;color:white;font-size:1.1rem;"><i class="fa-solid fa-calculator"></i></div>
                <div>
                    <h3 style="margin:0; font-weight:900; color:var(--dark); font-size:1.2rem;">Preview Kalkulasi Seleksi</h3>
                    <p style="margin:0; font-size:.78rem; color:var(--gray); font-weight:600;" id="previewSubtitle">0 siswa akan dihitung</p>
                </div>
            </div>
            <button onclick="closePreview()" style="background:#f1f5f9;border:none;cursor:pointer;width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.2rem;color:var(--gray);transition:all .2s;" onmouseover="this.style.background='#e2e8f0'" onmouseout="this.style.background='#f1f5f9'">×</button>
        </div>

        {{-- Formula Legend --}}
        <div class="formula-box" style="margin-bottom:1.5rem;">
            <div style="font-size:.65rem; font-weight:800; color:#3b82f6; text-transform:uppercase; letter-spacing:.08em; margin-bottom:.3rem;">📐 Formula Seleksi</div>
            <div>Skor Akhir = (({{ $bobotRapor / 100 }} × Rapor) + ({{ $bobotUjian / 100 }} × CBT))</div>
        </div>

        {{-- Preview Table Container --}}
        <div style="overflow-x:auto; margin-bottom:1.5rem; border:1px solid #f1f5f9; border-radius:16px;">
            <table class="preview-table" id="previewTableBody">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama / Jurusan</th>
                        <th style="text-align:center;">Rapor</th>
                        <th style="text-align:center;">CBT</th>
                        <th>Rumus Perhitungan</th>
                        <th style="text-align:center;">Skor Akhir</th>
                        <th>Status</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody id="previewRows">
                    <tr><td colspan="10" style="text-align:center;padding:3rem;color:var(--gray);">
                        <div style="animation:pulse 1.5s infinite;font-size:1.5rem;margin-bottom:.5rem;">⏳</div>
                        Memuat data kalkulasi...
                    </td></tr>
                </tbody>
            </table>
        </div>

        {{-- Action Buttons --}}
        <div style="display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap:wrap;">
            <div style="display:flex; gap:.5rem; flex-wrap:wrap;">
                <button class="btn-g" style="background:#f59e0b;" onclick="markReview()">
                    <i class="fa-solid fa-triangle-exclamation"></i> Tandai Perlu Review
                </button>
            </div>
            <div style="display:flex; gap:.5rem; flex-wrap:wrap;">
                <button class="btn-g" style="background:#f1f5f9;color:#475569;box-shadow:none;" onclick="closePreview()">Tutup</button>
                <button class="btn-g bg-primary" onclick="submitCalculation()">
                    <i class="fa-solid fa-save"></i> Simpan Hasil Hitung
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════ --}}
{{-- ─── DETAIL & REVIEW MODAL ─── --}}
{{-- ═══════════════════════════════════════════════ --}}
<div class="modal-overlay" id="detailModal">
    <div class="modal-glass" style="max-width:850px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
            <div style="display:flex; align-items:center; gap:.75rem;">
                <div style="width:44px;height:44px;background:#f1f5f9;border-radius:14px;display:flex;align-items:center;justify-content:center;color:var(--primary);font-size:1.1rem;"><i class="fa-solid fa-user-graduate"></i></div>
                <div>
                    <h3 style="margin:0; font-weight:900; color:var(--dark);" id="dName">-</h3>
                    <p style="margin:0; font-size:.8rem; color:var(--gray); font-weight:600;" id="dJurusan">-</p>
                </div>
            </div>
            <button onclick="closeDetail()" style="background:#f1f5f9;border:none;cursor:pointer;width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.2rem;color:var(--gray);">×</button>
        </div>

        {{-- Info Banner --}}
        <div id="reviewBadgeBanner" style="display:none; background:#fff7ed; border-radius:16px; padding:.75rem 1.25rem; margin-bottom:1.5rem; display:flex; align-items:center; gap:.75rem; border:1px solid #fed7aa;">
            <i class="fa-solid fa-triangle-exclamation" style="color:#c2410c;"></i>
            <span style="font-size:.78rem; font-weight:700; color:#c2410c;">Siswa ini ditandai PERLU REVIEW. Silakan validasi data atau gunakan override manual.</span>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:1.5rem; margin-bottom:1.5rem;">
            {{-- MODE A: HASIL SISTEM (Readonly) --}}
            <div class="mode-section" id="sectionModeA">
                <div class="mode-header">
                    <span class="mode-title"><i class="fa-solid fa-robot"></i> MODE A — HASIL SISTEM</span>
                    <span class="badge-m" style="background:#f1f5f9; color:var(--gray);">Read Only</span>
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                    <div class="mode-field">
                        <label class="mode-label">Nilai Rapor</label>
                        <input type="text" id="daRapor" class="mode-input" disabled>
                    </div>
                    <div class="mode-field">
                        <label class="mode-label">Nilai CBT</label>
                        <input type="text" id="daCbt" class="mode-input" disabled>
                    </div>
                    <div class="mode-field">
                        <label class="mode-label">Skor Akhir</label>
                        <input type="text" id="daSkor" class="mode-input" disabled style="font-weight:900; color:var(--primary);">
                    </div>
                    <div class="mode-field" style="grid-column: span 2;">
                        <label class="mode-label">Status Kelulusan</label>
                        <input type="text" id="daStatus" class="mode-input" disabled>
                    </div>
                </div>
            </div>

            {{-- MODE B: OVERRIDE MANUAL --}}
            <div class="mode-section" id="sectionModeB">
                <div class="mode-header">
                    <span class="mode-title"><i class="fa-solid fa-user-gear"></i> MODE B — OVERRIDE MANUAL</span>
                    <label class="switch">
                        <input type="checkbox" id="toggleOverride" onchange="toggleManualInputs()">
                        <span class="slider"></span>
                    </label>
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">

                    <div class="mode-field" style="grid-column: span 2;">
                        <label class="mode-label">Status Manual</label>
                        <select id="dbStatus" class="mode-input manual-field">
                            <option value="DITERIMA">DITERIMA</option>
                            <option value="TIDAK DITERIMA">TIDAK DITERIMA</option>
                        </select>
                    </div>
                    <div class="mode-field" style="grid-column: span 2;">
                        <label class="mode-label">Catatan Admin <span style="color:var(--danger);">*</span></label>
                        <textarea id="dbCatatan" class="mode-input manual-field" rows="3" style="resize:none;" placeholder="Alasan perubahan data..."></textarea>
                    </div>
                </div>
                <div id="overrideLogBox" style="margin-top:1rem; padding-top:1rem; border-top:1px dashed #e2e8f0; font-size:.65rem; color:var(--gray); display:none;">
                    <i class="fa-solid fa-clock-rotate-left"></i> Terakhir diubah: <span id="overrideLogInfo">-</span>
                </div>
            </div>
        </div>

        <div style="display:flex; justify-content:space-between; align-items:center;">
            <div id="dStatusProsesBadge"></div>
            <div style="display:flex; gap:.6rem;">
                <button class="btn-g" style="background:#f1f5f9;color:#475569;box-shadow:none;" onclick="closeDetail()">Tutup</button>
                <button class="btn-g bg-primary" id="btnSaveFinal" onclick="saveFinalDecision()"><i class="fa-solid fa-check-double"></i> Simpan Keputusan Final</button>
            </div>
        </div>
    </div>
</div>

<script>
const CSRF = '{{ csrf_token() }}';
let previewData = [];
let previewMode = 'all';
let previewIds = [];
let currentDetailId = null;

// ─── Checkbox Logic ───
document.getElementById('selectAll').addEventListener('change', function() {
    document.querySelectorAll('.student-cb').forEach(cb => cb.checked = this.checked);
});

function getSelectedIds() {
    return Array.from(document.querySelectorAll('.student-cb:checked')).map(cb => cb.value);
}

// ─── Preview Request ───
function requestPreview(mode) {
    const ids = getSelectedIds();
    previewMode = mode;
    previewIds = ids;

    if (mode === 'selected' && ids.length === 0) {
        toast('Pilih minimal satu siswa terlebih dahulu.', 'warning');
        return;
    }

    document.getElementById('previewModal').style.display = 'flex';
    document.getElementById('previewRows').innerHTML = `<tr><td colspan="10" style="text-align:center;padding:3rem;color:var(--gray);"><div style="animation:pulse 1.5s infinite;font-size:1.5rem;margin-bottom:.5rem;">⏳</div>Memuat data kalkulasi...</td></tr>`;

    fetch('{{ route("admin.penempatan.preview") }}', {
        method: 'POST',
        headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ mode, selected_ids: ids })
    })
    .then(r => r.json())
    .then(res => {
        if (res.error) { toast(res.error, 'error'); closePreview(); return; }
        previewData = res.data;
        renderPreview(res.data);
    })
    .catch(err => { toast('Gagal memuat preview: ' + err.message, 'error'); closePreview(); });
}

function requestPreviewSingle(id) {
    previewMode = 'selected';
    previewIds = [String(id)];
    document.getElementById('previewModal').style.display = 'flex';
    document.getElementById('previewRows').innerHTML = `<tr><td colspan="10" style="text-align:center;padding:2rem;color:var(--gray);"><div style="animation:pulse 1.5s infinite;">⏳</div></td></tr>`;

    fetch('{{ route("admin.penempatan.preview") }}', {
        method: 'POST',
        headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ mode: 'selected', selected_ids: [id] })
    })
    .then(r => r.json())
    .then(res => {
        if (res.error) { toast(res.error, 'error'); closePreview(); return; }
        previewData = res.data;
        renderPreview(res.data);
    })
    .catch(err => { toast('Gagal memuat preview.', 'error'); closePreview(); });
}

function renderPreview(data) {
    document.getElementById('previewSubtitle').innerText = `${data.length} siswa akan dihitung`;
    let html = '';
    data.forEach((s, i) => {
        const katBadge = s.kategori === 'DITERIMA'
            ? `<span class="badge-m b-diterima"><i class="fa-solid fa-circle-check" style="font-size:.45rem;"></i> ${s.kategori}</span>`
            : s.kategori === 'TIDAK HADIR CBT'
                ? `<span class="badge-m b-cbt"><i class="fa-solid fa-circle-xmark" style="font-size:.45rem;"></i> ${s.kategori}</span>`
                : `<span class="badge-m b-tidak"><i class="fa-solid fa-circle-xmark" style="font-size:.45rem;"></i> ${s.kategori}</span>`;



        const publishWarn = s.sudah_publish ? '<span class="badge-m b-review" style="margin-left:3px;" title="Sudah dipublish sebelumnya">⚠ Published</span>' : '';

        html += `<tr>
            <td style="font-weight:800;color:var(--gray);text-align:center;">${i + 1}</td>
            <td>
                <div style="font-weight:700;color:var(--dark);font-size:.82rem;">${s.nama} ${publishWarn}</div>
                <div style="font-size:.62rem;color:var(--gray);font-weight:600;">${s.jurusan} · ${s.nisn}</div>
            </td>
            <td style="text-align:center;font-weight:800;">${s.rapor}</td>
            <td style="text-align:center;font-weight:800;color:${s.has_cbt ? 'var(--primary)' : '#ef4444'};">${s.cbt !== null ? s.cbt : '<span style="font-size:.65rem;">✕ N/A</span>'}</td>
            <td>
                <div class="formula-box" style="padding:.5rem .8rem;margin:0;font-size:.7rem;border-radius:10px;">${s.formula}</div>
            </td>
            <td style="text-align:center;font-weight:900;font-size:1rem;color:var(--dark);">${s.skor_akhir}</td>
            <td>${katBadge}</td>
            <td>
                <input type="text" class="catatan-input" data-id="${s.pendaftaran_id}" placeholder="Tambah catatan..." style="width:100%;padding:.35rem .5rem;border:1px solid #e2e8f0;border-radius:8px;font-size:.7rem;outline:none;">
            </td>
        </tr>`;
    });
    document.getElementById('previewRows').innerHTML = html;
}

function closePreview() {
    document.getElementById('previewModal').style.display = 'none';
    previewData = [];
}

// ─── Save Calculation ───
function submitCalculation() {
    if (previewData.length === 0) return;

    // Collect any catatan inputs
    const overrides = {};
    document.querySelectorAll('.catatan-input').forEach(inp => {
        const id = inp.dataset.id;
        if (inp.value.trim()) {
            overrides[id] = { catatan: inp.value.trim() };
        }
    });

    Swal.fire({
        title: `Simpan ${previewData.length} Hasil?`,
        text: 'Hasil perhitungan akan disimpan. Admin masih bisa review sebelum publish ke siswa.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Simpan Sekarang',
        confirmButtonColor: '#3b82f6',
        cancelButtonText: 'Batal'
    }).then(result => {
        if (!result.isConfirmed) return;

        Swal.fire({ title:'Menyimpan...', allowOutsideClick:false, didOpen:()=>Swal.showLoading() });

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.penempatan.hitung") }}';
        form.innerHTML = `<input type="hidden" name="_token" value="${CSRF}">
            <input type="hidden" name="mode" value="${previewMode}">`;

        previewIds.forEach(id => {
            const inp = document.createElement('input');
            inp.type = 'hidden'; inp.name = 'selected_ids[]'; inp.value = id;
            form.appendChild(inp);
        });

        // Append overrides
        Object.keys(overrides).forEach(pid => {
            Object.keys(overrides[pid]).forEach(key => {
                const inp = document.createElement('input');
                inp.type = 'hidden';
                inp.name = `overrides[${pid}][${key}]`;
                inp.value = overrides[pid][key];
                form.appendChild(inp);
            });
        });

        document.body.appendChild(form);
        form.submit();
    });
}

// ─── Mark Review ───
function markReview() {
    if (previewData.length === 0) return;

    Swal.fire({
        title: 'Tandai Perlu Review?',
        text: `${previewData.length} siswa akan ditandai "Perlu Review". Data tidak akan bisa dipublish sampai divalidasi admin.`,
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Ya, Tandai',
        confirmButtonColor: '#f59e0b'
    }).then(result => {
        if (!result.isConfirmed) return;

        Swal.fire({ title:'Menyimpan...', allowOutsideClick:false, didOpen:()=>Swal.showLoading() });

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.penempatan.hitung") }}';
        form.innerHTML = `<input type="hidden" name="_token" value="${CSRF}">
            <input type="hidden" name="mode" value="${previewMode}">
            <input type="hidden" name="mark_review" value="1">`;

        previewIds.forEach(id => {
            let inp = document.createElement('input');
            inp.type='hidden'; inp.name='selected_ids[]'; inp.value=id;
            form.appendChild(inp);
            inp = document.createElement('input');
            inp.type='hidden'; inp.name='review_ids[]'; inp.value=id;
            form.appendChild(inp);
        });

        document.body.appendChild(form);
        form.submit();
    });
}

// ─── Publish ───
function handlePublish(mode) {
    Swal.fire({
        title: 'Publish Semua Hasil?',
        text: 'Setelah dipublish, hasil akan langsung terlihat oleh siswa.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Publish!',
        confirmButtonColor: '#10b981'
    }).then(result => {
        if (!result.isConfirmed) return;
        Swal.fire({ title:'Memproses...', allowOutsideClick:false, didOpen:()=>Swal.showLoading() });
        submitPublishForm(mode, []);
    });
}

function handlePublishSingle(id) {
    Swal.fire({
        title: 'Publish Hasil Siswa Ini?',
        text: 'Hasil akan segera tampil di dashboard siswa.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Publish',
        confirmButtonColor: '#10b981'
    }).then(result => {
        if (!result.isConfirmed) return;
        Swal.fire({ title:'Memproses...', allowOutsideClick:false, didOpen:()=>Swal.showLoading() });
        submitPublishForm('selected', [id]);
    });
}

function submitPublishForm(mode, ids) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("admin.penempatan.publish") }}';
    form.innerHTML = `<input type="hidden" name="_token" value="${CSRF}"><input type="hidden" name="mode" value="${mode}">`;
    ids.forEach(id => {
        const inp = document.createElement('input');
        inp.type='hidden'; inp.name='selected_ids[]'; inp.value=id;
        form.appendChild(inp);
    });
    document.body.appendChild(form);
    form.submit();
}

// ─── Detail & Review Modal ───
function showDetail(id) {
    currentDetailId = id;
    fetch(`/admin/penempatan/detail/${id}`)
        .then(r => r.json())
        .then(d => {
            document.getElementById('dName').innerText = d.nama;
            document.getElementById('dJurusan').innerText = `${d.jurusan} · ${d.nisn}`;
            
            const h = d.hasil;
            if (!h) {
                toast('Hitung seleksi terlebih dahulu sebelum review.', 'info');
                return;
            }

            // Mode A (Sistem)
            document.getElementById('daRapor').value = d.rapor;
            document.getElementById('daCbt').value = d.cbt ?? 'Tidak Ikut';
            document.getElementById('daSkor').value = h.skor_sistem;
            document.getElementById('daStatus').value = h.kategori_sistem ?? 'BELUM DIHITUNG';

            // Mode B (Manual)
            document.getElementById('toggleOverride').checked = h.is_override;

            document.getElementById('dbStatus').value = h.kategori || 'TIDAK DITERIMA';
            document.getElementById('dbCatatan').value = h.catatan || '';

            // Banner & Badge logic
            document.getElementById('reviewBadgeBanner').style.display = h.status_proses === 'Perlu Review' ? 'flex' : 'none';
            
            const sp = h.status_proses || (h.is_publish ? 'Sudah Dipublish' : 'Sudah Dihitung');
            document.getElementById('dStatusProsesBadge').innerHTML = `<span class="badge-m ${sp==='Sudah Dipublish'?'b-publish':(sp==='Perlu Review'?'b-review':'b-proses')}">${sp}</span>`;

            if (h.is_override) {
                document.getElementById('overrideLogBox').style.display = 'block';
                document.getElementById('overrideLogInfo').innerText = `${h.override_by} pada ${h.override_at}`;
            } else {
                document.getElementById('overrideLogBox').style.display = 'none';
            }

            toggleManualInputs();
            document.getElementById('detailModal').style.display = 'flex';
        });
}

function toggleManualInputs() {
    const isOverride = document.getElementById('toggleOverride').checked;
    const fields = document.querySelectorAll('.manual-field');
    const secA = document.getElementById('sectionModeA');
    const secB = document.getElementById('sectionModeB');

    fields.forEach(f => f.disabled = !isOverride);
    
    if(isOverride) {
        secB.classList.add('active');
        secA.classList.remove('active');
    } else {
        secA.classList.add('active');
        secB.classList.remove('active');
    }
}

function saveFinalDecision() {
    if (!currentDetailId) return;

    const isOverride = document.getElementById('toggleOverride').checked;
    const data = {
        is_manual_override: isOverride,

        kategori_manual: document.getElementById('dbStatus').value,
        catatan: document.getElementById('dbCatatan').value
    };

    if (isOverride && !data.catatan.trim()) {
        toast('Catatan admin wajib diisi untuk override manual.', 'error');
        return;
    }

    Swal.fire({ title:'Menyimpan Keputusan...', allowOutsideClick:false, didOpen:()=>Swal.showLoading() });

    fetch(`/admin/penempatan/update/${currentDetailId}`, {
        method: 'POST',
        headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify(data)
    })
    .then(r => r.json())
    .then(res => {
        if(res.success) {
            Swal.fire({ icon:'success', title:'Tersimpan', text:res.success }).then(() => location.reload());
        } else {
            toast(res.error || 'Terjadi kesalahan.', 'error');
        }
    });
}

function closeDetail() { document.getElementById('detailModal').style.display = 'none'; }

// ─── Toast Helper ───
function toast(msg, icon = 'success') {
    Swal.fire({ icon, title: icon === 'success' ? 'Berhasil!' : icon === 'error' ? 'Gagal!' : 'Perhatian!', text: msg, toast: true, position: 'top-end', showConfirmButton: false, timer: 3500, timerProgressBar: true });
}

// ─── Session Toasts ───
@if(session('success'))
    toast("{{ session('success') }}", 'success');
@endif
@if(session('error'))
    toast("{{ session('error') }}", 'error');
@endif

// ─── Close modals on ESC ───
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') { closePreview(); closeDetail(); }
});
</script>
@endsection
