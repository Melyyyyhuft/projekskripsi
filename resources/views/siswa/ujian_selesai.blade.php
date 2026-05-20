@extends('layouts.siswa')
@section('title', 'Hasil Ujian CBT Anda')

@section('content')
@php
    $skor = $hasilUjian->skor;
    if ($skor >= 85) { $grade='A'; $gradeBg='linear-gradient(135deg,#f59e0b,#d97706)'; $gradeLabel='Sangat Baik'; $gradeColor='#fff'; }
    elseif ($skor >= 75) { $grade='B'; $gradeBg='linear-gradient(135deg,#10b981,#059669)'; $gradeLabel='Baik'; $gradeColor='#fff'; }
    elseif ($skor >= 60) { $grade='C'; $gradeBg='linear-gradient(135deg,#3b82f6,#1d4ed8)'; $gradeLabel='Cukup'; $gradeColor='#fff'; }
    else { $grade='D'; $gradeBg='linear-gradient(135deg,#ef4444,#dc2626)'; $gradeLabel='Perlu Ditingkatkan'; $gradeColor='#fff'; }

    // Info periode ujian
    $periodeUjian = null;
    if ($ujian && $ujian->jadwal_mulai && $ujian->jadwal_selesai) {
        $mulai   = \Carbon\Carbon::parse($ujian->jadwal_mulai);
        $selesai = \Carbon\Carbon::parse($ujian->jadwal_selesai);
        // Format: "10–13 April 2026"
        if ($mulai->format('M Y') === $selesai->format('M Y')) {
            $periodeUjian = $mulai->format('d') . '–' . $selesai->isoFormat('D MMMM YYYY');
        } else {
            $periodeUjian = $mulai->isoFormat('D MMMM') . ' – ' . $selesai->isoFormat('D MMMM YYYY');
        }
    }
    $durasiMenit = $ujian->durasi_menit ?? 75;
@endphp

<style>
@keyframes fadeInUp { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }
.fade-up { animation: fadeInUp .5s ease forwards; }
.delay-1 { animation-delay:.1s; opacity:0; }
.delay-2 { animation-delay:.2s; opacity:0; }
</style>

