@extends('layouts.siswa')
@section('title', 'Pendaftaran')

@section('content')
@php
    $berkasWajibIds = ['skl', 'rapor', 'pasfoto'];
    $sudahUploadSemua = collect($berkasWajibIds)->every(fn($id) => !empty($berkasAktif[$id]));
    
    $isRevisi = $pendaftaran && $pendaftaran->status == 'revisi';
    $isLolos = $pendaftaran && in_array($pendaftaran->status, ['lolos_admin', 'sudah_ujian', 'diterima', 'tidak_diterima']);
    $isMenunggu = $pendaftaran && $pendaftaran->status == 'menunggu_verifikasi';

    // Status Banner Mapping
    $banner = [
        'revisi' => [
            'color' => '#f59e0b',
            'bg' => 'rgba(251, 191, 36, 0.08)',
            'border' => 'rgba(251, 191, 36, 0.2)',
            'icon' => 'fa-triangle-exclamation',
            'title' => 'Perlu Revisi Dokumen',
            'desc' => 'Beberapa dokumen Anda perlu diperbaiki sesuai catatan dari admin. Silakan upload ulang dokumen secepatnya.',
            'btn' => 'Lihat Detail Revisi'
        ],
        'lolos_admin' => [
            'color' => '#10b981',
            'bg' => 'rgba(16, 185, 129, 0.08)',
            'border' => 'rgba(16, 185, 129, 0.2)',
            'icon' => 'fa-circle-check',
            'title' => 'Status Pendaftaran: LOLOS ADMIN',
            'desc' => 'Selamat! Berkas Anda telah diverifikasi dan dinyatakan lengkap.',
            'btn' => 'Lihat Detail Status'
        ],
        'menunggu_verifikasi' => [
            'color' => '#3b82f6',
            'bg' => 'rgba(59, 130, 246, 0.08)',
            'border' => 'rgba(59, 130, 246, 0.2)',
            'icon' => 'fa-clock-rotate-left',
            'title' => 'Menunggu Verifikasi',
            'desc' => 'Berkas Anda sedang dalam antrean verifikasi oleh panitia. Mohon cek berkala.',
            'btn' => 'Refresh Status'
        ],
        'default' => [
            'color' => '#64748b',
            'bg' => 'rgba(100, 116, 139, 0.08)',
            'border' => 'rgba(100, 116, 139, 0.2)',
            'icon' => 'fa-circle-info',
            'title' => 'Status Pendaftaran',
            'desc' => 'Lengkapi data dan dokumen pendaftaran Anda untuk melanjutkan proses.',
            'btn' => 'Panduan Pendaftaran'
        ]
    ];

    $sKey = $pendaftaran->status ?? 'default';
    if (!isset($banner[$sKey])) $sKey = 'default';
    if ($isLolos && $sKey != 'revisi') $sKey = 'lolos_admin';
    $curBanner = $banner[$sKey];

    // Timeline Data
    $steps = [
        ['label' => 'Registrasi Akun', 'sub' => 'Akun berhasil dibuat', 'icon' => 'fa-user-check', 'date' => Auth::user()->created_at],
        ['label' => 'Isi Formulir', 'sub' => 'Data berhasil disubmit', 'icon' => 'fa-file-lines', 'date' => $pendaftaran?->created_at],
        ['label' => 'Upload Berkas', 'sub' => 'Berkas berhasil diupload', 'icon' => 'fa-cloud-arrow-up', 'date' => $sudahUploadSemua ? ($pendaftaran?->updated_at ?? $pendaftaran?->created_at) : null],
        ['label' => 'Verifikasi Admin', 'sub' => $isRevisi ? 'Berkas perlu revisi' : ($isLolos ? 'Berkas telah diverifikasi' : 'Menunggu verifikasi'), 'icon' => $isRevisi ? 'fa-triangle-exclamation' : ($isLolos ? 'fa-shield-check' : 'fa-hourglass-half'), 'date' => ($isLolos || $isRevisi) ? $pendaftaran?->updated_at : null],
        ['label' => 'CBT Online', 'sub' => 'Menunggu jadwal ujian', 'icon' => 'fa-laptop-code', 'date' => null],
        ['label' => 'Hasil Seleksi', 'sub' => 'Menunggu hasil seleksi', 'icon' => 'fa-award', 'date' => null],
    ];

    // Determine current index for timeline
    $activeIndex = 0;
    if ($pendaftaran) $activeIndex = 1;
    if ($sudahUploadSemua) $activeIndex = 2;
    if ($isMenunggu) $activeIndex = 3;
    if ($isRevisi) $activeIndex = 3; 
    if ($isLolos) $activeIndex = 4;
    // ... further steps based on examination status if needed
