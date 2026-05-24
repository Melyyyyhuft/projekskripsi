@extends('layouts.admin')
@section('title', 'Pengaturan Sistem')

@section('content')

@if(session('success'))
    <div style="background:#d1fae5;color:#059669;padding:.875rem 1.25rem;border-radius:12px;margin-bottom:1.5rem;font-weight:600;border:1px solid #a7f3d0;">✅ {{ session('success') }}</div>
@endif
@if(session('error'))
    <div style="background:#fee2e2;color:#dc2626;padding:.875rem 1.25rem;border-radius:12px;margin-bottom:1.5rem;font-weight:600;border:1px solid #fca5a5;">⚠️ {{ session('error') }}</div>
@endif
@if($errors->any())
    <div style="background:#fee2e2;color:#dc2626;padding:.875rem 1.25rem;border-radius:12px;margin-bottom:1.5rem;border:1px solid #fca5a5;">
        <ul style="margin:0;padding-left:1.25rem;font-size:.875rem;">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
@endif

{{-- Page Header --}}
<div style="margin-bottom:2rem;">
    <h1 style="font-size:1.5rem;font-weight:800;color:#0f172a;margin:0 0 .25rem;">Pengaturan Sistem</h1>
    <p style="color:#64748b;font-size:.9rem;margin:0;">Konfigurasi umum, periode, bobot seleksi, dan kuota jurusan.</p>
</div>

{{-- Tab Navigation --}}
<div style="display:flex;gap:.5rem;margin-bottom:1.5rem;background:#f1f5f9;padding:.35rem;border-radius:14px;width:fit-content;">
    <button class="tab-btn active" onclick="openTab(event,'tab-umum')" style="padding:.6rem 1.25rem;border:none;border-radius:10px;font-weight:700;font-size:.875rem;cursor:pointer;background:white;color:#0f172a;box-shadow:0 1px 4px rgba(0,0,0,.08);">
        🏫 Umum
    </button>
    <button class="tab-btn" onclick="openTab(event,'tab-periode')" style="padding:.6rem 1.25rem;border:none;border-radius:10px;font-weight:700;font-size:.875rem;cursor:pointer;background:transparent;color:#64748b;">
        📅 Seleksi & CBT
    </button>
    <button class="tab-btn" onclick="openTab(event,'tab-sosmed')" style="padding:.6rem 1.25rem;border:none;border-radius:10px;font-weight:700;font-size:.875rem;cursor:pointer;background:transparent;color:#64748b;">
        📱 Media Sosial
    </button>
    <button class="tab-btn" onclick="openTab(event,'tab-jurusan')" style="padding:.6rem 1.25rem;border:none;border-radius:10px;font-weight:700;font-size:.875rem;cursor:pointer;background:transparent;color:#64748b;">
        🎓 Kuota Jurusan
    </button>
</div>

{{-- Tab: Umum --}}
<div id="tab-umum" class="tab-pane">
    <div class="glass-card">
        <h3 style="margin:0 0 1.5rem;font-size:1.05rem;font-weight:700;color:#0f172a;">🏫 Informasi Umum Sekolah</h3>
        <form action="{{ route('admin.pengaturan.umum') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;margin-bottom:1.5rem;">
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Nama Sekolah</label>
                    <input type="text" name="nama_sekolah" class="form-control" value="{{ $settings['nama_sekolah'] ?? 'SMK MITRA BINTARO' }}" required>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Tahun Ajaran Aktif</label>
                    <input type="text" name="tahun_ajaran" class="form-control" value="{{ $settings['tahun_ajaran'] ?? '2026/2027' }}" required>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Logo Sekolah</label>
                    <input type="file" name="logo_sekolah" class="form-control">
                    @if($settings['logo_sekolah'] ?? '')
                        <div style="margin-top:0.5rem; display:flex; align-items:center; gap:0.5rem;">
                            <img src="{{ asset('storage/'.$settings['logo_sekolah']) }}" height="30">
                            <span style="font-size:0.75rem; color:var(--gray-text);">Logo aktif</span>
                        </div>
                    @endif
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Status PPDB</label>
                    <select name="status_ppdb" class="form-control" required>
                        <option value="buka" {{ ($settings['status_ppdb'] ?? '') == 'buka' ? 'selected' : '' }}>🟢 Buka (Siswa dapat mendaftar)</option>
                        <option value="tutup" {{ ($settings['status_ppdb'] ?? '') == 'tutup' ? 'selected' : '' }}>🔴 Tutup (Pendaftaran dikunci)</option>
                    </select>
                </div>
            </div>
            <div style="display:flex;justify-content:flex-end;">
                <button type="submit" class="btn-primary" style="padding:.75rem 2rem;">💾 Simpan Pengaturan Umum</button>
            </div>
        </form>
    </div>
