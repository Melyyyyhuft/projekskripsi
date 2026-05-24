@extends('layouts.admin')
@section('title', 'Bank Soal (CBT)')

@section('content')
<style>
    /* ─── Modern Scrollbar ─── */
    .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.1); border-radius: 10px; }
    .custom-scrollbar:hover::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.2); }
    [data-theme="dark"] .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); }
    [data-theme="dark"] .custom-scrollbar:hover::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); }

    /* ─── Fixed Layout Logic ─── */
    .dashboard-container {
        display: flex;
        flex-direction: column;
        height: calc(100vh - 120px); /* Adjust based on header+padding */
        gap: 1.5rem;
    }

    .top-section { flex-shrink: 0; }
    
    .scrollable-section {
        flex: 1;
        min-height: 0; /* Important for flex-child scrolling */
        display: flex;
        flex-direction: column;
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.5);
        border-radius: 24px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }

    .table-container {
        flex: 1;
        overflow-y: auto;
        scroll-behavior: smooth;
    }

    /* ─── Sticky Header ─── */
    .sticky-thead th {
        position: sticky;
        top: 0;
        background: #f8fafc;
        z-index: 10;
        box-shadow: 0 1px 0 #e2e8f0;
    }
    [data-theme="dark"] .sticky-thead th {
        background: #1e293b;
        box-shadow: 0 1px 0 #334155;
    }

    /* ─── Hover Animations ─── */
    .hover-row:hover {
        background: rgba(59, 130, 246, 0.03) !important;
        transition: background 0.2s ease;
    }

    .form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        transform: translateY(-1px);
    }
    
    .glass-card-mini {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.4);
        border-radius: 20px;
        padding: 1.5rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
    }
</style>

