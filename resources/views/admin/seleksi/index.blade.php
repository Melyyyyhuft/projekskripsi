@extends('layouts.admin')
@section('title', 'Proses Seleksi Fleksibel')

@section('content')

@if(session('success'))
    <div style="background:#d1fae5;color:#059669;padding:.875rem 1.25rem;border-radius:12px;margin-bottom:1.5rem;font-weight:600;border:1px solid #a7f3d0;">✅ {{ session('success') }}</div>
@endif
@if(session('error'))
    <div style="background:#fee2e2;color:#dc2626;padding:.875rem 1.25rem;border-radius:12px;margin-bottom:1.5rem;font-weight:600;border:1px solid #fca5a5;">⚠️ {{ session('error') }}</div>
@endif

{{-- Page Header --}}
<div style="margin-bottom:2rem;">
    <h1 style="font-size:1.5rem;font-weight:800;color:#0f172a;margin:0 0 .25rem;">Proses Seleksi PPDB</h1>
    <p style="color:#64748b;font-size:.9rem;margin:0;">Hitung nilai, tentukan kelulusan, dan finalisasi hasil seleksi.</p>
</div>

{{-- Alur Tahapan --}}
<div class="glass-card" style="margin-bottom:1.5rem;">
    <div style="font-size:.75rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;margin-bottom:.875rem;">Alur Proses Seleksi</div>
    <div style="display:flex;align-items:center;gap:.4rem;flex-wrap:wrap;">
        @php
            $steps = [
                ['label'=>'Registrasi','done'=>true],
                ['label'=>'Verifikasi','done'=>true],
                ['label'=>'Ujian CBT','done'=>true],
                ['label'=>'Proses Seleksi','done'=>$adaHasilSeleksi||$sudahDifinalisasi,'active'=>!$adaHasilSeleksi&&!$sudahDifinalisasi],
                ['label'=>'Finalisasi','done'=>$sudahDifinalisasi,'active'=>$adaHasilSeleksi&&!$sudahDifinalisasi],
                ['label'=>'Pengumuman','done'=>$sudahDifinalisasi],
            ];
        @endphp
        @foreach($steps as $step)
            <span style="padding:.3rem .8rem;border-radius:999px;font-weight:700;font-size:.78rem;
                background:{{ ($step['done']??false)?'#d1fae5':(($step['active']??false)?'#dbeafe':'#f1f5f9') }};
                color:{{ ($step['done']??false)?'#059669':(($step['active']??false)?'#1d4ed8':'#94a3b8') }};
                border:1.5px solid {{ ($step['done']??false)?'#6ee7b7':(($step['active']??false)?'#93c5fd':'#e2e8f0') }};">
                {{ ($step['done']??false)?'✓ ':'' }}{{ $step['label'] }}
            </span>
            @if(!$loop->last)<span style="color:#cbd5e1;font-size:.9rem;">›</span>@endif
        @endforeach
    </div>
</div>