</div>

{{-- Tab: Periode & Bobot --}}
<div id="tab-periode" class="tab-pane" style="display:none;">
    <div style="display:grid;grid-template-columns:1fr 1.2fr;gap:1.5rem;">
        {{-- Periode --}}
        <div class="glass-card">
            <h3 style="margin:0 0 1.5rem;font-size:1.05rem;font-weight:700;color:#0f172a;">📅 Periode Pendaftaran</h3>
            <form action="{{ route('admin.pengaturan.periode') }}" method="POST">
                @csrf
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                    <div class="form-group">
                        <label class="form-label">Tgl Buka</label>
                        <input type="date" name="tgl_buka" class="form-control" value="{{ $settings['tgl_buka'] ?? '' }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tgl Tutup</label>
                        <input type="date" name="tgl_tutup" class="form-control" value="{{ $settings['tgl_tutup'] ?? '' }}" required>
                    </div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                    <div class="form-group">
                        <label class="form-label">Tgl Mulai CBT</label>
                        <input type="date" name="tgl_mulai_cbt" class="form-control" value="{{ $settings['tgl_mulai_cbt'] ?? '' }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Durasi (Hari)</label>
                        <input type="number" name="durasi_cbt" class="form-control" value="{{ $settings['durasi_cbt'] ?? '' }}" required min="1">
                    </div>
                </div>
                <button type="submit" class="btn-primary" style="width:100%;padding:.75rem;">💾 Simpan Periode</button>
            </form>
        </div>

        {{-- Bobot --}}
        <div class="glass-card">
            <h3 style="margin:0 0 1.5rem;font-size:1.05rem;font-weight:700;color:#0f172a;">⚖️ Bobot Nilai & Ambang Batas</h3>
            <form action="{{ route('admin.pengaturan.bobot') }}" method="POST">
                @csrf
                <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:10px;padding:0.75rem;margin-bottom:1rem;font-size:.78rem;color:#1e40af;">
                    💡 Rumus: <code>(Bobot Ujian × Nilai CBT) + (Bobot Rapor × Nilai Rapor)</code>. Total bobot harus 100%.
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                    <div class="form-group">
                        <label class="form-label">Bobot Ujian (%)</label>
                        <input type="number" name="bobot_ujian" class="form-control" value="{{ $settings['bobot_ujian'] ?? 30 }}" required min="0" max="100">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Bobot Rapor (%)</label>
                        <input type="number" name="bobot_rapor" class="form-control" value="{{ $settings['bobot_rapor'] ?? 70 }}" required min="0" max="100">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Ambang Batas Kelas Unggulan</label>
                    <input type="number" step="0.1" name="ambang_unggulan" class="form-control" value="{{ $settings['ambang_unggulan'] ?? 70.0 }}" required>
                    <small style="color:var(--gray-text);font-size:0.75rem;">Siswa dengan skor akhir di atas nilai ini akan masuk kelas Unggulan.</small>
                </div>
                <button type="submit" class="btn-primary" style="width:100%;padding:.75rem;">💾 Simpan Bobot & Ambang Batas</button>
            </form>
        </div>
    </div>
</div>

