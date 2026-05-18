@extends('layouts.siswa')
@section('title', 'Hasil Seleksi PPDB')

@section('content')
@php
    $namaSekolah   = $settings['nama_sekolah'] ?? 'SMK PPDB';
    $tahunAjaran   = $settings['tahun_ajaran'] ?? date('Y') . '/' . (date('Y')+1);
    $logoSekolah   = isset($settings['logo_sekolah']) ? asset('storage/' . $settings['logo_sekolah']) : null;
    $tglPengumuman = isset($settings['tgl_pengumuman']) ? \Carbon\Carbon::parse($settings['tgl_pengumuman'])->isoFormat('D MMMM Y') : null;

    $status    = $pendaftaran->status;
    $gugur     = in_array($status, ['tidak_mengikuti_ujian', 'gugur']);
    $belumFinal = !$hasil || !$hasil->is_finalisasi;
    $kategori   = $hasil ? $hasil->kategori_kelulusan : null;
    $lulus      = $kategori === 'DITERIMA';

    $nomorSurat = sprintf('SK-%03d/PPDB/%s', $pendaftaran->id, date('Y'));
    $kodeVerif  = strtoupper(substr(md5($pendaftaran->id . $pendaftaran->nisn . date('Y')), 0, 12));

    // Status Badges untuk timeline
    $statusBadges = [
        'Registrasi & Pendaftaran' => true,
        'Verifikasi Berkas'        => !in_array($status, ['draft', 'menunggu_verifikasi']),
        'Ujian Online CBT'         => $hasilUjian ? true : false,
        'Proses Seleksi'           => $hasil ? true : false,
        'Pengumuman Hasil'         => $hasil && $hasil->is_finalisasi,
    ];
@endphp

<style>
/* ─── Animations ─── */
@keyframes fadeInUp { from { opacity:0; transform:translateY(24px); } to { opacity:1; transform:translateY(0); } }
@keyframes pulse-ring { 0%,100% { box-shadow: 0 0 0 0 rgba(16,185,129,.3); } 50% { box-shadow: 0 0 0 12px rgba(16,185,129,0); } }
@keyframes confetti-fall { 0% { transform:translateY(-20px) rotate(0deg); opacity:1; } 100% { transform:translateY(60px) rotate(720deg); opacity:0; } }

.fade-up { animation: fadeInUp .6s ease forwards; }
.delay-1 { animation-delay:.1s; opacity:0; }
.delay-2 { animation-delay:.2s; opacity:0; }
.delay-3 { animation-delay:.3s; opacity:0; }

/* ─── Certificate buttons ─── */
.cert-btn {
    display: inline-flex; align-items: center; gap: .5rem;
    padding: .75rem 1.5rem; border-radius: 12px; font-weight: 700;
    font-size: .925rem; cursor: pointer; border: none; transition: all .2s;
}
.cert-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,0,0,.15); }

/* ─── Modal Surat ─── */
#modalSurat {
    display: none; position: fixed; inset: 0;
    background: rgba(0,0,0,.75); backdrop-filter: blur(6px);
    z-index: 9999; overflow-y: auto; padding: 2rem 1rem;
}
#modalSuratBox {
    background: white; border-radius: 16px; max-width: 820px; margin: 0 auto;
    box-shadow: 0 24px 80px rgba(0,0,0,.4); overflow: hidden;
}
#modalSuratHeader {
    background: linear-gradient(135deg,#0f172a,#1e3a5f);
    padding: 1rem 1.5rem; display: flex; justify-content: space-between; align-items: center;
}

/* ─── Print ─── */
@media print {
    body * { visibility: hidden !important; }
    #surat-print-area, #surat-print-area * { visibility: visible !important; }
    #surat-print-area {
        position: fixed !important; inset: 0 !important;
        width: 210mm !important; margin: 0 auto !important;
        padding: 15mm 20mm !important; background: white !important;
    }
}