{{-- Stats Grid --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(130px,1fr));gap:1rem;margin-bottom:1.5rem;">
    @php $badges = [
        ['label'=>'Belum Ujian','val'=>$statusSummary['belum_ujian'],'bg'=>'#fef3c7','color'=>'#92400e','icon'=>'🕐'],
        ['label'=>'Sudah Ujian','val'=>$statusSummary['sudah_ujian'],'bg'=>'#d1fae5','color'=>'#065f46','icon'=>'✅'],
        ['label'=>'Diseleksi','val'=>$statusSummary['sudah_diseleksi'],'bg'=>'#dbeafe','color'=>'#1e40af','icon'=>'🏆'],
        ['label'=>'Belum Seleksi','val'=>$statusSummary['belum_diseleksi'],'bg'=>'#f1f5f9','color'=>'#475569','icon'=>'⏳'],
        ['label'=>'Ditunda','val'=>$statusSummary['ditunda'],'bg'=>'#fde68a','color'=>'#78350f','icon'=>'⏸'],
        ['label'=>'Tdk Ikut Ujian','val'=>$statusSummary['tidak_mengikuti_ujian'],'bg'=>'#fee2e2','color'=>'#991b1b','icon'=>'❌'],
    ]; @endphp
    @foreach($badges as $b)
    <div style="background:{{ $b['bg'] }};border-radius:12px;padding:1rem;text-align:center;">
        <div style="font-size:1.4rem;margin-bottom:.25rem;">{{ $b['icon'] }}</div>
        <div style="font-size:1.6rem;font-weight:800;color:{{ $b['color'] }};">{{ $b['val'] }}</div>
        <div style="font-size:.72rem;color:{{ $b['color'] }};opacity:.85;font-weight:600;line-height:1.3;margin-top:.15rem;">{{ $b['label'] }}</div>
    </div>
    @endforeach
</div>

{{-- Aksi Utama --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;margin-bottom:2rem;">
    {{-- Proses Seleksi --}}
    <div class="glass-card">
        <h4 style="color:var(--primary);margin:0 0 .5rem;font-size:1rem;">⚡ Proses Seleksi Fleksibel</h4>
        <p style="color:var(--gray-text);font-size:.82rem;margin-bottom:1rem;line-height:1.5;">
            Rumus: <code style="background:#f1f5f9;padding:.1rem .35rem;border-radius:4px;font-size:.8rem;">Skor = (60% × Ujian) + (40% × Rapor)</code><br>
            <strong>≥ 85</strong> → Lulus Unggulan &nbsp;|&nbsp; <strong>&lt; 85</strong> → Lulus Reguler
        </p>
        @if($sudahDifinalisasi)
            <div style="background:#d1fae5;color:#059669;padding:.75rem 1rem;border-radius:8px;font-size:.85rem;">
                🔒 Hasil seleksi sudah difinalisasi.
            </div>
        @else
            <form action="{{ route('admin.seleksi.run') }}" method="POST" id="formProsesSemuaSiswa">
                @csrf
                <input type="hidden" name="mode" value="semua">
                <button type="button" onclick="konfirmProsesSemua()" class="btn-primary"
                    style="width:100%;padding:.8rem;margin-bottom:.5rem;{{ !$adaYangBisaSeleksi?'opacity:.45;cursor:not-allowed;':'' }}"
                    {{ !$adaYangBisaSeleksi ? 'disabled' : '' }}>
                    ⚡ Proses Semua Yang Eligible
                </button>
            </form>
            <form action="{{ route('admin.seleksi.run') }}" method="POST" id="formProsesTerpilih">
                @csrf
                <input type="hidden" name="mode" value="terpilih">
                <div id="hiddenCheckedIds"></div>
                <button type="button" onclick="konfirmProsesTerpilih()" class="btn-primary"
                    style="width:100%;padding:.8rem;background:linear-gradient(135deg,#8b5cf6,#6d28d9);">
                    ✅ Proses Siswa Terpilih
                </button>
            </form>
            @if(!$adaYangBisaSeleksi)
                <p style="color:#f59e0b;font-size:.78rem;margin-top:.5rem;">⚠️ Belum ada siswa yang sudah ujian & aktif.</p>
            @endif
        @endif
    </div>

    {{-- Finalisasi --}}
    <div class="glass-card">
        <h4 style="color:var(--primary);margin:0 0 .5rem;font-size:1rem;">🔒 Finalisasi Hasil</h4>
        <p style="color:var(--gray-text);font-size:.82rem;margin-bottom:1rem;line-height:1.5;">
            Setelah finalisasi, data <strong>dikunci permanen</strong>. Siswa belum diseleksi tidak ikut dikunci. Siswa tidak ikut ujian → otomatis <strong>Gugur</strong>.
        </p>
        @if($sudahDifinalisasi)
            <div style="background:#d1fae5;color:#059669;padding:.75rem 1rem;border-radius:8px;font-size:.85rem;">
                ✅ Hasil telah difinalisasi. Siswa sudah dapat melihat pengumuman.
            </div>
        @elseif($adaHasilSeleksi)
            <form action="{{ route('admin.seleksi.finalisasi') }}" method="POST" id="formFinalisasi">
                @csrf
                <button type="button" onclick="konfirmFinalisasi()" class="btn-primary"
                    style="width:100%;padding:.85rem;background:linear-gradient(135deg,#10b981,#059669);">
                    🔒 Finalisasi Hasil Seleksi
                </button>
            </form>
        @else
            <button disabled class="btn-primary" style="width:100%;padding:.85rem;opacity:.35;cursor:not-allowed;">
                🔒 Finalisasi Hasil Seleksi
            </button>
            <p style="color:var(--gray-text);font-size:.78rem;margin-top:.5rem;text-align:center;">Jalankan Proses Seleksi terlebih dahulu.</p>
        @endif
    </div>
</div>

{{-- Tabel Semua Siswa --}}
<div class="glass-card" style="margin-bottom:2rem;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.25rem;flex-wrap:wrap;gap:.75rem;">
        <h3 style="color:#0f172a;margin:0;font-size:1.1rem;font-weight:700;">📋 Daftar Siswa Seleksi</h3>
        @if(!$sudahDifinalisasi)
        <div style="display:flex;gap:.5rem;">
            <button onclick="selectAll(true)" style="font-size:.78rem;padding:.4rem .8rem;border:1px solid #cbd5e1;border-radius:8px;background:#f8fafc;cursor:pointer;font-weight:600;">☑ Pilih Semua</button>
            <button onclick="selectAll(false)" style="font-size:.78rem;padding:.4rem .8rem;border:1px solid #cbd5e1;border-radius:8px;background:#f8fafc;cursor:pointer;font-weight:600;">☐ Batal</button>
        </div>
        @endif
    </div>

    <div style="overflow-x:auto;border-radius:12px;border:1px solid #e2e8f0;">
        <table style="width:100%;border-collapse:collapse;min-width:700px;">
            <thead>
                <tr style="background:#f8fafc;">
                    @if(!$sudahDifinalisasi)<th style="padding:.75rem 1rem;border-bottom:1px solid #e2e8f0;width:40px;"></th>@endif
                    <th style="padding:.75rem 1rem;text-align:left;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;border-bottom:1px solid #e2e8f0;">Nama Siswa</th>
                    <th style="padding:.75rem 1rem;text-align:left;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;border-bottom:1px solid #e2e8f0;">Jurusan</th>
                    <th style="padding:.75rem 1rem;text-align:center;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;border-bottom:1px solid #e2e8f0;">Status Ujian</th>
                    <th style="padding:.75rem 1rem;text-align:center;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;border-bottom:1px solid #e2e8f0;">Rapor</th>
                    <th style="padding:.75rem 1rem;text-align:center;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;border-bottom:1px solid #e2e8f0;">Ujian</th>
                    <th style="padding:.75rem 1rem;text-align:center;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;border-bottom:1px solid #e2e8f0;">Skor Akhir</th>
                    <th style="padding:.75rem 1rem;text-align:center;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;border-bottom:1px solid #e2e8f0;">Status Seleksi</th>
                    @if(!$sudahDifinalisasi)<th style="padding:.75rem 1rem;text-align:right;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;border-bottom:1px solid #e2e8f0;">Aksi</th>@endif
                </tr>
            </thead>
            <tbody>
                @forelse($semua as $p)
                @php
                    $nilaiUjian = $p->hasil_ujian ? $p->hasil_ujian->skor : null;
                    $skorAkhir  = ($nilaiUjian !== null) ? round((0.6 * $nilaiUjian) + (0.4 * $p->nilai_rapor), 2) : null;
                    $sudahUjian = in_array($p->status, ['sudah_ujian','siap_finalisasi','siap_diumumkan']);
                    $belumUjian = $p->status === 'lolos_admin';
                    $tidakIkut  = in_array($p->status, ['tidak_mengikuti_ujian','gugur']);
                    $hasilS     = $p->hasilSeleksi;
                @endphp
                <tr style="border-bottom:1px solid #f1f5f9;{{ $p->ditunda_seleksi ? 'opacity:.6;background:#fffbeb;' : '' }}">
                    @if(!$sudahDifinalisasi)
                    <td style="padding:.75rem 1rem;text-align:center;">
                        @if($sudahUjian && !$p->ditunda_seleksi)
                            <input type="checkbox" class="cbSiswa" value="{{ $p->id }}" style="width:16px;height:16px;cursor:pointer;accent-color:var(--primary);">
                        @endif
                    </td>
                    @endif
                    <td style="padding:.75rem 1rem;font-weight:700;color:#0f172a;font-size:.9rem;">{{ $p->user->name }}</td>
                    <td style="padding:.75rem 1rem;font-size:.85rem;color:#475569;white-space:nowrap;">{{ $p->jurusan->nama }}</td>
                    <td style="padding:.75rem 1rem;text-align:center;">
                        @if($sudahUjian)
                            <span style="background:#d1fae5;color:#059669;padding:.25rem .65rem;border-radius:999px;font-size:.72rem;font-weight:700;">✅ Sudah</span>
                        @elseif($tidakIkut)
                            <span style="background:#fee2e2;color:#dc2626;padding:.25rem .65rem;border-radius:999px;font-size:.72rem;font-weight:700;">❌ Tidak Ikut</span>
                        @else
                            <span style="background:#fef3c7;color:#92400e;padding:.25rem .65rem;border-radius:999px;font-size:.72rem;font-weight:700;">🕐 Belum</span>
                        @endif
                    </td>
                    <td style="padding:.75rem 1rem;text-align:center;font-weight:600;color:#475569;">{{ $p->nilai_rapor ?? '—' }}</td>
                    <td style="padding:.75rem 1rem;text-align:center;font-weight:600;color:#475569;">{{ $nilaiUjian !== null ? $nilaiUjian : '—' }}</td>
                    <td style="padding:.75rem 1rem;text-align:center;">
                        @if($skorAkhir !== null)
                            <strong style="color:var(--primary);font-size:1rem;">{{ $skorAkhir }}</strong>
                        @elseif($tidakIkut)
                            <span style="color:#dc2626;font-weight:600;">Gugur</span>
                        @else
                            <span style="color:#94a3b8;">—</span>
                        @endif
                    </td>
                    <td style="padding:.75rem 1rem;text-align:center;">
                        @if($p->ditunda_seleksi)
                            <span style="background:#fde68a;color:#78350f;padding:.25rem .65rem;border-radius:999px;font-size:.72rem;font-weight:700;">⏸ Ditunda</span>
                        @elseif($tidakIkut)
                            <span style="background:#fee2e2;color:#991b1b;padding:.25rem .65rem;border-radius:999px;font-size:.72rem;font-weight:700;">❌ Gugur</span>
                        @elseif($belumUjian)
                            <span style="background:#f1f5f9;color:#64748b;padding:.25rem .65rem;border-radius:999px;font-size:.72rem;font-weight:700;">⏳ Belum</span>
                        @elseif($hasilS && $hasilS->is_finalisasi)
                            <span style="background:#e0f2fe;color:#0369a1;padding:.25rem .65rem;border-radius:999px;font-size:.72rem;font-weight:700;">🔒 Dikunci</span>
                        @elseif($hasilS)
                            <span style="background:#dbeafe;color:#1e40af;padding:.25rem .65rem;border-radius:999px;font-size:.72rem;font-weight:700;">🏆 Diseleksi</span>
                        @else
                            <span style="background:#f1f5f9;color:#64748b;padding:.25rem .65rem;border-radius:999px;font-size:.72rem;font-weight:700;">⏳ Belum</span>
                        @endif
                    </td>
                    @if(!$sudahDifinalisasi)
                    <td style="padding:.75rem 1rem;text-align:right;white-space:nowrap;">
                        @if($belumUjian)
                            <form action="{{ route('admin.seleksi.tanda-tidak-ujian') }}" method="POST" style="display:inline;"
                                onsubmit="return confirm('Tandai {{ $p->user->name }} sebagai Tidak Mengikuti Ujian?');">
                                @csrf
                                <input type="hidden" name="pendaftaran_id" value="{{ $p->id }}">
                                <button type="submit" style="font-size:.75rem;padding:.3rem .65rem;border:none;border-radius:7px;background:#fee2e2;color:#dc2626;cursor:pointer;font-weight:600;">❌ Tdk Ikut</button>
                            </form>
                        @elseif($sudahUjian && !($hasilS && $hasilS->is_finalisasi))
                            <form action="{{ route('admin.seleksi.tunda') }}" method="POST" style="display:inline;">
                                @csrf
                                <input type="hidden" name="pendaftaran_id" value="{{ $p->id }}">
                                @if($p->ditunda_seleksi)
                                    <input type="hidden" name="aksi" value="aktifkan">
                                    <button type="submit" style="font-size:.75rem;padding:.3rem .65rem;border:none;border-radius:7px;background:#d1fae5;color:#059669;cursor:pointer;font-weight:600;">▶ Aktifkan</button>
                                @else
                                    <input type="hidden" name="aksi" value="tunda">
                                    <button type="submit" style="font-size:.75rem;padding:.3rem .65rem;border:none;border-radius:7px;background:#fde68a;color:#78350f;cursor:pointer;font-weight:600;">⏸ Tunda</button>
                                @endif
                            </form>
                        @else
                            <span style="color:#cbd5e1;font-size:.78rem;">—</span>
                        @endif
                    </td>
                    @endif
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="padding:3rem;text-align:center;color:#94a3b8;">
                        <div style="font-size:2rem;margin-bottom:.5rem;">📭</div>
                        Belum ada siswa yang lolos administrasi.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Tabel Hasil Seleksi --}}
@if($hasil->isNotEmpty())
<div class="glass-card">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.25rem;flex-wrap:wrap;gap:.75rem;">
        <h3 style="color:#0f172a;margin:0;font-size:1.1rem;font-weight:700;">🏆 Hasil Seleksi {{ $sudahDifinalisasi ? '(Final)' : '(Draft)' }}</h3>
        @if(!$sudahDifinalisasi)
        <span style="background:#fef3c7;color:#92400e;font-size:.78rem;padding:.3rem .75rem;border-radius:8px;font-weight:600;">⏳ Draft — Masih dapat diubah</span>
        @endif
    </div>
    <div style="overflow-x:auto;border-radius:12px;border:1px solid #e2e8f0;">
        <table style="width:100%;border-collapse:collapse;min-width:500px;">
            <thead>
                <tr style="background:#f8fafc;">
                    <th style="padding:.75rem 1rem;text-align:center;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;border-bottom:1px solid #e2e8f0;width:60px;">Ranking</th>
                    <th style="padding:.75rem 1rem;text-align:left;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;border-bottom:1px solid #e2e8f0;">Nama</th>
                    <th style="padding:.75rem 1rem;text-align:left;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;border-bottom:1px solid #e2e8f0;">Jurusan</th>
                    <th style="padding:.75rem 1rem;text-align:center;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;border-bottom:1px solid #e2e8f0;">Skor Akhir</th>
                    <th style="padding:.75rem 1rem;text-align:center;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;border-bottom:1px solid #e2e8f0;">Kategori</th>
                    <th style="padding:.75rem 1rem;text-align:center;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;border-bottom:1px solid #e2e8f0;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($hasil as $h)
                <tr style="border-bottom:1px solid #f1f5f9;background:{{ $h->kategori_kelulusan==='Unggulan'?'#fffbeb':'#f0fdf4' }};">
                    <td style="padding:.75rem 1rem;text-align:center;">
                        <span style="font-size:1rem;font-weight:800;color:{{ $h->kategori_kelulusan==='Unggulan'?'#d97706':'#059669' }};">#{{ $h->ranking }}</span>
                    </td>
                    <td style="padding:.75rem 1rem;font-weight:700;color:#0f172a;">{{ $h->pendaftaran->user->name }}</td>
                    <td style="padding:.75rem 1rem;font-size:.875rem;color:#475569;">{{ $h->pendaftaran->jurusan->nama }}</td>
                    <td style="padding:.75rem 1rem;text-align:center;"><strong style="font-size:1rem;">{{ $h->skor_akhir }}</strong></td>
                    <td style="padding:.75rem 1rem;text-align:center;">
                        @if($h->kategori_kelulusan === 'Unggulan')
                            <span style="background:#fbbf24;color:#78350f;padding:.3rem .75rem;border-radius:999px;font-size:.75rem;font-weight:700;">⭐ Unggulan</span>
                        @else
                            <span style="background:#6ee7b7;color:#065f46;padding:.3rem .75rem;border-radius:999px;font-size:.75rem;font-weight:700;">✅ Reguler</span>
                        @endif
                    </td>
                    <td style="padding:.75rem 1rem;text-align:center;">
                        @if($h->is_finalisasi)
                            <span style="background:#e0f2fe;color:#0369a1;padding:.25rem .65rem;border-radius:999px;font-size:.72rem;font-weight:700;">🔒 Dikunci</span>
                        @else
                            <span style="background:#fef3c7;color:#92400e;padding:.25rem .65rem;border-radius:999px;font-size:.72rem;font-weight:700;">⏳ Draft</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

<script>
function selectAll(state) { document.querySelectorAll('.cbSiswa').forEach(cb => cb.checked = state); }

function konfirmProsesSemua() {
    if (confirm('Proses seleksi untuk SEMUA siswa yang sudah ujian?\n\nRumus: Skor = (60% × Ujian) + (40% × Rapor)\n• ≥ 85 → Lulus Unggulan\n• < 85 → Lulus Reguler\n\nHasil masih dapat diperbarui sebelum finalisasi.')) {
        document.getElementById('formProsesSemuaSiswa').submit();
    }
}

function konfirmProsesTerpilih() {
    const checked = [...document.querySelectorAll('.cbSiswa:checked')].map(cb => cb.value);
    if (checked.length === 0) { alert('⚠️ Belum ada siswa yang dipilih!'); return; }
    if (!confirm(`Proses seleksi untuk ${checked.length} siswa yang dipilih?`)) return;
    const container = document.getElementById('hiddenCheckedIds');
    container.innerHTML = '';
    checked.forEach(id => {
        const inp = document.createElement('input');
        inp.type = 'hidden'; inp.name = 'pendaftaran_ids[]'; inp.value = id;
        container.appendChild(inp);
    });
    document.getElementById('formProsesTerpilih').submit();
}

function konfirmFinalisasi() {
    if (confirm('⚠️ PERHATIAN!\n\nApakah Anda yakin ingin memfinalisasi hasil seleksi?\nData TIDAK DAPAT DIUBAH setelah ini.')) {
        document.getElementById('formFinalisasi').submit();
    }
}
</script>
@endsection
