@extends('layouts.siswa')
@section('title', 'Dashboard Calon Siswa')

@section('content')




@php
    // ── Status-status baru termasuk dari sistem seleksi fleksibel ──
    $statusDiterima   = ['siap_diumumkan', 'diterima', 'tidak_diterima'];
    $statusSudahUjian = ['sudah_ujian', 'siap_finalisasi', 'siap_diumumkan', 'diterima', 'tidak_diterima'];
    $statusGugur      = ['gugur', 'tidak_mengikuti_ujian'];
    $statusDitolak    = ['ditolak_admin'];

    $s = $pendaftaran ? $pendaftaran->status : null;

    // Step progress
    $step1Completed = $pendaftaran !== null;
    $step2Completed = $step1Completed && !in_array($s, ['draft', 'menunggu_verifikasi', 'ditolak_admin']);
    $step3Completed = $step2Completed && in_array($s, array_merge($statusSudahUjian, $statusGugur));

    // Progress bar
    if (!$pendaftaran) {
        $progressPercent = 0;
    } elseif ($s === 'revisi') {
        $progressPercent = 15;
    } elseif ($s === 'menunggu_verifikasi') {
        $progressPercent = 30;
    } elseif ($s === 'lolos_admin') {
        $progressPercent = 55;
    } elseif ($step3Completed) {
        $progressPercent = 100;
    } else {
        $progressPercent = 10;
    }

    // ── Apakah bisa ujian ──
    $bisaUjian = $step2Completed
        && $s === 'lolos_admin'
        && $ujian_aktif !== null
        && !$hasilUjian; // belum pernah ujian

    // ── Teks Status Ujian ──
    if ($hasilUjian) {
        $statusUjianText = 'Selesai ✓';
        $statusUjianColor = '#059669';
    } elseif ($bisaUjian) {
        $statusUjianText = 'Siap Dikerjakan';
        $statusUjianColor = 'var(--primary)';
    } elseif (in_array($s, $statusGugur)) {
        $statusUjianText = 'Tidak Mengikuti';
        $statusUjianColor = '#dc2626';
    } elseif ($s === 'lolos_admin' && !$ujian_aktif) {
        $statusUjianText = 'Menunggu Jadwal';
        $statusUjianColor = '#d97706';
    } else {
        $statusUjianText = 'Belum Tersedia';
        $statusUjianColor = '#94a3b8';
    }

    // ── Label status pendaftaran yang ramah ──
    $labelStatus = [
        'draft'                  => 'Draft',
        'menunggu_verifikasi'    => 'Menunggu Verifikasi',
        'revisi'                 => '<span style="color:#ef4444;font-weight:800;">PERLU REVISI ⚠️</span>',
        'lolos_admin'            => 'Lolos Administrasi ✓',
        'ditolak_admin'          => 'Ditolak Admin',
        'sudah_ujian'            => 'Sudah Ujian ✓',
        'tidak_mengikuti_ujian'  => 'Tidak Mengikuti Ujian',
        'siap_finalisasi'        => 'Dalam Proses Seleksi',
        'siap_diumumkan'         => 'Hasil Telah Diumumkan ✓',
        'gugur'                  => 'Gugur',
        'diterima'               => 'Diterima ✓',
        'tidak_diterima'         => 'Tidak Diterima',
    ];
    $statusLabel = $s ? ($labelStatus[$s] ?? ucwords(str_replace('_', ' ', $s))) : 'Belum Mendaftar';
@endphp


