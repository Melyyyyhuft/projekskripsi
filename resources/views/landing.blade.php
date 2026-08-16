@extends('layouts.app')
@section('title', 'Beranda — PPDB Online SMK Mitra Bintaro')

@section('content')
@php
    $tahunAjaran  = $settings['tahun_ajaran'] ?? '2026/2027';
    $tglBukaRaw   = $settings['tgl_buka'] ?? null;
    $tglTutupRaw  = $settings['tgl_tutup'] ?? null;
    $linkWa       = $settings['link_wa'] ?? 'https://wa.me/628119999999';
    $formatBuka   = $tglBukaRaw  ? \Carbon\Carbon::parse($tglBukaRaw)->translatedFormat('d F Y')  : 'Segera Diumumkan';
    $formatTutup  = $tglTutupRaw ? \Carbon\Carbon::parse($tglTutupRaw)->translatedFormat('d F Y') : 'Segera Diumumkan';
@endphp

<style>
    /* ======= HERO ======= */
    .hero-premium {
        min-height: 88vh;
        margin-top: 72px;
        display: flex;
        align-items: center;
        background: radial-gradient(ellipse at top right, #eff6ff 0%, #ffffff 60%);
        overflow: hidden;
        position: relative;
    }
    .hero-container { width: 100%; max-width: 1400px; margin: 0 auto; display: flex; padding: 0; }
    .hero-left {
        flex: 1.1;
        padding: 3.5rem 2rem 3.5rem 5rem;
        z-index: 10;
        display: flex; flex-direction: column; justify-content: center;
    }
    .hero-right { flex: 1.2; position: relative; height: 620px; margin-left: -120px; }
    .hero-img {
        width: 100%; height: 100%; object-fit: cover;
        mask-image: linear-gradient(to left, black 65%, transparent 100%);
        -webkit-mask-image: linear-gradient(to left, black 65%, transparent 100%);
    }
    .hero-status-badge {
        display: inline-flex; align-items: center; gap: 0.5rem;
        padding: 0.4rem 1rem; border-radius: 999px; font-size: 0.8rem; font-weight: 700;
        margin-bottom: 1.25rem; width: fit-content; letter-spacing: 0.2px;
    }
    .badge-open  { background:#ecfdf5; color:#059669; border:1px solid #a7f3d0; }
    .badge-closed{ background:#fef2f2; color:#dc2626; border:1px solid #fecaca; }
    .status-dot { width:8px; height:8px; border-radius:50%; display:inline-block; }
    .dot-open   { background:#10b981; animation: pulse-dot 2s infinite; }
    .dot-closed { background:#ef4444; }
    @keyframes pulse-dot {
        0%   { box-shadow: 0 0 0 0 rgba(16,185,129,.7); }
        70%  { box-shadow: 0 0 0 7px rgba(16,185,129,0); }
        100% { box-shadow: 0 0 0 0 rgba(16,185,129,0); }
    }
    .hero-title-p {
        font-family:'Outfit',sans-serif;
        font-size: clamp(2.3rem, 4vw, 3.6rem);
        font-weight:900; color:#0f172a; line-height:1.15;
        margin-bottom:1rem; letter-spacing:-1px;
    }
    .hero-title-script { font-family:'Dancing Script',cursive; color:#1d4ed8; font-weight:700; }
    .hero-subtitle-p { font-size:1.05rem; color:#475569; line-height:1.65; margin-bottom:1.75rem; max-width:520px; font-weight:500; }
    .hero-cta-row { display:flex; gap:0.85rem; flex-wrap:wrap; align-items:center; margin-bottom:2rem; }
    .btn-daftar-now {
        background:linear-gradient(135deg,#0c42bb,#2563eb); color:white;
        padding:0.9rem 2.4rem; border-radius:12px; font-size:1.05rem; font-weight:800;
        text-decoration:none; display:inline-flex; align-items:center; gap:0.55rem;
        transition:all .3s cubic-bezier(.175,.885,.32,1.275);
        box-shadow:0 10px 25px -5px rgba(12,66,187,.35);
    }
    .btn-daftar-now:hover { background:linear-gradient(135deg,#093395,#0c42bb); transform:translateY(-3px) scale(1.02); color:white; box-shadow:0 15px 35px -5px rgba(12,66,187,.45); }
    .btn-outline-ghost {
        background:white; color:#1e293b; border:1.5px solid #e2e8f0;
        padding:0.85rem 1.6rem; border-radius:12px; font-size:0.95rem; font-weight:700;
        text-decoration:none; display:inline-flex; align-items:center; gap:0.5rem; transition:all .25s ease;
    }
    .btn-outline-ghost:hover { background:#f8fafc; border-color:#cbd5e1; color:#1d4ed8; transform:translateY(-2px); }
    .hero-period-badge {
        display:inline-flex; align-items:center; gap:0.6rem;
        background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px;
        padding:0.6rem 1rem; font-size:0.83rem; color:#475569; font-weight:600;
    }
    /* ======= SECTION SHARED ======= */
    .pg-section { padding:5rem 2rem; }
    .pg-section-alt { background:#f8fafc; }
    .pg-inner { max-width:1200px; margin:0 auto; }
    .pg-tag { display:inline-flex;align-items:center;gap:.4rem;font-size:.78rem;font-weight:800;text-transform:uppercase;letter-spacing:1px;color:#1d4ed8;background:#eff6ff;padding:.4rem 1rem;border-radius:999px;margin-bottom:.65rem; }
    .pg-h2 { font-family:'Outfit',sans-serif;font-size:clamp(1.8rem,3vw,2.4rem);font-weight:900;color:#0f172a;margin:0 0 .75rem;letter-spacing:-.5px; }
    .pg-desc { font-size:1rem;color:#64748b;line-height:1.6;margin:0; }
    .pg-grid-3 { display:grid; grid-template-columns:repeat(auto-fit,minmax(300px,1fr)); gap:1.75rem; margin-top:2.5rem; }
    .pg-card {
        background:white; border-radius:18px; border:1px solid #e2e8f0; padding:1.75rem;
        transition:all .3s ease; box-shadow:0 2px 4px rgba(0,0,0,.03);
    }
    .pg-card:hover { transform:translateY(-5px); box-shadow:0 16px 30px -5px rgba(0,0,0,.07); border-color:#cbd5e1; }

    @media (max-width:1024px) {
        .hero-premium { min-height:auto; }
        .hero-container { flex-direction:column; }
        .hero-left { padding:2.5rem 1.5rem; text-align:center; align-items:center; }
        .hero-subtitle-p { margin-left:auto; margin-right:auto; }
        .hero-cta-row { justify-content:center; }
        .hero-right { order:-1; height:280px; margin-left:0; width:100%; }
        .hero-img { mask-image:none; -webkit-mask-image:none; }
    }
</style>

{{-- ==================== HERO ==================== --}}
<section class="hero-premium">
    <div class="hero-container">
        <div class="hero-left animate-slide-up">

            {{-- Status Badge --}}
            <div class="hero-status-badge {{ $isPPDBOpen ? 'badge-open' : 'badge-closed' }}">
                <span class="status-dot {{ $isPPDBOpen ? 'dot-open' : 'dot-closed' }}"></span>
                PPDB {{ $tahunAjaran }} —
                <strong>{{ $isPPDBOpen ? 'PENDAFTARAN DIBUKA' : 'PENDAFTARAN DITUTUP' }}</strong>
            </div>

            <h1 class="hero-title-p">
                Langkah Awal<br>Meraih Masa Depan<br>
                <span class="hero-title-script">Bersama SMK Mitra Bintaro</span>
            </h1>

            <p class="hero-subtitle-p">
                PPDB Online yang <strong>mudah, cepat,</strong> dan <strong>transparan</strong> — daftarkan diri Anda sekarang untuk menjadi bagian dari generasi unggul SMK Mitra Bintaro.
            </p>

            <div class="hero-cta-row">
                @if($isPPDBOpen)
                    <a href="{{ url('/register') }}" class="btn-daftar-now">
                        <i class="fa-solid fa-user-plus"></i> Daftar Sekarang
                    </a>
                @else
                    <button class="btn-daftar-now" style="background:#94a3b8;cursor:not-allowed;" disabled>
                        <i class="fa-solid fa-lock"></i> Pendaftaran Ditutup
                    </button>
                @endif
                <a href="{{ route('public.periode') }}" class="btn-outline-ghost">
                    <i class="fa-solid fa-calendar-days" style="color:#1d4ed8;"></i> Lihat Jadwal PPDB
                </a>
            </div>

            {{-- Periode Info Mini --}}
            <div class="hero-period-badge">
                <i class="fa-solid fa-clock" style="color:#1d4ed8;"></i>
                <span>Periode: <strong>{{ $formatBuka }}</strong> s.d. <strong>{{ $formatTutup }}</strong></span>
                <a href="{{ route('public.periode') }}" style="color:#1d4ed8;font-size:0.78rem;margin-left:.35rem;">Selengkapnya →</a>
            </div>
        </div>

        <div class="hero-right">
            <img src="{{ asset('images/hero_school.png') }}" alt="SMK Mitra Bintaro" class="hero-img">
        </div>
    </div>
</section>

{{-- ==================== KEUNGGULAN SEKOLAH & SISTEM + CTA ==================== --}}
<section class="pg-section" style="background:#ffffff;">
    <div class="pg-inner">
        <div style="text-align:center;margin-bottom:2.75rem;">
            <span class="pg-tag"><i class="fa-solid fa-star"></i> Keunggulan Sekolah</span>
            <h2 class="pg-h2">Kenapa Memilih SMK Mitra Bintaro?</h2>
            <p class="pg-desc" style="max-width:620px;margin:0 auto;">Kami menawarkan sistem dan lingkungan pendidikan terbaik untuk mendukung perkembangan akademik dan karakter siswa.</p>
        </div>

        <div class="pg-grid-3" style="margin-bottom:4rem;">
            <div class="pg-card" style="text-align:center;">
                <div style="width:56px;height:56px;background:#eff6ff;color:#1d4ed8;border-radius:16px;display:flex;align-items:center;justify-content:center;font-size:1.5rem;margin:0 auto 1.25rem;">
                    <i class="fa-solid fa-bolt"></i>
                </div>
                <h3 style="font-size:1.15rem;margin:0 0 .5rem;color:#0f172a;font-weight:800;">Pendaftaran Cepat</h3>
                <p style="color:#64748b;font-size:0.9rem;margin:0;line-height:1.55;">Proses registrasi yang mudah dan cepat dari mana saja tanpa perlu antre di sekolah.</p>
            </div>
            <div class="pg-card" style="text-align:center;">
                <div style="width:56px;height:56px;background:#ecfdf5;color:#059669;border-radius:16px;display:flex;align-items:center;justify-content:center;font-size:1.5rem;margin:0 auto 1.25rem;">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <h3 style="font-size:1.15rem;margin:0 0 .5rem;color:#0f172a;font-weight:800;">Sistem Transparan</h3>
                <p style="color:#64748b;font-size:0.9rem;margin:0;line-height:1.55;">Hasil seleksi dapat dipantau secara real-time dan dijamin objektivitasnya.</p>
            </div>
            <div class="pg-card" style="text-align:center;">
                <div style="width:56px;height:56px;background:#fef3c7;color:#d97706;border-radius:16px;display:flex;align-items:center;justify-content:center;font-size:1.5rem;margin:0 auto 1.25rem;">
                    <i class="fa-solid fa-laptop-code"></i>
                </div>
                <h3 style="font-size:1.15rem;margin:0 0 .5rem;color:#0f172a;font-weight:800;">Ujian Berbasis CBT</h3>
                <p style="color:#64748b;font-size:0.9rem;margin:0;line-height:1.55;">Tes kemampuan akademik menggunakan Computer Based Test yang canggih dan akurat.</p>
            </div>
        </div>

        {{-- CTA disatukan di bawah keunggulan --}}
        <div style="background:linear-gradient(135deg,#0f172a,#1e40af);border-radius:20px;padding:3rem 2.5rem;text-align:center;box-shadow:0 15px 30px -5px rgba(15,23,42,.25);">
            <h2 style="font-family:'Outfit',sans-serif;font-size:clamp(1.6rem,3vw,2.2rem);font-weight:900;color:white;margin:0 0 .75rem;letter-spacing:-.4px;">Siap Bergabung Bersama SMK Mitra Bintaro?</h2>
            <p style="font-size:1rem;color:rgba(255,255,255,.85);margin:0 auto 2rem;max-width:520px;line-height:1.6;">Jangan lewatkan kesempatan untuk menjadi bagian dari komunitas belajar terbaik bersama ribuan siswa berprestasi.</p>
            @if($isPPDBOpen)
                <a href="{{ url('/register') }}" style="background:white;color:#0f172a;padding:.9rem 2.5rem;border-radius:12px;font-weight:900;font-size:1.05rem;text-decoration:none;display:inline-flex;align-items:center;gap:.6rem;box-shadow:0 8px 20px rgba(0,0,0,.2);transition:all .25s ease;">
                    <i class="fa-solid fa-user-plus" style="color:#1d4ed8;"></i> Daftar Sekarang
                </a>
            @else
                <button style="background:rgba(255,255,255,.15);color:rgba(255,255,255,.6);padding:.9rem 2.5rem;border-radius:12px;font-weight:800;font-size:1.05rem;border:1px solid rgba(255,255,255,.25);cursor:not-allowed;" disabled>
                    Pendaftaran Belum Dibuka
                </button>
            @endif
        </div>
    </div>
</section>


{{-- ==================== RINGKASAN KUOTA ==================== --}}
<section class="pg-section pg-section-alt">
    <div class="pg-inner">
        <div style="text-align:center;margin-bottom:2.5rem;">
            <span class="pg-tag"><i class="fa-solid fa-graduation-cap"></i> Program Keahlian</span>
            <h2 class="pg-h2">Ringkasan Kuota Jurusan</h2>
            <p class="pg-desc">Pilih jurusan yang sesuai minat dan bakat Anda. Pantau sisa kuota secara real-time.</p>
        </div>
        <div class="pg-grid-3">
            @forelse($jurusans as $jurusan)
            @php
                $kuota = $jurusan->kuota ?? 0;
                $sisa  = $jurusan->sisa_kuota ?? 0;
                $persen = $kuota > 0 ? min(100, round((($kuota-$sisa)/$kuota)*100)) : 0;
                $warna  = $sisa <= 0 ? '#ef4444' : ($sisa <= 15 ? '#f59e0b' : '#10b981');
                $label  = $sisa <= 0 ? 'Penuh' : ($sisa <= 15 ? 'Hampir Penuh' : 'Tersedia');
            @endphp
            <div class="pg-card">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:1rem;">
                    <div style="width:44px;height:44px;border-radius:12px;background:linear-gradient(135deg,#1e40af,#3b82f6);display:flex;align-items:center;justify-content:center;color:white;font-size:1.2rem;">
                        <i class="fa-solid fa-graduation-cap"></i>
                    </div>
                    <span style="font-size:0.75rem;font-weight:700;padding:0.3rem 0.7rem;border-radius:999px;background:{{ $sisa<=0?'#fee2e2':($sisa<=15?'#fef3c7':'#ecfdf5') }};color:{{ $sisa<=0?'#991b1b':($sisa<=15?'#92400e':'#065f46') }};">{{ $label }}</span>
                </div>
                <h3 style="font-size:1.1rem;font-weight:800;color:#0f172a;margin:0 0 .35rem;">{{ $jurusan->nama }}</h3>
                <div style="display:flex;justify-content:space-between;font-size:0.8rem;color:#64748b;margin-bottom:.5rem;">
                    <span>Kuota: <strong>{{ $kuota }}</strong></span>
                    <span>Sisa: <strong style="color:{{ $warna }};">{{ $sisa }}</strong></span>
                </div>
                <div style="width:100%;height:7px;background:#e2e8f0;border-radius:999px;overflow:hidden;">
                    <div style="height:100%;border-radius:999px;background:{{ $warna }};width:{{ $persen }}%;transition:width .8s ease;"></div>
                </div>
                <div style="font-size:0.75rem;color:#94a3b8;margin-top:.4rem;">{{ $persen }}% terisi</div>
            </div>
            @empty
            <div class="pg-card" style="grid-column:1/-1;text-align:center;padding:3rem;">
                <p style="color:#94a3b8;">Belum ada data jurusan.</p>
            </div>
            @endforelse
        </div>
        <div style="text-align:center;margin-top:2rem;">
            <a href="{{ route('public.jurusan') }}" class="btn-outline-ghost">
                <i class="fa-solid fa-table-list"></i> Lihat Detail & Semua Jurusan
            </a>
        </div>
    </div>
</section>

{{-- ==================== ALUR RINGKAS ==================== --}}
<section class="pg-section">
    <div class="pg-inner">
        <div style="text-align:center;margin-bottom:2.5rem;">
            <span class="pg-tag"><i class="fa-solid fa-route"></i> Alur Pendaftaran</span>
            <h2 class="pg-h2">Cara Mendaftar PPDB</h2>
            <p class="pg-desc">4 langkah mudah untuk bergabung dengan SMK Mitra Bintaro.</p>
        </div>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:1.5rem;">
            @foreach([
                ['num'=>1,'icon'=>'fa-user-plus','title'=>'Buat Akun','desc'=>'Registrasi dengan nama dan email aktif.','color'=>'#eff6ff','tcolor'=>'#1d4ed8'],
                ['num'=>2,'icon'=>'fa-file-circle-check','title'=>'Isi & Upload Berkas','desc'=>'Lengkapi biodata, nilai rapor, dan unggah dokumen.','color'=>'#ecfdf5','tcolor'=>'#059669'],
                ['num'=>3,'icon'=>'fa-laptop-code','title'=>'Ikuti Ujian CBT','desc'=>'Kerjakan tes seleksi secara online.','color'=>'#fef3c7','tcolor'=>'#d97706'],
                ['num'=>4,'icon'=>'fa-circle-check','title'=>'Cek Hasil','desc'=>'Pantau pengumuman kelulusan di akun Anda.','color'=>'#f0fdf4','tcolor'=>'#15803d'],
            ] as $step)
            <div class="pg-card" style="text-align:center;padding:1.75rem 1.25rem;">
                <div style="width:50px;height:50px;border-radius:50%;background:{{ $step['color'] }};color:{{ $step['tcolor'] }};display:flex;align-items:center;justify-content:center;font-size:1.2rem;margin:0 auto 1rem;border:2px solid {{ $step['tcolor'] }}20;">
                    <i class="fa-solid {{ $step['icon'] }}"></i>
                </div>
                <div style="font-size:0.7rem;font-weight:800;color:{{ $step['tcolor'] }};text-transform:uppercase;letter-spacing:.5px;margin-bottom:.4rem;">Langkah {{ $step['num'] }}</div>
                <h3 style="font-size:1.05rem;font-weight:800;color:#0f172a;margin:0 0 .4rem;">{{ $step['title'] }}</h3>
                <p style="font-size:0.85rem;color:#64748b;margin:0;line-height:1.5;">{{ $step['desc'] }}</p>
            </div>
            @endforeach
        </div>
        <div style="text-align:center;margin-top:2rem;">
            <a href="{{ route('public.alur') }}" class="btn-outline-ghost">
                <i class="fa-solid fa-arrow-right"></i> Lihat Alur Lengkap
            </a>
        </div>
    </div>
</section>

{{-- ==================== INFO BANTUAN ==================== --}}
<section class="pg-section pg-section-alt">
    <div class="pg-inner">
        <div style="background:linear-gradient(135deg,#0f172a,#1e40af);border-radius:20px;padding:3rem;display:grid;grid-template-columns:1fr auto;gap:2rem;align-items:center;box-shadow:0 15px 30px -5px rgba(15,23,42,.25);">
            <div>
                <span style="background:rgba(255,255,255,.15);color:white;padding:.35rem .85rem;border-radius:8px;font-size:.78rem;font-weight:700;display:inline-block;margin-bottom:.85rem;">
                    <i class="fa-solid fa-headset"></i> LAYANAN BANTUAN PPDB
                </span>
                <h2 style="font-family:'Outfit',sans-serif;font-size:clamp(1.5rem,2.5vw,2rem);color:white;font-weight:900;margin:0 0 .75rem;">Mengalami Kendala Pendaftaran?</h2>
                <p style="color:rgba(255,255,255,.85);font-size:.95rem;line-height:1.6;margin:0 0 1.25rem;max-width:560px;">
                    Bagi calon siswa atau orang tua yang mengalami kendala, dapat datang langsung ke <strong style="color:#93c5fd;">Ruang PPDB</strong> untuk mendapat pendampingan petugas. Kedatangan ke sekolah adalah layanan bantuan — bukan jalur offline tersendiri.
                </p>
                <div style="display:flex;gap:1rem;flex-wrap:wrap;">
                    <a href="{{ $linkWa }}" target="_blank" style="background:#10b981;color:white;padding:.6rem 1.25rem;border-radius:10px;font-weight:700;font-size:.88rem;text-decoration:none;display:inline-flex;align-items:center;gap:.4rem;">
                        <i class="fa-brands fa-whatsapp"></i> Chat WhatsApp
                    </a>
                    <a href="{{ route('public.kontak') }}" style="background:rgba(255,255,255,.15);color:white;padding:.6rem 1.25rem;border-radius:10px;font-weight:700;font-size:.88rem;text-decoration:none;display:inline-flex;align-items:center;gap:.4rem;border:1px solid rgba(255,255,255,.25);">
                        <i class="fa-solid fa-location-dot"></i> Lihat Lokasi
                    </a>
                </div>
            </div>
            <div style="text-align:center;opacity:.9;">
                <i class="fa-solid fa-headset" style="font-size:5rem;color:rgba(255,255,255,.2);"></i>
            </div>
        </div>
    </div>
</section>

@endsection
