@extends('layouts.admin')
@section('title', 'Manajemen Ujian CBT')

@section('content')

@if(session('success'))
    <div style="background:#d1fae5;color:#059669;padding:.875rem 1.25rem;border-radius:12px;margin-bottom:1.5rem;font-weight:600;display:flex;align-items:center;gap:.5rem;border:1px solid #a7f3d0;">
        ✅ {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div style="background:#fee2e2;color:#dc2626;padding:.875rem 1.25rem;border-radius:12px;margin-bottom:1.5rem;font-weight:600;display:flex;align-items:center;gap:.5rem;border:1px solid #fca5a5;">
        ⚠️ {{ session('error') }}
    </div>
@endif

{{-- ─── Page Header ─── --}}
<div style="margin-bottom:2rem;">
    <h1 style="font-size:1.5rem;font-weight:800;color:#0f172a;margin:0 0 .25rem;">Modul Ujian CBT</h1>
    <p style="color:#64748b;font-size:.9rem;margin:0;">Kelola sesi ujian online untuk calon siswa PPDB.</p>
</div>

{{-- ─── Daftar Ujian ─── --}}
<div class="glass-card" style="margin-bottom:2rem;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem;">
        <h3 style="margin:0;font-size:1.1rem;font-weight:700;color:#0f172a;">📋 Daftar Sesi Ujian</h3>
        <button onclick="toggleForm()" class="btn-primary" style="padding:.6rem 1.25rem;font-size:.9rem;display:flex;align-items:center;gap:.5rem;">
            <span>➕</span> Buat Sesi Baru
        </button>
    </div>

    {{-- Tabel responsif --}}
    <div style="overflow-x:auto;border-radius:12px;border:1px solid #e2e8f0;">
        <table style="width:100%;border-collapse:collapse;min-width:600px;">
            <thead>
                <tr style="background:#f8fafc;">
                    <th style="padding:.875rem 1rem;text-align:left;font-size:.75rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;border-bottom:1px solid #e2e8f0;">Judul Ujian</th>
                    <th style="padding:.875rem 1rem;text-align:left;font-size:.75rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;border-bottom:1px solid #e2e8f0;">Durasi</th>
                    <th style="padding:.875rem 1rem;text-align:left;font-size:.75rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;border-bottom:1px solid #e2e8f0;">Jadwal</th>
                    <th style="padding:.875rem 1rem;text-align:center;font-size:.75rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;border-bottom:1px solid #e2e8f0;">Status</th>
                    <th style="padding:.875rem 1rem;text-align:right;font-size:.75rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;border-bottom:1px solid #e2e8f0;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ujians as $u)
                <tr style="border-bottom:1px solid #f1f5f9;transition:background .15s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                    <td style="padding:.875rem 1rem;">
                        <div style="font-weight:700;color:#0f172a;margin-bottom:.2rem;">{{ $u->judul }}</div>
                        <div style="font-size:.75rem;color:#94a3b8;">
                            @if($u->acak_soal) <span style="background:#eef2ff;color:#4f46e5;padding:.1rem .4rem;border-radius:4px;margin-right:.25rem;">🔀 Soal</span> @endif
                            @if($u->acak_jawaban) <span style="background:#eef2ff;color:#4f46e5;padding:.1rem .4rem;border-radius:4px;">🔀 Jawaban</span> @endif
                        </div>
                    </td>
                    <td style="padding:.875rem 1rem;">
                        <span style="font-weight:600;color:#0f172a;">{{ $u->durasi_menit }}</span>
                        <span style="color:#64748b;font-size:.875rem;"> menit</span>
                    </td>
                    <td style="padding:.875rem 1rem;font-size:.8rem;color:#475569;">
                        @if($u->jadwal_mulai)
                            <div>▶ {{ \Carbon\Carbon::parse($u->jadwal_mulai)->format('d M Y, H:i') }}</div>
                            <div>⏹ {{ \Carbon\Carbon::parse($u->jadwal_selesai)->format('d M Y, H:i') }}</div>
                        @else
                            <span style="color:#cbd5e1;">—</span>
                        @endif
                    </td>
                    <td style="padding:.875rem 1rem;text-align:center;">
                        @if($u->is_tutup)
                            <span style="background:#fee2e2;color:#dc2626;padding:.3rem .75rem;border-radius:999px;font-weight:700;font-size:.75rem;display:inline-flex;align-items:center;gap:.25rem;">🔒 Ditutup</span>
                        @elseif($u->is_active)
                            <span style="background:#d1fae5;color:#059669;padding:.3rem .75rem;border-radius:999px;font-weight:700;font-size:.75rem;display:inline-flex;align-items:center;gap:.25rem;">✅ Aktif</span>
                        @else
                            <span style="background:#f1f5f9;color:#94a3b8;padding:.3rem .75rem;border-radius:999px;font-size:.75rem;">Nonaktif</span>
                        @endif
                    </td>
                    <td style="padding:.875rem 1rem;text-align:right;">
                        <div style="display:flex;gap:.5rem;justify-content:flex-end;align-items:center;">
                            <a href="{{ route('admin.ujian.show', $u->id) }}" style="background:#3b82f6;color:white;padding:.45rem 1rem;border-radius:8px;font-size:.8rem;font-weight:600;text-decoration:none;white-space:nowrap;display:inline-flex;align-items:center;gap:.35rem;">
                                📝 Kelola
                            </a>
                            @if(!$u->is_tutup)
                                <form action="{{ route('admin.ujian.tutup', $u->id) }}" method="POST" onsubmit="return confirm('Tutup ujian? Siswa yang belum ujian akan otomatis berstatus Tidak Mengikuti.');">
                                    @csrf
                                    <button type="submit" style="background:#ef4444;color:white;padding:.45rem .9rem;border-radius:8px;font-size:.8rem;font-weight:600;border:none;cursor:pointer;white-space:nowrap;">
                                        🔒 Tutup
                                    </button>
                                </form>
                            @else
                                <span style="font-size:.75rem;color:#94a3b8;white-space:nowrap;">Sudah ditutup</span>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="padding:3rem;text-align:center;color:#94a3b8;">
                        <div style="font-size:2rem;margin-bottom:.5rem;">📭</div>
                        Belum ada sesi ujian. Klik <strong>Buat Sesi Baru</strong> untuk memulai.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ─── Form Tambah (Collapsible) ─── --}}
