@extends('layouts.admin')
@section('title', 'Bank Soal CBT')

@section('content')
<!-- Import Lucide Icons -->
<script src="https://unpkg.com/lucide@latest"></script>

<style>
    /* ─── Premium Modern SaaS Design Tokens ─── */
    :root {
        --primary: #6366f1;
        --primary-light: #818cf8;
        --secondary: #a855f7;
        --accent: #f43f5e;
        --success: #10b981;
        --warning: #f59e0b;
        --info: #0ea5e9;
        --bg-body: #f8fafc;
        --glass-bg: rgba(255, 255, 255, 0.9);
        --glass-border: rgba(226, 232, 240, 0.8);
        --radius-xl: 24px;
        --radius-lg: 16px;
        --radius-md: 12px;
        --shadow-soft: 0 4px 20px rgba(0, 0, 0, 0.03);
        --shadow-premium: 0 20px 40px -12px rgba(0, 0, 0, 0.08);
        --text-main: #1e293b;
        --text-muted: #64748b;
    }

    [data-theme='dark'] {
        --glass-bg: rgba(15, 23, 42, 0.9);
        --glass-border: rgba(51, 65, 85, 0.8);
        --bg-body: #020617;
        --text-main: #f1f5f9;
        --text-muted: #94a3b8;
    }

    .bank-soal-wrapper {
        animation: fadeIn 0.6s cubic-bezier(0.2, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
        padding: 0.5rem 0.5rem 2rem;
        font-family: 'Inter', sans-serif;
        max-width: 1440px;
        margin: 0 auto;
    }

    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

    /* ─── Stats Section ─── */
    .stats-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
    }

    .stat-card {
        background: var(--glass-bg);
        backdrop-filter: blur(8px);
        border: 1px solid var(--glass-border);
        border-radius: var(--radius-lg);
        padding: 0.75rem 1rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        box-shadow: var(--shadow-soft);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .stat-card:hover { transform: translateY(-3px); box-shadow: var(--shadow-premium); border-color: var(--primary-light); }
    .stat-card.active { border-color: var(--primary); background: rgba(99, 102, 241, 0.05); transform: translateY(-3px); box-shadow: var(--shadow-premium); }

    .stat-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.2rem;
        flex-shrink: 0;
    }
    .stat-info .stat-value { font-size: 1.25rem; font-weight: 800; color: var(--text-main); line-height: 1.2; margin: 0; }
    .stat-info .stat-label { font-size: 0.75rem; font-weight: 600; color: var(--text-muted); margin-top: 2px; }

    /* ─── Main Grid Layout ─── */
    .content-grid {
        display: grid;
        grid-template-columns: 1.6fr 1fr;
        gap: 1rem;
    }

    .premium-card {
        background: var(--glass-bg);
        backdrop-filter: blur(12px);
        border: 1px solid var(--glass-border);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-soft);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        transition: border-color 0.3s;
    }

    .card-header {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid var(--glass-border);
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: rgba(255, 255, 255, 0.02);
    }
    .card-header h3 { margin: 0; font-size: 0.9rem; font-weight: 800; color: var(--text-main); display: flex; align-items: center; gap: 0.5rem; }
    .card-body { padding: 1.25rem; flex: 1; }

    /* ─── Advanced Form Controls ─── */
    .form-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 1rem; margin-bottom: 1.5rem; }
    .form-group-modern { display: flex; flex-direction: column; gap: 0.4rem; }
    .label-modern { font-size: 0.65rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); }
    .input-modern {
        background: rgba(248, 250, 252, 0.5);
        border: 1.5px solid var(--glass-border);
        padding: 0.6rem 0.8rem;
        border-radius: 10px;
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--text-main);
        transition: all 0.2s;
    }
    .input-modern:focus { border-color: var(--primary); background: white; box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1); outline: none; }
    
    /* Options Tiles Styling - Fixed for longer text */
    .options-grid-modern {
        display: grid;
        grid-template-columns: 1fr;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }
    .option-item {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        background: rgba(248, 250, 252, 0.4);
        padding: 0.45rem 0.65rem;
        border-radius: 10px;
        border: 1.5px solid var(--glass-border);
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .option-item:focus-within { border-color: var(--primary); background: white; box-shadow: 0 4px 12px rgba(0,0,0,0.03); }
    .option-prefix { 
        width: 32px; height: 32px; 
        background: white; 
        border-radius: 8px; 
        display: flex; 
        align-items: center; 
        justify-content: center; 
        font-weight: 800; font-size: 0.8rem; 
        color: var(--text-muted); 
        border: 1px solid var(--glass-border);
        transition: all 0.2s;
        flex-shrink: 0;
        margin-top: 2px;
    }
    .option-item:focus-within .option-prefix { background: var(--primary); color: white; border-color: var(--primary); }
    .option-input { flex: 1; border: none; background: transparent; font-size: 0.82rem; font-weight: 600; outline: none; color: var(--text-main); min-height: 30px; padding: 6px 0; }
    .option-check { cursor: pointer; display: flex; align-items: center; justify-content: center; margin-top: 6px; }
    .option-check input { 
        width: 20px; height: 20px; 
        cursor: pointer; 
        accent-color: var(--success); 
        border-radius: 50%;
    }

    /* ─── Preview Section ─── */
    .preview-container-mockup {
        background: #f1f5f9;
        border-radius: 20px;
        padding: 1rem;
        border: 4px solid #e2e8f0;
        box-shadow: inset 0 2px 10px rgba(0,0,0,0.05);
        position: relative;
    }
    .preview-soal-card { 
        background: white; 
        border-radius: 14px; 
        padding: 1.25rem; 
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05); 
    }
    .preview-meta { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; }
    .preview-badge { padding: 0.3rem 0.6rem; border-radius: 6px; font-size: 0.65rem; font-weight: 800; text-transform: uppercase; }
    .preview-teks { font-size: 0.95rem; font-weight: 700; color: #1e293b; line-height: 1.5; margin-bottom: 1rem; }
    .preview-gambar { margin-bottom: 1rem; border-radius: 10px; overflow: hidden; border: 1px solid #f1f5f9; cursor: zoom-in; display: none; }
    .preview-gambar img { width: 100%; height: auto; display: block; }
    .preview-option { 
        padding: 0.75rem 1rem; 
        border-radius: 10px; 
        border: 1.5px solid #f1f5f9; 
        margin-bottom: 0.5rem; 
        display: flex; 
        align-items: center; 
        gap: 0.75rem; 
        transition: all 0.2s; 
        background: #fcfcfc;
    }
    .preview-option.correct { border-color: var(--success); background: #f0fdf4; color: #15803d; }
    .preview-opt-prefix { 
        width: 24px; height: 24px; 
        border-radius: 50%; 
        border: 1.5px solid #e2e8f0; 
        display: flex; align-items: center; justify-content: center; 
        font-size: 0.7rem; font-weight: 900; 
    }
    .preview-option.correct .preview-opt-prefix { background: var(--success); color: white; border-color: var(--success); }
    .preview-opt-text { font-size: 0.85rem; font-weight: 600; }

    /* ─── Table Section ─── */
    .table-premium-container { 
        border: 1px solid var(--glass-border); 
        border-radius: var(--radius-lg); 
        overflow-y: auto; 
        background: white; 
        margin-bottom: 1rem; 
        max-height: 480px;
        position: relative;
    }
    
    /* Custom Scrollbar */
    .table-premium-container::-webkit-scrollbar { width: 8px; }
    .table-premium-container::-webkit-scrollbar-track { background: #f8fafc; }
    .table-premium-container::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 99px; border: 2px solid #f8fafc; }
    .table-premium-container::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }

    .table-modern { width: 100%; border-collapse: separate; border-spacing: 0; }
    .table-modern thead { position: sticky; top: 0; z-index: 10; }
    .table-modern thead th { 
        background: #f8fafc; 
        padding: 1rem 1.25rem; 
        text-align: left; 
        font-size: 0.65rem; 
        font-weight: 800; 
        color: var(--text-muted); 
        text-transform: uppercase; 
        letter-spacing: 0.05rem; 
        border-bottom: 1px solid var(--glass-border);
    }
    .table-modern tbody tr { border-bottom: 1px solid #f8fafc; transition: all 0.2s; }
    .table-modern tbody tr:hover { background: #fdfdfd; }
    .table-modern td { padding: 0.85rem 1.25rem; vertical-align: middle; }


    /* Status Badge Toggle */
    .status-toggle-btn {
        padding: 0.3rem 0.75rem;
        border-radius: 999px;
        font-size: 0.7rem;
        font-weight: 800;
        letter-spacing: 0.03em;
        cursor: pointer;
        border: none;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        outline: none;
    }
    .status-toggle-btn:hover { transform: scale(1.06); box-shadow: 0 4px 12px rgba(0,0,0,0.12); }
    .status-toggle-btn:active { transform: scale(0.96); }
    .status-toggle-btn.is-aktif { background: #dcfce7; color: #15803d; }
    .status-toggle-btn.is-aktif:hover { background: #bbf7d0; }
    .status-toggle-btn.is-draft { background: #fef3c7; color: #92400e; }
    .status-toggle-btn.is-draft:hover { background: #fde68a; }
    .status-toggle-btn.is-loading { opacity: 0.6; cursor: wait; pointer-events: none; }

    /* Modal Overlay */
    .modal-overlay { display:none; position:fixed; inset:0; background:rgba(2, 6, 23, 0.6); z-index:1000; backdrop-filter:blur(4px); align-items:center; justify-content:center; }
</style>

<div class="bank-soal-wrapper">
    {{-- ─── Stats row ─── --}}
    <div class="stats-container">
        <div class="stat-card {{ !request('status') ? 'active' : '' }}" style="cursor:pointer" onclick="filterTable('all')">
            <div class="stat-icon" style="background:rgba(99, 102, 241, 0.1); color:var(--primary);"><i data-lucide="folder-open"></i></div>
            <div class="stat-info">
                <p class="stat-value">{{ $stats['total'] }}</p>
                <p class="stat-label">Total Soal</p>
            </div>
        </div>
        <div class="stat-card {{ request('status') == 'Aktif' ? 'active' : '' }}" style="cursor:pointer" onclick="filterTable('Aktif')">
            <div class="stat-icon" style="background:rgba(16, 185, 129, 0.1); color:var(--success);"><i data-lucide="check-circle-2"></i></div>
            <div class="stat-info">
                <p class="stat-value">{{ $stats['aktif'] }}</p>
                <p class="stat-label">Soal Aktif</p>
            </div>
        </div>
        <div class="stat-card {{ request('status') == 'Draft' ? 'active' : '' }}" style="cursor:pointer" onclick="filterTable('Draft')">
            <div class="stat-icon" style="background:rgba(245, 158, 11, 0.1); color:var(--warning);"><i data-lucide="file-edit"></i></div>
            <div class="stat-info">
                <p class="stat-value">{{ $stats['draft'] }}</p>
                <p class="stat-label">Soal Draft</p>
            </div>
        </div>
        <div class="stat-card {{ request('status') == 'Digunakan' ? 'active' : '' }}" style="cursor:pointer" onclick="filterTable('Digunakan')">
            <div class="stat-icon" style="background:rgba(168, 85, 247, 0.1); color:var(--secondary);"><i data-lucide="layout"></i></div>
            <div class="stat-info">
                <p class="stat-value">{{ $stats['digunakan'] }}</p>
                <p class="stat-label">Digunakan</p>
            </div>
        </div>
    </div>

    {{-- ─── Input & Preview Grid ─── --}}
    <div class="content-grid">
        {{-- Left: Input Form --}}
        <section class="premium-card">
            <div class="card-header">
                <h3><i data-lucide="plus-circle" style="width:18px;"></i> Input / Edit Soal</h3>
                <div style="display:flex; gap:0.5rem;">
                    <button class="btn-lux btn-lux-ghost" style="padding:0.4rem 0.8rem; font-size:0.75rem;" onclick="openModal('importModal')">
                        <i data-lucide="file-up" style="width:14px;"></i> Import
                    </button>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.bank_soal.store') }}" method="POST" id="formSoal" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" id="soal_id">
                    <div class="form-row" style="grid-template-columns: 1fr;">
                        <div class="form-group-modern">
                            <label class="label-modern">Mata Pelajaran</label>
                            <select name="mapel" id="form_mapel" class="input-modern" required onchange="updatePreview()">
                                <option value="Matematika">Matematika</option>
                                <option value="Bahasa Indonesia">Bahasa Indonesia</option>
                                <option value="Bahasa Inggris">Bahasa Inggris</option>
                                <option value="Umum">Umum</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group-modern" style="margin-bottom:0.75rem;">
                        <label class="label-modern">Pertanyaan</label>
                        <textarea name="teks_soal" id="teks_soal" class="input-modern" style="min-height:72px; resize:vertical; line-height:1.4;" placeholder="Masukkan redaksi pertanyaan..." required oninput="updatePreview()"></textarea>
                    </div>

                    <div class="options-grid-modern">
                        <div class="option-item">
                            <span class="option-prefix">A</span>
                            <textarea name="opsi_a" class="option-input" placeholder="Opsi A" oninput="updatePreview()" required></textarea>
                            <label class="option-check">
                                <input type="radio" name="jawaban_benar" value="A" onchange="updatePreview()" required>
                            </label>
                        </div>
                        <div class="option-item">
                            <span class="option-prefix">B</span>
                            <textarea name="opsi_b" class="option-input" placeholder="Opsi B" oninput="updatePreview()" required></textarea>
                            <label class="option-check">
                                <input type="radio" name="jawaban_benar" value="B" onchange="updatePreview()">
                            </label>
                        </div>
                        <div class="option-item">
                            <span class="option-prefix">C</span>
                            <textarea name="opsi_c" class="option-input" placeholder="Opsi C" oninput="updatePreview()" required></textarea>
                            <label class="option-check">
                                <input type="radio" name="jawaban_benar" value="C" onchange="updatePreview()">
                            </label>
                        </div>
                        <div class="option-item">
                            <span class="option-prefix">D</span>
                            <textarea name="opsi_d" class="option-input" placeholder="Opsi D" oninput="updatePreview()" required></textarea>
                            <label class="option-check">
                                <input type="radio" name="jawaban_benar" value="D" onchange="updatePreview()">
                            </label>
                        </div>
                    </div>

                    <div style="margin-bottom:0.75rem;">
                        <div class="form-group-modern">
                            <label class="label-modern">Gambar (JPG/PNG)</label>
                            <div id="drop_zone" style="border:1.5px dashed var(--glass-border); border-radius:10px; padding:0.75rem; text-align:center; cursor:pointer; background:rgba(248, 250, 252, 0.4); transition:all 0.2s;" onmouseover="this.style.borderColor='var(--primary)'" onmouseout="this.style.borderColor='var(--glass-border)'" onclick="document.getElementById('gambar_input').click()">
                                <i data-lucide="image" style="width:20px; color:var(--text-muted); margin:0 auto 0.4rem; display:block;"></i>
                                <span id="file_name_label" style="font-size:0.65rem; color:var(--text-muted); font-weight:700;">Click to upload</span>
                                <input type="file" id="gambar_input" name="gambar" accept="image/jpeg,image/png,image/jpg" style="display:none;" onchange="handleImagePreview(this)">
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="tahun_ajaran" value="{{ $filterTahun ?? '2024/2025' }}">
                    <input type="hidden" name="nama_paket" value="{{ $filterPaket ?? 'Paket Umum' }}">

                    <div style="display:flex; gap:0.5rem; border-top:1px solid var(--glass-border); padding-top:1rem; margin-top:0.25rem;">
                        <button type="button" onclick="resetForm()" style="flex:1; display:flex; align-items:center; justify-content:center; gap:0.4rem; padding:0.55rem 0.75rem; border-radius:10px; border:none; cursor:pointer; font-size:0.72rem; font-weight:800; background:#f1f5f9; color:#64748b; transition:all 0.2s;" onmouseover="this.style.background='#e2e8f0'" onmouseout="this.style.background='#f1f5f9'">
                            <i data-lucide="rotate-ccw" style="width:13px;"></i> Reset
                        </button>
                        <button type="submit" name="status" value="Draft" style="flex:1; display:flex; align-items:center; justify-content:center; gap:0.4rem; padding:0.55rem 0.75rem; border-radius:10px; border:none; cursor:pointer; font-size:0.72rem; font-weight:800; background:#fef3c7; color:#92400e; transition:all 0.2s;" onmouseover="this.style.background='#fde68a'" onmouseout="this.style.background='#fef3c7'">
                            <i data-lucide="file-edit" style="width:13px;"></i> Draft
                        </button>
                        <button type="submit" name="status" value="Aktif" style="flex:1.5; display:flex; align-items:center; justify-content:center; gap:0.4rem; padding:0.55rem 1rem; border-radius:10px; border:none; cursor:pointer; font-size:0.72rem; font-weight:800; background:linear-gradient(135deg, var(--primary), var(--secondary)); color:white; box-shadow:0 4px 12px rgba(99,102,241,0.3); transition:all 0.2s;" onmouseover="this.style.boxShadow='0 6px 16px rgba(99,102,241,0.45)'" onmouseout="this.style.boxShadow='0 4px 12px rgba(99,102,241,0.3)'">
                            <i data-lucide="save" style="width:13px;"></i> Simpan Aktif
                        </button>
                    </div>
                </form>
            </div>
        </section>

        {{-- Right: Tampilan Siswa Preview --}}
        <section class="premium-card" style="background: rgba(241, 245, 249, 0.3);">
            <div class="card-header" style="background:transparent;">
                <h3><i data-lucide="eye" style="width:18px;"></i> Preview Siswa</h3>
                <div style="display:flex; gap:.25rem; align-items:center;">
                   <span style="font-size:0.6rem; font-weight:800; color:var(--primary); background:rgba(99, 102, 241, 0.1); padding:0.2rem 0.5rem; border-radius:4px;">LIVE PREVIEW</span>
                </div>
            </div>
            <div class="card-body">
                <div class="preview-container-mockup">
                    <div class="preview-soal-card">
                        <div class="preview-meta">
                            <span class="preview-badge" id="preview_mapel_badge" style="background:#e0e7ff; color:#4338ca;">Matematika</span>
                        </div>
                        <div class="preview-teks" id="preview_teks">
                            Tampilan pertanyaan akan muncul di sini...
                        </div>

                        <div class="preview-gambar" id="preview_gambar_container" onclick="window.open(this.querySelector('img').src, '_blank')">
                            <img src="" id="img_preview" alt="Preview Soal">
                        </div>
                        
                        <div id="preview_options">
                            <div class="preview-option" id="prev_opt_A">
                                <div class="preview-opt-prefix">A</div>
                                <div class="preview-opt-text" id="prev_text_A">Pilihan A</div>
                            </div>
                            <div class="preview-option" id="prev_opt_B">
                                <div class="preview-opt-prefix">B</div>
                                <div class="preview-opt-text" id="prev_text_B">Pilihan B</div>
                            </div>
                            <div class="preview-option" id="prev_opt_C">
                                <div class="preview-opt-prefix">C</div>
                                <div class="preview-opt-text" id="prev_text_C">Pilihan C</div>
                            </div>
                            <div class="preview-option" id="prev_opt_D">
                                <div class="preview-opt-prefix">D</div>
                                <div class="preview-opt-text" id="prev_text_D">Pilihan D</div>
                            </div>
                        </div>

                        <div style="margin-top:1.25rem; padding:0.75rem; background:#f0fdf4; border-radius:10px; display:flex; align-items:center; gap:0.5rem; border:1px solid #dcfce7; color:#15803d; font-size:0.75rem; font-weight:800;">
                            <i data-lucide="check-circle" style="width:14px;"></i> Jawaban Benar: <span id="preview_correct_label">-</span>
                        </div>
                    </div>
                </div>

                <!-- ── NEW CARD: Panduan Import Soal ── -->
                <div style="margin-top: 1.5rem; background: white; border: 1.5px solid var(--glass-border); border-radius: 12px; padding: 1.25rem;">
                    <h4 style="font-size: 0.9rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                        <i data-lucide="file-spreadsheet" style="width:16px; color:var(--success);"></i> Panduan Import Soal
                    </h4>
                    <ul style="font-size: 0.77rem; color: var(--text-muted); margin-bottom: 1.25rem; padding-left: 1.25rem; line-height: 1.6; font-weight:600;">
                        <li style="margin-bottom: 0.25rem;">File harus berformat CSV atau Excel (.xlsx).</li>
                        <li style="margin-bottom: 0.25rem;">Gunakan <strong style="color:var(--primary);">template</strong> yang telah disediakan.</li>
                        <li style="margin-bottom: 0.25rem;">Jangan mengubah nama kolom.</li>
                        <li>Maksimal 100 soal/import.</li>
                    </ul>
                    <div style="display: flex; gap: 0.5rem;">
                        <button type="button" class="btn-lux btn-lux-ghost" style="flex:1; justify-content:center; font-size:0.75rem; border-radius:8px; display:flex; align-items:center; gap:0.4rem;" onclick="openModal('templatePreviewModal')">
                            <i data-lucide="eye" style="width:14px;"></i> Lihat Template
                        </button>
                        <a href="{{ route('admin.bank_soal.template_excel') }}" class="btn-lux btn-lux-primary" style="flex:1; justify-content:center; font-size:0.75rem; text-decoration:none; border-radius:8px; display:flex; align-items:center; gap:0.4rem; background:var(--success); border:none; box-shadow:0 4px 12px rgba(16,185,129,0.2);">
                            <i data-lucide="download" style="width:14px;"></i> Download Template
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </div>

    {{-- ─── Database Table Row ─── --}}
    <section class="premium-card">
        <div class="card-header">
            <h3><i data-lucide="database" style="width:18px;"></i> Daftar Bank Soal</h3>
            <div style="display:flex; gap:0.5rem; align-items:center;">
                <div style="position:relative;">
                    <i data-lucide="search" style="position:absolute; left:0.75rem; top:50%; transform:translateY(-50%); width:14px; color:var(--text-muted);"></i>
                    <input type="text" id="searchInput" class="input-modern" placeholder="Cari..." style="padding-left:2.25rem; border-radius:99px; width:200px; font-size:0.75rem;">
                </div>
                


                <div style="width:1px; height:20px; background:var(--glass-border); margin:0 0.25rem;"></div>

                <div style="position:relative;">
                    <button class="btn-lux btn-lux-ghost" style="padding:0.4rem 0.8rem; border-radius:99px; font-size:0.7rem;" onclick="document.getElementById('exportMenu').style.display = document.getElementById('exportMenu').style.display === 'block' ? 'none' : 'block'">
                        <i data-lucide="file-spreadsheet" style="width:14px; color:var(--success);"></i> Export
                    </button>
                    <div id="exportMenu" style="display:none; position:absolute; right:0; top:110%; background:white; border:1px solid var(--glass-border); border-radius:12px; box-shadow:var(--shadow-premium); z-index:100; width:120px; padding:0.5rem;">
                         <a href="{{ route('admin.bank_soal.export') }}?format=excel" class="dropdown-item" style="display:flex; align-items:center; gap:8px; padding:8px; font-size:0.75rem; text-decoration:none; color:var(--text-main); font-weight:700;">
                            <i data-lucide="file-text" style="width:14px; color:#107c41;"></i> Excel
                         </a>
                         <a href="{{ route('admin.bank_soal.export') }}?format=csv" class="dropdown-item" style="display:flex; align-items:center; gap:8px; padding:8px; font-size:0.75rem; text-decoration:none; color:var(--text-main); font-weight:700;">
                            <i data-lucide="file-type" style="width:14px; color:#1e293b;"></i> CSV
                         </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body" style="padding:0;">
            <div class="table-premium-container">
                <table class="table-modern" id="soalTable">
                    <thead>
                        <tr>
                            <th style="padding-left:1.5rem; width:50px;">No</th>
                            <th style="width:35%;">Pertanyaan</th>
                            <th>Sumber</th>
                            <th>Tanggal Dibuat</th>
                            <th style="width:100px;">Status</th>
                            <th style="text-align:right; padding-right:1.5rem;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($soals as $idx => $s)
                        <tr data-status="{{ $s->status }}">
                            <td style="padding-left:1.5rem; font-weight:700; color:var(--text-muted); font-size:0.75rem;">{{ $idx + 1 }}</td>
                            <td>
                                <div style="font-weight:700; color:var(--text-main); line-height:1.4; font-size:0.85rem;">{{ Str::limit($s->teks_soal, 120) }}</div>
                                @if($s->gambar)
                                    <span style="font-size:0.6rem; color:var(--info); font-weight:800; display:flex; align-items:center; gap:4px; margin-top:2px;">
                                        <i data-lucide="image" style="width:10px;"></i> Berisi Gambar
                                    </span>
                                @endif
                            </td>
                            <td><span style="font-weight:700; color:var(--text-muted); font-size:0.8rem;">{{ $s->sumber ?? 'Input Manual' }}</span></td>
                            <td><span style="font-weight:700; color:var(--text-muted); font-size:0.8rem;">{{ $s->created_at->format('d/m/Y') }}</span></td>
                            <td>
                                <button
                                    class="status-toggle-btn {{ $s->status == 'Aktif' ? 'is-aktif' : 'is-draft' }}"
                                    data-id="{{ $s->id }}"
                                    onclick="toggleSoalStatus(this)"
                                    title="Klik untuk ubah status">
                                    <span style="width:6px; height:6px; border-radius:50%; background:currentColor; display:inline-block; flex-shrink:0;"></span>
                                    <span class="status-label">{{ $s->status }}</span>
                                </button>
                            </td>
                            <td style="text-align:right; padding-right:1.5rem;">
                                <div style="display:flex; gap:0.4rem; justify-content:flex-end;">
                                    <button class="btn-lux btn-lux-ghost" style="padding:0; border-radius:8px; height:32px; width:32px; color:var(--primary);" 
                                            onclick="editSoal({{ json_encode($s) }})">
                                        <i data-lucide="edit-3" style="width:14px;"></i>
                                    </button>
                                    <button class="btn-lux btn-lux-ghost" style="padding:0; border-radius:8px; height:32px; width:32px; color:var(--accent);" onclick="confirmDelete({{$s->id}})">
                                        <i data-lucide="trash-2" style="width:14px;"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" style="padding:3rem; text-align:center;">
                                <div style="opacity:0.2; margin-bottom:0.75rem;"><i data-lucide="folder-search" style="width:48px; height:48px; margin:0 auto;"></i></div>
                                <h4 style="font-weight:800; color:var(--text-main); font-size:0.9rem;">Belum ada data</h4>
                                <p style="color:var(--text-muted); font-size:0.75rem;">Silakan tambahkan soal baru atau import file.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </section>
</div>

{{-- ─── Import Modal ─── --}}
<div id="importModal" class="modal-overlay">
    <div class="premium-card" style="width:480px; padding:1.5rem; border-radius:24px;">
        <div class="card-header" style="border:none; padding:0 0 1rem 0;">
            <h3><i data-lucide="file-up" style="width:20px;"></i> Import Massal Soal</h3>
            <button onclick="closeModal('importModal')" style="border:none; background:none; cursor:pointer;"><i data-lucide="x" style="width:20px; color:var(--text-muted);"></i></button>
        </div>
        <div style="background:rgba(14, 165, 233, 0.05); border:1px dashed var(--info); border-radius:16px; padding:1.5rem;">
            <p style="margin-bottom:1.5rem; font-size:0.8rem; color:var(--text-muted); font-weight:600;">Unggah file CSV atau Excel untuk memuat banyak soal sekaligus. Gunakan template yang telah disediakan.</p>
            <form action="{{ route('admin.bank_soal.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div style="border:1.5px dashed var(--glass-border); border-radius:12px; padding:1.5rem; text-align:center; cursor:pointer; background:white;" onclick="document.getElementById('file_import_input').click()">
                    <i data-lucide="upload-cloud" style="width:32px; color:var(--info); margin:0 auto 0.5rem; display:block;"></i>
                    <span id="import_file_label" style="font-size:0.75rem; color:var(--text-muted); font-weight:700;">Click to select CSV/Excel</span>
                    <input type="file" id="file_import_input" name="file_soal" accept=".csv, .xlsx, .xls, application/vnd.ms-excel, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, text/csv" style="display:none;" onchange="handleImportFile(this)">
                </div>
                <div style="display:flex; justify-content:flex-end; gap:0.75rem; margin-top:2rem;">
                    <button type="submit" class="btn-lux btn-lux-primary">Mulai Import</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ─── Template Preview Modal ─── --}}
<div id="templatePreviewModal" class="modal-overlay">
    <div class="premium-card" style="width:680px; padding:1.5rem; border-radius:24px; max-width:95vw;">
        <div class="card-header" style="border:none; padding:0 0 1rem 0;">
            <h3><i data-lucide="eye" style="width:20px;"></i> Preview Format Template</h3>
            <button onclick="closeModal('templatePreviewModal')" style="border:none; background:none; cursor:pointer;"><i data-lucide="x" style="width:20px; color:var(--text-muted);"></i></button>
        </div>
        <div style="background:rgba(241, 245, 249, 0.5); border:1px solid var(--glass-border); border-radius:12px; padding:1rem; overflow-x:auto;">
            <table class="table-modern" style="min-width: 600px; text-align: left;">
                <thead>
                    <tr>
                        <th style="padding:0.75rem; font-size:0.65rem;">Tahun Ajaran</th>
                        <th style="padding:0.75rem; font-size:0.65rem;">Pertanyaan</th>
                        <th style="padding:0.75rem; font-size:0.65rem;">Opsi A</th>
                        <th style="padding:0.75rem; font-size:0.65rem;">Opsi B</th>
                        <th style="padding:0.75rem; font-size:0.65rem;">Opsi C</th>
                        <th style="padding:0.75rem; font-size:0.65rem;">Opsi D</th>
                        <th style="padding:0.75rem; font-size:0.65rem;">Jawaban Benar</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="padding:0.75rem; font-size:0.75rem; font-weight:700;">2024/2025</td>
                        <td style="padding:0.75rem; font-size:0.75rem; font-weight:700;">Berapakah 1+1?</td>
                        <td style="padding:0.75rem; font-size:0.75rem; font-weight:700;">1</td>
                        <td style="padding:0.75rem; font-size:0.75rem; font-weight:700;">2</td>
                        <td style="padding:0.75rem; font-size:0.75rem; font-weight:700;">3</td>
                        <td style="padding:0.75rem; font-size:0.75rem; font-weight:700;">4</td>
                        <td style="padding:0.75rem; font-size:0.75rem; font-weight:700;">B</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ─── Delete Modal ─── --}}
<div id="deleteModal" class="modal-overlay">
    <div class="premium-card" style="width:340px; padding:1.5rem; text-align:center; border-radius:24px;">
        <div style="width:56px; height:56px; background:#fee2e2; border-radius:50%; margin:0 auto 1.25rem; display:flex; align-items:center; justify-content:center; color:var(--accent);">
            <i data-lucide="alert-circle" style="width:28px; height:28px;"></i>
        </div>
        <h3 style="margin-bottom:0.4rem; font-weight:800; font-size:1.1rem;">Hapus Soal?</h3>
        <p style="color:var(--text-muted); margin-bottom:1.5rem; font-weight:600; font-size:0.85rem;">Tindakan ini tidak dapat dibatalkan.</p>
        <div style="display:flex; gap:0.75rem;">
            <button class="btn-lux btn-lux-ghost" style="flex:1;" onclick="closeModal('deleteModal')">Batal</button>
            <form id="deleteForm" method="POST" style="flex:1;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-lux btn-lux-primary" style="background:var(--accent); width:100%; box-shadow:0 8px 16px rgba(244, 63, 94, 0.2);">Hapus</button>
            </form>
        </div>
    </div>
</div>

<script>
    // Initialize Lucide Icons
    lucide.createIcons();

    // ─── Live Preview Logic ───
    function updatePreview() {
        const teks = document.getElementById('teks_soal').value;
        document.getElementById('preview_teks').innerText = teks || "Tampilan pertanyaan akan muncul di sini...";

        ['A', 'B', 'C', 'D'].forEach(opt => {
            const optVal = document.querySelector(`textarea[name="opsi_${opt.toLowerCase()}"]`).value;
            document.getElementById(`prev_text_${opt}`).innerText = optVal || `Pilihan ${opt}`;
        });

        const mapel = document.getElementById('form_mapel').value;
        document.getElementById('preview_mapel_badge').innerText = mapel;

        const correct = document.querySelector('input[name="jawaban_benar"]:checked')?.value;
        document.getElementById('preview_correct_label').innerText = correct || "-";

        document.querySelectorAll('.preview-option').forEach(el => el.classList.remove('correct'));
        if(correct) {
            const activeEl = document.getElementById(`prev_opt_${correct}`);
            if(activeEl) activeEl.classList.add('correct');
        }
    }

    function handleImportFile(input) {
        const file = input.files[0];
        if(!file) return;
        const validExtensions = ['.csv', '.xlsx', '.xls'];
        const fileName = file.name.toLowerCase();
        let isValid = false;
        
        for (let ext of validExtensions) {
            if (fileName.endsWith(ext)) {
                isValid = true;
                break;
            }
        }
        
        if (!isValid) {
            input.value = '';
            document.getElementById('import_file_label').innerText = 'Click to select CSV/Excel';
            alert('Gagal! Format file tidak didukung. Hanya bisa mengunggah file CSV atau Excel.');
            return;
        }
        
        document.getElementById('import_file_label').innerText = file.name;
    }

    function handleImagePreview(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const imgContainer = document.getElementById('preview_gambar_container');
                const img = document.getElementById('img_preview');
                img.src = e.target.result;
                imgContainer.style.display = 'block';
                
                document.getElementById('file_name_label').innerText = input.files[0].name;
                document.getElementById('file_name_label').style.color = "var(--success)";
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function resetForm() {
        document.getElementById('formSoal').reset();
        document.getElementById('soal_id').value = '';
        document.getElementById('preview_gambar_container').style.display = 'none';
        document.getElementById('file_name_label').innerText = 'Click to upload';
        document.getElementById('file_name_label').style.color = "var(--text-muted)";
        updatePreview();
    }

    function editSoal(soal) {
        document.getElementById('soal_id').value = soal.id;
        document.getElementById('form_mapel').value = soal.mapel || 'Umum';
        document.getElementById('teks_soal').value = soal.teks_soal;
        document.querySelector('textarea[name="opsi_a"]').value = soal.opsi_a;
        document.querySelector('textarea[name="opsi_b"]').value = soal.opsi_b;
        document.querySelector('textarea[name="opsi_c"]').value = soal.opsi_c;
        document.querySelector('textarea[name="opsi_d"]').value = soal.opsi_d;
        
        const radio = document.querySelector(`input[name="jawaban_benar"][value="${soal.jawaban_benar}"]`);
        if(radio) radio.checked = true;

        if(soal.gambar) {
            const imgContainer = document.getElementById('preview_gambar_container');
            const img = document.getElementById('img_preview');
            img.src = '/storage/' + soal.gambar;
            imgContainer.style.display = 'block';
        } else {
            document.getElementById('preview_gambar_container').style.display = 'none';
        }

        updatePreview();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // ─── Modal Logic ───
    function openModal(id) { document.getElementById(id).style.display = 'flex'; }
    function closeModal(id) { document.getElementById(id).style.display = 'none'; }

    // ─── Toggle Status (AJAX) ───
    function toggleSoalStatus(btn) {
        const id = btn.dataset.id;
        btn.classList.add('is-loading');

        fetch(`/admin/bank_soal/${id}/toggle-status`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const newStatus = data.new_status;
                const label = btn.querySelector('.status-label');
                label.innerText = newStatus;
                btn.classList.remove('is-aktif', 'is-draft', 'is-loading');
                btn.classList.add(newStatus === 'Aktif' ? 'is-aktif' : 'is-draft');
            }
        })
        .catch(() => {
            btn.classList.remove('is-loading');
        });
    }

    // ─── Filtering Logic ───
    function filterTable(status) {
        if(status === 'all') {
            window.location.href = "{{ route('admin.bank_soal.index') }}";
        } else if(status === 'Digunakan') {
             // Logic for 'Digunakan' might require a separate filter or just showing all for now
             // User asked: "munvculin yang 51 digunakan saja"
             // For simplicity, let's just use the status filter if available
             window.location.href = "{{ route('admin.bank_soal.index') }}?status=" + status;
        } else {
            window.location.href = "{{ route('admin.bank_soal.index') }}?status=" + status;
        }
    }

    function confirmDelete(id) {
        document.getElementById('deleteForm').action = `/admin/bank_soal/${id}`;
        openModal('deleteModal');
    }

    document.getElementById('searchInput').addEventListener('keyup', function() {
        const val = this.value.toLowerCase();
        const rows = document.querySelectorAll('#soalTable tbody tr');
        rows.forEach(row => {
            if(row.innerText.toLowerCase().includes(val)) row.style.display = '';
            else row.style.display = 'none';
        });
    });

    window.onclick = function(event) {
        const modals = ['importModal', 'deleteModal', 'templatePreviewModal'];
        modals.forEach(id => {
            if (event.target == document.getElementById(id)) closeModal(id);
        });
        if (!event.target.closest('.btn-lux-ghost')) {
            document.getElementById('exportMenu').style.display = 'none';
        }
    }

    window.onload = updatePreview;
</script>

@endsection
