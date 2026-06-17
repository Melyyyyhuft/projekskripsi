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
            ['label' => 'Isi Biodata & Berkas',  'done' => $step1Completed, 'active' => !$step1Completed],
            ['label' => 'Verifikasi Panitia',     'done' => $step2Completed, 'active' => $step1Completed && !$step2Completed],
            ['label' => 'Ujian Online',            'done' => $step3Completed, 'active' => $step2Completed && !$step3Completed],
            ['label' => 'Hasil Seleksi',           'done' => $step4Completed, 'active' => $step3Completed && !$step4Completed],
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
            <div style="font-size:.8rem;font-weight:600;text-align:center;color:{{ $step['done'] ? '#10b981' : ($step['active'] ? 'var(--primary)' : 'var(--gray-text)') }};line-height:1.3;padding:0 4px;">
                {{ $step['label'] }}
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- ─── Action Cards ─── --}}

<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:1.5rem;" class="animate-slide-up">

    {{-- Card 1: Pendaftaran --}}
    <div class="glass-card hover-scale" style="{{ $step1Completed ? 'border:2px solid #10b981;' : 'border:2px solid var(--primary);' }}">
        <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1rem;">
            <div style="width:48px;height:48px;border-radius:12px;background:{{ $step1Completed?'#10b981':'var(--primary)' }};color:white;display:flex;align-items:center;justify-content:center;font-size:1.5rem;">
                <i class="fa-solid fa-file-signature"></i>
            </div>
            <h3 style="font-size:1.15rem;margin:0;">Biodata &amp; Pendaftaran</h3>
        </div>
        <p style="color:var(--gray-text);margin-bottom:1.5rem;min-height:48px;">
            Lengkapi data diri, nilai rapor, dokumen persyaratan, dan pilih jurusan tujuan Anda.
        </p>
        @if($s === 'revisi')
            <a href="{{ route('siswa.pendaftaran') }}" class="btn-primary"
               style="display:block;width:100%;text-align:center;background:#f59e0b;border-color:#f59e0b;">
               🔄 Revisi Berkas Sekarang
            </a>
        @elseif($step1Completed)
            <a href="{{ route('siswa.pendaftaran') }}" class="btn-outline"
               style="display:block;width:100%;text-align:center;border-color:#10b981;color:#10b981;">
               Lihat / Edit Data
            </a>
        @else
            <a href="{{ route('siswa.pendaftaran') }}" class="btn-primary"
               style="display:block;width:100%;text-align:center;">
               Mulai Isi Data
            </a>
        @endif
    </div>

    {{-- Card 2: Jadwal & Ujian Online --}}
    <div class="glass-card hover-scale"
         style="opacity:{{ ($bisaUjian || $step3Completed || $ujian_aktif) ? '1' : '0.65' }};
                {{ $bisaUjian ? 'border:2px solid var(--primary);' : ($step3Completed ? 'border:2px solid #10b981;' : '') }}">
        <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1rem;">
            <div style="width:48px;height:48px;border-radius:12px;
                        background:{{ $bisaUjian ? 'var(--primary)' : ($hasilUjian ? '#10b981' : (in_array($s,$statusGugur)?'#dc2626':($ujian_aktif ? '#f59e0b' : 'var(--gray-text)'))) }};
                        color:white;display:flex;align-items:center;justify-content:center;font-size:1.5rem;">
                <i class="fa-solid fa-desktop"></i>
            </div>
            <h3 style="font-size:1.15rem;margin:0;">Jadwal & Ujian CBT</h3>
        </div>

        <div style="color:var(--gray-text);margin-bottom:1.5rem;min-height:48px;font-size:.9rem;">
            @php
                $tglMulaiGlobal = $settings['cbt_tgl_mulai'] ?? null;
                $tglSelesaiGlobal = $settings['cbt_tgl_selesai'] ?? null;
                $durasiGlobal = (int) ($settings['cbt_durasi_default'] ?? 0);
                $statusCbt = $settings['cbt_status'] ?? 'ditutup';
                
                $now = now();
                $isPeriodActive = $statusCbt == 'aktif' && $tglMulaiGlobal && $tglSelesaiGlobal && $now->between(\Carbon\Carbon::parse($tglMulaiGlobal), \Carbon\Carbon::parse($tglSelesaiGlobal));
                $isBeforePeriod = $tglMulaiGlobal && $now->lt(\Carbon\Carbon::parse($tglMulaiGlobal));
                $isAfterPeriod = $tglSelesaiGlobal && $now->gt(\Carbon\Carbon::parse($tglSelesaiGlobal));
            @endphp

            @if($tglMulaiGlobal && $tglSelesaiGlobal)
                <div style="margin-bottom: .75rem; background: #f0f9ff; padding: .75rem; border-radius: 8px; border: 1px solid #bae6fd;">
                    <p style="margin:0; color:#0369a1; font-weight:700;"><i class="fa-solid fa-calendar-days"></i> Periode Ujian CBT:</p>
                    <p style="margin:0; font-size:.85rem;">{{ \Carbon\Carbon::parse($tglMulaiGlobal)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($tglSelesaiGlobal)->format('d M Y') }}</p>
                </div>
            @elseif($ujian_aktif)
                <div style="margin-bottom: .5rem; background: #f8fafc; padding: .5rem; border-radius: 8px;">
                    <p style="margin:0;"><strong>Tgl:</strong> {{ \Carbon\Carbon::parse($ujian_aktif->jadwal_mulai)->format('d M Y H:i') }}</p>
                    <p style="margin:0;"><strong>Durasi:</strong> {{ $ujian_aktif->durasi_menit }} Menit</p>
                </div>
            @endif

            @if($hasilUjian)
                Anda sudah mengikuti ujian. Nilai CBT: <strong style="color:#059669;font-size:1.1rem;">{{ $hasilUjian->skor }}</strong>.
            @elseif($isAfterPeriod)
                <strong style="color:#dc2626;">Periode ujian telah berakhir.</strong>
            @elseif($isBeforePeriod)
                <strong style="color:#d97706;">Ujian belum dimulai.</strong>
            @elseif($bisaUjian && $isPeriodActive)
                Berkas diverifikasi. Silakan kerjakan ujian sekarang.
            @elseif(in_array($s, $statusGugur))
                Status: <strong style="color:#dc2626;">Gugur</strong> (Tidak mengikuti ujian).
            @elseif($s === 'lolos_admin' && (!$ujian_aktif || !$isPeriodActive))
                Belum ada jadwal ujian aktif atau periode belum dimulai.
            @elseif($s === 'ditolak_admin')
                Pendaftaran ditolak.
            @else
                Tunggu verifikasi panitia untuk akses ujian.
            @endif
        </div>

        @if($hasilUjian)
            <button disabled
               style="display:block;width:100%;text-align:center;padding:.875rem 2rem;background:#10b981;color:white;border:none;border-radius:999px;font-weight:600;cursor:not-allowed;">
               <i class="fa-solid fa-check"></i> Ujian Selesai
            </button>
        @elseif($isBeforePeriod)
            <button disabled
               style="display:block;width:100%;text-align:center;padding:.875rem 2rem;background:#f1f5f9;color:#94a3b8;border:1px solid #e2e8f0;border-radius:999px;font-weight:600;cursor:not-allowed;">
               ⏳ Belum Dimulai
            </button>
        @elseif($isAfterPeriod)
            <button disabled
               style="display:block;width:100%;text-align:center;padding:.875rem 2rem;background:#fee2e2;color:#dc2626;border:none;border-radius:999px;font-weight:600;cursor:not-allowed;">
               🛑 Periode Berakhir
            </button>
        @elseif($bisaUjian && $isPeriodActive)
            <a href="{{ route('siswa.ujian') }}" class="btn-primary"
               style="display:block;width:100%;text-align:center;">
               💻 Mulai Ujian Sekarang
            </a>
        @elseif(in_array($s, $statusGugur))
            <button disabled
               style="display:block;width:100%;text-align:center;padding:.875rem 2rem;background:#fee2e2;color:#dc2626;border:none;border-radius:999px;font-weight:600;cursor:not-allowed;">
               ❌ Tidak Mengikuti Ujian
            </button>
        @else
            <button disabled
               style="display:block;width:100%;text-align:center;padding:.875rem 2rem;background:#f1f5f9;color:#94a3b8;border:1px solid #e2e8f0;border-radius:999px;font-weight:600;cursor:not-allowed;">
               🔒 Belum Tersedia
            </button>
        @endif
    </div>

    {{-- Card 3: Hasil Seleksi --}}
    <div class="glass-card hover-scale"
         style="{{ in_array($s, $statusDiterima) ? 'border:2px solid #f59e0b;' : 'opacity:.65;' }}">
        <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1rem;">
            <div style="width:48px;height:48px;border-radius:12px;
                        background:{{ in_array($s,$statusDiterima)?'#f59e0b':'var(--gray-text)' }};
                        color:white;display:flex;align-items:center;justify-content:center;font-size:1.5rem;">
                <i class="fa-solid fa-trophy"></i>
            </div>
            <h3 style="font-size:1.15rem;margin:0;">Hasil Seleksi</h3>
        </div>

        <div style="color:var(--gray-text);margin-bottom:1.5rem;min-height:48px;">
            @if(in_array($s, $statusDiterima) && $hasilSeleksi)
                <div style="background: #fdf4ff; padding: .75rem; border-radius: 8px; border: 1px solid #fbcfe8;">
                    <p style="margin:0 0 .25rem; font-size:.85rem;">Status Kelulusan:</p>
                    <p style="margin:0 0 .5rem; font-size:1.1rem; font-weight:800; color:#9333ea;">{{ $hasilSeleksi->kategori_kelulusan ?? ($hasilSeleksi->is_lulus ? 'Lulus' : 'Tidak Lulus') }}</p>
                    <p style="margin:0; font-size:.85rem;">Skor Akhir: <strong style="color:var(--primary);">{{ $hasilSeleksi->skor_akhir }}</strong></p>
                </div>
            @elseif(in_array($s, $statusGugur))
                Anda dinyatakan <strong style="color:#dc2626;">Gugur</strong> karena tidak mengikuti ujian seleksi.
            @elseif($s === 'siap_finalisasi')
                Seleksi sedang diproses oleh Admin. Hasil akan diumumkan setelah finalisasi.
            @else
                Pengumuman hasil seleksi akan muncul di sini setelah Admin melakukan finalisasi.
            @endif
        </div>

        @if(in_array($s, $statusDiterima))
            <div style="display:flex; gap:.5rem;">
                <a href="{{ route('siswa.hasil') }}" class="btn-primary"
                   style="flex:1; text-align:center; background:#f59e0b; padding:.6rem;">
                   Lihat Detail
                </a>
                <button onclick="window.print()" class="btn-outline" style="flex:1; text-align:center; border-color:#f59e0b; color:#f59e0b; padding:.6rem;">
                   <i class="fa-solid fa-download"></i> Unduh PDF
                </button>
            </div>
        @elseif(in_array($s, $statusGugur))
            <a href="{{ route('siswa.hasil') }}" class="btn-outline"
               style="display:block;width:100%;text-align:center;border-color:#dc2626;color:#dc2626;">
               Lihat Detail Status
            </a>
        @else
            <button disabled
               style="display:block;width:100%;text-align:center;padding:.875rem 2rem;background:#f1f5f9;color:#94a3b8;border:1px solid #e2e8f0;border-radius:999px;font-weight:600;cursor:not-allowed;">
               ⏳ Belum Diumumkan
            </button>
        @endif
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
