@extends('layouts.siswa')
@section('title', 'Hasil Seleksi')

@section('content')
<style>
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    .fade-up { animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    
    .glass-card { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.5); border-radius: 30px; box-shadow: 0 10px 40px rgba(0, 0, 0, 0.05); padding: 2.5rem; }
    
    .status-hero { text-align: center; padding: 3rem 1.5rem; border-radius: 24px; margin-bottom: 2rem; position: relative; overflow: hidden; }
    .status-hero::before { content: ""; position: absolute; top: -50px; right: -50px; width: 150px; height: 150px; border-radius: 50%; background: rgba(255,255,255,0.1); }
    
    .hero-accepted { background: linear-gradient(135deg, #10b981, #059669); color: white; }
    .hero-rejected { background: linear-gradient(135deg, #ef4444, #b91c1c); color: white; }
    .hero-waiting { background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: white; }
    
    .result-score { font-size: 3.5rem; font-weight: 950; letter-spacing: -2px; line-height: 1; margin: 1rem 0; }
    .result-label { font-size: 0.9rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em; opacity: 0.9; }
    
    .info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-top: 2rem; }
    .info-item { background: rgba(255,255,255,0.1); padding: 1.25rem; border-radius: 18px; border: 1px solid rgba(255,255,255,0.2); }
    .info-lbl { font-size: 0.75rem; font-weight: 700; opacity: 0.8; text-transform: uppercase; margin-bottom: 0.25rem; }
    .info-val { font-size: 1.1rem; font-weight: 800; }
    
    .btn-download { display: inline-flex; align-items: center; gap: 0.75rem; background: white; color: #059669; padding: 1rem 2rem; border-radius: 16px; font-weight: 800; text-decoration: none; margin-top: 2rem; transition: all 0.3s; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
    .btn-download:hover { transform: translateY(-3px); box-shadow: 0 8px 25px rgba(0,0,0,0.2); filter: brightness(1.05); }
</style>

<div class="fade-up">
    <div style="margin-bottom: 2.5rem; text-align: center;">
        <h1 style="font-size: 2rem; font-weight: 900; color: #0f172a; margin: 0 0 0.5rem; letter-spacing: -0.02em;">📢 Pengumuman Hasil Seleksi</h1>
        <p style="color: #64748b; font-size: 1.1rem;">Hasil resmi seleksi Penerimaan Peserta Didik Baru.</p>
    </div>

    @if(!$hasil || !$hasil->is_finalisasi)
        {{-- Case: Results not published yet --}}
        <div class="glass-card" style="text-align: center; padding: 5rem 2rem;">
            <div style="font-size: 4rem; margin-bottom: 1.5rem;">⏳</div>
            <h2 style="font-size: 1.75rem; font-weight: 900; color: #1e293b; margin: 0 0 1rem;">Belum Ada Pengumuman</h2>
            <p style="color: #64748b; font-size: 1.1rem; max-width: 500px; margin: 0 auto; line-height: 1.6;">
                Panitia sedang melakukan tahap seleksi dan penempatan. Hasil akan diumumkan secara serentak melalui halaman ini. Silakan cek kembali dalam waktu dekat.
            </p>
            <div style="margin-top: 2.5rem;">
                <a href="{{ route('siswa.dashboard') }}" style="color: #3b82f6; font-weight: 700; font-size: 0.95rem; text-decoration: none;">🏠 Kembali ke Dashboard</a>
            </div>
        </div>
    @else
        @php
            $isAccepted = $hasil->kategori_kelulusan === 'DITERIMA';
            $isGugur = $hasil->kategori_kelulusan === 'GUGUR';
        @endphp

        @if($isAccepted)
            {{-- Case: Accepted --}}
            <div class="status-hero hero-accepted">
                <div style="font-size: 1rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.2em; margin-bottom: 1rem;">SELAMAT! ANDA DINYATAKAN</div>
                <h2 style="font-size: 3rem; font-weight: 950; margin: 0; line-height: 1;">DITERIMA</h2>
                
                <div style="max-width: 800px; margin: 2rem auto 0;">
                    <p style="font-size: 1.1rem; opacity: 0.9; font-weight: 500; line-height: 1.8;">
                        Berdasarkan hasil penilaian raport, tes CBT, dan prestasi yang Anda kumpulkan, Anda dinyatakan memenuhi kriteria untuk bergabung dengan <strong>{{ $settings['nama_sekolah'] ?? 'Sekolah Kami' }}</strong>.
                    </p>
                    
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-lbl">Skor Akhir</div>
                            <div class="info-val">{{ number_format($hasil->skor_akhir, 2) }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-lbl">Penempatan Kelas</div>
                            <div class="info-val">✨ {{ $hasil->penempatan_kelas }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-lbl">Jurusan Pilihan</div>
                            <div class="info-val">{{ $pendaftaran->jurusan->nama }}</div>
                        </div>
                    </div>

                    <a href="{{ route('siswa.hasil.download') }}" class="btn-download">
                        📄 Download Surat Hasil (PDF)
                    </a>
                </div>
            </div>

            <div class="glass-card" style="margin-top: 2rem;">
                <h3 style="font-size: 1.1rem; font-weight: 800; color: #0f172a; margin: 0 0 1.25rem;">📝 Langkah Selanjutnya</h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">
                    <div style="background: #f8fafc; padding: 1.5rem; border-radius: 20px;">
                        <span style="font-size: 1.5rem; display: block; margin-bottom: 0.75rem;">📥</span>
                        <div style="font-weight: 800; color: #1e293b; margin-bottom: 0.4rem;">Cetak Bukti</div>
                        <p style="font-size: 0.85rem; color: #64748b; margin: 0; line-height: 1.6;">Simpan/cetak surat hasil seleksi untuk persyaratan daftar ulang.</p>
                    </div>
                    <div style="background: #f8fafc; padding: 1.5rem; border-radius: 20px;">
                        <span style="font-size: 1.5rem; display: block; margin-bottom: 0.75rem;">📅</span>
                        <div style="font-weight: 800; color: #1e293b; margin-bottom: 0.4rem;">Daftar Ulang</div>
                        <p style="font-size: 0.85rem; color: #64748b; margin: 0; line-height: 1.6;">Silakan datang ke sekolah sesuai jadwal yang tertera di surat pengumuman.</p>
                    </div>
                </div>
            </div>
        @else
            {{-- Case: Not Accepted or Gugur --}}
            <div class="status-hero {{ $isGugur ? 'hero-waiting' : 'hero-rejected' }}">
                <div style="font-size: 1rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.2em; margin-bottom: 1rem;">HASIL SELEKSI</div>
                <h2 style="font-size: 3rem; font-weight: 950; margin: 0; line-height: 1;">
                    {{ $isGugur ? 'TIDAK HADIR' : 'TIDAK DITERIMA' }}
                </h2>
                
                <div style="max-width: 700px; margin: 2rem auto 0;">
                    <p style="font-size: 1.1rem; opacity: 0.9; font-weight: 500; line-height: 1.8;">
                        @if($isGugur)
                            Mohon maaf, Anda dinyatakan gugur karena tidak mengikuti tahapan ujian CBT sesuai jadwal yang ditentukan.
                        @else
                            Terima kasih atas partisipasi Anda dalam proses seleksi ini. Mohon maaf, berdasarkan kuota dan skor akumulasi, Anda belum dapat diterima di {{ $settings['nama_sekolah'] ?? 'Sekolah Kami' }}.
                        @endif
                    </p>

                    @if($hasil->alasan_penolakan)
                        <div style="background: rgba(0,0,0,0.1); border: 1px solid rgba(255,255,255,0.2); padding: 1.25rem; border-radius: 18px; margin-top: 1.5rem; text-align: left;">
                            <div style="font-size: 0.75rem; font-weight: 700; opacity: 0.8; text-transform: uppercase; margin-bottom: 0.5rem;">Catatan Panitia:</div>
                            <div style="font-size: 0.95rem; font-weight: 600;">"{{ $hasil->alasan_penolakan }}"</div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="glass-card" style="margin-top: 2rem; text-align: center;">
                <p style="color: #64748b; font-size: 1rem; margin-bottom: 1.5rem;">Tetap semangat dan sukses untuk pendidikan Anda selanjutnya!</p>
                <a href="{{ route('siswa.dashboard') }}" style="display: inline-block; padding: 0.75rem 1.75rem; background: #f1f5f9; color: #475569; border-radius: 12px; font-weight: 700; text-decoration: none; transition: 0.2s;" onmouseover="this.style.background='#e2e8f0'" onmouseout="this.style.background='#f1f5f9'">🏠 Kembali ke Dashboard</a>
            </div>
        @endif
    @endif
</div>
@endsection