{{-- Wrapper to prevent layout items from shrinking as flex items of .main-content --}}
<div style="display:flex; flex-direction:column; flex-shrink:0; width:100%;">

    {{-- ─── Hero Banner ─── --}}
    <div style="background:linear-gradient(135deg,#0f172a,#1e3a5f);border-radius:24px;padding:2.5rem;color:white;margin-bottom:1.5rem;display:flex;align-items:center;gap:2rem;flex-wrap:wrap;position:relative;overflow:hidden;flex-shrink:0;" class="fade-up">
        <div style="position:absolute;top:-50px;right:-40px;width:200px;height:200px;background:rgba(255,255,255,.04);border-radius:50%;"></div>
        <div style="width:90px;height:90px;border-radius:50%;background:{{ $gradeBg }};display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 8px 30px rgba(0,0,0,.3);">
            <span style="font-size:2.5rem;font-weight:900;color:{{ $gradeColor }};">{{ $grade }}</span>
        </div>
        <div style="flex:1;min-width:180px;">
            <div style="font-size:.75rem;font-weight:700;opacity:.7;text-transform:uppercase;letter-spacing:.1em;margin-bottom:.4rem;">Ujian CBT Selesai ✓</div>
            <h2 style="font-size:1.6rem;font-weight:900;margin:0 0 .4rem;">{{ $ujian->judul ?? 'Ujian CBT' }}</h2>
            <p style="opacity:.8;font-size:.9rem;margin:0;">{{ $gradeLabel }} — Ujian telah berhasil diselesaikan dan disimpan.</p>
        </div>
        <div style="text-align:center;flex-shrink:0;">
            <div style="font-size:3.5rem;font-weight:900;line-height:1;letter-spacing:-.02em;">{{ number_format($skor, 1) }}</div>
            <div style="font-size:.8rem;opacity:.7;margin-top:.25rem;text-transform:uppercase;letter-spacing:.06em;">Nilai CBT</div>
        </div>
    </div>

    {{-- ─── 2 Stats: Nilai CBT + Nilai Rapor ─── --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1.5rem;flex-shrink:0;" class="fade-up delay-1">

        <div style="background:white;border-radius:16px;padding:1.5rem;border:1px solid #e2e8f0;box-shadow:0 1px 4px rgba(0,0,0,.04);text-align:center;flex-shrink:0;">
            <div style="font-size:1.75rem;margin-bottom:.4rem;">📝</div>
            <div style="font-size:2.2rem;font-weight:900;color:var(--primary);line-height:1;">{{ number_format($skor, 1) }}</div>
            <div style="font-size:.78rem;color:#94a3b8;font-weight:600;margin-top:.4rem;text-transform:uppercase;letter-spacing:.04em;">Nilai Ujian CBT</div>
        </div>

        <div style="background:white;border-radius:16px;padding:1.5rem;border:1px solid #e2e8f0;box-shadow:0 1px 4px rgba(0,0,0,.04);text-align:center;flex-shrink:0;">
            <div style="font-size:1.75rem;margin-bottom:.4rem;">📚</div>
            <div style="font-size:2.2rem;font-weight:900;color:#8b5cf6;line-height:1;">{{ number_format($pendaftaran->nilai_rapor ?? 0, 1) }}</div>
            <div style="font-size:.78rem;color:#94a3b8;font-weight:600;margin-top:.4rem;text-transform:uppercase;letter-spacing:.04em;">Nilai Rapor</div>
        </div>

    </div>

    {{-- ─── Info Jalur Kelulusan ─── --}}
    <div class="glass-card fade-up delay-1" style="margin-bottom:1.5rem;flex-shrink:0;">
        <h4 style="color:#0f172a;margin:0 0 .5rem;font-size:.95rem;font-weight:700;">🏆 Ketentuan Jalur Kelulusan</h4>
        <p style="font-size:.82rem;color:#64748b;margin:0 0 1rem;">Penentuan jalur ditetapkan secara resmi oleh panitia berdasarkan nilai ujian CBT Anda.</p>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:.875rem;">
            <div style="background:#fffbeb;border:1.5px solid #fde68a;border-radius:14px;padding:1.25rem;text-align:center;">
                <div style="font-size:1.5rem;margin-bottom:.5rem;">⭐</div>
                <div style="font-weight:900;color:#d97706;font-size:1.05rem;margin-bottom:.3rem;">Jalur Unggulan</div>
                <div style="font-size:.82rem;color:#92400e;font-weight:600;">Skor Ujian CBT ≥ 85</div>
            </div>
            <div style="background:#f0fdf4;border:1.5px solid #86efac;border-radius:14px;padding:1.25rem;text-align:center;">
                <div style="font-size:1.5rem;margin-bottom:.5rem;">✅</div>
                <div style="font-weight:900;color:#059669;font-size:1.05rem;margin-bottom:.3rem;">Jalur Reguler</div>
                <div style="font-size:.82rem;color:#065f46;font-weight:600;">Skor Ujian CBT &lt; 85</div>
            </div>
        </div>
    </div>

    {{-- ─── Info Detail Ujian ─── --}}
    <div class="glass-card fade-up delay-2" style="margin-bottom:1.5rem;flex-shrink:0;">
        <h4 style="color:#0f172a;margin:0 0 1rem;font-size:.95rem;font-weight:700;">ℹ️ Informasi Sesi Ujian</h4>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:.875rem;">
            <div style="background:#f8fafc;border-radius:10px;padding:.875rem;">
                <div style="font-size:.72rem;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;margin-bottom:.3rem;">Nama Ujian</div>
                <div style="font-weight:700;color:#0f172a;font-size:.9rem;">{{ $ujian->judul ?? '—' }}</div>
            </div>
            <div style="background:#f8fafc;border-radius:10px;padding:.875rem;">
                <div style="font-size:.72rem;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;margin-bottom:.3rem;">Durasi Pengerjaan</div>
                <div style="font-weight:700;color:#0f172a;font-size:.9rem;">⏱️ {{ $durasiMenit }} menit</div>
            </div>
            <div style="background:#f8fafc;border-radius:10px;padding:.875rem;">
                <div style="font-size:.72rem;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;margin-bottom:.3rem;">Waktu Anda Selesai</div>
                <div style="font-weight:700;color:#0f172a;font-size:.9rem;">{{ $hasilUjian->created_at->format('d M Y, H:i') }}</div>
            </div>
            @if($periodeUjian)
            <div style="background:#eff6ff;border-radius:10px;padding:.875rem;border:1px solid #bfdbfe;">
                <div style="font-size:.72rem;color:#1e40af;text-transform:uppercase;letter-spacing:.06em;margin-bottom:.3rem;">📅 Periode Ujian (3 Hari)</div>
                <div style="font-weight:700;color:#1d4ed8;font-size:.9rem;">{{ $periodeUjian }}</div>
            </div>
            @else
            <div style="background:#eff6ff;border-radius:10px;padding:.875rem;border:1px solid #bfdbfe;">
                <div style="font-size:.72rem;color:#1e40af;text-transform:uppercase;letter-spacing:.06em;margin-bottom:.3rem;">📅 Periode Ujian</div>
                <div style="font-weight:700;color:#1d4ed8;font-size:.9rem;">Sesuai jadwal panitia</div>
            </div>
            @endif
            <div style="background:#d1fae5;border-radius:10px;padding:.875rem;">
                <div style="font-size:.72rem;color:#166534;text-transform:uppercase;letter-spacing:.06em;margin-bottom:.3rem;">Status Ujian</div>
                <div style="font-weight:700;color:#059669;font-size:.9rem;">✅ Sudah Mengikuti Ujian</div>
            </div>
            <div style="background:#fef3c7;border-radius:10px;padding:.875rem;border:1px solid #fde68a;">
                <div style="font-size:.72rem;color:#92400e;text-transform:uppercase;letter-spacing:.06em;margin-bottom:.3rem;">Catatan Penting</div>
                <div style="font-weight:700;color:#92400e;font-size:.85rem;">🔒 Ujian hanya 1 kali per akun</div>
            </div>
        </div>
    </div>

    {{-- ─── Tombol Navigasi ─── --}}
    <div style="display:flex;gap:1rem;flex-wrap:wrap;flex-shrink:0;margin-bottom:2rem;" class="fade-up delay-2">
        <a href="{{ route('siswa.dashboard') }}" class="btn-outline" style="flex:1;text-align:center;min-width:150px;padding:.875rem;">
            🏠 Kembali ke Dashboard
        </a>
        <a href="{{ route('siswa.hasil') }}" class="btn-primary" style="flex:1;text-align:center;min-width:150px;padding:.875rem;background:linear-gradient(135deg,#f59e0b,#d97706);">
            📢 Pantau Hasil Seleksi
        </a>
    </div>

</div>

@endsection