{{-- ─── Inline Alert Dashboard (Revisi) ─── --}}
@if($s === 'revisi')
<div class="animate-slide-up" style="margin-bottom:1.5rem; padding:1rem 1.5rem; background:linear-gradient(135deg, #fff5f5 0%, #fffafa 100%); border:1px solid #fee2e2; border-left:5px solid #ef4444; border-radius:12px; display:flex; align-items:center; gap:1.25rem; box-shadow:0 8px 20px rgba(239, 68, 68, 0.04); position:relative;">
    <div style="width:42px; height:42px; border-radius:10px; background:#fecaca; color:#ef4444; display:flex; align-items:center; justify-content:center; font-size:1.1rem; flex-shrink:0;">
        <i class="fa-solid fa-triangle-exclamation"></i>
    </div>
    
    <div style="flex:1;">
        <h2 style="margin:0; font-size:1.1rem; color:#991b1b; font-weight:800; letter-spacing:-0.01em;">Perlu Revisi</h2>
        <p style="margin:0.1rem 0 0; color:#b91c1c; font-size:0.9rem; font-weight:500; opacity:0.9;">
            Beberapa data pendaftaran Anda harus diperbaiki.
        </p>
    </div>
    
    <div style="flex-shrink:0;">
        <a href="{{ route('siswa.pendaftaran') }}" class="btn-primary" style="background:#ef4444; border:none; padding:0.5rem 1.25rem; font-size:0.8rem; border-radius:8px; font-weight:700; box-shadow:0 5px 15px rgba(239,68,68,0.2); display:flex; align-items:center; gap:.4rem;">
            <span>Lihat Revisi</span>
            <i class="fa-solid fa-arrow-right" style="font-size:0.7rem;"></i>
        </a>
    </div>
</div>
@endif