<div id="formContainer" class="glass-card" style="display:none;border-top:3px solid var(--primary);">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
        <h3 style="margin:0;color:var(--primary);font-size:1.1rem;font-weight:700;">➕ Buat Sesi Ujian Baru</h3>
        <button onclick="toggleForm()" style="background:none;border:none;cursor:pointer;font-size:1.2rem;color:#94a3b8;padding:.25rem;" title="Tutup">✕</button>
    </div>

    <form action="{{ route('admin.ujian.store') }}" method="POST">
        @csrf
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;">
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label" for="judul">Judul/Materi Ujian</label>
                <input type="text" name="judul" id="judul" class="form-control" required placeholder="Contoh: Tes Skolastik & Jurusan">
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label" for="durasi_menit">Durasi (Menit)</label>
                <input type="number" name="durasi_menit" id="durasi_menit" class="form-control" required placeholder="Contoh: 60">
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label" for="jadwal_mulai">📅 Tanggal Mulai</label>
                <input type="datetime-local" name="jadwal_mulai" id="jadwal_mulai" class="form-control">
                <small style="color:var(--gray-text);font-size:.75rem;">Kosongkan jika tanpa batas waktu.</small>
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label" for="jadwal_selesai">📅 Tanggal Selesai</label>
                <input type="datetime-local" name="jadwal_selesai" id="jadwal_selesai" class="form-control">
                <small style="color:var(--gray-text);font-size:.75rem;">Setelah ini siswa tidak bisa akses.</small>
            </div>
        </div>

        <div style="background:#f8fafc;padding:1rem;border-radius:10px;border:1px solid #e2e8f0;margin-top:1.25rem;display:flex;gap:2rem;flex-wrap:wrap;">
            <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer;font-weight:600;font-size:.9rem;">
                <input type="checkbox" name="acak_soal" id="acak_soal" value="1" style="width:18px;height:18px;accent-color:var(--primary);">
                🔀 Acak Urutan Soal
            </label>
            <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer;font-weight:600;font-size:.9rem;">
                <input type="checkbox" name="acak_jawaban" id="acak_jawaban" value="1" style="width:18px;height:18px;accent-color:var(--primary);">
                🔀 Acak Pilihan Jawaban
            </label>
        </div>

        <div style="display:flex;gap:1rem;margin-top:1.5rem;justify-content:flex-end;">
            <button type="button" onclick="toggleForm()" class="btn-outline">Batal</button>
            <button type="submit" class="btn-primary" style="padding:.75rem 2rem;">✅ Simpan Sesi Ujian</button>
        </div>
    </form>
</div>

<script>
    function toggleForm() {
        const fc = document.getElementById('formContainer');
        fc.style.display = fc.style.display === 'none' ? 'block' : 'none';
        if (fc.style.display === 'block') fc.scrollIntoView({behavior: 'smooth', block: 'start'});
    }
</script>
@endsection
