@extends('layouts.admin')
@section('title', 'Bank Soal (CBT)')

@section('content')

{{-- ─── Page Header ─── --}}
<div style="margin-bottom:2rem;">
    <h1 style="font-size:1.5rem;font-weight:800;color:#0f172a;margin:0 0 .25rem;">Bank Soal CBT</h1>
    <p style="color:#64748b;font-size:.9rem;margin:0;">Kelola dan impor kumpulan soal ujian untuk semua modul.</p>
</div>

@if(session('success'))
    <div style="background:#d1fae5;color:#059669;padding:.875rem 1.25rem;border-radius:12px;margin-bottom:1.5rem;font-weight:600;border:1px solid #a7f3d0;">✅ {{ session('success') }}</div>
@endif
@if(session('error'))
    <div style="background:#fee2e2;color:#dc2626;padding:.875rem 1.25rem;border-radius:12px;margin-bottom:1.5rem;font-weight:600;border:1px solid #fca5a5;">⚠️ {{ session('error') }}</div>
@endif

{{-- ─── Top Row: Form Tambah + Import ─── --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;margin-bottom:2rem;">

    {{-- Form Tambah Soal --}}
    <div class="glass-card">
        <h3 style="margin:0 0 1.5rem;font-size:1.05rem;font-weight:700;color:var(--primary);">✏️ Tambah Soal Manual</h3>
        <form action="{{ route('admin.bank_soal.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label" for="tahun_ajaran">Tahun Ajaran</label>
                <input type="text" name="tahun_ajaran" id="tahun_ajaran" class="form-control" required value="{{ $filterTahun ?? '2024/2025' }}">
            </div>
            <div class="form-group">
                <label class="form-label" for="teks_soal">Pertanyaan</label>
                <textarea name="teks_soal" id="teks_soal" class="form-control" required rows="3" placeholder="Tulis pertanyaan soal di sini..."></textarea>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem;margin-bottom:1.25rem;">
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label" for="opsi_a">Opsi A</label>
                    <input type="text" name="opsi_a" id="opsi_a" class="form-control" required>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label" for="opsi_b">Opsi B</label>
                    <input type="text" name="opsi_b" id="opsi_b" class="form-control" required>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label" for="opsi_c">Opsi C</label>
                    <input type="text" name="opsi_c" id="opsi_c" class="form-control" required>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label" for="opsi_d">Opsi D</label>
                    <input type="text" name="opsi_d" id="opsi_d" class="form-control" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="jawaban_benar">Kunci Jawaban Benar</label>
                <select name="jawaban_benar" id="jawaban_benar" class="form-control" required>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                    <option value="D">D</option>
                </select>
            </div>

            <button type="submit" class="btn-primary" style="width:100%;padding:.75rem;">💾 Simpan Soal</button>
        </form>
    </div>

    {{-- Import Massal --}}
    <div class="glass-card" style="border-top:3px solid #f59e0b;">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:1.5rem;flex-wrap:wrap;gap:.75rem;">
            <h3 style="margin:0;font-size:1.05rem;font-weight:700;color:#d97706;">📤 Import Soal Massal</h3>
            <div style="display:flex; gap:0.5rem; flex-wrap:wrap;">
                <a href="{{ route('admin.bank_soal.template') }}" style="font-size:.8rem;color:var(--primary);text-decoration:none;background:#eff6ff;padding:.4rem .8rem;border-radius:8px;font-weight:600;white-space:nowrap;border:1px solid #bfdbfe;transition:all 0.2s;" onmouseover="this.style.background='#dbeafe'" onmouseout="this.style.background='#eff6ff'">
                    📄 Template TXT
                </a>
                <a href="{{ route('admin.bank_soal.template_excel') }}" style="font-size:.8rem;color:#059669;text-decoration:none;background:#d1fae5;padding:.4rem .8rem;border-radius:8px;font-weight:600;white-space:nowrap;border:1px solid #a7f3d0;transition:all 0.2s;" onmouseover="this.style.background='#bbf7d0'" onmouseout="this.style.background='#d1fae5'">
                    📊 Template Excel (.xlsx)
                </a>
            </div>
        </div>

        <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:1rem;margin-bottom:1.25rem;">
            <p style="margin:0;font-size:.82rem;color:#92400e;font-weight:600;">⚠️ Format file harus sesuai template. Minimal 50 soal valid.</p>
            <p style="margin:.25rem 0 0;font-size:.75rem;color:#b45309;">*Jika menggunakan Template Excel, silakan isi data lalu <strong>Save As (Simpan Sebagai) format CSV (Comma delimited)</strong> sebelum di-upload.</p>
        </div>

        <form action="{{ route('admin.bank_soal.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="form-label" for="file_soal">Upload File (.csv / .txt)</label>
                <input type="file" name="file_soal" id="file_soal" class="form-control" accept=".csv,.txt" required>
            </div>
            <button type="submit" style="width:100%;padding:.75rem;background:linear-gradient(135deg,#f59e0b,#d97706);color:white;border:none;border-radius:999px;font-weight:700;cursor:pointer;font-size:.95rem;">
                🚀 Upload & Proses
            </button>
        </form>
    </div>
</div>

{{-- ─── Daftar Soal ─── --}}
<div class="glass-card">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem;">
        <h3 style="margin:0;font-size:1.1rem;font-weight:700;color:#0f172a;">📋 Daftar Soal Tersimpan</h3>
        <form action="{{ route('admin.bank_soal.index') }}" method="GET" style="display:flex;gap:.5rem;align-items:center;">
            <label style="font-size:.8rem;font-weight:600;color:#475569;white-space:nowrap;">Tahun Ajaran:</label>
            <select name="tahun_ajaran" class="form-control" style="padding:.45rem .75rem;height:auto;font-size:.85rem;border-radius:8px;" onchange="this.form.submit()">
                @foreach($tahunAjarans as $ta)
                    <option value="{{ $ta }}" {{ $filterTahun == $ta ? 'selected' : '' }}>{{ $ta }}</option>
                @endforeach
                @if(!$tahunAjarans->contains($filterTahun))
                    <option value="{{ $filterTahun }}" selected>{{ $filterTahun }}</option>
                @endif
            </select>
        </form>
    </div>

    <div style="overflow-x:auto;border-radius:12px;border:1px solid #e2e8f0;">
        <table style="width:100%;border-collapse:collapse;min-width:500px;">
            <thead>
                <tr style="background:#f8fafc;">
                    <th style="padding:.875rem 1rem;text-align:left;font-size:.75rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;border-bottom:1px solid #e2e8f0;">#</th>
                    <th style="padding:.875rem 1rem;text-align:left;font-size:.75rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;border-bottom:1px solid #e2e8f0;">Pertanyaan & Opsi</th>
                    <th style="padding:.875rem 1rem;text-align:center;font-size:.75rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;border-bottom:1px solid #e2e8f0;">Kunci</th>
                    <th style="padding:.875rem 1rem;text-align:right;font-size:.75rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;border-bottom:1px solid #e2e8f0;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($soals as $i => $s)
                <tr style="border-bottom:1px solid #f1f5f9;transition:background .15s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                    <td style="padding:.875rem 1rem;color:#94a3b8;font-size:.875rem;width:40px;">{{ $i + 1 }}</td>
                    <td style="padding:.875rem 1rem;">
                        <div style="font-weight:600;color:#0f172a;margin-bottom:.4rem;font-size:.9rem;">{{ \Illuminate\Support\Str::limit($s->teks_soal, 80) }}</div>
                        <div style="display:flex;gap:.75rem;flex-wrap:wrap;font-size:.75rem;color:#94a3b8;">
                            <span>A. {{ Str::limit($s->opsi_a, 25) }}</span>
                            <span>B. {{ Str::limit($s->opsi_b, 25) }}</span>
                            <span>C. {{ Str::limit($s->opsi_c, 25) }}</span>
                            <span>D. {{ Str::limit($s->opsi_d, 25) }}</span>
                        </div>
                    </td>
                    <td style="padding:.875rem 1rem;text-align:center;">
                        <span style="background:#d1fae5;color:#059669;padding:.25rem .75rem;border-radius:8px;font-weight:800;font-size:.95rem;">{{ $s->jawaban_benar }}</span>
                    </td>
                    <td style="padding:.875rem 1rem;text-align:right;">
                        <form action="{{ route('admin.bank_soal.destroy', $s->id) }}" method="POST" onsubmit="return confirm('Hapus soal ini permanen?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="background:#fef2f2;color:#ef4444;padding:.35rem .8rem;border-radius:8px;font-size:.8rem;font-weight:600;border:1px solid #fca5a5;cursor:pointer;">
                                🗑 Hapus
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="padding:3rem;text-align:center;color:#94a3b8;">
                        <div style="font-size:2rem;margin-bottom:.5rem;">📭</div>
                        Belum ada soal untuk tahun ajaran ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
