@extends('layouts.admin')
@section('title', 'Manajemen Ujian CBT')

@section('content')

@if(session('success'))
    <div style="background:#d1fae5;color:#059669;padding:.875rem 1.25rem;border-radius:12px;margin-bottom:1.5rem;font-weight:600;display:flex;align-items:center;gap:.5rem;border:1px solid #a7f3d0;">
        ✅ {{ session('success') }}
    </div>
@endif

{{-- ─── Page Header ─── --}}
<div style="margin-bottom:2rem;">
    <h1 style="font-size:1.5rem;font-weight:800;color:#0f172a;margin:0 0 .25rem;">Modul Ujian CBT</h1>
    <p style="color:#64748b;font-size:.9rem;margin:0;">Kelola periode global dan modul ujian per jurusan.</p>
</div>

{{-- ─── Card Pengaturan CBT (Glassmorphism Gradient) ─── --}}
<div style="background: linear-gradient(135deg, #e0e7ff 0%, #f3e8ff 100%); border-radius: 24px; padding: 2rem; margin-bottom: 2.5rem; border: 1px solid rgba(255,255,255,0.6); box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05); backdrop-filter: blur(10px);">
    <div style="display:flex; align-items:center; gap:1rem; margin-bottom:1.5rem;">
        <div style="background:white; width:48px; height:48px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:1.5rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">⚙️</div>
        <div>
            <h3 style="margin:0; font-size:1.25rem; font-weight:800; color:#1e1b4b;">Pengaturan Global CBT</h3>
            <p style="margin:0; font-size:.875rem; color:#4338ca; font-weight:500;">Tentukan periode aktif untuk seluruh sistem CBT.</p>
        </div>
    </div>

    <form action="{{ route('admin.ujian.cbt_settings') }}" method="POST">
        @csrf
        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:1.5rem;">
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label" style="color:#312e81; font-weight:700;">📅 Tanggal Mulai</label>
                <input type="datetime-local" name="cbt_tgl_mulai" class="form-control" style="background:rgba(255,255,255,0.7); border-color:#c7d2fe;" value="{{ \Carbon\Carbon::parse($settings['cbt_tgl_mulai'] ?? now())->format('Y-m-d\TH:i') }}" required>
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label" style="color:#312e81; font-weight:700;">📅 Tanggal Selesai</label>
                <input type="datetime-local" name="cbt_tgl_selesai" class="form-control" style="background:rgba(255,255,255,0.7); border-color:#c7d2fe;" value="{{ \Carbon\Carbon::parse($settings['cbt_tgl_selesai'] ?? now()->addDays(3))->format('Y-m-d\TH:i') }}" required>
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label" style="color:#312e81; font-weight:700;">⏱️ Durasi Default (Menit)</label>
                <input type="number" name="cbt_durasi_default" class="form-control" style="background:rgba(255,255,255,0.7); border-color:#c7d2fe;" value="{{ $settings['cbt_durasi_default'] ?? 60 }}" required>
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label" style="color:#312e81; font-weight:700;">🔄 Maks. Percobaan</label>
                <input type="number" name="cbt_max_percobaan" class="form-control" style="background:rgba(255,255,255,0.7); border-color:#c7d2fe;" value="{{ $settings['cbt_max_percobaan'] ?? 1 }}" required>
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label" style="color:#312e81; font-weight:700;">🚦 Status CBT</label>
                <select name="cbt_status" class="form-control" style="background:rgba(255,255,255,0.7); border-color:#c7d2fe; font-weight:600;">
                    <option value="aktif" {{ ($settings['cbt_status'] ?? '') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="ditutup" {{ ($settings['cbt_status'] ?? '') == 'ditutup' ? 'selected' : '' }}>Ditutup</option>
                </select>
            </div>
        </div>

        <div style="margin-top:2rem; display:flex; justify-content:flex-end;">
            <button type="submit" class="btn-primary" style="padding:.75rem 2.5rem; background:linear-gradient(to right, #4f46e5, #7c3aed); border:none; box-shadow: 0 4px 15px rgba(79,70,229,0.3);">
                💾 Simpan Pengaturan
            </button>
        </div>
    </form>
</div>

{{-- ─── Daftar Ujian Per Jurusan ─── --}}
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
    <h2 style="font-size:1.25rem; font-weight:800; color:#1e293b; margin:0;">📋 Daftar Ujian Per Jurusan</h2>
    <button onclick="toggleForm()" class="btn-primary" style="padding:.6rem 1.25rem; font-size:.875rem;">
        ➕ Buat Modul Baru
    </button>
</div>

<div class="glass-card" style="padding:0; overflow:hidden;">
    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="background:#f8fafc; border-bottom:1px solid #e2e8f0;">
                    <th style="padding:1rem 1.5rem; text-align:left; font-size:.75rem; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.05em;">Jurusan</th>
                    <th style="padding:1rem 1.5rem; text-align:center; font-size:.75rem; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.05em;">Jumlah Soal</th>
                    <th style="padding:1rem 1.5rem; text-align:center; font-size:.75rem; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.05em;">Durasi</th>
                    <th style="padding:1rem 1.5rem; text-align:center; font-size:.75rem; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.05em;">Status</th>
                    <th style="padding:1rem 1.5rem; text-align:right; font-size:.75rem; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.05em;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ujians as $u)
                <tr style="border-bottom:1px solid #f1f5f9; transition:all 0.2s;" onmouseover="this.style.background='rgba(248,250,252,0.8)'" onmouseout="this.style.background='transparent'">
                    <td style="padding:1rem 1.5rem;">
                        <div style="font-weight:700; color:#0f172a; font-size:.95rem;">{{ $u->jurusan->nama ?? 'Umum / Semua Jurusan' }}</div>
                        <div style="font-size:.75rem; color:#94a3b8; margin-top:2px;">{{ $u->judul }}</div>
                    </td>
                    <td style="padding:1rem 1.5rem; text-align:center;">
                        <span style="background:#f0f9ff; color:#0369a1; padding:.25rem .75rem; border-radius:999px; font-weight:700; font-size:.8rem;">
                            {{ $u->soals_count }} Soal
                        </span>
                    </td>
                    <td style="padding:1rem 1.5rem; text-align:center; color:#475569; font-weight:600; font-size:.9rem;">
                        {{ $u->durasi_menit }} Menit
                    </td>
                    <td style="padding:1rem 1.5rem; text-align:center;">
                        <form action="{{ route('admin.ujian.toggle', $u->id) }}" method="POST">
                            @csrf
                            <button type="submit" style="background:none; border:none; cursor:pointer; padding:0;">
                                @if($u->is_active)
                                    <span style="background:#d1fae5; color:#059669; padding:.3rem .75rem; border-radius:999px; font-weight:700; font-size:.75rem; display:inline-flex; align-items:center; gap:4px;">
                                        <span style="width:6px; height:6px; background:#10b981; border-radius:50%;"></span> Aktif
                                    </span>
                                @else
                                    <span style="background:#f1f5f9; color:#64748b; padding:.3rem .75rem; border-radius:999px; font-weight:700; font-size:.75rem; display:inline-flex; align-items:center; gap:4px;">
                                        <span style="width:6px; height:6px; background:#94a3b8; border-radius:50%;"></span> Nonaktif
                                    </span>
                                @endif
                            </button>
                        </form>
                    </td>
                    <td style="padding:1rem 1.5rem; text-align:right;">
                        <div style="display:flex; gap:.5rem; justify-content:flex-end;">
                            <a href="{{ route('admin.ujian.show', $u->id) }}" class="btn-primary" style="padding:.45rem .9rem; font-size:.8rem; border-radius:8px; display:inline-flex; align-items:center; gap:4px;">
                                📝 Kelola Soal
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="padding:4rem; text-align:center; color:#94a3b8;">
                        <div style="font-size:3rem; margin-bottom:1rem;">📂</div>
                        <p style="margin:0; font-weight:600;">Belum ada modul ujian per jurusan.</p>
                        <p style="margin:0.5rem 0 0; font-size:.875rem;">Klik "Buat Modul Baru" untuk memulai.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ─── Form Tambah Modul (Collapsible) ─── --}}
