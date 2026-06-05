@extends('layouts.siswa')
@section('title', 'Hasil Seleksi PPDB')

@section('content')
<style>
    /* ─── Modern Premium Hasil View ─── */
    :root {
        --primary: #3b82f6;
        --success: #10b981;
        --danger: #ef4444;
        --warning: #f59e0b;
        --dark: #0f172a;
        --gray: #64748b;
    }

    .hasil-wrapper {
        display: flex;
        flex-direction: column;
        gap: 2rem;
        padding-bottom: 2rem;
        animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1);
    }

    @keyframes slideUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }

    .premium-card {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.5);
        border-radius: 32px;
        padding: 3rem;
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.03);
    }

    .hero-container {
        text-align: center;
        padding: 4rem 2rem;
        border-radius: 40px;
        position: relative;
        overflow: hidden;
        color: white;
        margin-bottom: 2rem;
    }

    .hero-container::before {
        content: ""; position: absolute; top: -100px; right: -100px;
        width: 300px; height: 300px; border-radius: 50%;
        background: rgba(255, 255, 255, 0.1); filter: blur(50px);
    }

    .bg-accepted { background: linear-gradient(135deg, #10b981, #059669); box-shadow: 0 20px 50px rgba(16, 185, 129, 0.2); }
    .bg-rejected { background: linear-gradient(135deg, #ef4444, #b91c1c); box-shadow: 0 20px 50px rgba(239, 68, 68, 0.2); }
    .bg-pending { background: linear-gradient(135deg, #475569, #1e293b); box-shadow: 0 20px 50px rgba(71, 85, 105, 0.2); }

    .status-badge {
        display: inline-flex;
        padding: 0.6rem 2rem;
        background: rgba(255, 255, 255, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 999px;
        font-weight: 900;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.2em;
        margin-bottom: 1.5rem;
        backdrop-filter: blur(10px);
    }

    .main-title { font-size: 4.5rem; font-weight: 950; letter-spacing: -3px; line-height: 1; margin: 0; }
    
    .stats-overlay {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 1.5rem;
        margin-top: 3.5rem;
    }
    .stat-item {
        background: rgba(0, 0, 0, 0.1);
        padding: 1.5rem;
        border-radius: 24px;
        border: 1px solid rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(5px);
        transition: transform 0.3s ease;
    }
    .stat-item:hover { transform: translateY(-5px); background: rgba(0,0,0,0.15); }
    .stat-lbl { font-size: 0.75rem; font-weight: 800; opacity: 0.8; text-transform: uppercase; margin-bottom: 0.5rem; }
    .stat-val { font-size: 1.5rem; font-weight: 900; }

    .btn-download {
        display: inline-flex;
        align-items: center;
        gap: 0.8rem;
        background: white;
        color: #059669;
        padding: 1.1rem 2.5rem;
        border-radius: 20px;
        font-weight: 900;
        text-decoration: none;
        margin-top: 3rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }
    .btn-download:hover { transform: scale(1.05); box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3); color: #064e3b; }

    .action-step {
        background: white;
        padding: 2rem;
        border-radius: 28px;
        display: flex;
        gap: 1.5rem;
        align-items: flex-start;
        transition: transform 0.3s ease;
        border: 1px solid #f1f5f9;
    }
    .action-step:hover { transform: translateX(10px); border-color: #e2e8f0; }
    .step-icon {
        width: 54px; height: 54px; border-radius: 16px; background: #f8fafc;
        display: flex; align-items: center; justify-content: center; font-size: 1.5rem;
    }
</style>

<div class="hasil-wrapper">
    @if(!$hasil || !$hasil->is_finalisasi)
        {{-- CASE: MENUNGGU --}}
        <div class="premium-card" style="text-align: center; padding: 6rem 2rem;">
            <div style="width: 120px; height: 120px; background: #f8fafc; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 2.5rem; font-size: 3.5rem; color: #cbd5e1; border: 3px dashed #e2e8f0;">
                <i class="fa-solid fa-hourglass-half"></i>
            </div>
            <h2 style="font-size: 2.25rem; font-weight: 950; color: var(--dark); margin: 0 0 1rem; letter-spacing: -1px;">Hasil Belum Diumumkan</h2>
            <p style="color: var(--gray); font-size: 1.1rem; max-width: 600px; margin: 0 auto 3rem; line-height: 1.8;">
                Tim seleksi sedang melakukan finalisasi data dan penempatan kelas. Hasil resmi akan segera dipublikasikan melalui dashboard ini. Mohon bersabar dan cek secara berkala.
            </p>
            <a href="{{ route('siswa.dashboard') }}" style="font-weight: 800; color: var(--primary); text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 0.6rem;">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard
            </a>
        </div>
    @else
        @php
            $isAccepted = $hasil->kategori_kelulusan === 'DITERIMA';
            $isNoCBT = $hasil->kategori_kelulusan === 'TIDAK HADIR CBT';
            $heroClass = $isAccepted ? 'bg-accepted' : ($isNoCBT ? 'bg-rejected' : 'bg-rejected');
        @endphp

        <div class="hero-container {{ $heroClass }}">
            <div class="status-badge">
                {{ $isAccepted ? 'Congratulations' : 'Result Announcement' }}
            </div>
            <h2 class="main-title">
                {{ $isAccepted ? 'DITERIMA' : ($isNoCBT ? 'TIDAK HADIR CBT' : 'TIDAK DITERIMA') }}
            </h2>
            
            <p style="max-width: 750px; margin: 2rem auto 0; font-size: 1.15rem; line-height: 1.8; opacity: 0.95; font-weight: 500;">
                @if($isAccepted)
                    Selamat! Berdasarkan hasil seleksi akumulatif, Anda dinyatakan <strong>Lolos Seleksi</strong> dan berhak menjadi bagian dari keluarga besar {{ $settings['nama_sekolah'] ?? 'Sekolah Kami' }}.
                @elseif($isNoCBT)
                    Mohon maaf, Anda dinyatakan <strong>Tidak Lolos Seleksi</strong> karena tercatat tidak mengikuti tahapan ujian CBT (Computer Based Test) sesuai jadwal yang telah ditentukan.
                @else
                    Terima kasih atas antusiasme Anda. Mohon maaf, berdasarkan kuota dan skor akhir, Anda dinyatakan <strong>Belum Berhasil</strong> dalam seleksi PPDB tahun ini.
                @endif
            </p>

            <div class="stats-overlay">
                <div class="stat-item">
                    <div class="stat-lbl">Skor Akhir</div>
                    <div class="stat-val">{{ number_format($hasil->skor_akhir, 2) }}</div>
                </div>
                <div class="stat-item">
                    <div class="stat-lbl">Penempatan</div>
                    <div class="stat-val">{{ $hasil->penempatan_kelas ?? '-' }}</div>
                </div>
                <div class="stat-item">
                    <div class="stat-lbl">Jurusan</div>
                    <div class="stat-val">{{ $pendaftaran->jurusan->nama }}</div>
                </div>
            </div>

            {{-- Small Ketentuan Info --}}
            <div style="margin-top: 1.5rem; display: flex; justify-content: center; gap: 1rem; flex-wrap: wrap; opacity: 0.8; font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">
                <span style="background: rgba(255,255,255,0.1); padding: 0.4rem 0.8rem; border-radius: 8px; border: 1px solid rgba(255,255,255,0.1);">
                    🏆 Skor ≥ 70 : Unggulan
                </span>
                <span style="background: rgba(255,255,255,0.1); padding: 0.4rem 0.8rem; border-radius: 8px; border: 1px solid rgba(255,255,255,0.1);">
                    ✅ Skor < 70 : Reguler
                </span>
            </div>

            @if($isAccepted)
                <a href="{{ route('siswa.hasil.download') }}" class="btn-download">
                    <i class="fa-solid fa-file-pdf"></i> Download Surat Kelulusan (PDF)
                </a>
            @endif
        </div>

        @if($isAccepted)
            <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                <div class="action-step">
                    <div class="step-icon" style="color:var(--primary);"><i class="fa-solid fa-print"></i></div>
                    <div>
                        <h4 style="margin:0 0 0.5rem; font-weight:900; color:var(--dark);">Cetak Bukti</h4>
                        <p style="margin:0; font-size:0.85rem; color:var(--gray); line-height:1.6;">Gunakan file PDF yang telah didownload sebagai bukti otentik kelulusan pendaftaran.</p>
                    </div>
                </div>
                <div class="action-step">
                    <div class="step-icon" style="color:var(--warning);"><i class="fa-solid fa-calendar-check"></i></div>
                    <div>
                        <h4 style="margin:0 0 0.5rem; font-weight:900; color:var(--dark);">Daftar Ulang</h4>
                        <p style="margin:0; font-size:0.85rem; color:var(--gray); line-height:1.6;">Segera lakukan proses daftar ulang ke sekolah sesuai periode yang ditentukan.</p>
                    </div>
                </div>
            </div>
        @else
            @if($hasil->alasan_penolakan)
                <div class="premium-card" style="border-left: 8px solid var(--danger); padding: 2rem 3rem;">
                    <h4 style="margin:0 0 1rem; color:var(--danger); font-weight:900; text-transform:uppercase; font-size:0.8rem; letter-spacing:0.1em;">Catatan Dari Panitia:</h4>
                    <p style="margin:0; font-style:italic; color:var(--dark); font-weight:700; font-size:1.1rem; line-height:1.6;">"{{ $hasil->alasan_penolakan }}"</p>
                </div>
            @endif
            <div style="text-align:center;">
                <p style="color:var(--gray); font-weight:600;">Sukses untuk pendidikan Anda selanjutnya!</p>
                <a href="{{ route('siswa.dashboard') }}" style="color:var(--primary); font-weight:800; text-decoration:none;">Kembali ke Dashboard</a>
            </div>
        @endif
    @endif
</div>
@endsection