/* ─── Timeline ─── */
.timeline-step { display:flex; align-items:flex-start; gap:1rem; margin-bottom:1.25rem; }
.timeline-dot { width:36px; height:36px; border-radius:50%; flex-shrink:0; display:flex; align-items:center; justify-content:center; font-size:.85rem; font-weight:800; }
.timeline-line { width:2px; background:#e2e8f0; height:100%; margin: 0 auto; }
</style>

{{-- ══════════════════════════════════════════════
     CASE A: GUGUR
══════════════════════════════════════════════ --}}
@if($gugur)
<div class="fade-up" style="max-width:640px;margin:0 auto;">
    <div style="background:linear-gradient(135deg,#1e1b4b,#7c3aed);border-radius:24px;padding:3rem 2rem;text-align:center;color:white;box-shadow:0 20px 60px rgba(109,40,217,.35);">
        <div style="width:80px;height:80px;background:rgba(239,68,68,.2);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:2.5rem;margin:0 auto 1.5rem;">😔</div>
        <h2 style="font-size:1.75rem;font-weight:900;margin:0 0 .75rem;">Mohon Maaf</h2>
        <p style="opacity:.85;font-size:1rem;line-height:1.7;margin-bottom:1.5rem;">
            Anda dinyatakan <strong style="font-size:1.2rem;color:#fca5a5;">GUGUR</strong> dari seleksi PPDB<br>
            karena tidak mengikuti ujian seleksi online.
        </p>
        <div style="background:rgba(255,255,255,.1);border-radius:12px;padding:1rem 1.5rem;text-align:left;font-size:.875rem;line-height:1.7;">
            <p style="margin:0;opacity:.9;">💡 <strong>Tetap semangat!</strong> Jangan menyerah dan terus kejar impianmu. Setiap kegagalan adalah langkah awal menuju keberhasilan. 💪</p>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════
     CASE B: BELUM FINAL
══════════════════════════════════════════════ --}}
@elseif($belumFinal)
<div style="max-width:720px;margin:0 auto;">

    {{-- Status Card --}}
    <div class="glass-card fade-up" style="margin-bottom:1.5rem;border-top:4px solid var(--primary);">
        <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1.5rem;flex-wrap:wrap;">
            <div style="width:56px;height:56px;background:linear-gradient(135deg,var(--primary),#6366f1);border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:1.5rem;flex-shrink:0;">⏳</div>
            <div>
                <h2 style="font-size:1.3rem;font-weight:800;color:#0f172a;margin:0 0 .25rem;">Belum Ada Pengumuman</h2>
                <p style="color:#64748b;margin:0;font-size:.875rem;">Hasil seleksi masih dalam proses finalisasi oleh admin PPDB.</p>
            </div>
        </div>

        {{-- Status Badge --}}
        @php
            $statusMap = [
                'lolos_admin'     => ['bg'=>'#d1fae5','color'=>'#065f46','icon'=>'✅','teks'=>'Berkas Diverifikasi','sub'=>'Administrasi Anda sudah lolos. Menunggu jadwal ujian atau proses seleksi.'],
                'sudah_ujian'     => ['bg'=>'#dbeafe','color'=>'#1e40af','icon'=>'📝','teks'=>'Sudah Mengikuti Ujian','sub'=>'Ujian CBT selesai. Admin sedang memproses hasil seleksi.'],
                'siap_finalisasi' => ['bg'=>'#ede9fe','color'=>'#5b21b6','icon'=>'🔄','teks'=>'Sedang Diproses Seleksi','sub'=>'Seleksi telah dijalankan. Menunggu finalisasi dari admin PPDB.'],
            ];
            $info = $statusMap[$status] ?? ['bg'=>'#f1f5f9','color'=>'#475569','icon'=>'⏳','teks'=>ucwords(str_replace('_',' ',$status)),'sub'=>'Hasil akan ditampilkan setelah admin melakukan finalisasi.'];
        @endphp

        <div style="background:{{ $info['bg'] }};border-radius:14px;padding:1.25rem 1.5rem;display:flex;align-items:center;gap:1rem;flex-wrap:wrap;">
            <span style="font-size:1.5rem;">{{ $info['icon'] }}</span>
            <div>
                <div style="font-weight:700;color:{{ $info['color'] }};font-size:1rem;">{{ $info['teks'] }}</div>
                <div style="font-size:.82rem;color:{{ $info['color'] }};opacity:.8;margin-top:.2rem;">{{ $info['sub'] }}</div>
            </div>
        </div>
    </div>

    {{-- Timeline Progress --}}
    <div class="glass-card fade-up delay-1">
        <h3 style="font-size:1rem;font-weight:700;color:#0f172a;margin:0 0 1.5rem;">📍 Progress Seleksi Anda</h3>
        @foreach($statusBadges as $label => $done)
        @php $last = $loop->last; @endphp
        <div style="display:flex;gap:1rem;align-items:flex-start;{{ !$last ? 'margin-bottom:.25rem;' : '' }}">
            <div style="display:flex;flex-direction:column;align-items:center;flex-shrink:0;">
                <div style="width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.85rem;font-weight:800;
                    background:{{ $done ? '#10b981' : '#e2e8f0' }};color:{{ $done ? 'white' : '#94a3b8' }};">
                    {{ $done ? '✓' : ($loop->index + 1) }}
                </div>
                @if(!$last)<div style="width:2px;height:28px;background:{{ $done ? '#10b981' : '#e2e8f0' }};margin:0 auto;"></div>@endif
            </div>
            <div style="padding-top:.5rem;{{ !$last ? 'padding-bottom:.5rem;' : '' }}">
                <div style="font-weight:700;font-size:.9rem;color:{{ $done ? '#059669' : '#94a3b8' }};">{{ $label }}</div>
            </div>
        </div>
        @endforeach

        @if($tglPengumuman)
        <div style="background:#fef3c7;border:1px solid #fde68a;border-radius:10px;padding:.875rem 1.25rem;margin-top:1.5rem;display:flex;align-items:center;gap:.75rem;">
            <span style="font-size:1.25rem;">📅</span>
            <div>
                <div style="font-size:.75rem;font-weight:700;color:#92400e;text-transform:uppercase;letter-spacing:.05em;">Estimasi Tanggal Pengumuman</div>
                <div style="font-weight:700;color:#78350f;font-size:1rem;">{{ $tglPengumuman }}</div>
            </div>
        </div>
        @endif
    </div>
