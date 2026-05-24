@extends('layouts.siswa')
@section('title', 'Ujian Online CBT')

@section('content')
@php
    $s = $pendaftaran ? $pendaftaran->status : null;
    $now = now();
    $settings = $settings ?? [];

    // Settings CBT Global
    $startCbt = \Carbon\Carbon::parse($settings['cbt_tgl_mulai'] ?? now());
    $endCbt   = \Carbon\Carbon::parse($settings['cbt_tgl_selesai'] ?? now()->addDays(3));
    $statusCbt = $settings['cbt_status'] ?? 'aktif';

    // Derived variables (previously undefined)
    $tglMulaiGlobal  = $startCbt;
    $tglSelesaiGlobal = $endCbt;
    $periodeAktif = $statusCbt === 'aktif' && $now->between($startCbt, $endCbt);
    $mulaiTs  = $startCbt->timestamp * 1000;
    $selesaiTs = $endCbt->timestamp * 1000;

    // Status siswa
    $sudahUjian   = in_array($s, ['sudah_ujian','siap_finalisasi','siap_diumumkan','gugur','tidak_mengikuti_ujian']);
    $bisaUjian    = ($s === 'lolos_admin') && $statusCbt === 'aktif' && $now->between($startCbt, $endCbt);
    $belumVerif   = in_array($s, [null, 'draft', 'menunggu_verifikasi', 'perlu_revisi']);
    $ditolak      = ($s === 'ditolak_admin');

    // Check if exam module for specific jurusan exists
    $ujianAda = isset($ujian) && $ujian;
@endphp