{{-- Tab: Media Sosial --}}
<div id="tab-sosmed" class="tab-pane" style="display:none;">
    <div class="glass-card" style="max-width: 600px;">
        <h3 style="margin:0 0 1.5rem;font-size:1.05rem;font-weight:700;color:#0f172a;">📱 Konfigurasi Media Sosial</h3>
        <p style="color:var(--gray-text); font-size:0.875rem; margin-bottom:1.5rem;">Link ini akan otomatis ditampilkan pada footer landing page.</p>
        <form action="{{ route('admin.pengaturan.sosmed') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label"><i class="fa-brands fa-tiktok"></i> TikTok URL</label>
                <input type="url" name="tiktok" class="form-control" value="{{ $settings['tiktok'] ?? '' }}" placeholder="https://tiktok.com/@username">
            </div>
            <div class="form-group">
                <label class="form-label"><i class="fa-brands fa-instagram"></i> Instagram URL</label>
                <input type="url" name="instagram" class="form-control" value="{{ $settings['instagram'] ?? '' }}" placeholder="https://instagram.com/username">
            </div>
            <div class="form-group">
                <label class="form-label"><i class="fa-brands fa-youtube"></i> YouTube URL</label>
                <input type="url" name="youtube" class="form-control" value="{{ $settings['youtube'] ?? '' }}" placeholder="https://youtube.com/@channel">
            </div>
            <div style="display:flex;justify-content:flex-end;">
                <button type="submit" class="btn-primary" style="padding:.75rem 2rem;">💾 Simpan Media Sosial</button>
            </div>
        </form>
    </div>
</div>