</div>

{{-- ══════════════════════════════════════════════
     CASE C: HASIL FINAL
══════════════════════════════════════════════ --}}
@else
<div style="max-width:720px;margin:0 auto;">

    @if($lulus)
    {{-- ── LULUS ── --}}
    @php
        $gradFrom   = '#059669';
        $gradTo     = '#10b981';
        $gradClass  = 'linear-gradient(135deg,#f0fdf4,#dcfce7)';
        $accentColor = '#059669';
        $badgeText  = '✅ Jalur Seleksi';
    @endphp

    {{-- Hero Card --}}
    <div style="background:linear-gradient(135deg,{{ $gradFrom }},{{ $gradTo }});border-radius:24px;padding:2.5rem;text-align:center;color:white;box-shadow:0 20px 60px rgba(0,0,0,.2);margin-bottom:1.5rem;position:relative;overflow:hidden;" class="fade-up">
        <div style="position:absolute;top:-40px;right:-40px;width:200px;height:200px;background:rgba(255,255,255,.08);border-radius:50%;"></div>
        <div style="position:absolute;bottom:-30px;left:-20px;width:150px;height:150px;background:rgba(255,255,255,.06);border-radius:50%;"></div>

        <div style="width:72px;height:72px;background:rgba(255,255,255,.25);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:2rem;margin:0 auto 1rem;">
            {{ $isUnggulan ? '⭐' : '🎉' }}
        </div>
        <p style="font-size:.875rem;font-weight:600;opacity:.9;margin:0 0 .5rem;letter-spacing:.1em;text-transform:uppercase;">Selamat!</p>
        <h1 style="font-size:2rem;font-weight:900;margin:0 0 .75rem;letter-spacing:-.02em;">Anda Dinyatakan LULUS</h1>
        <p style="font-size:1rem;opacity:.9;margin:0 0 1.5rem;">{{ $namaSekolah }} — Tahun Ajaran {{ $tahunAjaran }}</p>

        <div style="background:rgba(255,255,255,.2);backdrop-filter:blur(10px);border-radius:14px;padding:1.25rem 1.5rem;display:inline-block;text-align:left;min-width:300px;">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem;">
                <div>
                    <div style="font-size:.72rem;opacity:.8;text-transform:uppercase;letter-spacing:.06em;">Jurusan</div>
                    <div style="font-weight:800;font-size:1rem;margin-top:.2rem;">{{ $pendaftaran->jurusan->nama }}</div>
                </div>
                <div>
                    <div style="font-size:.72rem;opacity:.8;text-transform:uppercase;letter-spacing:.06em;">Jalur Penerimaan</div>
                    <div style="font-weight:800;font-size:1rem;margin-top:.2rem;">{{ $badgeText }}</div>
                </div>
                <div>
                    <div style="font-size:.72rem;opacity:.8;text-transform:uppercase;letter-spacing:.06em;">Skor Akhir</div>
                    <div style="font-weight:800;font-size:1.25rem;margin-top:.2rem;">{{ $hasil->skor_akhir }}</div>
                </div>
                <div>
                    <div style="font-size:.72rem;opacity:.8;text-transform:uppercase;letter-spacing:.06em;">Ranking</div>
                    <div style="font-weight:800;font-size:1.25rem;margin-top:.2rem;">#{{ $hasil->ranking }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Detail Card --}}
    <div class="glass-card fade-up delay-1" style="margin-bottom:1.5rem;">
        <h3 style="font-size:1rem;font-weight:700;color:#0f172a;margin:0 0 1.25rem;">📋 Detail Hasil Seleksi</h3>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
            <div style="background:#f8fafc;border-radius:12px;padding:1rem;">
                <div style="font-size:.72rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;margin-bottom:.4rem;">Nama Lengkap</div>
                <div style="font-weight:700;color:#0f172a;">{{ Auth::user()->name }}</div>
            </div>
            <div style="background:#f8fafc;border-radius:12px;padding:1rem;">
                <div style="font-size:.72rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;margin-bottom:.4rem;">NISN</div>
                <div style="font-weight:700;color:#0f172a;font-family:monospace;">{{ $pendaftaran->nisn }}</div>
            </div>
            <div style="background:#f8fafc;border-radius:12px;padding:1rem;">
                <div style="font-size:.72rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;margin-bottom:.4rem;">Nilai CBT</div>
                <div style="font-weight:700;color:var(--primary);font-size:1.2rem;">{{ $hasilUjian ? $hasilUjian->skor : '—' }}</div>
            </div>
            <div style="background:#f8fafc;border-radius:12px;padding:1rem;">
                <div style="font-size:.72rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;margin-bottom:.4rem;">Nilai Rapor</div>
                <div style="font-weight:700;color:#0f172a;font-size:1.2rem;">{{ $pendaftaran->nilai_rapor }}</div>
            </div>
        </div>

        {{-- Kode Verifikasi --}}
        <div style="margin-top:1.25rem;background:linear-gradient(135deg,#0f172a,#1e3a5f);border-radius:12px;padding:1rem 1.25rem;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.75rem;">
            <div>
                <div style="font-size:.7rem;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;margin-bottom:.3rem;">Kode Verifikasi Dokumen</div>
                <div style="font-family:monospace;font-size:1.1rem;font-weight:800;color:#38bdf8;letter-spacing:.15em;">{{ $kodeVerif }}</div>
            </div>
            <div style="font-size:.75rem;color:#64748b;max-width:180px;text-align:right;line-height:1.4;">Gunakan kode ini untuk memverifikasi keaslian surat.</div>
        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="glass-card fade-up delay-2">
        <h3 style="font-size:1rem;font-weight:700;color:#0f172a;margin:0 0 1.25rem;">📄 Surat Kelulusan Resmi</h3>
        <p style="color:#64748b;font-size:.875rem;margin:0 0 1.25rem;">Unduh atau cetak surat kelulusan resmi Anda sebagai bukti penerimaan di {{ $namaSekolah }}.</p>
        <div style="display:flex;gap:.875rem;flex-wrap:wrap;">
            <button onclick="openModal()" class="cert-btn" style="background:white;color:#0f172a;border:2px solid #e2e8f0;flex:1;min-width:140px;justify-content:center;">
                <span>👁️</span> Preview Surat
            </button>
            <button onclick="downloadPDF()" class="cert-btn" style="background:linear-gradient(135deg,var(--primary),#6366f1);color:white;flex:1;min-width:140px;justify-content:center;">
                <span>⬇️</span> Download PDF
            </button>
            <button onclick="window.print()" class="cert-btn" style="background:linear-gradient(135deg,#059669,#10b981);color:white;flex:1;min-width:140px;justify-content:center;">
                <span>🖨️</span> Cetak
            </button>
        </div>
    </div>

    @else
    {{-- ── TIDAK LULUS ── --}}
    <div style="background:linear-gradient(135deg,#1e293b,#334155);border-radius:24px;padding:3rem 2rem;text-align:center;color:white;box-shadow:0 20px 60px rgba(0,0,0,.2);" class="fade-up">
        <div style="width:72px;height:72px;background:rgba(239,68,68,.15);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:2rem;margin:0 auto 1.5rem;">💙</div>
        <h2 style="font-size:1.75rem;font-weight:900;margin:0 0 .75rem;">Mohon Maaf</h2>
        <p style="opacity:.85;font-size:1rem;line-height:1.7;margin-bottom:1.5rem;">
            Hasil seleksi menunjukkan bahwa Anda belum berhasil<br>diterima pada tahap ini.
        </p>
        <div style="background:rgba(255,255,255,.08);border-radius:12px;padding:1.25rem 1.5rem;text-align:left;font-size:.875rem;line-height:1.8;max-width:420px;margin:0 auto;">
            <p style="margin:0;color:#cbd5e1;">
                💡 Tetap semangat dan jangan menyerah. Setiap proses adalah bagian dari perjalanan belajar. Terus tingkatkan kemampuanmu dan coba peluang lainnya. 💪
            </p>
        </div>
    </div>
    @endif

