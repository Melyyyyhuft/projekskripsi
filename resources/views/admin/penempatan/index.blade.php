@extends('layouts.admin')
@section('title', 'Seleksi & Penempatan')

@section('content')
<style>
.sp-badge{display:inline-flex;align-items:center;gap:.3rem;padding:.28rem .75rem;border-radius:999px;font-size:.72rem;font-weight:700;white-space:nowrap;}
.sp-card{background:white;border-radius:16px;padding:1.25rem 1.5rem;box-shadow:0 2px 12px rgba(0,0,0,.06);border:1px solid #f1f5f9;}
.sp-btn{display:inline-flex;align-items:center;gap:.5rem;padding:.72rem 1.4rem;border-radius:12px;font-size:.875rem;font-weight:700;border:none;cursor:pointer;transition:all .2s;}
.sp-btn:hover:not(:disabled){transform:translateY(-2px);box-shadow:0 8px 20px rgba(0,0,0,.18);}
.sp-btn:disabled{opacity:.4;cursor:not-allowed;}
.sp-table th{padding:.75rem 1rem;font-size:.7rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.05em;background:#f8fafc;border-bottom:2px solid #e2e8f0;white-space:nowrap;}
.sp-table td{padding:.8rem 1rem;border-bottom:1px solid #f1f5f9;font-size:.875rem;vertical-align:middle;}
.sp-table tr:hover td{background:#fafbfc;}
.sp-table tr:last-child td{border-bottom:none;}
.filter-sel{padding:.55rem .9rem;border:1.5px solid #e2e8f0;border-radius:10px;font-size:.85rem;color:#0f172a;background:white;cursor:pointer;font-weight:600;outline:none;}
.filter-sel:focus{border-color:#3b82f6;}
.stat-num{font-size:1.9rem;font-weight:900;line-height:1;}
.stat-lbl{font-size:.75rem;font-weight:600;color:#64748b;margin-top:.25rem;}
@keyframes fadeUp{from{opacity:0;transform:translateY(14px);}to{opacity:1;transform:translateY(0);}}
.fu{animation:fadeUp .35s ease forwards;}
</style>

{{-- Flash --}}
@if(session('success'))
<div id="fl-ok" style="background:#d1fae5;color:#065f46;padding:.9rem 1.2rem;border-radius:12px;margin-bottom:1.5rem;font-weight:600;border:1px solid #a7f3d0;display:flex;align-items:center;gap:.65rem;">✅ {{ session('success') }}</div>
@endif
@if(session('error'))
<div id="fl-err" style="background:#fee2e2;color:#991b1b;padding:.9rem 1.2rem;border-radius:12px;margin-bottom:1.5rem;font-weight:600;border:1px solid #fca5a5;display:flex;align-items:center;gap:.65rem;">⚠️ {{ session('error') }}</div>
@endif

{{-- Header --}}
<div class="fu" style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:1rem;margin-bottom:1.75rem;">
    <div>
        <h1 style="font-size:1.5rem;font-weight:900;color:#0f172a;margin:0 0 .3rem;letter-spacing:-.02em;">🏫 Seleksi &amp; Penempatan Kelas</h1>
        <p style="color:#64748b;font-size:.875rem;margin:0;">Hitung skor akhir, tentukan kelas, lalu publish hasil ke siswa.</p>
    </div>
    <div style="display:flex;gap:.75rem;flex-wrap:wrap;align-items:center;">
        @if($sudahPublish)
            <span class="sp-badge" style="background:#d1fae5;color:#065f46;font-size:.8rem;padding:.5rem 1.1rem;">🔒 Hasil Sudah Dipublish</span>
        @else
            {{-- Hitung Semua --}}
            <form action="{{ route('admin.penempatan.proses') }}" method="POST" id="fHitung">@csrf</form>
            <button type="button" onclick="konfirmHitung()" form="fHitung"
                class="sp-btn" style="background:linear-gradient(135deg,#3b82f6,#6366f1);color:white;"
                {{ $totalSiswa == 0 ? 'disabled' : '' }}>
                ⚡ Hitung Semua
            </button>
            {{-- Publish Hasil --}}
            <form action="{{ route('admin.penempatan.publish') }}" method="POST" id="fPublish">@csrf</form>
            <button type="button" onclick="konfirmPublish()" form="fPublish"
                class="sp-btn" style="background:linear-gradient(135deg,#10b981,#059669);color:white;"
                {{ !$adaDraft ? 'disabled' : '' }}>
                📢 Publish Hasil
            </button>
        @endif
    </div>
</div>

{{-- Formula bar --}}
<div class="fu" style="background:linear-gradient(135deg,#0f172a,#1e3a5f);border-radius:16px;padding:1.1rem 1.5rem;margin-bottom:1.75rem;display:flex;align-items:center;flex-wrap:wrap;gap:1.25rem;">
    <span style="color:#94a3b8;font-size:.75rem;font-weight:700;letter-spacing:.07em;text-transform:uppercase;flex-shrink:0;">📐 Formula</span>
    <code style="color:#f8fafc;font-size:.9rem;background:rgba(255,255,255,.08);padding:.45rem 1rem;border-radius:9px;font-weight:600;">
        Skor = (0.7 × Nilai Rapor) + (0.3 × Nilai CBT) + Bonus Sertifikat
    </code>
    <div style="display:flex;gap:.6rem;flex-wrap:wrap;margin-left:auto;">
        <span style="background:rgba(251,191,36,.15);color:#fbbf24;padding:.3rem .75rem;border-radius:8px;font-size:.72rem;font-weight:700;">⭐ ≥85 → Unggulan</span>
        <span style="background:rgba(96,165,250,.15);color:#60a5fa;padding:.3rem .75rem;border-radius:8px;font-size:.72rem;font-weight:700;">📘 &lt;85 → Reguler</span>
    </div>
</div>

{{-- Stat Cards --}}
<div class="fu" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:1rem;margin-bottom:1.75rem;">
    @php $stats = [
        ['icon'=>'👥','val'=>$totalSiswa,         'lbl'=>'Total Siswa',     'bg'=>'#eff6ff','ic_bg'=>'#dbeafe'],
        ['icon'=>'✅','val'=>$totalDiterima,       'lbl'=>'Diterima',        'bg'=>'#f0fdf4','ic_bg'=>'#d1fae5'],
        ['icon'=>'❌','val'=>$totalTidakDiterima,  'lbl'=>'Tidak Diterima',  'bg'=>'#fff7ed','ic_bg'=>'#fed7aa'],
        ['icon'=>'🚫','val'=>$totalTidakHadir,    'lbl'=>'Tidak Hadir CBT', 'bg'=>'#fef2f2','ic_bg'=>'#fee2e2'],
        ['icon'=>'🏆','val'=>$sudahDihitung,       'lbl'=>'Sudah Dihitung',  'bg'=>'#f5f3ff','ic_bg'=>'#ede9fe'],
    ]; @endphp
    @foreach($stats as $s)
    <div class="sp-card" style="background:{{ $s['bg'] }};display:flex;align-items:center;gap:1rem;border:none;">
        <div style="width:44px;height:44px;border-radius:12px;background:{{ $s['ic_bg'] }};display:flex;align-items:center;justify-content:center;font-size:1.2rem;flex-shrink:0;">{{ $s['icon'] }}</div>
        <div>
            <div class="stat-num">{{ $s['val'] }}</div>
            <div class="stat-lbl">{{ $s['lbl'] }}</div>
        </div>
    </div>
    @endforeach
</div>

{{-- Draft notice --}}
@if($adaDraft && !$sudahPublish)
<div class="fu" style="background:#fef3c7;border:1px solid #fde68a;border-radius:12px;padding:.9rem 1.25rem;margin-bottom:1.5rem;display:flex;align-items:center;gap:.75rem;">
    <span style="font-size:1.2rem;">🔍</span>
    <div>
        <span style="font-weight:700;color:#92400e;">Mode Review —</span>
        <span style="color:#78350f;font-size:.875rem;"> Hasil perhitungan sudah tersimpan sebagai draft. Periksa data di tabel, lalu klik <strong>Publish Hasil</strong> jika sudah yakin.</span>
    </div>
</div>
@endif

{{-- Table Card --}}
<div class="sp-card fu">
    {{-- Toolbar --}}
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;margin-bottom:1.25rem;">
        <h3 style="margin:0;font-size:1rem;font-weight:800;color:#0f172a;">📋 Daftar Siswa</h3>
        <div style="display:flex;align-items:center;gap:.6rem;flex-wrap:wrap;">
            <form method="GET" action="{{ route('admin.penempatan.index') }}" style="display:flex;align-items:center;gap:.5rem;">
                <label style="font-size:.78rem;font-weight:700;color:#64748b;">Jurusan:</label>
                <select name="jurusan_id" class="filter-sel" onchange="this.form.submit()">
                    <option value="">Semua</option>
                    @foreach($jurusans as $j)
                        <option value="{{ $j->id }}" {{ $filterJurusan == $j->id ? 'selected' : '' }}>{{ $j->nama }}</option>
                    @endforeach
                </select>
                @if($filterJurusan)
                <a href="{{ route('admin.penempatan.index') }}" style="font-size:.78rem;color:#64748b;font-weight:600;padding:.35rem .7rem;border:1px solid #e2e8f0;border-radius:8px;text-decoration:none;background:white;">✕</a>
                @endif
            </form>
            <span style="font-size:.78rem;background:#f1f5f9;color:#64748b;padding:.35rem .8rem;border-radius:8px;font-weight:600;border:1px solid #e2e8f0;">{{ $rows->count() }} siswa</span>
        </div>
    </div>

    {{-- Bonus info --}}
    <div style="background:#f8fafc;border-radius:10px;padding:.7rem 1rem;margin-bottom:1.25rem;display:flex;flex-wrap:wrap;gap:.5rem .75rem;align-items:center;">
        <span style="font-size:.72rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;">Bonus Sertifikat:</span>
        <span style="font-size:.75rem;color:#475569;font-weight:600;">🌍 Internasional <strong>+3</strong></span>
        <span style="color:#cbd5e1;">·</span>
        <span style="font-size:.75rem;color:#475569;font-weight:600;">🇮🇩 Nasional <strong>+2</strong></span>
        <span style="color:#cbd5e1;">·</span>
        <span style="font-size:.75rem;color:#475569;font-weight:600;">🏛️ Provinsi <strong>+1</strong></span>
        <span style="color:#cbd5e1;">·</span>
        <span style="font-size:.75rem;color:#475569;font-weight:600;">🏙️ Kab/Kota <strong>+0.5</strong></span>
        <span style="color:#cbd5e1;">·</span>
        <span style="font-size:.75rem;color:#94a3b8;">Kecamatan/Sekolah <strong>+0</strong></span>
    </div>

    <div style="overflow-x:auto;border-radius:12px;border:1px solid #e2e8f0;">
        <table class="sp-table" style="width:100%;border-collapse:collapse;min-width:920px;">
            <thead>
                <tr>
                    <th style="text-align:left;padding-left:1.25rem;">Nama Siswa</th>
                    <th style="text-align:left;">Jurusan</th>
                    <th style="text-align:center;">Rapor</th>
                    <th style="text-align:center;">CBT</th>
                    <th style="text-align:center;">Sertifikat</th>
                    <th style="text-align:center;">Bonus</th>
                    <th style="text-align:center;">Skor Akhir</th>
                    <th style="text-align:center;">Penempatan</th>
                    <th style="text-align:center;">Status</th>
                    <th style="text-align:center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse($rows as $row)
            @php
                $st = $row['status'];
                // Status badge
                [$sBg,$sFg,$sIco] = match($st) {
                    'Diterima'       => ['#d1fae5','#065f46','✅'],
                    'Tidak Diterima' => ['#fee2e2','#991b1b','❌'],
                    'Tidak Hadir CBT'=> ['#f1f5f9','#475569','🚫'],
                    'Belum CBT'      => ['#fef3c7','#92400e','⏳'],
                    default          => ['#ede9fe','#5b21b6','🔄'],
                };
                // Penempatan badge
                $pe = $row['penempatan'];
                [$pBg,$pFg,$pIco] = match($pe) {
                    'Unggulan' => ['#fef3c7','#92400e','⭐'],
                    'Reguler'  => ['#dbeafe','#1e40af','📘'],
                    default    => ['#f1f5f9','#94a3b8','—'],
                };
                // Skor color
                $skorColor = match($pe) {
                    'Unggulan' => '#d97706',
                    'Reguler'  => '#2563eb',
                    default    => '#94a3b8',
                };
                $sudahFinal = $row['sudah_publish'];
            @endphp
            <tr style="{{ $sudahFinal ? 'background:#f0fdf4;' : '' }}">
                <td style="padding-left:1.25rem;">
                    <div style="font-weight:700;color:#0f172a;">{{ $row['nama'] }}</div>
                    <div style="font-size:.72rem;color:#94a3b8;">{{ $row['pendaftaran']->nisn }}</div>
                </td>
                <td style="color:#475569;font-size:.85rem;">{{ $row['jurusan'] }}</td>
                <td style="text-align:center;font-weight:700;color:#0f172a;">{{ number_format($row['nilai_rapor'],1) }}</td>
                <td style="text-align:center;">
                    @if($row['nilai_cbt'] !== null)
                        <span style="font-weight:700;color:#0f172a;">{{ number_format($row['nilai_cbt'],1) }}</span>
                    @else
                        <span style="color:#cbd5e1;">—</span>
                    @endif
                </td>
                <td style="text-align:center;">
                    @if($row['sertifikat_level'])
                        <span style="font-size:.75rem;font-weight:600;color:#475569;">{{ $row['sertifikat_level'] }}</span>
                    @else
                        <span style="color:#cbd5e1;font-size:.8rem;">—</span>
                    @endif
                </td>
                <td style="text-align:center;">
                    @if($row['bonus_sertifikat'] > 0)
                        <span style="background:#fef9c3;color:#92400e;padding:.2rem .55rem;border-radius:7px;font-weight:700;font-size:.78rem;">+{{ $row['bonus_sertifikat'] }}</span>
                    @else
                        <span style="color:#cbd5e1;font-size:.8rem;">+0</span>
                    @endif
                </td>
                <td style="text-align:center;">
                    @if($row['skor_akhir'] !== null)
                        <strong style="color:{{ $skorColor }};font-size:1rem;">{{ number_format($row['skor_akhir'],2) }}</strong>
                    @else
                        <span style="color:#cbd5e1;">—</span>
                    @endif
                </td>
                <td style="text-align:center;">
                    @if($pe)
                        <span class="sp-badge" style="background:{{ $pBg }};color:{{ $pFg }};">{{ $pIco }} {{ $pe }}</span>
                    @else
                        <span style="color:#cbd5e1;font-size:.8rem;">Belum</span>
                    @endif
                </td>
                <td style="text-align:center;">
                    <span class="sp-badge" style="background:{{ $sBg }};color:{{ $sFg }};">{{ $sIco }} {{ $st }}</span>
                </td>
                <td style="text-align:center;">
                    <a href="{{ route('admin.pendaftaran.show', $row['pendaftaran']->id) }}"
                       style="font-size:.75rem;padding:.35rem .75rem;background:#f1f5f9;color:#475569;border-radius:8px;text-decoration:none;font-weight:600;display:inline-block;transition:background .15s;"
                       onmouseover="this.style.background='#e2e8f0'" onmouseout="this.style.background='#f1f5f9'">
                        👁️ Detail
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="10" style="padding:3.5rem;text-align:center;color:#94a3b8;">
                    <div style="font-size:2.5rem;margin-bottom:.5rem;">📭</div>
                    <div style="font-weight:700;margin-bottom:.35rem;">Belum ada data siswa</div>
                    <div style="font-size:.85rem;">Siswa yang sudah lolos verifikasi dan mengikuti CBT akan muncul di sini.</div>
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{-- Legend --}}
    @if($rows->isNotEmpty())
    <div style="margin-top:1rem;padding-top:1rem;border-top:1px solid #f1f5f9;display:flex;gap:.6rem;flex-wrap:wrap;align-items:center;">
        <span style="font-size:.7rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;">Status:</span>
        <span class="sp-badge" style="background:#d1fae5;color:#065f46;">✅ Diterima</span>
        <span class="sp-badge" style="background:#fee2e2;color:#991b1b;">❌ Tidak Diterima</span>
        <span class="sp-badge" style="background:#f1f5f9;color:#475569;">🚫 Tidak Hadir CBT</span>
        <span class="sp-badge" style="background:#fef3c7;color:#92400e;">⏳ Belum CBT</span>
        <span class="sp-badge" style="background:#ede9fe;color:#5b21b6;">🔄 Belum Dihitung</span>
    </div>
    @endif
</div>

{{-- Ringkasan Penempatan --}}
@php
    $jmlUnggulan = $rows->where('penempatan','Unggulan')->count();
    $jmlReguler  = $rows->where('penempatan','Reguler')->count();
@endphp
@if($jmlUnggulan + $jmlReguler > 0)
<div class="sp-card fu" style="margin-top:1.5rem;">
    <h3 style="margin:0 0 1.1rem;font-size:1rem;font-weight:800;color:#0f172a;">📊 Ringkasan Penempatan</h3>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:1rem;">
        <div style="background:linear-gradient(135deg,#fef3c7,#fde68a);border-radius:14px;padding:1.25rem;text-align:center;">
            <div style="font-size:2rem;margin-bottom:.3rem;">⭐</div>
            <div style="font-size:2.2rem;font-weight:900;color:#92400e;">{{ $jmlUnggulan }}</div>
            <div style="font-weight:700;color:#78350f;font-size:.9rem;">Kelas Unggulan</div>
            <div style="font-size:.75rem;color:#92400e;margin-top:.25rem;">Skor ≥ 85</div>
        </div>
        <div style="background:linear-gradient(135deg,#dbeafe,#bfdbfe);border-radius:14px;padding:1.25rem;text-align:center;">
            <div style="font-size:2rem;margin-bottom:.3rem;">📘</div>
            <div style="font-size:2.2rem;font-weight:900;color:#1e40af;">{{ $jmlReguler }}</div>
            <div style="font-weight:700;color:#1e3a8a;font-size:.9rem;">Kelas Reguler</div>
            <div style="font-size:.75rem;color:#1e40af;margin-top:.25rem;">Skor &lt; 85</div>
        </div>
    </div>
</div>
@endif

<script>
function konfirmHitung() {
    Swal.fire({
        title: '⚡ Hitung Semua Siswa?',
        html: `Sistem akan menghitung skor seluruh siswa yang sudah CBT:<br><br>
               <code style="background:#f1f5f9;padding:.35rem .75rem;border-radius:8px;font-size:.82rem;display:block;margin:.5rem 0;">
                 Skor = (0.7 × Rapor) + (0.3 × CBT) + Bonus
               </code>
               Hasil <strong>tidak langsung tampil ke siswa</strong>.<br>
               Anda dapat memeriksa dahulu sebelum publish.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3b82f6',
        cancelButtonColor: '#94a3b8',
        confirmButtonText: '⚡ Ya, Hitung!',
        cancelButtonText: 'Batal',
    }).then(r => { if (r.isConfirmed) document.getElementById('fHitung').submit(); });
}

function konfirmPublish() {
    Swal.fire({
        title: '📢 Publish Hasil Seleksi?',
        html: `Setelah dipublish:<ul style="text-align:left;margin:.75rem 0 0;padding-left:1.2rem;font-size:.875rem;line-height:1.8;">
               <li>Hasil <strong>dikunci permanen</strong></li>
               <li>Siswa <strong>Diterima</strong> dapat download surat PDF</li>
               <li>Siswa <strong>Tidak Diterima</strong> hanya melihat informasi</li>
               <li>Siswa tidak hadir CBT otomatis berstatus <strong>Gugur</strong></li></ul>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#94a3b8',
        confirmButtonText: '📢 Publish Sekarang!',
        cancelButtonText: 'Batal',
    }).then(r => { if (r.isConfirmed) document.getElementById('fPublish').submit(); });
}

// Auto-hide flash
setTimeout(() => {
    ['fl-ok','fl-err'].forEach(id => {
        const el = document.getElementById(id);
        if (el) { el.style.transition='opacity .5s'; el.style.opacity='0'; setTimeout(()=>el?.remove(),500); }
    });
}, 5000);
</script>
@endsection