{{-- ─── Top Statistics ─── --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:1.5rem;margin-bottom:2rem;" class="animate-slide-up">

    {{-- Stat 1: Status Pendaftaran --}}
    <div class="stats-card">
        <div class="stats-icon" style="background:rgba(59,130,246,.1);color:var(--primary);">
            <i class="fa-solid fa-clipboard-user"></i>
        </div>
        <div style="flex:1;">
            <p style="margin:0;font-size:.875rem;color:var(--gray-text);font-weight:600;">Status Pendaftaran</p>
            <h3 style="margin:0;font-size:1rem;color:var(--dark);">{!! $statusLabel !!}</h3>
        </div>
    </div>

    {{-- Stat 2: Status Ujian --}}
    <div class="stats-card">
        <div class="stats-icon" style="background:rgba(139,92,246,.1);color:var(--secondary);">
            <i class="fa-solid fa-laptop-file"></i>
        </div>
        <div style="flex:1;">
            <p style="margin:0;font-size:.875rem;color:var(--gray-text);font-weight:600;">Status Ujian</p>
            <h3 style="margin:0;font-size:1rem;color:{{ $statusUjianColor }};">{{ $statusUjianText }}</h3>
        </div>
    </div>

    {{-- Stat 3: Nilai Ujian (jika sudah ujian) --}}
    @if($hasilUjian)
    <div class="stats-card">
        <div class="stats-icon" style="background:rgba(16,185,129,.1);color:#059669;">
            <i class="fa-solid fa-star"></i>
        </div>
        <div style="flex:1;">
            <p style="margin:0;font-size:.875rem;color:var(--gray-text);font-weight:600;">Nilai Ujian CBT</p>
            <h3 style="margin:0;font-size:1.3rem;color:#059669;font-weight:800;">{{ $hasilUjian->skor }}</h3>
        </div>
    </div>
    @else
    {{-- Stat 3: Progress --}}
    <div class="stats-card" style="flex-direction:column;align-items:stretch;justify-content:center;gap:.25rem;">
        <div style="display:flex;justify-content:space-between;align-items:center;">
            <p style="margin:0;font-size:.875rem;color:var(--gray-text);font-weight:600;">Progres Pendaftaran</p>
            <span style="font-weight:700;color:var(--primary);">{{ $progressPercent }}%</span>
        </div>
        <div class="progress-bar-container">
            <div class="progress-bar-fill" style="width:{{ $progressPercent }}%;"></div>
        </div>
    </div>
    @endif
</div>

{{-- ─── Welcome Card ─── --}}
<div class="glass-card animate-slide-up" style="margin-bottom:2.5rem;padding:2.5rem;background:#ffffff;border-left:4px solid var(--primary);border-radius:12px;position:relative;">
    @if($pendaftaran && $pendaftaran->nomor_pendaftaran)
        <div style="position:absolute; top:1.5rem; right:1.5rem; background:rgba(59,130,246,0.1); color:var(--primary); padding:0.4rem 1rem; border-radius:999px; font-size:0.75rem; font-weight:800; border:1px solid rgba(59,130,246,0.2);">
            <i class="fa-solid fa-hashtag"></i> {{ $pendaftaran->nomor_pendaftaran }}
        </div>
    @endif
    <h2 style="font-size:1.8rem;margin-bottom:.5rem;color:var(--dark);">Selamat Datang, {{ Auth::user()->name }}! 👋</h2>
    <p style="color:var(--gray-text);font-size:1.1rem;max-width:700px;position:relative;z-index:1;">
        Portal pendaftaran peserta didik baru SMK. Ikuti tahapan di bawah ini secara berurutan untuk menyelesaikan proses pendaftaran Anda.
    </p>
</div>

{{-- ─── Progress Steps ─── --}}
<div class="glass-card animate-slide-up" style="margin-bottom:2rem;padding:2.5rem;animation-delay:.2s;">
    <h3 style="text-align:center;margin-bottom:2.5rem;color:var(--dark);">Alur Pendaftaran Anda</h3>

    @php
        $step4Completed = in_array($s, $statusDiterima);
        $steps = [
            ['label' => 'Lengkapi Data',     'desc' => 'Isi Biodata & Berkas', 'done' => $step1Completed, 'active' => !$step1Completed],
            ['label' => 'Verifikasi',         'desc' => 'Pengecekan Panitia',  'done' => $step2Completed, 'active' => $step1Completed && !$step2Completed],
            ['label' => 'Ujian CBT',          'desc' => 'Tes Online Sekolah',  'done' => $step3Completed, 'active' => $step2Completed && !$step3Completed],
            ['label' => 'Hasil Akhir',        'desc' => 'Status Kelulusan',    'done' => $step4Completed, 'active' => $step3Completed && !$step4Completed],
        ];
    @endphp

    {{-- Wrapper relatif agar garis bisa diposisikan secara absolut --}}
    <div style="position:relative;display:flex;align-items:flex-start;justify-content:space-between;padding:0 24px;">

        {{-- Garis abu-abu background: dari pusat step-1 ke pusat step-4 --}}
        <div style="position:absolute;top:24px;left:calc(24px + 24px);right:calc(24px + 24px);height:3px;background:#e2e8f0;z-index:0;transform:translateY(-50%);"></div>

        @foreach($steps as $i => $step)
        <div style="position:relative;z-index:1;display:flex;flex-direction:column;align-items:center;gap:.5rem;flex:1;min-width:0;">

            {{-- Garis hijau: ditampilkan jika step ini SELESAI dan bukan step pertama --}}
            @if($step['done'] && $i > 0)
            <div style="position:absolute;top:24px;left:0;right:50%;height:3px;background:#10b981;z-index:0;transform:translateY(-50%);"></div>
            @endif
            {{-- Garis hijau sisi kanan: dari tengah circle ke kanan sampai step berikutnya --}}
            @if($step['done'] && $i < count($steps) - 1)
            <div style="position:absolute;top:24px;left:50%;right:0;height:3px;background:#10b981;z-index:0;transform:translateY(-50%);"></div>
            @endif

            {{-- Circle --}}
            <div style="width:48px;height:48px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:1rem;border:4px solid white;position:relative;z-index:2;box-shadow:0 0 0 3px {{ $step['done'] ? 'rgba(16,185,129,.25)' : ($step['active'] ? 'rgba(30,64,175,.25)' : 'transparent') }};background:{{ $step['done'] ? '#10b981' : ($step['active'] ? 'var(--primary)' : '#e2e8f0') }};color:{{ ($step['done'] || $step['active']) ? 'white' : '#64748b' }};">
                @if($step['done'])
                    <i class="fa-solid fa-check"></i>
                @else
                    {{ $i + 1 }}
                @endif
            </div>

            {{-- Label --}}
            <div style="font-size:.85rem;font-weight:800;text-align:center;color:{{ $step['done'] ? '#065f46' : ($step['active'] ? 'var(--primary)' : '#475569') }};line-height:1.2;padding:0 4px;margin-top:0.4rem;">
                {{ $step['label'] }}
            </div>
            {{-- Deskripsi (Tulisan Kecil) --}}
            <div style="font-size:0.65rem;text-align:center;color:#94a3b8;font-weight:600;max-width:100px;line-height:1.2;">
                {{ $step['desc'] }}
            </div>
        </div>
        @endforeach
    </div>
</div>

<style>
    .dashboard-landscape-grid {
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
    }
    .card-landscape {
        background: white;
        border-radius: 16px;
        border: 1px solid #f1f5f9;
        box-shadow: 0 4px 15px rgba(0,0,0,0.02);
        display: flex;
        overflow: hidden;
        transition: all 0.3s ease;
        min-height: 100px;
    }
    .card-landscape:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.05);
    }
    .card-landscape-left {
        padding: 1rem 1.5rem;
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: 0.25rem;
        width: 30%;
        border-right: 1px solid #f8fafc;
        position: relative;
    }
    .card-landscape-right {
        padding: 1rem 1.5rem;
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1.5rem;
        background: #fafbfc;
    }
    .card-landscape-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        margin-bottom: 0.1rem;
    }
    .card-landscape-title {
        font-size: 1rem;
        font-weight: 800;
        color: #1e293b;
        margin: 0;
    }
    .card-landscape-desc {
        color: #64748b;
        font-size: 0.8rem;
        line-height: 1.4;
        margin: 0;
    }
    .status-window {
        padding: 0.6rem 1rem;
        border-radius: 12px;
        background: white;
        border: 1px solid #e2e8f0;
        flex: 1;
        margin: 0;
    }

    @media (max-width: 900px) {
        .card-landscape { flex-direction: column; }
        .card-landscape-left { width: 100%; border-right: none; border-bottom: 1px solid #f1f5f9; }
        .card-landscape-right { width: 100%; flex-direction: column; align-items: stretch; gap: 1rem; }
    }
</style>

<div class="dashboard-landscape-grid animate-slide-up" style="animation-delay: 0.3s;">

    {{-- Card 1: Pendaftaran --}}
    <div class="card-landscape" style="{{ $step1Completed ? 'border-left: 6px solid #10b981;' : 'border-left: 6px solid var(--primary);' }}">
        <div class="card-landscape-left">
            <div class="card-landscape-icon" style="background:{{ $step1Completed ? '#ecfdf5' : 'rgba(59,130,246,0.1)' }}; color:{{ $step1Completed ? '#10b981' : 'var(--primary)' }};">
                <i class="fa-solid fa-file-signature"></i>
            </div>
            <h3 class="card-landscape-title">Biodata & Pendaftaran</h3>
            <p class="card-landscape-desc">Lengkapi profil diri dan nilai rapor.</p>
        </div>
        <div class="card-landscape-right">
            <div class="status-window" style="display: flex; align-items: center; justify-content: space-between; gap: 1rem;">
                <div>
                    <p style="margin:0; font-size: 0.65rem; font-weight: 800; color: #94a3b8; text-transform: uppercase;">Progres Berkas</p>
                    <p style="margin:0; font-size: 0.9rem; font-weight: 700; color: {{ $step1Completed ? '#10b981' : '#1e40af' }};">
                        {{ $step1Completed ? '✓ Berhasil Diisi' : '⚡ Menunggu' }}
                    </p>
                </div>
                <div style="flex: 1; max-width: 100px;">
                    <div class="progress-bar-container" style="height: 6px; margin-bottom: 0;">
                        <div class="progress-bar-fill" style="width: {{ $step1Completed ? '100' : '0' }}%; background: {{ $step1Completed ? '#10b981' : '#e2e8f0' }};"></div>
                    </div>
                </div>
            </div>
            
            <div style="width: 200px; flex-shrink: 0;">
                @if($s === 'revisi')
                    <a href="{{ route('siswa.pendaftaran') }}" class="btn-primary" style="display: block; text-align: center; background: #f59e0b; border-color: #f59e0b; padding: 0.6rem;">
                        <i class="fa-solid fa-arrows-rotate"></i> Revisi Berkas
                    </a>
                @elseif($step1Completed)
                    <a href="{{ route('siswa.pendaftaran') }}" class="btn-outline" style="display: block; text-align: center; border-color: #10b981; color: #10b981; padding: 0.6rem;">
                        <i class="fa-solid fa-eye"></i> Lihat / Edit Biodata
                    </a>
                @else
                    <a href="{{ route('siswa.pendaftaran') }}" class="btn-primary" style="display: block; text-align: center; padding: 0.6rem;">
                        <i class="fa-solid fa-plus"></i> Mulai Isi
                    </a>
                @endif
            </div>
        </div>
    </div>

    {{-- Card 2: Jadwal & Ujian Online --}}
    <div class="card-landscape" style="{{ $hasilUjian ? 'border-left: 6px solid #10b981;' : ($bisaUjian ? 'border-left: 6px solid var(--primary);' : 'border-left: 6px solid #94a3b8;') }}">
        <div class="card-landscape-left">
            <div class="card-landscape-icon" style="background:{{ $hasilUjian ? '#ecfdf5' : ($bisaUjian ? 'rgba(59,130,246,0.1)' : '#f8fafc') }}; color:{{ $hasilUjian ? '#10b981' : ($bisaUjian ? 'var(--primary)' : '#94a3b8') }};">
                <i class="fa-solid fa-desktop"></i>
            </div>
            <h3 class="card-landscape-title">Jadwal & Ujian CBT</h3>
            <p class="card-landscape-desc">Akses ujian online sesuai jadwal.</p>
        </div>
        <div class="card-landscape-right">
            @php
                $tglMulaiGlobal = $settings['cbt_tgl_mulai'] ?? null;
                $tglSelesaiGlobal = $settings['cbt_tgl_selesai'] ?? null;
                $statusCbt = $settings['cbt_status'] ?? 'ditutup';
                $now = now();
                $isPeriodActive = $statusCbt == 'aktif' && $tglMulaiGlobal && $tglSelesaiGlobal && $now->between(\Carbon\Carbon::parse($tglMulaiGlobal), \Carbon\Carbon::parse($tglSelesaiGlobal));
                $isBeforePeriod = $tglMulaiGlobal && $now->lt(\Carbon\Carbon::parse($tglMulaiGlobal));
                $isAfterPeriod = $tglSelesaiGlobal && $now->gt(\Carbon\Carbon::parse($tglSelesaiGlobal));
            @endphp

            <div class="status-window" style="display: flex; gap: 1rem; align-items: center; justify-content: space-between;">
                <div>
                    <p style="margin:0; font-size: 0.65rem; font-weight: 800; color: #94a3b8; text-transform: uppercase;">Pelaksanaan</p>
                    <p style="margin:0; font-size: 0.85rem; font-weight: 700; color: #1e293b;">
                        @if($tglMulaiGlobal)
                            {{ \Carbon\Carbon::parse($tglMulaiGlobal)->translatedFormat('d M') }} — {{ \Carbon\Carbon::parse($tglSelesaiGlobal)->translatedFormat('d M') }}
                        @else
                            Belum Diset
                        @endif
                    </p>
                </div>
                <div style="text-align: right;">
                    <p style="margin:0; font-size: 0.65rem; font-weight: 800; color: #94a3b8; text-transform: uppercase;">Status</p>
                    <p style="margin:0; font-size: 0.9rem; font-weight: 700; color: {{ $statusUjianColor }};">
                        {{ $statusUjianText }}
                    </p>
                </div>
            </div>

            <div style="width: 200px; flex-shrink: 0;">
                @if($hasilUjian)
                    <button disabled class="btn-primary" style="display: block; width: 100%; background:#10b981; border:none; opacity:1; cursor:default; padding: 0.6rem; font-size: 0.8rem;">
                        <i class="fa-solid fa-circle-check"></i> Ujian Selesai
                    </button>
                @elseif($bisaUjian && $isPeriodActive)
                    <a href="{{ route('siswa.ujian') }}" class="btn-primary" style="display: block; text-align: center; padding: 0.6rem;">
                        <i class="fa-solid fa-play"></i> Mulai Ujian
                    </a>
                @else
                    <button disabled class="btn-outline" style="display: block; width: 100%; background:#f8fafc; border-color:#e2e8f0; color:#94a3b8; cursor:not-allowed; padding: 0.6rem; font-size: 0.8rem;">
                        <i class="fa-solid fa-lock"></i> Belum Tersedia
                    </button>
                @endif
            </div>
        </div>
    </div>

    {{-- Card 3: Hasil Seleksi --}}
    <div class="card-landscape" style="{{ in_array($s, $statusDiterima) ? 'border-left: 6px solid #f59e0b;' : 'border-left: 6px solid #94a3b8;' }}">
        <div class="card-landscape-left">
            <div class="card-landscape-icon" style="background:{{ in_array($s, $statusDiterima) ? '#fff7ed' : '#f8fafc' }}; color:{{ in_array($s, $statusDiterima) ? '#f59e0b' : '#94a3b8' }};">
                <i class="fa-solid fa-trophy"></i>
            </div>
            <h3 class="card-landscape-title">Hasil Seleksi</h3>
            <p class="card-landscape-desc">Pengumuman kelulusan final.</p>
        </div>
        <div class="card-landscape-right">
            @if(in_array($s, $statusDiterima) && $hasilSeleksi)
                <div class="status-window" style="background: linear-gradient(135deg, #fffcf5 0%, #fff7ed 100%); border-color: #ffedd5; display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <p style="margin:0; font-size: 0.65rem; font-weight: 800; color: #9a3412; text-transform: uppercase;">Keputusan</p>
                        <p style="margin:0; font-size: 1rem; font-weight: 900; color: #f59e0b;">
                            {{ strtoupper($hasilSeleksi->kategori_kelulusan ?? ($hasilSeleksi->is_lulus ? 'LULUS' : 'TIDAK LULUS')) }}
                        </p>
                    </div>
                    <div style="text-align: right;">
                        <p style="margin:0; font-size: 0.65rem; font-weight: 800; color: #9a3412;">SKOR</p>
                        <p style="margin:0; font-size: 1rem; font-weight: 900; color: #1e293b;">{{ $hasilSeleksi->skor_akhir }}</p>
                    </div>
                </div>
                <div style="width: 200px; flex-shrink: 0;">
                    <a href="{{ route('siswa.hasil') }}" class="btn-primary" style="display: block; text-align: center; background: #f59e0b; border-color: #f59e0b; padding: 0.6rem;">
                        <i class="fa-solid fa-list-check"></i> Detail Seleksi
                    </a>
                </div>
            @else
                <div class="status-window" style="background: #f8fafc; border-style: dashed; text-align: center; display: flex; align-items: center; justify-content: center;">
                    <p style="margin:0; color: #64748b; font-size: 0.8rem;">
                        <i class="fa-solid fa-clock-rotate-left"></i> 
                        {{ $s === 'siap_finalisasi' ? 'Hasil sedang diproses' : 'Belum diumumkan' }}
                    </p>
                </div>
                <div style="width: 200px; flex-shrink: 0;">
                    <button disabled class="btn-outline" style="display: block; width: 100%; opacity: 0.6; cursor: not-allowed; padding: 0.6rem; font-size: 0.8rem;">
                        <i class="fa-solid fa-hourglass-half"></i> Menunggu
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    @if($s === 'revisi')
        Swal.fire({
            icon: 'error',
            title: 'Perlu Revisi!',
            text: 'Admin meminta Anda memperbaiki beberapa berkas. Silakan cek detail di dashboard.',
            confirmButtonText: 'Tutup',
            confirmButtonColor: '#ef4444',
            timer: 10000,
            timerProgressBar: true,
            toast: true,
            position: 'top-end',
        });
    @endif
</script>
@endsection