<div id="formContainer" class="glass-card" style="display:none; margin-top:2rem; border-top:4px solid #4f46e5;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
        <h3 style="margin:0; font-weight:800; color:#1e293b;">➕ Buat Modul Ujian Baru</h3>
        <button onclick="toggleForm()" style="background:none; border:none; cursor:pointer; color:#94a3b8; font-size:1.25rem;">✕</button>
    </div>

    <form action="{{ route('admin.ujian.store') }}" method="POST">
        @csrf
        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap:1.5rem;">
            <div class="form-group">
                <label class="form-label">Nama/Judul Modul</label>
                <input type="text" name="judul" class="form-control" placeholder="Contoh: Tes Skolastik Jurusan RPL" required>
            </div>
            <div class="form-group">
                <label class="form-label">Tentukan Jurusan</label>
                <select name="jurusan_id" class="form-control" required>
                    <option value="">-- Pilih Jurusan --</option>
                    @foreach($jurusans as $j)
                        <option value="{{ $j->id }}">{{ $j->nama }}</option>
                    @endforeach
                </select>
                <small style="color:#64748b; font-size:.75rem;">Siswa jurusan ini hanya akan melihat modul ini.</small>
            </div>
            <div class="form-group">
                <label class="form-label">Durasi (Menit)</label>
                <input type="number" name="durasi_menit" class="form-control" value="{{ $settings['cbt_durasi_default'] ?? 60 }}" required>
            </div>
        </div>

        <div style="background:#f8fafc; padding:1.25rem; border-radius:12px; border:1px solid #e2e8f0; margin-top:1rem; display:flex; gap:2rem;">
            <label style="display:flex; align-items:center; gap:0.5rem; cursor:pointer; font-weight:600; font-size:.9rem; color:#475569;">
                <input type="checkbox" name="acak_soal" value="1" style="width:18px; height:18px; accent-color:#4f46e5;"> Acak Soal
            </label>
            <label style="display:flex; align-items:center; gap:0.5rem; cursor:pointer; font-weight:600; font-size:.9rem; color:#475569;">
                <input type="checkbox" name="acak_jawaban" value="1" style="width:18px; height:18px; accent-color:#4f46e5;"> Acak Jawaban
            </label>
        </div>

        <div style="display:flex; justify-content:flex-end; gap:1rem; margin-top:2rem;">
            <button type="button" onclick="toggleForm()" class="btn-outline">Batal</button>
            <button type="submit" class="btn-primary" style="padding:.75rem 2rem;">🚀 Buat Modul Ujian</button>
        </div>
    </form>
</div>

<script>
    function toggleForm() {
        const fc = document.getElementById('formContainer');
        fc.style.display = fc.style.display === 'none' ? 'block' : 'none';
        if(fc.style.display === 'block') {
            fc.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }
</script>

@endsection
