@extends('layouts.admin')
@section('title', 'Bank Soal CBT')

@section('content')
<style>
    /* ─── Modern SaaS Design Tokens ─── */
    :root {
        --primary: #6366f1;
        --primary-soft: rgba(99, 102, 241, 0.1);
        --secondary: #a855f7;
        --secondary-soft: rgba(168, 85, 247, 0.1);
        --accent: #f43f5e;
        --bg-body: #f8fafc;
        --glass-bg: rgba(255, 255, 255, 0.75);
        --glass-border: rgba(255, 255, 255, 0.5);
        --radius-lg: 24px;
        --radius-md: 16px;
        --shadow-soft: 0 10px 30px rgba(0, 0, 0, 0.04);
        --shadow-glow: 0 8px 20px rgba(99, 102, 241, 0.2);
    }

    /* ─── Scrollbar ─── */
    .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.1); border-radius: 10px; }
    .custom-scrollbar:hover::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.2); }

    /* ─── Animations ─── */
    @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes shimmer { 0% { background-position: -468px 0; } 100% { background-position: 468px 0; } }

    .animate-slide-up { animation: slideUp 0.5s ease-out forwards; }
    .animate-fade-in { animation: fadeIn 0.4s ease-out forwards; }

    /* ─── Layout ─── */
    .dashboard-wrapper {
        display: flex;
        flex-direction: column;
        gap: 2rem;
        padding-bottom: 2rem;
    }

    /* ─── Components ─── */
    .premium-card {
        background: var(--glass-bg);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid var(--glass-border);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-soft);
        padding: 2rem;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    /* ─── Form Elements ─── */
    .form-group-modern { margin-bottom: 1.5rem; }
    .label-modern {
        display: block;
        font-size: 0.85rem;
        font-weight: 700;
        color: #64748b;
        margin-bottom: 0.6rem;
        letter-spacing: 0.01em;
    }
    .input-modern {
        width: 100%;
        background: white;
        border: 1px solid #e2e8f0;
        padding: 0.75rem 1rem;
        border-radius: 14px;
        font-size: 0.95rem;
        font-weight: 600;
        color: #1e293b;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .input-modern:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        outline: none;
        transform: translateY(-1px);
    }
    .input-modern::placeholder { color: #94a3b8; font-weight: 500; }

    .textarea-modern {
        min-height: 120px;
        resize: none;
    }

    .btn-gradient {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: white;
        border: none;
        padding: 0.75rem 1.75rem;
        border-radius: 14px;
        font-weight: 800;
        font-size: 0.925rem;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.6rem;
        transition: all 0.3s ease;
        box-shadow: var(--shadow-glow);
    }
    .btn-gradient:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 25px rgba(99, 102, 241, 0.3);
        filter: brightness(1.1);
    }

    .btn-outline-soft {
        background: transparent;
        border: 1px solid #e2e8f0;
        color: #64748b;
        padding: 0.75rem 1.5rem;
        border-radius: 14px;
        font-weight: 700;
        font-size: 0.875rem;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s;
    }
    .btn-outline-soft:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
        color: #1e293b;
    }

    /* ─── Table ─── */
    .table-wrapper {
        position: relative;
        overflow: hidden;
        border-radius: var(--radius-md);
        background: rgba(255, 255, 255, 0.3);
    }
    .internal-scroll {
        max-height: 600px;
        overflow-y: auto;
    }
    .modern-table { width: 100%; border-collapse: separate; border-spacing: 0; }
    .modern-table thead th {
        position: sticky;
        top: 0;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        z-index: 10;
        padding: 1.25rem 1.5rem;
        text-align: left;
        font-size: 0.75rem;
        font-weight: 800;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: 2px solid #f1f5f9;
        white-space: nowrap;
    }
    .modern-table tbody tr { transition: all 0.2s; }
    .modern-table tbody tr:hover { background: rgba(99, 102, 241, 0.02) !important; }
    .modern-table tbody td {
        padding: 1.25rem 1.5rem;
        font-size: 0.9rem;
        color: #1e293b;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }

    /* ─── Badges ─── */
    .badge-soft {
        padding: 0.4rem 0.8rem;
        border-radius: 10px;
        font-size: 0.75rem;
        font-weight: 800;
        letter-spacing: 0.02em;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
    }
    .badge-rpl { background: #e0e7ff; color: #4338ca; }
    .badge-tkj { background: #f3e8ff; color: #7e22ce; }
    .badge-mm { background: #fce7f3; color: #be185d; }
    .badge-mplb { background: #ffedd5; color: #c2410c; }
    
    .badge-answer {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #d1fae5;
        color: #059669;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 900;
        font-size: 0.85rem;
        border: 2px solid #a7f3d0;
    }

    /* ─── Tooltip Custom (Simplest) ─── */
    .tooltip-container { position: relative; }
    .tooltip-text {
        visibility: hidden;
        background: #1e293b;
        color: white;
        text-align: center;
        padding: 4px 10px;
        border-radius: 6px;
        position: absolute;
        z-index: 100;
        bottom: 125%;
        left: 50%;
        transform: translateX(-50%);
        font-size: 0.7rem;
        white-space: nowrap;
        opacity: 0;
        transition: opacity 0.2s;
    }
    .tooltip-container:hover .tooltip-text { visibility: visible; opacity: 1; }

    /* ─── Loading Skeleton ─── */
    .skeleton-line {
        height: 12px;
        width: 100%;
        background: #f1f5f9;
        background-image: linear-gradient(90deg, #f1f5f9 0px, #e2e8f0 40px, #f1f5f9 80px);
        background-size: 600px 100%;
        animation: shimmer 2s infinite linear;
        border-radius: 6px;
    }
</style>

<div class="dashboard-wrapper">
    {{-- ─── Header Section ─── --}}
    <div style="display:flex; justify-content:space-between; align-items:flex-end;">
        <div>
            <h1 style="font-size:1.75rem; font-weight:900; color:#0f172a; margin:0 0 .25rem; letter-spacing:-.03em;">Bank Soal CBT</h1>
            <p style="color:#64748b; font-size:1rem; margin:0; font-weight:500;">Kelola seluruh database soal ujian CBT PPDB</p>
        </div>
        <div style="display:flex; gap:1rem;">
            <button class="btn-outline-soft" onclick="document.getElementById('import_container').scrollIntoView({behavior: 'smooth'})">
                <i class="fa-solid fa-file-excel"></i> Import Excel
            </button>
            <button class="btn-gradient" onclick="document.getElementById('teks_soal').focus()">
                <i class="fa-solid fa-plus"></i> Tambah Soal
            </button>
        </div>
    </div>

    {{-- ─── Form Tambah Soal ─── --}}
    <section class="premium-card animate-slide-up">
        <div style="display:flex; align-items:center; gap:.75rem; margin-bottom:2rem; color:var(--primary);">
            <i class="fa-solid fa-list-check" style="font-size:1.25rem;"></i>
            <h3 style="margin:0; font-size:1.2rem; font-weight:800; color:#1e293b;">Tambah Soal Baru</h3>
        </div>

        <form action="{{ route('admin.bank_soal.store') }}" method="POST" id="formSoal">
            @csrf
            <div style="display:grid; grid-template-columns: 1fr 1fr 1.5fr; gap:2rem;">
                {{-- Column 1 --}}
                <div>
                    <div class="form-group-modern">
                        <label class="label-modern">Tahun Ajaran</label>
                        <select name="tahun_ajaran" class="input-modern" required>
                            @php $years = ['2023/2024', '2024/2025', '2025/2026']; @endphp
                            @foreach($years as $y)
                                <option value="{{ $y }}" {{ ($filterTahun ?? '2024/2025') == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group-modern">
                        <label class="label-modern">Paket Ujian / Mapel</label>
                        <select name="nama_paket" class="input-modern">
                            @php $pakets = ['Paket RPL', 'Paket TKJ', 'Paket Multimedia', 'Paket MPLB', 'Umum']; @endphp
                            @foreach($pakets as $p)
                                <option value="{{ $p }}">{{ $p }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Column 2 (Options Grid inside) --}}
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <div class="form-group-modern">
                        <label class="label-modern">Opsi A</label>
                        <input type="text" name="opsi_a" class="input-modern" placeholder="Ketik opsi A" required>
                    </div>
                    <div class="form-group-modern">
                        <label class="label-modern">Opsi C</label>
                        <input type="text" name="opsi_c" class="input-modern" placeholder="Ketik opsi C" required>
                    </div>
                    <div class="form-group-modern">
                        <label class="label-modern">Opsi B</label>
                        <input type="text" name="opsi_b" class="input-modern" placeholder="Ketik opsi B" required>
                    </div>
                    <div class="form-group-modern">
                        <label class="label-modern">Opsi D</label>
                        <input type="text" name="opsi_d" class="input-modern" placeholder="Ketik opsi D" required>
                    </div>
                </div>

                {{-- Column 3 (Textarea) --}}
                <div style="position:relative;">
                    <div class="form-group-modern" style="margin-bottom:0;">
                        <label class="label-modern">Pertanyaan</label>
                        <textarea name="teks_soal" id="teks_soal" class="input-modern textarea-modern" 
                                  placeholder="Ketik pertanyaan di sini..." required oninput="updateCharCount(this)"></textarea>
                        <div id="char_counter" style="position:absolute; bottom:12px; right:15px; font-size:0.75rem; color:#94a3b8; font-weight:700;">0 / 1000</div>
                    </div>
                    <div class="form-group-modern" style="margin-top:1.5rem;">
                        <label class="label-modern">Jawaban Benar</label>
                        <select name="jawaban_benar" class="input-modern" required>
                            <option value="" disabled selected>Pilih jawaban benar</option>
                            <option value="A">Opsi A</option>
                            <option value="B">Opsi B</option>
                            <option value="C">Opsi C</option>
                            <option value="D">Opsi D</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Footer Form --}}
            <div style="display:flex; justify-content:space-between; align-items:center; margin-top:1rem; padding-top:1.5rem; border-top:1px solid #f1f5f9;">
                <div style="display:flex; align-items:center; gap:0.75rem; color:#64748b; background:rgba(226, 232, 240, 0.3); padding:.6rem 1.25rem; border-radius:14px; font-size:0.8rem; font-weight:600;">
                    <i class="fa-solid fa-circle-info" style="color:var(--primary);"></i>
                    Setelah disimpan, form akan otomatis dikosongkan dan cursor kembali ke pertanyaan.
                </div>
                <div style="display:flex; gap:1.5rem; align-items:center;">
                    <div style="text-align:right;">
                        <button type="button" class="btn-outline-soft" style="height:48px;" onclick="resetForm()">
                            <i class="fa-solid fa-rotate-right"></i> Reset
                        </button>
                        <p style="margin:.25rem 0 0; font-size:0.65rem; color:#94a3b8; font-weight:700;">Kosongkan form</p>
                    </div>
                    <button type="submit" class="btn-gradient" style="height:48px; min-width:180px;">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan Soal
                    </button>
                </div>
            </div>
        </form>
    </section>

    {{-- ─── Database Section ─── --}}
    <section class="premium-card animate-slide-up" style="animation-delay: 0.1s;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:2rem;">
            <div style="display:flex; align-items:center; gap:.75rem;">
                <h3 style="margin:0; font-size:1.2rem; font-weight:800; color:#1e293b;">Database Pertanyaan</h3>
                <span style="font-size:0.75rem; font-weight:800; color:var(--primary); background:var(--primary-soft); padding:0.35rem 0.85rem; border-radius:99px;">
                    {{ $soals->count() }} Data
                </span>
            </div>
            
            <div style="display:flex; gap:1rem; align-items:center;">
                {{-- Search --}}
                <div style="position:relative; min-width:300px;">
                    <i class="fa-solid fa-magnifying-glass" style="position:absolute; left:16px; top:50%; transform:translateY(-50%); color:#94a3b8; font-size:0.9rem;"></i>
                    <input type="text" id="searchInput" class="input-modern" placeholder="Cari pertanyaan..." style="padding-left:2.75rem;">
                </div>

                {{-- Filters --}}
                <form action="{{ route('admin.bank_soal.index') }}" method="GET" style="display:flex; gap:0.75rem; align-items:center;">
                    <select name="tahun_ajaran" class="input-modern" style="padding:.75rem 2rem .75rem 1rem;" onchange="this.form.submit()">
                        <option value="">Tahun: Semua</option>
                        @foreach($tahunAjarans as $ta)
                            <option value="{{ $ta }}" {{ $filterTahun == $ta ? 'selected' : '' }}>Tahun: {{ $ta }}</option>
                        @endforeach
                    </select>
                    <select name="nama_paket" class="input-modern" style="padding:.75rem 2rem .75rem 1rem;" onchange="this.form.submit()">
                        <option value="">Paket: Semua</option>
                        @foreach($namaPakets as $np)
                            <option value="{{ $np }}" {{ $filterPaket == $np ? 'selected' : '' }}>{{ $np }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn-outline-soft" style="height:48px;">
                        <i class="fa-solid fa-filter"></i> Filter
                    </button>
                </form>
            </div>
        </div>

        <div class="table-wrapper">
            <div class="internal-scroll custom-scrollbar" id="tableScrollArea">
                <table class="modern-table" id="soalTable">
                    <thead>
                        <tr>
                            <th style="min-width:300px;">Pertanyaan</th>
                            <th>Paket / Mapel</th>
                            <th>Tahun Ajaran</th>
                            <th style="text-align:center;">Kunci</th>
                            <th>Sumber Data</th>
                            <th style="text-align:right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($soals as $s)
                        @php
                            $paketClass = 'badge-rpl';
                            if(str_contains($s->nama_paket, 'TKJ')) $paketClass = 'badge-tkj';
                            if(str_contains($s->nama_paket, 'Multimedia')) $paketClass = 'badge-mm';
                            if(str_contains($s->nama_paket, 'MPLB')) $paketClass = 'badge-mplb';
                        @endphp
                        <tr>
                            <td>
                                <div style="font-weight:700; line-height:1.5; color:#1e293b; max-width:400px;">{{ $s->teks_soal }}</div>
                                <div style="display:flex; gap:0.75rem; margin-top:0.5rem;">
                                    <span style="font-size:0.725rem; font-weight:800; color:var(--primary); background:var(--primary-soft); padding:0.15rem 0.5rem; border-radius:6px;">4 Opsi</span>
                                </div>
                            </td>
                            <td>
                                <span class="badge-soft {{ $paketClass }}">{{ $s->nama_paket ?? 'General' }}</span>
                            </td>
                            <td>
                                <span style="font-weight:700; color:#475569;">{{ $s->tahun_ajaran }}</span>
                            </td>
                            <td style="text-align:center;">
                                <div style="display:inline-flex;" class="badge-answer">{{ $s->jawaban_benar }}</div>
                            </td>
                            <td>
                                <span style="font-weight:600; color:#64748b; font-size:0.85rem;">
                                    {{ $s->sumber_data ?? (str_contains($s->nama_paket ?? '', '.') ? 'Import Excel' : 'Manual') }}
                                </span>
                            </td>
                            <td style="text-align:right;">
                                <div style="display:flex; gap:0.4rem; justify-content:flex-end;">
                                    <div class="tooltip-container">
                                        <button class="btn-action-icon" style="color:var(--primary); background:var(--primary-soft);" onclick="editSoal({{ $s->id }})">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                        <span class="tooltip-text">Edit</span>
                                    </div>
                                    <div class="tooltip-container">
                                        <button class="btn-action-icon" style="color:#64748b; background:#f1f5f9;">
                                            <i class="fa-solid fa-copy"></i>
                                        </button>
                                        <span class="tooltip-text">Duplicate</span>
                                    </div>
                                    <div class="tooltip-container">
                                        <button class="btn-action-icon" style="color:var(--accent); background:rgba(244, 63, 94, 0.1);" onclick="confirmDelete({{ $s->id }})">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                        <span class="tooltip-text">Delete</span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" style="padding:4rem 2rem; text-align:center;">
                                <div style="width:120px; height:120px; background:#f8fafc; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 1.5rem; color:#cbd5e1; font-size:3rem; border:2px dashed #e2e8f0;">
                                    <i class="fa-solid fa-box-open"></i>
                                </div>
                                <h4 style="margin:0; font-weight:900; color:#1e293b; font-size:1.1rem;">Belum Ada Soal CBT</h4>
                                <p style="margin:0.25rem 0 1.5rem; color:#94a3b8; font-weight:600;">Mulai tambahkan pertanyaan pertama untuk paket ujian ini.</p>
                                <button class="btn-gradient" onclick="document.getElementById('teks_soal').focus()">
                                    <i class="fa-solid fa-plus"></i> Tambah Soal
                                </button>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination info simplified --}}
        <div style="margin-top:1.5rem; display:flex; justify-content:space-between; align-items:center;">
            <p style="margin:0; font-size:0.85rem; font-weight:700; color:#94a3b8;">
                Menampilkan 1 - {{ $soals->count() }} dari {{ $soals->count() }} soal
            </p>
            <div id="pagination_placeholder" style="display:flex; gap:0.5rem;">
                {{-- Mock pagination dots for pure CSS look --}}
                <div style="width:32px; height:32px; border-radius:8px; background:var(--primary); color:white; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:0.85rem;">1</div>
            </div>
        </div>
    </section>
    
    {{-- ─── Hidden Components for Logic ─── --}}
    <div id="import_container" style="padding-top:1rem;">
        <section class="premium-card" style="background:rgba(245, 158, 11, 0.04); border-color:rgba(245, 158, 11, 0.2);">
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <div style="display:flex; gap:1rem; align-items:center;">
                    <i class="fa-solid fa-file-export" style="color:#d97706; font-size:1.25rem;"></i>
                    <h3 style="margin:0; font-size:1rem; font-weight:800; color:#92400e;">Import Database Soal</h3>
                </div>
                <form action="{{ route('admin.bank_soal.import') }}" method="POST" enctype="multipart/form-data" id="importForm" style="display:flex; gap:1rem; align-items:center;">
                    @csrf
                    <input type="file" name="file_soal" class="input-modern" style="padding:.5rem; background:white; font-size:0.85rem;" accept=".csv,.txt" required>
                    <button type="submit" class="btn-gradient" style="background:linear-gradient(135deg, #f59e0b, #d97706); height:40px;">
                        <i class="fa-solid fa-upload"></i> Proses Import
                    </button>
                    <div style="display:flex; gap:0.4rem;">
                        <a href="{{ route('admin.bank_soal.template') }}" class="btn-outline-soft" style="padding:.5rem .75rem; height:40px;"><i class="fa-solid fa-file-lines"></i></a>
                        <a href="{{ route('admin.bank_soal.template_excel') }}" class="btn-outline-soft" style="padding:.5rem .75rem; height:40px;"><i class="fa-solid fa-file-excel"></i></a>
                    </div>
                </form>
            </div>
        </section>
    </div>
</div>

{{-- ─── Action Components (Toasts & Modals) ─── --}}

{{-- Delete Modal --}}
<div id="deleteModal" style="display:none; position:fixed; z-index:1000; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); backdrop-filter:blur(4px); align-items:center; justify-content:center;">
    <div class="premium-card" style="width:400px; text-align:center; padding:2.5rem;">
        <div style="width:64px; height:64px; background:rgba(244, 63, 94, 0.1); color:var(--accent); border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 1.5rem; font-size:1.75rem;">
            <i class="fa-solid fa-circle-exclamation"></i>
        </div>
        <h3 style="margin:0 0 .5rem; font-weight:900; color:#1e293b;">Hapus soal ini?</h3>
        <p style="margin:0 0 2rem; color:#64748b; font-weight:600; font-size:0.9rem;">Tindakan ini tidak dapat dibatalkan.</p>
        <div style="display:flex; gap:1rem;">
            <button class="btn-outline-soft" style="flex:1;" onclick="closeModal()">Batal</button>
            <form id="deleteForm" method="POST" style="flex:1;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-gradient" style="background:var(--accent); width:100%; box-shadow:0 8px 20px rgba(244, 63, 94, 0.2);">Hapus</button>
            </form>
        </div>
    </div>
</div>

{{-- Success Toast --}}
@if(session('success'))
<div id="statusToast" style="position:fixed; top:2rem; right:2rem; z-index:1100; background:rgba(255,255,255,0.9); backdrop-filter:blur(10px); border:1px solid #d1fae5; border-left:6px solid #10b981; border-radius:16px; padding:1.25rem 2rem; box-shadow:0 15px 40px rgba(0,0,0,0.1); display:flex; align-items:center; gap:1rem; animation: slideUp 0.3s ease-out;">
    <div style="width:32px; height:32px; background:#d1fae5; color:#059669; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:0.9rem;">
        <i class="fa-solid fa-check"></i>
    </div>
    <div style="flex:1;">
        <p style="margin:0; font-weight:800; color:#064e3b; font-size:0.9rem;">{{ session('success') }}</p>
    </div>
    <button onclick="this.parentElement.remove()" style="background:transparent; border:none; color:#94a3b8; font-size:1.2rem; cursor:pointer;">&times;</button>
</div>
@endif

<style>
    .btn-action-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-action-icon:hover { transform: scale(1.1); filter: brightness(0.9); }
</style>

<script>
    // ─── Realtime Search ───
    document.getElementById('searchInput').addEventListener('keyup', function() {
        let value = this.value.toLowerCase();
        let rows = document.querySelectorAll('#soalTable tbody tr');
        let hasResult = false;

        rows.forEach(row => {
            let text = row.querySelector('td:first-child').textContent.toLowerCase();
            if (text.includes(value)) {
                row.style.display = '';
                hasResult = true;
            } else {
                row.style.display = 'none';
            }
        });

        // Toggle Empty State for search if needed
        // (For simplicity I'll just rely on CSS Display)
    });

    // ─── Textarea Auto-Resize ───
    function autoResizeTextarea(textarea) {
        textarea.style.height = 'auto';
        textarea.style.height = (textarea.scrollHeight) + 'px';
    }

    // ─── Character Counter ───
    function updateCharCount(textarea) {
        let count = textarea.value.length;
        document.getElementById('char_counter').innerText = count + ' / 1000';
    }

    // ─── Form Reset ───
    function resetForm() {
        document.getElementById('formSoal').reset();
        document.getElementById('char_counter').innerText = '0 / 1000';
        document.getElementById('teks_soal').focus();
    }

    // ─── Delete Confirmation ───
    function confirmDelete(id) {
        const modal = document.getElementById('deleteModal');
        const form = document.getElementById('deleteForm');
        form.action = `/admin/bank_soal/${id}`;
        modal.style.display = 'flex';
    }
    function closeModal() {
        document.getElementById('deleteModal').style.display = 'none';
    }

    // ─── Auto Close Toast ───
    window.onload = () => {
        const toast = document.getElementById('statusToast');
        if(toast) {
            setTimeout(() => {
                toast.style.transition = 'opacity 0.5s ease-out';
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 500);
            }, 3000);
        }
        
        // Final focus as requested
        @if(session('success'))
            document.getElementById('teks_soal').focus();
        @endif
    }
</script>
@endsection