<div class="dashboard-container">
    {{-- ─── Page Header ─── --}}
    <div class="top-section">
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <div>
                <h1 style="font-size:1.5rem;font-weight:900;color:#0f172a;margin:0 0 .25rem;letter-spacing:-.02em;">Bank Soal CBT</h1>
                <p style="color:#64748b;font-size:.9rem;margin:0;font-weight:500;">Manajemen terpusat untuk seluruh database pertanyaan ujian.</p>
            </div>
            
            @if(session('success'))
                <div style="background:#d1fae5;color:#059669;padding:.6rem 1.25rem;border-radius:12px;font-weight:700;font-size:.85rem;border:1px solid #a7f3d0;animation:slideIn .3s ease;">✅ {{ session('success') }}</div>
            @endif
        </div>

        {{-- ─── Form Tambah + Import Row ─── --}}
        <div style="display:grid;grid-template-columns:1.5fr 1fr;gap:1.5rem;margin-top:1.5rem;">
            {{-- Form Tambah Soal --}}
            <div class="glass-card-mini">
                <form action="{{ route('admin.bank_soal.store') }}" method="POST">
                    @csrf
                    <div style="display:flex; gap:1rem; align-items:flex-end;">
                        <div style="flex:1; display:grid; grid-template-columns: 1fr 1fr 2fr; gap:0.75rem;">
                            <div class="form-group" style="margin-bottom:0;">
                                <label class="form-label" style="font-size:0.7rem; color:#94a3b8;">Thn Ajaran</label>
                                <input type="text" name="tahun_ajaran" class="form-control" style="font-size:0.85rem; padding:0.45rem 0.75rem;" required value="{{ $filterTahun ?? '2024/2025' }}">
                            </div>
                            <div class="form-group" style="margin-bottom:0;">
                                <label class="form-label" style="font-size:0.7rem; color:#94a3b8;">Paket / Mapel</label>
                                <input type="text" name="nama_paket" class="form-control" style="font-size:0.85rem; padding:0.45rem 0.75rem;" placeholder="Nama Paket">
                            </div>
                            <div class="form-group" style="margin-bottom:0;">
                                <label class="form-label" style="font-size:0.7rem; color:#94a3b8;">Pertanyaan</label>
                                <input type="text" name="teks_soal" class="form-control" style="font-size:0.85rem; padding:0.45rem 0.75rem;" required placeholder="Ketik pertanyaan soal di sini...">
                            </div>
                        </div>
                        <div style="display:grid; grid-template-columns: repeat(4, 80px) 70px; gap:0.5rem;">
                            <input type="text" name="opsi_a" placeholder="Opsi A" class="form-control" style="font-size:0.8rem; padding:0.45rem;" required title="Opsi A">
                            <input type="text" name="opsi_b" placeholder="Opsi B" class="form-control" style="font-size:0.8rem; padding:0.45rem;" required title="Opsi B">
                            <input type="text" name="opsi_c" placeholder="Opsi C" class="form-control" style="font-size:0.8rem; padding:0.45rem;" required title="Opsi C">
                            <input type="text" name="opsi_d" placeholder="Opsi D" class="form-control" style="font-size:0.8rem; padding:0.45rem;" required title="Opsi D">
                            <select name="jawaban_benar" class="form-control" style="font-size:0.8rem; padding:0.45rem;" required title="Kunci Jawaban">
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="C">C</option>
                                <option value="D">D</option>
                            </select>
                        </div>
                        <button type="submit" class="btn-primary" style="padding:0.45rem 1.25rem; border-radius:10px; font-weight:700; white-space:nowrap; font-size:0.85rem;">
                            <i class="fa-solid fa-plus"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>

            {{-- Import Massal --}}
            <div class="glass-card-mini" style="background:rgba(245, 158, 11, 0.03); border-color: rgba(245, 158, 11, 0.2);">
                <form action="{{ route('admin.bank_soal.import') }}" method="POST" enctype="multipart/form-data" style="display:flex; gap:0.75rem; align-items:center;">
                    @csrf
                    <div style="flex:1;">
                        <input type="file" name="file_soal" id="file_soal" class="form-control" style="font-size:0.8rem; padding:0.35rem; height:auto;" accept=".csv,.txt" required>
                    </div>
                    <button type="submit" style="padding:0.45rem 1rem; background:linear-gradient(135deg,#f59e0b,#d97706); color:white; border:none; border-radius:10px; font-weight:700; cursor:pointer; font-size:0.85rem; white-space:nowrap;">
                        <i class="fa-solid fa-cloud-arrow-up"></i> Import
                    </button>
                    <div style="display:flex; flex-direction:column; gap:0.25rem;">
                        <a href="{{ route('admin.bank_soal.template') }}" title="Template TXT" style="color:#d97706; font-size:0.9rem;"><i class="fa-solid fa-file-lines"></i></a>
                        <a href="{{ route('admin.bank_soal.template_excel') }}" title="Template Excel" style="color:#059669; font-size:0.9rem;"><i class="fa-solid fa-file-excel"></i></a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ─── Daftar Soal (Scroll Area) ─── --}}
    <div class="scrollable-section">
        <div style="padding:1.5rem 2rem; border-bottom:1px solid rgba(0,0,0,0.05); display:flex; justify-content:space-between; align-items:center; background:rgba(255,255,255,0.4);">
            <h3 style="margin:0;font-size:1.1rem;font-weight:800;color:#0f172a;display:flex;align-items:center;gap:0.75rem;">
                <i class="fa-solid fa-list-check" style="color:var(--primary);"></i> Database Pertanyaan
                <span style="font-size:0.75rem; font-weight:600; color:#94a3b8; background:#f1f5f9; padding:0.25rem 0.75rem; border-radius:999px;">{{ $soals->count() }} Data</span>
            </h3>

            <form action="{{ route('admin.bank_soal.index') }}" method="GET" style="display:flex;gap:.75rem;align-items:center;">
                <div style="display:flex;gap:.75rem;align-items:center; background:#f8fafc; padding:0.4rem 1rem; border-radius:12px; border:1px solid #e2e8f0;">
                    <div style="display:flex;gap:.4rem;align-items:center;">
                        <label style="font-size:.7rem;font-weight:700;color:#94a3b8;text-transform:uppercase;">Tahun:</label>
                        <select name="tahun_ajaran" style="border:none; background:transparent; font-size:.85rem; font-weight:700; color:#475569; outline:none; cursor:pointer;" onchange="this.form.submit()">
                            <option value="">Semua</option>
                            @foreach($tahunAjarans as $ta)
                                <option value="{{ $ta }}" {{ $filterTahun == $ta ? 'selected' : '' }}>{{ $ta }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div style="width:1px; height:16px; background:#e2e8f0;"></div>
                    <div style="display:flex;gap:.4rem;align-items:center;">
                        <label style="font-size:.7rem;font-weight:700;color:#94a3b8;text-transform:uppercase;">Paket:</label>
                        <select name="nama_paket" style="border:none; background:transparent; font-size:.85rem; font-weight:700; color:#475569; outline:none; cursor:pointer;" onchange="this.form.submit()">
                            <option value="">Semua</option>
                            @foreach($namaPakets as $np)
                                <option value="{{ $np }}" {{ $filterPaket == $np ? 'selected' : '' }}>{{ $np }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @if($filterTahun || $filterPaket)
                    <a href="{{ route('admin.bank_soal.index') }}" style="text-decoration:none; color:#ef4444; font-size:.75rem; font-weight:800; text-transform:uppercase;">✕ Reset</a>
                @endif
            </form>
        </div>

        <div class="table-container custom-scrollbar">
            <table style="width:100%; border-collapse:collapse;">
                <thead class="sticky-thead">
                    <tr>
                        <th style="padding:1rem; text-align:left; font-size:.7rem; font-weight:800; color:#64748b; text-transform:uppercase; letter-spacing:.05em;">#</th>
                        <th style="padding:1rem; text-align:left; font-size:.7rem; font-weight:800; color:#64748b; text-transform:uppercase; letter-spacing:.05em;">Pertanyaan & Detail Opsi</th>
                        <th style="padding:1rem; text-align:left; font-size:.7rem; font-weight:800; color:#64748b; text-transform:uppercase; letter-spacing:.05em;">Sumber Data</th>
                        <th style="padding:1rem; text-align:center; font-size:.7rem; font-weight:800; color:#64748b; text-transform:uppercase; letter-spacing:.05em;">Kunci</th>
                        <th style="padding:1rem; text-align:right; font-size:.7rem; font-weight:800; color:#64748b; text-transform:uppercase; letter-spacing:.05em;">Aksi</th>
                    </tr>
                </thead>
                <tbody style="background:transparent;">
                    @forelse($soals as $i => $s)
                    <tr class="hover-row" style="border-bottom:1px solid rgba(0,0,0,0.03);">
                        <td style="padding:1.25rem 1rem; color:#94a3b8; font-size:.875rem; font-weight:600; width:40px;">{{ $i + 1 }}</td>
                        <td style="padding:1.25rem 1rem;">
                            <div style="font-weight:700; color:#1e293b; margin-bottom:.5rem; font-size:.95rem; line-height:1.5;">{{ $s->teks_soal }}</div>
                            <div style="display:flex; gap:1.25rem; font-size:.75rem; font-weight:600;">
                                <span style="display:flex; align-items:center; gap:0.25rem; color:#64748b;"><span style="color:var(--primary)">A.</span> {{ $s->opsi_a }}</span>
                                <span style="display:flex; align-items:center; gap:0.25rem; color:#64748b;"><span style="color:var(--primary)">B.</span> {{ $s->opsi_b }}</span>
                                <span style="display:flex; align-items:center; gap:0.25rem; color:#64748b;"><span style="color:var(--primary)">C.</span> {{ $s->opsi_c }}</span>
                                <span style="display:flex; align-items:center; gap:0.25rem; color:#64748b;"><span style="color:var(--primary)">D.</span> {{ $s->opsi_d }}</span>
                            </div>
                        </td>
                        <td style="padding:1.25rem 1rem;">
                            <div style="display:inline-flex; flex-direction:column; gap:0.25rem;">
                                <span style="font-size:.85rem; font-weight:800; color:#475569;">{{ $s->tahun_ajaran }}</span>
                                <span style="font-size:.7rem; font-weight:600; color:#94a3b8; background:#f1f5f9; padding:0.15rem 0.5rem; border-radius:6px; display:inline-block; border:1px solid #e2e8f0;">
                                    {{ $s->nama_paket ?? 'Manual Input' }}
                                </span>
                            </div>
                        </td>
                        <td style="padding:1.25rem 1rem; text-align:center;">
                            <div style="width:40px; height:40px; border-radius:12px; background:#d1fae5; color:#059669; display:inline-flex; align-items:center; justify-content:center; font-weight:900; font-size:1.1rem; border:2px solid #a7f3d0; box-shadow:0 4px 10px rgba(16, 185, 129, 0.1);">
                                {{ $s->jawaban_benar }}
                            </div>
                        </td>
                        <td style="padding:1.25rem 1rem; text-align:right;">
                            <div style="display:flex; gap:0.5rem; justify-content:flex-end;">
                                <form action="{{ route('admin.bank_soal.destroy', $s->id) }}" method="POST" onsubmit="return confirm('Hapus soal ini permanen?');" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="background:#fff1f2; color:#e11d48; width:36px; height:36px; border-radius:10px; border:1px solid #fecdd3; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:all 0.2s;" onmouseover="this.style.background='#ffe4e6'; this.style.transform='scale(1.1)'" onmouseout="this.style.background='#fff1f2'; this.style.transform='scale(1)'">
                                        <i class="fa-solid fa-trash-can" style="font-size:0.85rem;"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="padding:5rem 2rem; text-align:center;">
                            <div style="width:80px; height:80px; background:#f8fafc; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 1.5rem; color:#cbd5e1; font-size:2.5rem; border:2px dashed #e2e8f0;">
                                <i class="fa-solid fa-box-open"></i>
                            </div>
                            <h4 style="margin:0; font-weight:800; color:#1e293b;">Data Kosong</h4>
                            <p style="margin:0.25rem 0 0; color:#94a3b8; font-size:0.9rem;">Gunakan form di atas untuk menambahkan soal baru.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- ─── Footer Area (Optional) ─── --}}
        <div style="padding:1rem 2rem; background:rgba(255,255,255,0.4); border-top:1px solid rgba(0,0,0,0.05); display:flex; justify-content:space-between; align-items:center;">
            <p style="margin:0; font-size:0.75rem; font-weight:600; color:#94a3b8;">
                <i class="fa-solid fa-circle-info"></i> Menampilkan {{ $soals->count() }} soal yang tersimpan.
            </p>
            <p style="margin:0; font-size:0.75rem; font-weight:600; color:#94a3b8;">
                Total Database: <strong>{{ \App\Models\Soal::count() }}</strong> Pertanyaan
            </p>
        </div>
    </div>
</div>
@endsection