</div>
@endif

{{-- ══════════════════════════════════════════════
     SURAT KELULUSAN TEMPLATE (Hidden)
══════════════════════════════════════════════ --}}
@if($lulus && !$belumFinal)
{{-- Area cetak --}}
<div id="surat-print-area" style="display:none;">
    @include('siswa.partials.surat_kelulusan', [
        'namaSekolah'   => $namaSekolah,
        'logoSekolah'   => $logoSekolah,
        'tahunAjaran'   => $tahunAjaran,
        'nomorSurat'    => $nomorSurat,
        'kodeVerif'     => $kodeVerif,
        'pendaftaran'   => $pendaftaran,
        'hasil'         => $hasil,
        'hasilUjian'    => $hasilUjian,
        'badgeText'     => $badgeText,
    ])
</div>

{{-- Modal Preview --}}
<div id="modalSurat" onclick="if(event.target===this)closeModal()">
    <div id="modalSuratBox">
        <div id="modalSuratHeader">
            <span style="color:white;font-weight:700;font-size:.9rem;">📄 Preview Surat Kelulusan</span>
            <div style="display:flex;gap:.75rem;">
                <button onclick="downloadPDF()" style="background:linear-gradient(135deg,var(--primary),#6366f1);color:white;padding:.5rem 1.1rem;border-radius:8px;font-weight:700;font-size:.82rem;border:none;cursor:pointer;">⬇️ Download PDF</button>
                <button onclick="closeModal()" style="background:rgba(255,255,255,.15);color:white;padding:.5rem .875rem;border-radius:8px;font-size:.9rem;border:none;cursor:pointer;">✕ Tutup</button>
            </div>
        </div>
        <div id="modal-body-surat" style="padding:2rem;overflow-y:auto;max-height:75vh;"></div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
    function openModal() {
        const template = document.getElementById('surat-print-area').cloneNode(true);
        template.style.display = 'block';
        const body = document.getElementById('modal-body-surat');
        body.innerHTML = '';
        body.appendChild(template);
        document.getElementById('modalSurat').style.display = 'block';
        document.body.style.overflow = 'hidden';
    }
    function closeModal() {
        document.getElementById('modalSurat').style.display = 'none';
        document.body.style.overflow = '';
    }
    function downloadPDF() {
        const el = document.getElementById('surat-print-area').cloneNode(true);
        el.style.display = 'block';
        el.style.width = '210mm';
        el.style.padding = '15mm 20mm';
        el.style.background = 'white';
        document.body.appendChild(el);

        html2pdf().set({
            margin: 0,
            filename: 'Surat_Kelulusan_{{ Auth::user()->name }}.pdf',
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2, useCORS: true },
            jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
        }).from(el).save().then(() => document.body.removeChild(el));
    }
    document.addEventListener('keydown', e => { if(e.key==='Escape') closeModal(); });
</script>
@endif
@endsection
