@extends('layouts.admin')
@section('title', 'Proses Seleksi')

@section('content')

{{-- Flash Messages --}}
@if(session('success'))
    <div style="background: #d1fae5; color: #059669; padding: 1rem 1.25rem; border-radius: var(--radius-sm); margin-bottom: 1.5rem; font-weight: 500; display: flex; align-items: center; gap: 0.5rem;">
        ✅ {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div style="background: #fee2e2; color: #dc2626; padding: 1rem 1.25rem; border-radius: var(--radius-sm); margin-bottom: 1.5rem; font-weight: 500; display: flex; align-items: center; gap: 0.5rem;">
        ⚠️ {{ session('error') }}
    </div>
@endif

{{-- Alur Tahapan --}}
<div class="glass-card" style="margin-bottom: 2rem;">
    <h3 style="color: var(--primary); margin-bottom: 1rem;">Alur Proses Seleksi PPDB</h3>
    <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap; font-size: 0.85rem;">
        @php
            $steps = [
                ['label' => 'Registrasi', 'done' => true],
                ['label' => 'Verifikasi Administrasi', 'done' => true],
                ['label' => 'Ujian CBT', 'done' => true],
                ['label' => 'Proses Seleksi', 'done' => $adaHasilSeleksi || $sudahDifinalisasi, 'active' => !$adaHasilSeleksi && !$sudahDifinalisasi],
                ['label' => 'Finalisasi', 'done' => $sudahDifinalisasi, 'active' => $adaHasilSeleksi && !$sudahDifinalisasi],
                ['label' => 'Pengumuman', 'done' => $sudahDifinalisasi],
            ];
        @endphp
        @foreach($steps as $i => $step)
            <span style="
                padding: 0.4rem 0.9rem;
                border-radius: 999px;
                font-weight: 600;
                background: {{ ($step['done'] ?? false) ? '#d1fae5' : (($step['active'] ?? false) ? '#dbeafe' : '#f1f5f9') }};
                color: {{ ($step['done'] ?? false) ? '#059669' : (($step['active'] ?? false) ? '#1d4ed8' : '#94a3b8') }};
                border: 1.5px solid {{ ($step['done'] ?? false) ? '#6ee7b7' : (($step['active'] ?? false) ? '#93c5fd' : '#e2e8f0') }};
            ">
                {{ ($step['done'] ?? false) ? '✓ ' : '' }}{{ $step['label'] }}
            </span>
            @if(!$loop->last)
                <span style="color: #cbd5e1;">›</span>
            @endif
        @endforeach
    </div>
</div>

{{-- Panel Aksi Utama --}}
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">

    {{-- Card Proses Seleksi --}}
    <div class="glass-card">
        <h4 style="color: var(--primary); margin-bottom: 0.5rem;">⚡ Proses Seleksi</h4>
        <p style="color: var(--gray-text); font-size: 0.85rem; margin-bottom: 1rem;">
            Sistem akan menghitung Skor Akhir menggunakan rumus:<br>
            <code style="background: #f1f5f9; padding: 0.2rem 0.4rem; border-radius: 4px; font-size: 0.8rem;">Skor Akhir = (0.6 × Nilai Ujian) + (0.4 × Nilai Rapor)</code><br>
            Pengelompokan: <strong>≥85 Unggulan</strong> | <strong>70–84 Reguler</strong> | <strong>&lt;70 Tidak Lulus</strong>
        </p>

        @if($pesanTidakBoleh)
            <div style="background: #fef3c7; color: #92400e; padding: 0.75rem 1rem; border-radius: var(--radius-sm); font-size: 0.85rem; margin-bottom: 1rem; border-left: 3px solid #f59e0b;">
                ⚠️ {{ $pesanTidakBoleh }}
            </div>
        @endif

        @if($sudahDifinalisasi)
            <div style="background: #d1fae5; color: #059669; padding: 0.75rem 1rem; border-radius: var(--radius-sm); font-size: 0.85rem;">
                ✅ Hasil seleksi sudah difinalisasi dan tidak dapat diubah lagi.
            </div>
        @else
            <form action="{{ route('admin.seleksi.run') }}" method="POST" id="formProsesSeleksi">
                @csrf
                <button type="button"
                    id="btnProsesSeleksi"
                    onclick="konfirmasiProses()"
                    class="btn-primary"
                    style="width: 100%; padding: 0.85rem; font-size: 1rem; background: linear-gradient(135deg, #3b82f6, #1d4ed8); {{ !$bolehProsesSeleksi ? 'opacity: 0.5; cursor: not-allowed;' : '' }}"
                    {{ !$bolehProsesSeleksi ? 'disabled' : '' }}>
                    ⚡ Proses Seleksi
                </button>
            </form>
        @endif
    </div>

    {{-- Card Finalisasi --}}
    <div class="glass-card">
        <h4 style="color: var(--primary); margin-bottom: 0.5rem;">🔒 Finalisasi Hasil</h4>
        <p style="color: var(--gray-text); font-size: 0.85rem; margin-bottom: 1rem;">
            Setelah difinalisasi, data seleksi akan <strong>dikunci permanen</strong> dan tidak dapat diubah. Status berubah menjadi <strong>"Siap Diumumkan"</strong> dan siswa dapat melihat hasilnya.
        </p>

        @if($sudahDifinalisasi)
            <div style="background: #d1fae5; color: #059669; padding: 0.75rem 1rem; border-radius: var(--radius-sm); font-size: 0.85rem;">
                ✅ Hasil telah difinalisasi. Siswa sudah dapat melihat pengumuman.
            </div>
        @elseif($adaHasilSeleksi)
            <form action="{{ route('admin.seleksi.finalisasi') }}" method="POST" id="formFinalisasi">
                @csrf
                <button type="button"
                    onclick="konfirmasiFinalisasi()"
                    class="btn-primary"
                    style="width: 100%; padding: 0.85rem; font-size: 1rem; background: linear-gradient(135deg, #10b981, #059669);">
                    🔒 Finalisasi Hasil
                </button>
            </form>
        @else
            <button class="btn-primary" disabled style="width: 100%; padding: 0.85rem; font-size: 1rem; opacity: 0.4; cursor: not-allowed; background: #94a3b8;">
                🔒 Finalisasi Hasil
            </button>
            <p style="color: var(--gray-text); font-size: 0.8rem; margin-top: 0.5rem; text-align: center;">Jalankan Proses Seleksi terlebih dahulu.</p>
        @endif
    </div>
</div>

{{-- Tabel Daftar Siswa Seleksi --}}
<div class="glass-card" style="margin-bottom: 2rem;">
    <h3 style="margin-bottom: 1.5rem; color: var(--dark);">📋 Daftar Siswa (Lolos Administrasi & Ujian)</h3>
    <div class="table-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th>Nama Siswa</th>
                    <th>Jurusan</th>
                    <th>Status Ujian</th>
                    <th>Nilai Rapor</th>
                    <th>Nilai Ujian</th>
                    <th>Skor Akhir</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($siswaDaftarSeleksi as $p)
                @php
                    $nilaiUjian = $p->hasil_ujian ? $p->hasil_ujian->skor : null;
                    $skorAkhir  = ($nilaiUjian !== null)
                        ? round((0.6 * $nilaiUjian) + (0.4 * $p->nilai_rapor), 2)
                        : null;
                @endphp
                <tr>
                    <td><strong>{{ $p->user->name }}</strong></td>
                    <td>{{ $p->jurusan->nama }}</td>
                    <td>
                        @if($p->hasil_ujian)
                            <span style="background: #d1fae5; color: #059669; padding: 0.2rem 0.5rem; border-radius: 4px; font-weight: 600; font-size: 0.8rem;">Sudah Ujian ✓</span>
                        @else
                            <span style="background: #fee2e2; color: #dc2626; padding: 0.2rem 0.5rem; border-radius: 4px; font-weight: 600; font-size: 0.8rem;">Belum Ujian ✗</span>
                        @endif
                    </td>
                    <td>{{ $p->nilai_rapor }}</td>
                    <td>{{ $nilaiUjian !== null ? $nilaiUjian : '—' }}</td>
                    <td>
                        @if($skorAkhir !== null)
                            <strong style="color: var(--primary);">{{ $skorAkhir }}</strong>
                        @else
                            <span style="color: #94a3b8;">—</span>
                        @endif
                    </td>
                    <td>
                        @if($p->status === 'siap_diumumkan')
                            <span style="background: #d1fae5; color: #059669; padding: 0.2rem 0.5rem; border-radius: 4px; font-weight: 600; font-size: 0.75rem;">SIAP DIUMUMKAN</span>
                        @elseif($p->status === 'siap_finalisasi')
                            <span style="background: #dbeafe; color: #1d4ed8; padding: 0.2rem 0.5rem; border-radius: 4px; font-weight: 600; font-size: 0.75rem;">SIAP FINALISASI</span>
                        @elseif($p->status === 'sudah_ujian')
                            <span style="background: #fef3c7; color: #d97706; padding: 0.2rem 0.5rem; border-radius: 4px; font-weight: 600; font-size: 0.75rem;">SUDAH UJIAN</span>
                        @else
                            <span style="background: #f1f5f9; color: #64748b; padding: 0.2rem 0.5rem; border-radius: 4px; font-weight: 600; font-size: 0.75rem;">LOLOS ADMIN</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; color: var(--gray-text); padding: 3rem;">
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
    <h3 style="margin-bottom: 1.5rem; color: var(--dark);">🏆 Hasil Seleksi</h3>
    <div class="table-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th>Ranking</th>
                    <th>Nama</th>
                    <th>Jurusan</th>
                    <th>Skor Akhir</th>
                    <th>Kategori</th>
                    <th>Status Kunci</th>
                </tr>
            </thead>
            <tbody>
                @foreach($hasil as $h)
                <tr style="{{ $h->kategori_kelulusan === 'Tidak Lulus' ? 'background: #fef2f2;' : ($h->kategori_kelulusan === 'Unggulan' ? 'background: #f0fdf4;' : 'background: #f0f9ff;') }}">
                    <td>
                        <span style="font-size: 1.2rem; font-weight: 800; color: {{ $h->kategori_kelulusan === 'Tidak Lulus' ? '#dc2626' : '#059669' }};">
                            #{{ $h->ranking }}
                        </span>
                    </td>
                    <td><strong>{{ $h->pendaftaran->user->name }}</strong></td>
                    <td>{{ $h->pendaftaran->jurusan->nama }}</td>
                    <td><strong>{{ $h->skor_akhir }}</strong></td>
                    <td>
                        @if($h->kategori_kelulusan === 'Unggulan')
                            <span style="background: #fbbf24; color: #78350f; padding: 0.25rem 0.75rem; border-radius: 999px; font-size: 0.85rem; font-weight: 700;">⭐ Unggulan</span>
                        @elseif($h->kategori_kelulusan === 'Reguler')
                            <span style="background: #6ee7b7; color: #065f46; padding: 0.25rem 0.75rem; border-radius: 999px; font-size: 0.85rem; font-weight: 700;">✅ Reguler</span>
                        @else
                            <span style="background: #fca5a5; color: #7f1d1d; padding: 0.25rem 0.75rem; border-radius: 999px; font-size: 0.85rem; font-weight: 700;">✗ Tidak Lulus</span>
                        @endif
                    </td>
                    <td>
                        @if($h->is_finalisasi)
                            <span style="background: #e0f2fe; color: #0369a1; padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.8rem; font-weight: 600;">🔒 Dikunci</span>
                        @else
                            <span style="background: #fef3c7; color: #92400e; padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.8rem; font-weight: 600;">⏳ Draft</span>
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
    function konfirmasiProses() {
        if (confirm('Apakah Anda yakin ingin mengubah status verifikasi?\n\nSistem akan memproses seleksi berdasarkan Skor Akhir = (0.6 × Nilai Ujian) + (0.4 × Nilai Rapor).\n\nPengelompokan:\n• ≥ 85 → Unggulan\n• 70–84 → Reguler\n• < 70 → Tidak Lulus')) {
            document.getElementById('formProsesSeleksi').submit();
        }
    }

    function konfirmasiFinalisasi() {
        if (confirm('⚠️ PERHATIAN!\n\nApakah Anda yakin ingin memfinalisasi hasil seleksi?\n\nData tidak dapat diubah setelah ini dan hasil akan langsung dapat dilihat oleh siswa.')) {
            document.getElementById('formFinalisasi').submit();
        }
    }
</script>

@endsection