<style>
@keyframes fadeInUp { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }
@keyframes pulse-glow { 0%,100%{box-shadow:0 0 0 0 rgba(59,130,246,.3);} 50%{box-shadow:0 0 0 10px rgba(59,130,246,0);} }
.fade-up { animation: fadeInUp .5s ease forwards; }
.delay-1 { animation-delay:.1s; opacity:0; }
.delay-2 { animation-delay:.2s; opacity:0; }
.peraturan-item { display:flex; align-items:flex-start; gap:.875rem; padding:.875rem 0; border-bottom:1px solid #f1f5f9; }
.peraturan-item:last-child { border-bottom:none; }
.peraturan-num { width:28px; height:28px; border-radius:50%; background:linear-gradient(135deg,var(--primary),#6366f1); color:white; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:.8rem; flex-shrink:0; margin-top:.05rem; }
.countdown-box { display:flex; align-items:center; justify-content:center; gap:1rem; flex-wrap:wrap; }
.countdown-unit { text-align:center; }
.countdown-num { font-size:2rem; font-weight:900; color:#0f172a; font-family:monospace; background:#f1f5f9; border-radius:10px; padding:.4rem .875rem; min-width:56px; display:block; }
.countdown-label { font-size:.7rem; color:#94a3b8; font-weight:700; text-transform:uppercase; letter-spacing:.06em; margin-top:.3rem; display:block; }
.countdown-sep { font-size:1.5rem; font-weight:900; color:#cbd5e1; margin-top:-.5rem; }
</style>

<div style="max-width:760px;margin:0 auto;">

{{-- ══════════════ SUDAH UJIAN ══════════════ --}}
@if($sudahUjian)
<div class="glass-card fade-up" style="text-align:center;padding:3rem 2rem;border-top:4px solid #10b981;">
    <div style="width:72px;height:72px;background:linear-gradient(135deg,#10b981,#059669);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.75rem;margin:0 auto 1.5rem;">✅</div>
    <h2 style="font-size:1.5rem;font-weight:800;color:#0f172a;margin:0 0 .75rem;">Anda Sudah Mengikuti Ujian CBT</h2>
    <p style="color:#64748b;font-size:.95rem;margin:0 0 1.5rem;line-height:1.7;">Ujian CBT Anda telah selesai. Hasil sedang diproses oleh panitia PPDB. Cek halaman <strong>Hasil Seleksi</strong> untuk update terbaru.</p>

    @if($hasilUjian ?? null)
    <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:14px;padding:1.25rem;display:inline-block;text-align:left;min-width:260px;margin-bottom:1.5rem;">
        <div style="font-size:.72rem;font-weight:700;color:#166534;text-transform:uppercase;letter-spacing:.06em;margin-bottom:.75rem;">Ringkasan Hasil Ujian Anda</div>
        <div style="display:flex;gap:2rem;">
            <div>
                <div style="font-size:.75rem;color:#4ade80;margin-bottom:.2rem;">Nilai CBT</div>
                <div style="font-size:1.75rem;font-weight:900;color:#059669;">{{ number_format($hasilUjian->skor ?? 0, 1) }}</div>
            </div>
            <div>
                <div style="font-size:.75rem;color:#4ade80;margin-bottom:.2rem;">Waktu Selesai</div>
                <div style="font-weight:700;color:#0f172a;">{{ isset($hasilUjian) ? $hasilUjian->created_at->format('d M Y, H:i') : '—' }}</div>
            </div>
        </div>
    </div>
    @endif

    <div style="display:flex;gap:.875rem;justify-content:center;flex-wrap:wrap;">
        <a href="{{ route('siswa.dashboard') }}" class="btn-outline" style="min-width:160px;text-align:center;">🏠 Dashboard</a>
        <a href="{{ route('siswa.hasil') }}" class="btn-primary" style="min-width:160px;text-align:center;">📢 Lihat Hasil</a>
    </div>
</div>

{{-- ══════════════ LOLOS ADMIN — SIAP UJIAN ══════════════ --}}
@elseif($bisaUjian && $ujianAda)

    {{-- Hero: Siap Ujian --}}
    <div style="background:linear-gradient(135deg,#0f172a,#1e3a5f,#1d4ed8);border-radius:24px;padding:2.5rem;color:white;margin-bottom:1.5rem;position:relative;overflow:hidden;" class="fade-up">
        <div style="position:absolute;top:-60px;right:-40px;width:220px;height:220px;background:rgba(255,255,255,.04);border-radius:50%;"></div>
        <div style="position:absolute;bottom:-40px;left:-20px;width:160px;height:160px;background:rgba(59,130,246,.1);border-radius:50%;"></div>

        <div style="display:flex;align-items:flex-start;gap:1.25rem;flex-wrap:wrap;position:relative;z-index:1;">
            <div style="width:56px;height:56px;background:rgba(59,130,246,.3);border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:1.5rem;flex-shrink:0;">🖥️</div>
            <div style="flex:1;">
                <div style="font-size:.75rem;font-weight:700;opacity:.7;text-transform:uppercase;letter-spacing:.1em;margin-bottom:.5rem;">Ujian Online CBT — Periode Aktif</div>
                <h2 style="font-size:1.6rem;font-weight:900;margin:0 0 .5rem;">{{ $ujian->judul }}</h2>
                <div style="display:flex;gap:1.5rem;flex-wrap:wrap;font-size:.875rem;opacity:.85;">
                    <span>⏱️ Durasi: <strong>{{ $ujian->durasi_menit }} menit</strong></span>
                    <span>📝 Soal: <strong>{{ $ujian->soals()->count() }} soal</strong></span>
                    <span>📅 Ditutup: <strong>{{ $endCbt->format('d M Y, H:i') }}</strong></span>
                </div>
            </div>
        </div>
    </div>

    {{-- Countdown Penutupan --}}
    <div class="glass-card fade-up delay-1" style="margin-bottom:1.5rem;text-align:center;">
        <div style="font-size:.75rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;margin-bottom:1rem;">⏳ Sisa Waktu Ujian Ditutup</div>
        <div class="countdown-box" id="countdown-main">
            <div class="countdown-unit"><span class="countdown-num" id="cd-days">00</span><span class="countdown-label">Hari</span></div>
            <span class="countdown-sep">:</span>
            <div class="countdown-unit"><span class="countdown-num" id="cd-hours">00</span><span class="countdown-label">Jam</span></div>
            <span class="countdown-sep">:</span>
            <div class="countdown-unit"><span class="countdown-num" id="cd-mins">00</span><span class="countdown-label">Menit</span></div>
            <span class="countdown-sep">:</span>
            <div class="countdown-unit"><span class="countdown-num" id="cd-secs">00</span><span class="countdown-label">Detik</span></div>
        </div>
        <p style="font-size:.8rem;color:#94a3b8;margin:1rem 0 0;">Setelah periode ditutup pada {{ $endCbt->format('d M Y, H:i') }}, Anda tidak dapat lagi mengakses ujian.</p>
    </div>

    {{-- Peraturan --}}
    <div class="glass-card fade-up delay-1" style="margin-bottom:1.5rem;">
        <h3 style="font-size:1rem;font-weight:700;color:#0f172a;margin:0 0 1.25rem;">📋 Peraturan & Ketentuan</h3>
        <div>
            <div class="peraturan-item">
                <div class="peraturan-num">1</div>
                <div>
                    <div style="font-weight:700;color:#0f172a;margin-bottom:.2rem;">Ujian Jurusan: {{ $pendaftaran->jurusan->nama ?? '-' }}</div>
                    <div style="font-size:.82rem;color:#64748b;">Gunakan perangkat dengan layar cukup lebar dan internet stabil.</div>
                </div>
            </div>
            <div class="peraturan-item">
                <div class="peraturan-num">2</div>
                <div>
                    <div style="font-weight:700;color:#0f172a;margin-bottom:.2rem;">Timer Berjalan Otomatis</div>
                    <div style="font-size:.82rem;color:#64748b;">Setelah klik mulai, durasi {{ $ujian->durasi_menit }} menit akan dihitung mundur.</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tombol Mulai --}}
    <div class="glass-card fade-up delay-2" style="border-top:3px solid #10b981;">
        <form action="{{ route('siswa.ujian.mulai') }}" method="POST">
            @csrf
            <button type="submit" onclick="return confirm('Mulai ujian sekarang?')" class="btn-primary" 
                    style="width:100%; padding:1rem; font-size:1.1rem; background:linear-gradient(135deg,#10b981,#059669); border:none; border-radius:14px; color:white; cursor:pointer;">
                🚀 Mulai Ujian Sekarang
            </button>
        </form>
    </div>

{{-- ══════════════ STATUS LAIN / DIBATASI ══════════════ --}}
@else
<div class="glass-card fade-up" style="padding:3rem 2rem; text-align:center;">
    <div style="font-size:3.5rem;margin-bottom:1rem;">🔒</div>
    <h2 style="font-size:1.4rem;font-weight:800;color:#0f172a;margin:0 0 1rem;">Akses Ujian Dibatasi</h2>
    <div style="background:#f8fafc; border-radius:16px; padding:1.5rem; margin-bottom:2rem; border:1px solid #e2e8f0; display:inline-block; max-width:500px;">
        <p style="color:#475569; font-size:.95rem; font-weight:600; margin:0; line-height:1.6;">
            {{ $pesan ?? 'Ujian belum dapat diakses saat ini.' }}
        </p>
    </div>

    {{-- Info ujian hanya jika $ujian tersedia --}}
    @if($ujianAda)
    <div style="background:#f8fafc;border-radius:12px;padding:1.25rem;margin-bottom:1.5rem;border:1px solid #e2e8f0;">
        <div style="font-size:.72rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;margin-bottom:.875rem;">ℹ️ Informasi Ujian CBT</div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:.875rem;">
            <div>
                <div style="font-size:.78rem;color:#94a3b8;margin-bottom:.2rem;">Nama Ujian</div>
                <div style="font-weight:700;color:#0f172a;font-size:.9rem;">{{ $ujian->judul }}</div>
            </div>
            <div>
                <div style="font-size:.78rem;color:#94a3b8;margin-bottom:.2rem;">Durasi</div>
                <div style="font-weight:700;color:#0f172a;font-size:.9rem;">{{ $ujian->durasi_menit }} menit</div>
            </div>
            @if($ujian->jadwal_mulai ?? null)
            <div>
                <div style="font-size:.78rem;color:#94a3b8;margin-bottom:.2rem;">Periode Mulai</div>
                <div style="font-weight:600;color:#0f172a;font-size:.9rem;">{{ \Carbon\Carbon::parse($ujian->jadwal_mulai)->isoFormat('D MMM YYYY, HH:mm') }}</div>
            </div>
            <div>
                <div style="font-size:.78rem;color:#94a3b8;margin-bottom:.2rem;">Periode Selesai</div>
                <div style="font-weight:600;color:#0f172a;font-size:.9rem;">{{ \Carbon\Carbon::parse($ujian->jadwal_selesai)->isoFormat('D MMM YYYY, HH:mm') }}</div>
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- Info Periode CBT Global dari Pengaturan --}}
    @if($tglMulaiGlobal)
    <div style="background:#f0f9ff;border:1px solid #bae6fd;border-radius:12px;padding:1.25rem;margin-bottom:1.5rem;">
        <div style="font-size:.72rem;font-weight:700;color:#0369a1;text-transform:uppercase;letter-spacing:.06em;margin-bottom:.75rem;">📅 Periode Ujian CBT</div>
        <div style="display:flex;align-items:center;gap:1rem;flex-wrap:wrap;">
            <div>
                <div style="font-size:.78rem;color:#94a3b8;margin-bottom:.2rem;">Mulai</div>
                <div style="font-weight:700;color:#0f172a;font-size:.9rem;">{{ $tglMulaiGlobal->translatedFormat('d M Y') }}</div>
            </div>
            <div style="color:#cbd5e1;font-size:1.5rem;">→</div>
            <div>
                <div style="font-size:.78rem;color:#94a3b8;margin-bottom:.2rem;">Selesai</div>
                <div style="font-weight:700;color:#0f172a;font-size:.9rem;">{{ $tglSelesaiGlobal->translatedFormat('d M Y') }}</div>
            </div>
            <div style="margin-left:auto;">
                @if($periodeAktif)
                    <span style="background:#dcfce7;color:#166534;padding:.3rem .75rem;border-radius:999px;font-size:.8rem;font-weight:700;">🟢 Sedang Berlangsung</span>
                @elseif($now->lt($startCbt))
                    <span style="background:#fef9c3;color:#92400e;padding:.3rem .75rem;border-radius:999px;font-size:.8rem;font-weight:700;">⏳ Belum Dimulai</span>
                @else
                    <span style="background:#fee2e2;color:#991b1b;padding:.3rem .75rem;border-radius:999px;font-size:.8rem;font-weight:700;">🔴 Sudah Berakhir</span>
                @endif
            </div>
        </div>
    </div>
    @endif

    {{-- Panduan jika belum lolos --}}
    @if($belumVerif)
    <div style="background:#eff6ff;border-radius:12px;padding:1.25rem;margin-bottom:1.5rem;border-left:4px solid var(--primary);">
        <p style="font-size:.875rem;color:#1e40af;margin:0 0 .5rem;font-weight:700;">Langkah yang harus dilakukan:</p>
        <ol style="font-size:.82rem;color:#1e40af;margin:0;padding-left:1.25rem;line-height:1.8;">
            <li>Lengkapi form pendaftaran & upload berkas persyaratan</li>
            <li>Tunggu verifikasi berkas dari panitia PPDB</li>
            <li>Setelah berkas <strong>Lolos Administrasi</strong>, ujian CBT dapat diakses</li>
        </ol>
    </div>
    @elseif($ditolak)
    <div style="background:#fef2f2;border-radius:12px;padding:1.25rem;margin-bottom:1.5rem;border-left:4px solid #ef4444;">
        <p style="font-size:.875rem;color:#991b1b;margin:0;line-height:1.6;">Pendaftaran Anda ditolak oleh admin. Silakan hubungi panitia PPDB untuk informasi lebih lanjut.</p>
    </div>
    @endif

    <div style="display:flex;gap:.875rem;flex-wrap:wrap;justify-content:center;">
        <a href="{{ route('siswa.dashboard') }}" class="btn-outline" style="min-width:140px;text-align:center;">🏠 Dashboard</a>
        @if($belumVerif)
        <a href="{{ route('siswa.pendaftaran') }}" class="btn-primary" style="min-width:140px;text-align:center;">📋 Form Pendaftaran</a>
        @elseif(in_array($s, ['siap_diumumkan','gugur','tidak_mengikuti_ujian','sudah_ujian','siap_finalisasi']))
        <a href="{{ route('siswa.hasil') }}" class="btn-primary" style="min-width:140px;text-align:center;background:linear-gradient(135deg,#f59e0b,#d97706);">📢 Lihat Hasil Seleksi</a>
        @endif
    </div>
</div>
@endif

</div>

@if($bisaUjian && $ujianAda && ($selesaiTs || $mulaiTs))
<script>
    // Only run countdown when the countdown elements are on the page
    const countdownEl = document.getElementById('cd-days');
    if (countdownEl) {
        const targetTs = {{ $periodeAktif ? $selesaiTs : $mulaiTs }};
        function updateCountdown() {
            const now = new Date().getTime();
            const diff = targetTs - now;
            if (diff <= 0) {
                // Prevent infinite reload: only reload once
                const reloadKey = 'cbt_reloaded_' + targetTs;
                if (!sessionStorage.getItem(reloadKey)) {
                    sessionStorage.setItem(reloadKey, '1');
                    location.reload();
                }
                // Show zeros instead of keep reloading
                ['days','hours','mins','secs'].forEach(id => {
                    const el = document.getElementById('cd-' + id);
                    if (el) el.textContent = '00';
                });
                return;
            }
            const days  = Math.floor(diff / (1000*60*60*24));
            const hours = Math.floor((diff % (1000*60*60*24)) / (1000*60*60));
            const mins  = Math.floor((diff % (1000*60*60)) / (1000*60));
            const secs  = Math.floor((diff % (1000*60)) / 1000);
            const pad = n => String(n).padStart(2,'0');
            ['days','hours','mins','secs'].forEach((id,i) => {
                const el = document.getElementById('cd-' + id);
                if (el) el.textContent = pad([days,hours,mins,secs][i]);
            });
        }
        updateCountdown();
        setInterval(updateCountdown, 1000);
    }
</script>
@endif
@endsection
