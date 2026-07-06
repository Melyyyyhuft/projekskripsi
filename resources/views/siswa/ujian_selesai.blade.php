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



    {{-- ─── Info Detail Ujian: Premium Modern Grid ─── --}}
    <div class="glass-card fade-up delay-2" style="margin-bottom:2rem; padding:2rem;">
        <h4 style="color:#0f172a; margin: 0 0 1.5rem; font-size: 1.1rem; font-weight: 800; display: flex; align-items: center; gap: 0.75rem;">
            <i class="fa-solid fa-circle-info" style="color: var(--primary);"></i> Informasi Sesi Ujian
        </h4>
        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem; margin-bottom: 1.25rem;">
            
            <div style="background:#f8fafc; border-radius:14px; padding:1.25rem; border: 1px solid #e2e8f0;">
                <div style="display: flex; align-items: center; gap: 0.875rem;">
                    <div style="width: 38px; height: 38px; background: #eff6ff; color: #3b82f6; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem;">
                        <i class="fa-solid fa-file-lines"></i>
                    </div>
                    <div>
                        <p style="margin:0; font-size: 0.65rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em;">Nama Ujian</p>
                        <p style="margin:0; font-weight: 700; color: #1e293b; font-size: 0.95rem;">{{ $ujian->judul ?? '—' }}</p>
                    </div>
                </div>
            </div>

            <div style="background:#f8fafc; border-radius:14px; padding:1.25rem; border: 1px solid #e2e8f0;">
                <div style="display: flex; align-items: center; gap: 0.875rem;">
                    <div style="width: 38px; height: 38px; background: #fdf2f8; color: #db2777; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem;">
                        <i class="fa-solid fa-clock"></i>
                    </div>
                    <div>
                        <p style="margin:0; font-size: 0.65rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em;">Durasi Pengerjaan</p>
                        <p style="margin:0; font-weight: 700; color: #1e293b; font-size: 0.95rem;">{{ $durasiMenit }} menit</p>
                    </div>
                </div>
            </div>

            <div style="background:#f8fafc; border-radius:14px; padding:1.25rem; border: 1px solid #e2e8f0;">
                <div style="display: flex; align-items: center; gap: 0.875rem;">
                    <div style="width: 38px; height: 38px; background: #f0fdf4; color: #166534; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem;">
                        <i class="fa-solid fa-calendar-check"></i>
                    </div>
                    <div>
                        <p style="margin:0; font-size: 0.65rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em;">Waktu Selesai</p>
                        <p style="margin:0; font-weight: 700; color: #1e293b; font-size: 0.95rem;">{{ $hasilUjian->created_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Status Ujian: Memanjang (Lebih Kecil) --}}
        <div style="background:#ecfdf5; border-radius:12px; padding:0.85rem 1.15rem; border: 1px solid #d1fae5; display: flex; align-items: center; gap: 1rem; margin-bottom: 0.85rem;">
            <div style="width: 36px; height: 36px; background: #10b981; color: white; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1rem; flex-shrink: 0;">
                <i class="fa-solid fa-check-double"></i>
            </div>
            <div>
                <p style="margin:0; font-size: 0.6rem; font-weight: 800; color: #065f46; text-transform: uppercase; letter-spacing: 0.05em; line-height: 1;">Status Ujian</p>
                <p style="margin:0; font-weight: 800; color: #047857; font-size: 0.95rem;">✓ Sudah Mengikuti Ujian CBT</p>
            </div>
        </div>

        {{-- Catatan: Memanjang (Diseimbangkan dengan Status) --}}
        <div style="background:#fffcf0; border-radius:12px; padding:0.85rem 1.15rem; border: 1px solid #fef3c7; display: flex; align-items: center; gap: 1rem;">
            <div style="width: 36px; height: 36px; background: #f59e0b; color: white; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 0.9rem; flex-shrink: 0;">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <div>
                <p style="margin:0; font-size: 0.6rem; font-weight: 800; color: #92400e; text-transform: uppercase; letter-spacing: 0.05em; line-height: 1;">Informasi Penting</p>
                <p style="margin:0; font-size: 0.85rem; font-weight: 700; color: #92400e;">
                    Catatan: <span style="font-weight: 500;">Hanya dapat diikuti satu kali per akun.</span>
                </p>
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