{{-- Tab: Jurusan --}}
<div id="tab-jurusan" class="tab-pane" style="display:none;">
    <div class="glass-card">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.25rem;flex-wrap:wrap;gap:1rem;">
            <h3 style="margin:0;font-size:1.05rem;font-weight:700;color:#0f172a;">🎓 Manajemen Kuota Jurusan</h3>
            <button type="button" class="btn-primary" onclick="showAddJurusanForm()" style="padding:.6rem 1.25rem;font-size:.875rem;">+ Tambah Jurusan</button>
        </div>

        {{-- Form Tambah --}}
        <div id="form-tambah-jurusan" style="display:none;background:#f8fafc;padding:1.25rem;border-radius:12px;margin-bottom:1.25rem;border:1px solid #e2e8f0;">
            <form action="{{ route('admin.jurusan-setting.store') }}" method="POST">
                @csrf
                <div style="display:flex;gap:1rem;align-items:flex-end;flex-wrap:wrap;">
                    <div class="form-group" style="margin-bottom:0;flex:2;min-width:180px;">
                        <label class="form-label">Nama Jurusan</label>
                        <input type="text" name="nama" class="form-control" placeholder="Contoh: Rekayasa Perangkat Lunak" required>
                    </div>
                    <div class="form-group" style="margin-bottom:0;flex:1;min-width:100px;">
                        <label class="form-label">Kuota</label>
                        <input type="number" name="kuota" class="form-control" placeholder="100" required min="1">
                    </div>
                    <button type="submit" class="btn-primary" style="padding:.75rem 1.25rem;white-space:nowrap;">Simpan</button>
                    <button type="button" class="btn-outline" onclick="hideAddJurusanForm()" style="padding:.75rem 1.25rem;white-space:nowrap;">Batal</button>
                </div>
            </form>
        </div>

        <div style="overflow-x:auto;border-radius:12px;border:1px solid #e2e8f0;">
            <table style="width:100%;border-collapse:collapse;min-width:500px;">
                <thead>
                    <tr style="background:#f8fafc;">
                        <th style="padding:.75rem 1rem;text-align:left;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;border-bottom:1px solid #e2e8f0;width:50px;">ID</th>
                        <th style="padding:.75rem 1rem;text-align:left;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;border-bottom:1px solid #e2e8f0;">Nama Jurusan</th>
                        <th style="padding:.75rem 1rem;text-align:center;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;border-bottom:1px solid #e2e8f0;">Kuota</th>
                        <th style="padding:.75rem 1rem;text-align:center;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;border-bottom:1px solid #e2e8f0;">Diterima</th>
                        <th style="padding:.75rem 1rem;text-align:center;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;border-bottom:1px solid #e2e8f0;">Sisa</th>
                        <th style="padding:.75rem 1rem;text-align:right;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;border-bottom:1px solid #e2e8f0;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($jurusans as $j)
                    <tr style="border-bottom:1px solid #f1f5f9;">
                        <td style="padding:.75rem 1rem;color:#94a3b8;font-size:.875rem;">{{ $j->id }}</td>
                        <td style="padding:.75rem 1rem;">
                            <form action="{{ route('admin.jurusan-setting.update', $j->id) }}" method="POST" id="edit-form-{{ $j->id }}" style="display:none;">
                                @csrf @method('PUT')
                                <div style="display:flex;gap:.5rem;align-items:center;flex-wrap:wrap;">
                                    <input type="text" name="nama" value="{{ $j->nama }}" class="form-control" style="padding:.45rem .75rem;min-width:160px;" required>
                                    <input type="number" name="kuota" value="{{ $j->kuota }}" class="form-control" style="padding:.45rem .75rem;width:80px;" required>
                                    <button type="submit" style="background:#10b981;color:white;padding:.45rem .9rem;border-radius:8px;font-weight:700;font-size:.8rem;border:none;cursor:pointer;">Simpan</button>
                                    <button type="button" onclick="toggleEdit({{ $j->id }})" style="background:#f1f5f9;color:#475569;padding:.45rem .9rem;border-radius:8px;font-weight:700;font-size:.8rem;border:none;cursor:pointer;">Batal</button>
                                </div>
                            </form>
                            <span id="display-nama-{{ $j->id }}" style="font-weight:700;color:#0f172a;">{{ $j->nama }}</span>
                        </td>
                        <td style="padding:.75rem 1rem;text-align:center;font-weight:600;" id="display-kuota-{{ $j->id }}">{{ $j->kuota }}</td>
                        <td style="padding:.75rem 1rem;text-align:center;">
                            <span style="background:#d1fae5;color:#059669;padding:.25rem .65rem;border-radius:999px;font-weight:700;font-size:.85rem;">{{ $j->diterima_count }}</span>
                        </td>
                        <td style="padding:.75rem 1rem;text-align:center;">
                            @if($j->sisa_kuota <= 0)
                                <span style="color:#ef4444;font-weight:700;">Penuh (0)</span>
                            @else
                                <span style="color:var(--primary);font-weight:700;">{{ $j->sisa_kuota }}</span>
                            @endif
                        </td>
                        <td style="padding:.75rem 1rem;text-align:right;">
                            <div style="display:flex;gap:.5rem;justify-content:flex-end;">
                                <button type="button" onclick="toggleEdit({{ $j->id }})" style="background:#eff6ff;color:var(--primary);padding:.3rem .7rem;border-radius:7px;font-size:.8rem;font-weight:600;border:1px solid #bfdbfe;cursor:pointer;">Edit</button>
                                <form action="{{ route('admin.jurusan-setting.destroy', $j->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin hapus jurusan ini?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" style="background:#fef2f2;color:#ef4444;padding:.3rem .7rem;border-radius:7px;font-size:.8rem;font-weight:600;border:1px solid #fca5a5;cursor:pointer;">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function openTab(evt, tabName) {
    document.querySelectorAll('.tab-pane').forEach(el => el.style.display = 'none');
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.style.background = 'transparent';
        btn.style.color = '#64748b';
        btn.style.boxShadow = 'none';
    });
    document.getElementById(tabName).style.display = 'block';
    evt.currentTarget.style.background = 'white';
    evt.currentTarget.style.color = '#0f172a';
    evt.currentTarget.style.boxShadow = '0 1px 4px rgba(0,0,0,.08)';
}
function showAddJurusanForm() { document.getElementById('form-tambah-jurusan').style.display = 'block'; }
function hideAddJurusanForm() { document.getElementById('form-tambah-jurusan').style.display = 'none'; }
function toggleEdit(id) {
    const form = document.getElementById('edit-form-' + id);
    const nm = document.getElementById('display-nama-' + id);
    const kt = document.getElementById('display-kuota-' + id);
    const hidden = form.style.display === 'none';
    form.style.display = hidden ? 'block' : 'none';
    nm.style.display = hidden ? 'none' : 'inline';
    kt.style.display = hidden ? 'none' : 'table-cell';
}
</script>
@endsection