@endphp

<style>
    /* ─── Layout & Page Styles ─── */
    .pendaftaran-container {
        max-width: 1080px;
        margin: 0 auto;
        animation: fadeIn .6s ease-out;
    }
    
    .page-header {
        margin-bottom: 1.5rem;
    }
    .page-title {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--dark);
        letter-spacing: -0.02em;
        margin-bottom: 0.25rem;
    }
    .page-subtitle {
        color: var(--gray-text);
        font-weight: 500;
        font-size: 0.875rem;
    }

    /* ─── Glass Cards ─── */
    .premium-card {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.4);
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02);
        padding: 1.5rem;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    /* ─── Status Banner ─── */
    .status-banner {
        display: flex;
        align-items: center;
        gap: 1.25rem;
        padding: 1rem 1.5rem;
        border-radius: 20px;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
        border: 1px solid {{ $curBanner['border'] }};
        background: {{ $curBanner['bg'] }};
    }
    .status-banner-icon {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: white;
        color: {{ $curBanner['color'] }};
        font-size: 1.25rem;
        box-shadow: 0 5px 15px rgba(0,0,0,0.03);
        flex-shrink: 0;
    }
    .status-banner-content { flex: 1; }
    .status-banner-title {
        font-size: 1.05rem;
        font-weight: 800;
        color: {{ $curBanner['color'] }};
        margin-bottom: 0.15rem;
    }
    .status-banner-desc {
        font-size: 0.85rem;
        color: #4b5563;
        font-weight: 500;
        line-height: 1.4;
    }
    .status-banner-btn {
        background: white;
        color: {{ $curBanner['color'] }};
        border: 1px solid {{ $curBanner['border'] }};
        padding: 0.5rem 1rem;
        border-radius: 10px;
        font-weight: 700;
        font-size: 0.8rem;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 0.4rem;
        white-space: nowrap;
    }
    .status-banner-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }

    /* ─── Grid Dashboard ─── */
    .pendaftaran-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
        margin-bottom: 2.5rem;
    }

    .card-title-area {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 2rem;
    }
    .card-title-main {
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    .card-icon-wrap {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        background: #f1f5f9;
        color: var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }
    .card-title-text h3 {
        margin: 0;
        font-size: 1.15rem;
        font-weight: 800;
        color: var(--dark);
    }
    .card-title-text p {
        margin: 0;
        font-size: 0.85rem;
        color: var(--gray-text);
        font-weight: 500;
    }
    .read-only-badge {
        background: #f1f5f9;
        color: #64748b;
        padding: 0.4rem 0.8rem;
        border-radius: 8px;
        font-size: 0.7rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 0.4rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    /* ─── Ringkasan List ─── */
    .data-list {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
    .data-item {
        display: grid;
        grid-template-columns: 32px 180px 1fr;
        align-items: center;
        padding: 0.75rem 1rem;
        border-radius: 12px;
        transition: background 0.2s;
    }
    .data-item:hover { background: rgba(248, 250, 252, 0.8); }
    .data-icon { color: #94a3b8; font-size: 0.95rem; }
    .data-label { font-size: 0.875rem; color: #64748b; font-weight: 600; }
    .data-value { font-size: 0.925rem; color: #0f172a; font-weight: 700; text-align: right; }

    /* ─── Document List ─── */
    .doc-list { display: flex; flex-direction: column; gap: 1rem; }
    .doc-item {
        background: white;
        border: 1px solid #f1f5f9;
        border-radius: 16px;
        padding: 1rem;
        transition: all 0.3s;
    }
    .doc-item:hover { border-color: var(--primary-light); transform: translateX(5px); }
    .doc-header {
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    .doc-icon-box {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: #f8fafc;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }
    .doc-info { flex: 1; }
    .doc-name { font-size: 0.925rem; font-weight: 700; color: var(--dark); margin: 0; }
    .doc-meta { font-size: 0.75rem; color: var(--gray-text); font-weight: 600; margin-top: 0.15rem; }
    .doc-status-badge {
        padding: 0.35rem 0.75rem;
        border-radius: 8px;
        font-size: 0.7rem;
        font-weight: 800;
        letter-spacing: 0.05em;
    }
    .status-ok { background: #dcfce7; color: #166534; }
    .status-rev { background: #fffbeb; color: #92400e; }
    
    .doc-actions { display: flex; gap: 0.5rem; }
    .btn-action {
        padding: 0.4rem 0.8rem;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s;
        border: 1px solid #e2e8f0;
        background: white;
        color: #64748b;
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }
    .btn-action:hover { background: #f8fafc; border-color: #cbd5e1; }
    .btn-rev-up { color: var(--primary) !important; border-color: var(--primary-light) !important; }
    .btn-rev-up:hover { background: rgba(59, 130, 246, 0.05) !important; }

    .admin-note {
        margin-top: 0.75rem;
        padding: 0.65rem 0.85rem;
        background: #fff5f5;
        border-left: 3px solid #ef4444;
        border-radius: 4px 10px 10px 4px;
        font-size: 0.8rem;
        color: #b91c1c;
        display: flex;
        gap: 0.5rem;
        font-weight: 500;
    }

    /* ─── Timeline ─── */
    .timeline-card { margin-bottom: 0.75rem; height: auto !important; }
    .timeline-container {
        display: flex;
        justify-content: space-between;
        position: relative;
        padding: 1.5rem 0;
        margin: 0 1.5rem;
    }
    .timeline-container::before {
        content: '';
        position: absolute;
        top: calc(1.5rem + 18px); /* Padding-top + Half of Dot size (36px) */
        left: 0;
        right: 0;
        height: 2px;
        background: #e2e8f0;
        z-index: 1;
    }
    .timeline-progress {
        position: absolute;
        top: calc(1.5rem + 18px);
        left: 0;
        height: 2px;
        background: {{ $isRevisi ? '#f59e0b' : 'var(--primary)' }};
        z-index: 2;
        width: {{ ($activeIndex / (count($steps)-1)) * 100 }}%;
        transition: width 1s ease-in-out;
    }
    .timeline-item {
        position: relative;
        z-index: 3;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        width: 110px;
    }
    .timeline-dot {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: white;
        border: 2px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.95rem;
        color: #cbd5e1;
        transition: all 0.3s;
        box-shadow: 0 0 0 3px white;
    }
    .timeline-item.completed .timeline-dot {
        background: #dcfce7;
        border-color: #22c55e;
        color: #22c55e;
    }
    .timeline-item.active .timeline-dot {
        background: var(--primary);
        border-color: var(--primary);
        color: white;
        box-shadow: 0 0 0 5px rgba(59, 130, 246, 0.1);
    }
    .timeline-item.warning .timeline-dot {
        background: #fffbeb;
        border-color: #f59e0b;
        color: #f59e0b;
        box-shadow: 0 0 0 5px rgba(245, 158, 11, 0.1);
    }
    .timeline-content {
        margin-top: 0.75rem;
    }
    .timeline-title { font-size: 0.8rem; font-weight: 800; color: var(--dark); margin: 0; }
    .timeline-sub { font-size: 0.675rem; color: var(--gray-text); font-weight: 600; margin-top: 0.15rem; }
    .timeline-date {
        margin-top: 0.5rem;
        font-size: 0.65rem;
        font-weight: 700;
        color: {{ $isRevisi ? '#f59e0b' : '#22c55e' }};
        background: {{ $isRevisi ? '#fffbeb' : '#f0fdf4' }};
        padding: 0.15rem 0.4rem;
        border-radius: 5px;
    }

    /* ─── Bottom Footer Info ─── */
    .footer-info {
        margin-top: 0;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 0.85rem 1.25rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .footer-text {
        font-size: 0.8rem;
        font-weight: 600;
        color: #64748b;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .footer-deadline {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 0.8rem;
        font-weight: 700;
        color: #b91c1c;
    }

    @media (max-width: 992px) {
        .pendaftaran-grid { grid-template-columns: 1fr; }
        .timeline-container { overflow-x: auto; padding: 2rem 1rem; justify-content: flex-start; gap: 3rem; }
        .timeline-container::before, .timeline-progress { display: none; }
    }
</style>

<div class="pendaftaran-container">
    <div class="page-header">
        <h1 class="page-title">Data & Dokumen Pendaftaran</h1>
        <p class="page-subtitle">Berikut adalah informasi pendaftaran Anda</p>
    </div>

    {{-- ── Banner Status ── --}}
    <div class="status-banner animate-slide-up">
        <div class="status-banner-icon">
            <i class="fa-solid {{ $curBanner['icon'] }}"></i>
        </div>
        <div class="status-banner-content">
            <h2 class="status-banner-title">{{ $curBanner['title'] }}</h2>
            <p class="status-banner-desc">{{ $curBanner['desc'] }}</p>
        </div>
        <button class="status-banner-btn">
            <i class="fa-solid fa-eye"></i>
            {{ $curBanner['btn'] }}
        </button>
    </div>

    <div class="pendaftaran-grid animate-slide-up" style="animation-delay: 0.1s;">
        {{-- ── Left: Ringkasan Data ── --}}
        <section class="premium-card">
            <div class="card-title-area">
                <div class="card-title-main">
                    <div class="card-icon-wrap"><i class="fa-solid fa-address-card"></i></div>
                    <div class="card-title-text">
                        <h3>Ringkasan Data Pendaftaran</h3>
                        <p>Data yang telah Anda isi saat pendaftaran</p>
                    </div>
                </div>
                <div class="read-only-badge"><i class="fa-solid fa-lock"></i> Read Only</div>
            </div>

            <div class="data-list">
                <div class="data-item">
                    <div class="data-icon"><i class="fa-solid fa-user"></i></div>
                    <div class="data-label">Nama Lengkap</div>
                    <div class="data-value">{{ Auth::user()->name }}</div>
                </div>
                <div class="data-item">
                    <div class="data-icon"><i class="fa-solid fa-calendar-days"></i></div>
                    <div class="data-label">Tempat, Tanggal Lahir</div>
                    <div class="data-value">
                        {{ $pendaftaran->tempat_lahir ?? '-' }}, 
                        {{ $pendaftaran->tanggal_lahir ? \Carbon\Carbon::parse($pendaftaran->tanggal_lahir)->translatedFormat('d M Y') : '-' }}
                    </div>
                </div>
                <div class="data-item">
                    <div class="data-icon"><i class="fa-solid fa-fingerprint"></i></div>
                    <div class="data-label">NISN</div>
                    <div class="data-value">{{ $pendaftaran->nisn ?? '-' }}</div>
                </div>
                <div class="data-item">
                    <div class="data-icon"><i class="fa-solid fa-school"></i></div>
                    <div class="data-label">Asal Sekolah</div>
                    <div class="data-value">{{ $pendaftaran->asal_sekolah ?? '-' }}</div>
                </div>
                <div class="data-item">
                    <div class="data-icon"><i class="fa-solid fa-graduation-cap"></i></div>
                    <div class="data-label">Jurusan Pilihan</div>
                    <div class="data-value">{{ $pendaftaran->jurusan->nama ?? '-' }}</div>
                </div>
                <div class="data-item">
                    <div class="data-icon"><i class="fa-solid fa-location-dot"></i></div>
                    <div class="data-label">Alamat Rumah</div>
                    <div class="data-value">{{ $pendaftaran->alamat ?? '-' }}</div>
                </div>
                <div class="data-item">
                    <div class="data-icon"><i class="fa-solid fa-phone"></i></div>
                    <div class="data-label">Nomor HP / WhatsApp</div>
                    <div class="data-value">{{ $pendaftaran->no_hp ?? '-' }}</div>
                </div>
                <div class="data-item">
                    <div class="data-icon"><i class="fa-solid fa-chart-line"></i></div>
                    <div class="data-label">Rata-rata Nilai Rapor</div>
                    <div class="data-value">{{ $pendaftaran->nilai_rapor ?? '-' }}</div>
                </div>
            </div>
        </section>

        {{-- ── Right: Dokumen Terupload ── --}}
        <section class="premium-card">
            <div class="card-title-area">
                <div class="card-title-main">
                    <div class="card-icon-wrap"><i class="fa-solid fa-folder-open"></i></div>
                    <div class="card-title-text">
                        <h3>Dokumen Terupload</h3>
                        <p>Berikut adalah berkas yang telah Anda upload</p>
                    </div>
                </div>
            </div>

            <div class="doc-list">
                @foreach($berkasAktif as $key => $berkas)
                @php
                    $isDocRevisi = $berkas->status_verifikasi == 'tidak_valid';
                    $isDocValid = $berkas->status_verifikasi == 'valid';
                @endphp
                <div class="doc-item">
                    <div class="doc-header">
                        <div class="doc-icon-box">
                            @if(strtolower($berkas->file_type) == 'pdf')
                                <i class="fa-solid fa-file-pdf" style="color: #ef4444;"></i>
                            @else
                                <i class="fa-solid fa-file-image" style="color: #3b82f6;"></i>
                            @endif
                        </div>
                        <div class="doc-info">
                            <h4 class="doc-name">{{ $berkas->nama_file }}</h4>
                            <p class="doc-meta">
                                {{ strtoupper($berkas->file_type) }} • 
                                {{ str_replace('_', ' ', $berkas->jenis_berkas) }}
                            </p>
                        </div>
                        <div style="display:flex; flex-direction:column; align-items:flex-end; gap:.5rem;">
                            @if($isDocValid)
                                <span class="doc-status-badge status-ok">TERVERIFIKASI</span>
                            @elseif($isDocRevisi)
                                <span class="doc-status-badge status-rev">PERLU REVISI</span>
                            @else
                                <span class="doc-status-badge" style="background:#f1f5f9;color:#64748b;">PENDING</span>
                            @endif

                            <div class="doc-actions">
                                @if($isDocRevisi && $isRevisi)
                                    <button class="btn-action btn-rev-up" onclick="toggleUploadForm('{{ $berkas->jenis_berkas }}')">
                                        <i class="fa-solid fa-upload"></i> Upload Ulang
                                    </button>
                                @endif
                                <button class="btn-action" onclick="openLightbox('{{ asset('storage/'.$berkas->file_path) }}', '{{ strtolower($berkas->file_type) == 'pdf' ? 'pdf' : 'img' }}')">
                                    <i class="fa-solid fa-eye"></i> Lihat
                                </button>
                            </div>
                        </div>
                    </div>

                    @if($isDocRevisi)
                    <div class="admin-note">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <div>
                            <strong>Catatan Admin:</strong> 
                            <span>{{ $berkas->catatan_admin ?? 'Berkas kurang jelas, mohon upload ulang.' }}</span>
                        </div>
                    </div>
                    @endif

                    {{-- Hidden Upload Form for individual revisions --}}
                    @if($isDocRevisi && $isRevisi)
                    <div id="upload-form-{{ $berkas->jenis_berkas }}" style="display:none; margin-top:1.5rem; padding:1.25rem; background:#f8fafc; border:1px dashed var(--primary-light); border-radius:12px;">
                        <form action="{{ route('siswa.pendaftaran.reupload') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="berkas_id_lama" value="{{ $berkas->id }}">
                            <input type="hidden" name="jenis_berkas" value="{{ $berkas->jenis_berkas }}">
                            <label style="display:block; font-size:.8rem; font-weight:700; color:#475569; margin-bottom:.75rem;">Pilih File Perbaikan (Maks 2MB):</label>
                            <input type="file" name="file_reupload" class="form-control" style="font-size:.85rem; padding:.5rem;" required>
                            <div style="display:flex; gap:.5rem; margin-top:1rem;">
                                <button type="submit" class="btn-primary" style="padding:.5rem 1rem; font-size:.8rem; flex:1;">🚀 Upload Sekarang</button>
                                <button type="button" class="btn-outline" style="padding:.5rem 1rem; font-size:.8rem;" onclick="toggleUploadForm('{{ $berkas->jenis_berkas }}')">Batal</button>
                            </div>
                        </form>
                    </div>
                    @endif
                </div>
                @endforeach

                @if($isRevisi)
                <div style="background:#fffbeb; border:1px solid #fef3c7; border-radius:12px; padding:1rem; display:flex; align-items:center; gap:0.75rem;">
                    <i class="fa-solid fa-circle-info" style="color:#d97706;"></i>
                    <p style="margin:0; font-size:0.8rem; color:#92400e; font-weight:700;">Dokumen yang bertanda "Perlu Revisi" harus diupload ulang.</p>
                </div>
                @elseif($isLolos)
                 <div style="background:#f0fdf4; border:1px solid #dcfce7; border-radius:12px; padding:1rem; display:flex; align-items:center; gap:0.75rem;">
                    <i class="fa-solid fa-circle-check" style="color:#16a34a;"></i>
                    <p style="margin:0; font-size:0.8rem; color:#166534; font-weight:700;">Semua dokumen telah diverifikasi dan dinyatakan lengkap.</p>
                </div>
                @endif
            </div>
        </section>
    </div>

    {{-- ── Timeline Pendaftaran ── --}}
    <section class="premium-card timeline-card animate-slide-up" style="animation-delay: 0.2s;">
        <div class="card-title-area" style="margin-bottom: 0;">
            <div class="card-title-main">
                <div class="card-title-text">
                    <h3>Alur Pendaftaran</h3>
                    <p>Tahapan pendaftaran yang telah Anda lalui</p>
                </div>
            </div>
        </div>

        <div class="timeline-container">
            <div class="timeline-progress"></div>
            @foreach($steps as $idx => $step)
                @php
                    $isCompleted = ($idx < $activeIndex);
                    $isCurrent = ($idx == $activeIndex);
                    $isWarn = $isRevisi && $isCurrent;
                    
                    $class = '';
                    if ($isCompleted) $class = 'completed';
                    if ($isCurrent) $class = 'active';
                    if ($isWarn) $class = 'warning';
                @endphp
                <div class="timeline-item {{ $class }}">
                    <div class="timeline-dot">
                        @if($isCompleted)
                            <i class="fa-solid fa-check"></i>
                        @else
                            <i class="fa-solid {{ $step['icon'] }}"></i>
                        @endif
                    </div>
                    <div class="timeline-content">
                        <h4 class="timeline-title">{{ $step['label'] }}</h4>
                        <p class="timeline-sub">{{ $step['sub'] }}</p>
                        @if($step['date'])
                        <div class="timeline-date">
                            {{ \Carbon\Carbon::parse($step['date'])->translatedFormat('d M Y') }}
                            <br>
                            {{ \Carbon\Carbon::parse($step['date'])->format('H:i') }} WIB
                        </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        {{-- ── Footer Alert (Moved Inside Card) ── --}}
        <div class="footer-info" style="margin-top: 2.5rem; animation: none;">
            <div class="footer-text">
                @if($isRevisi)
                    <i class="fa-solid fa-circle-info" style="color:#f59e0b;"></i>
                    <span>Silakan perbaiki dan upload ulang dokumen yang ditandai "Perlu Revisi" sebelum batas waktu berakhir.</span>
                @else
                    <i class="fa-solid fa-circle-info" style="color:var(--primary);"></i>
                    <span>Silakan cek menu Ujian Online (CBT) untuk informasi jadwal ujian Anda.</span>
                @endif
            </div>
            
            @if($isRevisi)
            <div class="footer-deadline">
                <i class="fa-solid fa-stopwatch"></i>
                <div>
                    <span style="display:block; font-size:.7rem; color:#94a3b8; text-transform:uppercase;">Batas Waktu Revisi</span>
                    <span>30 Mei 2026, 23:59 WIB</span>
                </div>
            </div>
            @else
            <a href="{{ route('siswa.ujian') }}" class="btn-primary" style="padding:.6rem 1.5rem; text-decoration:none; border-radius:12px; font-weight:800; font-size:.85rem; display:flex; align-items:center; gap:0.5rem; background:linear-gradient(135deg,var(--primary),#3b82f6); box-shadow:0 8px 20px rgba(59,130,246,0.25);">
                <i class="fa-solid fa-laptop-code"></i>
                Lihat Jadwal Ujian
            </a>
            @endif
        </div>
    </section>
</div>

<script>
    function toggleUploadForm(jenis) {
        const form = document.getElementById('upload-form-' + jenis);
        if (form.style.display === 'none') {
            form.style.display = 'block';
            form.classList.add('animate-slide-up');
        } else {
            form.style.display = 'none';
        }
    }
</script>

{{-- Lightbox --}}
<div id="lightbox" style="display:none; position:fixed; z-index:9999; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.85); align-items:center; justify-content:center; backdrop-filter: blur(8px);">
    <span onclick="closeLightbox()" style="position:absolute; top:20px; right:30px; color:white; font-size:40px; cursor:pointer; font-weight: bold;">&times;</span>
    <img id="lightbox-img" style="max-width:90%; max-height:90%; border-radius:12px; display:none; box-shadow: 0 20px 50px rgba(0,0,0,0.5);">
    <iframe id="lightbox-pdf" style="width:80%; height:90%; border-radius:12px; border:none; display:none; background:white;"></iframe>
</div>

<script>
    function openLightbox(src, type = 'img') {
        const img = document.getElementById('lightbox-img');
        const pdf = document.getElementById('lightbox-pdf');
        const lb = document.getElementById('lightbox');
        
        if(type === 'pdf') {
            img.style.display = 'none';
            pdf.src = src;
            pdf.style.display = 'block';
        } else {
            pdf.style.display = 'none';
            img.src = src;
            img.style.display = 'block';
        }
        lb.style.display = 'flex';
    }
    function closeLightbox() {
        document.getElementById('lightbox').style.display = 'none';
        document.getElementById('lightbox-pdf').src = '';
    }
    document.getElementById('lightbox').addEventListener('click', function(e) {
        if (e.target === this) closeLightbox();
    });
</script>
@endsection
