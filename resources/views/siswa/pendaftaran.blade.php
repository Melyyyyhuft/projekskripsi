@extends('layouts.siswa')
@section('title', 'Form Pendaftaran')

@section('content')
@php
    $berkasWajibIds = ['skl', 'rapor', 'pasfoto'];
    $sudahUploadSemua = collect($berkasWajibIds)->every(fn($id) => !empty($berkasAktif[$id]));
    $isRevisi  = $pendaftaran && $pendaftaran->status == 'revisi';
    $isLolos   = $pendaftaran && in_array($pendaftaran->status, ['lolos_admin', 'sudah_ujian', 'diterima', 'tidak_diterima']);
    $isMenunggu= $pendaftaran && $pendaftaran->status == 'menunggu_verifikasi';
@endphp

<style>
.pendaftaran-container { max-width: 900px; margin: 0 auto; padding-bottom: 5rem; }

/* ─── Cards ─── */
.form-card {
    background: white; border-radius: 14px; border: 1px solid #e2e8f0;
    padding: 2rem; margin-bottom: 2rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,.05);
}
.form-label { display:block; font-size:.875rem; font-weight:700; color:#334155; margin-bottom:.5rem; }
.form-label .req { color:#ef4444; margin-left:2px; }

.form-control {
    width:100%; padding:.75rem 1rem; border-radius:9px; border:1.5px solid #cbd5e1;
    background:#f8fafc; font-size:.95rem; color:#1e293b; transition:all .2s; outline:none;
}
.form-control::placeholder { color:#94a3b8; }
.form-control:focus { border-color:#3b82f6; background:white; box-shadow:0 0 0 3px rgba(59,130,246,.1); }
.form-control.is-valid   { border-color:#10b981; background:white; }
.form-control.is-invalid { border-color:#ef4444; background:#fff8f8; }

/* Inline error message */
.field-error {
    display:none; font-size:.75rem; color:#ef4444; font-weight:700; margin-top:.35rem;
    animation:fadeIn .2s; padding:.25rem .5rem; background:#fff1f1; border-radius:6px;
}
.field-error.show { display:flex; align-items:center; gap:.3rem; }

.form-help { font-size:.75rem; color:#94a3b8; margin-top:.35rem; font-weight:500; }

.form-grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:1.5rem; margin-bottom:1.5rem; }

/* ─── Upload ─── */
.upload-section-title { font-size:1.2rem; font-weight:900; color:#1e40af; margin:3rem 0 1.5rem; }
.alert-custom { border-radius:12px; padding:1.25rem; font-size:.85rem; line-height:1.6; margin-bottom:1.5rem; }
.alert-info    { background:#eff6ff; border-left:4px solid #3b82f6; color:#1e40af; }
.alert-warning { background:#fffbeb; border-left:4px solid #f59e0b; color:#92400e; }

.upload-item {
    background:#f8fafc; border:1px solid #e2e8f0; border-radius:14px;
    padding:1.5rem; margin-bottom:1.5rem;
}
.upload-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:1rem; }
.upload-title  { font-size:.95rem; font-weight:800; color:#334155; }
.badge         { padding:.25rem .75rem; border-radius:999px; font-size:.68rem; font-weight:800; }
.badge-pending { background:#fee2e2; color:#ef4444; }
.badge-ok      { background:#dcfce7; color:#166534; }

/* Review box */
.review-box {
    display:none; margin-top:1rem; padding:.85rem 1rem; background:white;
    border:1px solid #d1fae5; border-radius:10px; align-items:center; gap:.85rem;
}
.review-thumb { width:60px; height:60px; border-radius:8px; object-fit:cover; border:1px solid #e2e8f0; flex-shrink:0; }
.review-name  { font-size:.85rem; font-weight:800; color:#1e293b; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.btn-lihat {
    padding:.4rem .9rem; border-radius:8px; background:#eff6ff; color:#2563eb;
    border:1.5px solid #bfdbfe; font-size:.75rem; font-weight:800; cursor:pointer;
    transition:all .2s; white-space:nowrap; flex-shrink:0;
}
.btn-lihat:hover { background:#dbeafe; }

/* Submit */
.btn-submit {
    background:#6366f1; color:white; border:none; padding:1.2rem 3rem; border-radius:999px;
    font-weight:900; font-size:1.1rem; display:flex; align-items:center; justify-content:center; gap:.75rem;
    width:100%; max-width:480px; margin:4rem auto 0;
    box-shadow:0 10px 30px -5px rgba(99,102,241,.4); cursor:pointer; transition:all .3s;
}
.btn-submit:hover { transform:translateY(-3px); box-shadow:0 15px 40px -5px rgba(99,102,241,.5); }

/* Cert */
.cert-entry  { background:#f8fafc; border:1px solid #e2e8f0; border-radius:14px; padding:1.5rem; margin-bottom:1.25rem; position:relative; }
.btn-add-cert{
    background:white; color:#3b82f6; border:2px dashed #3b82f6; padding:.7rem 1.4rem;
    border-radius:12px; font-weight:800; font-size:.85rem; cursor:pointer;
    display:flex; align-items:center; gap:.5rem; transition:all .2s; margin-top:.5rem;
}
.btn-add-cert:hover { background:#eff6ff; }

/* Lightbox */
#previewLightbox {
    display:none; position:fixed; inset:0; z-index:99999;
    background:rgba(0,0,0,.85); backdrop-filter:blur(8px);
    align-items:center; justify-content:center; flex-direction:column;
}
#previewLightbox.open { display:flex; }
#lbClose {
    position:absolute; top:1.5rem; right:2rem; color:white; font-size:2.5rem;
    cursor:pointer; font-weight:300; line-height:1; z-index:2;
}
#lbImg  { max-width:90vw; max-height:85vh; border-radius:10px; display:none; }
#lbIframe{ width:88vw; height:88vh; border:none; border-radius:10px; display:none; background:white; }

@media(max-width:768px){ .form-grid-2 { grid-template-columns:1fr; } }

/* ── Summary-only styles ── */
.sum-page-title  { font-size:1.6rem; font-weight:900; color:#1e293b; margin-bottom:.2rem; }
.sum-page-sub    { font-size:.9rem;  font-weight:500; color:#64748b; margin-bottom:2rem; }

.sum-banner {
    display:flex; align-items:center; gap:1.5rem;
    padding:1.25rem 1.75rem; border-radius:16px; margin-bottom:2rem;
    border:1px solid;
}
.sum-banner.revisi  { background:#fff7ed; border-color:#fed7aa; }
.sum-banner.ok      { background:#f0fdf4; border-color:#bbf7d0; }
.sum-banner.pending { background:#eff6ff; border-color:#bfdbfe; }
.sum-banner-icon     { width:52px;height:52px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.5rem;flex-shrink:0; }
.sum-banner.revisi  .sum-banner-icon { background:#ffedd5; color:#ea580c; }
.sum-banner.ok      .sum-banner-icon { background:#dcfce7; color:#16a34a; }
.sum-banner.pending .sum-banner-icon { background:#dbeafe; color:#2563eb; }
.sum-banner-title   { font-size:1.05rem; font-weight:900; margin-bottom:.2rem; }
.sum-banner.revisi  .sum-banner-title { color:#c2410c; }
.sum-banner.ok      .sum-banner-title { color:#15803d; }
.sum-banner.pending .sum-banner-title { color:#1d4ed8; }
.sum-banner-desc    { font-size:.83rem; font-weight:500; color:#64748b; }
.sum-banner-btn     {
    padding:.7rem 1.4rem; border-radius:12px; font-weight:800; font-size:.82rem;
    white-space:nowrap; cursor:pointer; border:2px solid; flex-shrink:0;
    display:flex; align-items:center; gap:.5rem; text-decoration:none;
}
.sum-banner.revisi  .sum-banner-btn { color:#ea580c; border-color:#fdba74; background:white; }
.sum-banner.ok      .sum-banner-btn { color:#16a34a; border-color:#86efac; background:white; }
.sum-banner.pending .sum-banner-btn { color:#2563eb; border-color:#93c5fd; background:white; }

.sum-grid { display:grid; grid-template-columns:1fr 1fr; gap:1.5rem; margin-bottom:2rem; align-items:start; }
.sum-card { background:white; border:1px solid #e2e8f0; border-radius:16px; overflow:hidden; }
.sum-card-hdr { display:flex; align-items:center; justify-content:space-between; padding:1.1rem 1.4rem; border-bottom:1px solid #f1f5f9; }
.sum-card-hdr-left { display:flex; align-items:center; gap:.75rem; }
.sum-card-icon { width:36px;height:36px;border-radius:9px;background:#eff6ff;color:#3b82f6;display:flex;align-items:center;justify-content:center; }
.sum-card-title { font-size:.95rem; font-weight:900; color:#1e293b; }
.sum-card-sub   { font-size:.7rem;  font-weight:500; color:#94a3b8; }
.sum-readonly   { display:flex;align-items:center;gap:.3rem;background:#f1f5f9;color:#64748b;padding:.3rem .7rem;border-radius:7px;font-size:.65rem;font-weight:800; }

.sum-row { display:grid;grid-template-columns:22px 1fr auto;align-items:start;padding:.65rem 1.4rem;border-bottom:1px solid #f8fafc;gap:.6rem; }
.sum-row:last-child { border-bottom:none; }
.sum-row-icon  { color:#94a3b8; font-size:.8rem; padding-top:.15rem; }
.sum-row-label { font-size:.82rem; color:#64748b; font-weight:600; }
.sum-row-value { font-size:.85rem; color:#1e293b; font-weight:800; text-align:right; }

.doc-item-sum { padding:1rem 1.4rem; border-bottom:1px solid #f8fafc; }
.doc-item-sum:last-child { border-bottom:none; }
.doc-row      { display:flex; align-items:center; gap:.85rem; }
.doc-file-icon{ width:38px;height:38px;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:1rem;flex-shrink:0; }
.doc-info     { flex:1; min-width:0; }
.doc-fname    { font-size:.83rem; font-weight:800; color:#1e293b; white-space:nowrap;overflow:hidden;text-overflow:ellipsis; }
.doc-fmeta    { font-size:.7rem; color:#94a3b8; font-weight:600; margin-top:.1rem; }
.doc-actions  { display:flex; align-items:center; gap:.5rem; flex-shrink:0; }
.doc-badge    { padding:.25rem .7rem; border-radius:999px; font-size:.65rem; font-weight:900; white-space:nowrap; }
.doc-badge.valid   { background:#dcfce7; color:#166534; }
.doc-badge.revisi  { background:#fee2e2; color:#ef4444; }
.doc-badge.pending { background:#fef3c7; color:#92400e; }
.doc-btn { padding:.4rem .9rem; border-radius:9px; font-size:.73rem; font-weight:800; cursor:pointer; display:flex;align-items:center;gap:.35rem; border:none; text-decoration:none; white-space:nowrap; }
.doc-btn.lihat   { background:#eff6ff; color:#2563eb; }
.doc-btn.reupload{ background:#fff7ed; color:#ea580c; border:1.5px solid #fdba74; }
.doc-admin-note  { margin-top:.6rem; padding:.55rem .9rem; background:#fff5f5; border-left:3px solid #ef4444; border-radius:0 8px 8px 0; font-size:.75rem; color:#b91c1c; font-weight:600; }
.doc-admin-note i{ margin-right:.35rem; }

.tl-wrap  { background:white; border:1px solid #e2e8f0; border-radius:16px; padding:1.75rem 2rem; margin-bottom:1.5rem; }
.tl-title { font-size:1.05rem; font-weight:900; color:#1e293b; margin-bottom:.25rem; }
.tl-sub   { font-size:.8rem; color:#94a3b8; font-weight:500; margin-bottom:2rem; }
.tl-inner { display:flex; justify-content:space-between; position:relative; padding:0 1rem; }
.tl-inner::before { content:''; position:absolute; top:28px; left:1rem; right:1rem; height:2px; background:#e2e8f0; z-index:0; }
.tl-item  { display:flex; flex-direction:column; align-items:center; text-align:center; position:relative; z-index:1; flex:1; }
.tl-dot   { width:52px;height:52px;border-radius:50%;border:2.5px solid #e2e8f0;background:white;display:flex;align-items:center;justify-content:center;font-size:1rem;color:#cbd5e1;transition:all .3s;position:relative;z-index:2; }
.tl-item.done  .tl-dot { background:#dcfce7; border-color:#22c55e; color:#22c55e; }
.tl-item.active .tl-dot { background:var(--primary,#3b82f6); border-color:var(--primary,#3b82f6); color:white; box-shadow:0 0 0 5px rgba(59,130,246,.12); }
.tl-item.warn  .tl-dot { background:#fff7ed; border-color:#f59e0b; color:#f59e0b; box-shadow:0 0 0 5px rgba(245,158,11,.12); }
.tl-label  { font-size:.75rem; font-weight:800; color:#1e293b; margin-top:.6rem; }
.tl-lsub   { font-size:.65rem; color:#94a3b8; font-weight:500; margin-top:.15rem; }
.tl-date   { font-size:.65rem; font-weight:700; color:#22c55e; background:#f0fdf4; padding:.15rem .5rem; border-radius:5px; margin-top:.4rem; }
.tl-date.warn { color:#f59e0b; background:#fffbeb; }

.sum-footer { background:#fffbeb; border:1px solid #fde68a; border-radius:14px; padding:1rem 1.5rem; display:flex; align-items:center; justify-content:space-between; gap:1rem; }
.sum-footer-text { font-size:.85rem; color:#92400e; font-weight:600; display:flex; align-items:center; gap:.5rem; }
.sum-footer-deadline { text-align:right; font-size:.75rem; color:#94a3b8; font-weight:600; }
.sum-footer-deadline strong { display:block; font-size:.9rem; color:#dc2626; font-weight:900; }

@media(max-width:860px){ .sum-grid{ grid-template-columns:1fr; } .tl-inner{ flex-direction:column; gap:1.5rem; } .tl-inner::before{ display:none; } }
</style>

<div class="pendaftaran-container animate-slide-up">

@if(!$pendaftaran)
    {{-- ── FORM PENDAFTARAN ── --}}
    <div style="margin-bottom:2.5rem;">
        <h1 style="font-size:1.75rem;font-weight:900;color:#1e293b;margin-bottom:.4rem;">Formulir Pendaftaran</h1>
        <p style="color:#64748b;font-weight:500;">Isi formulir biodata dan unggah berkas pendaftaran Anda.</p>
    </div>

    @if($errors->any())
    <div class="alert-custom" style="background:#fff1f1;border-left:4px solid #ef4444;color:#991b1b;margin-bottom:1.5rem;">
        <strong>⚠ Terdapat kesalahan yang perlu diperbaiki:</strong>
        <ul style="margin:.5rem 0 0 1.25rem;padding:0;">
            @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('siswa.pendaftaran.store') }}" method="POST" enctype="multipart/form-data" id="mainForm" novalidate>
        @csrf
        {{-- Biodata Card --}}
        <div class="form-card">
            <h3 style="font-size:1.05rem;font-weight:900;color:#1e40af;margin-bottom:2rem;border-bottom:2px solid #f1f5f9;padding-bottom:1rem;">
                <i class="fa-solid fa-user-pen"></i> Biodata Lengkap Siswa
            </h3>
            <div class="form-group" style="margin-bottom:1.5rem;">
                <label class="form-label">Nama Lengkap <span class="req">*</span></label>
                <input type="text" name="nama" id="f_nama" class="form-control" value="{{ old('nama', Auth::user()->name) }}" placeholder="Nama sesuai ijazah" required oninput="validateLettersOnly(this,'f_nama_err','Nama lengkap hanya boleh berisi huruf dan spasi.')">
                <div class="field-error" id="f_nama_err"><i class="fa-solid fa-circle-exclamation"></i><span></span></div>
            </div>
            <div class="form-grid-2">
                <div class="form-group">
                    <label class="form-label">Tempat Lahir <span class="req">*</span></label>
                    <input type="text" name="tempat_lahir" id="f_tl" class="form-control" placeholder="Contoh: Bandung" required oninput="validateLettersOnly(this,'f_tl_err','Tempat lahir hanya boleh berisi huruf dan spasi.')">
                    <div class="field-error" id="f_tl_err"><i class="fa-solid fa-circle-exclamation"></i><span></span></div>
                </div>
                <div class="form-group">
                    <label class="form-label">Tanggal Lahir <span class="req">*</span></label>
                    <input type="date" name="tanggal_lahir" class="form-control" value="{{ old('tanggal_lahir') }}" required>
                </div>
            </div>
            <div class="form-group" style="margin-bottom:1.5rem;">
                <label class="form-label">Alamat Rumah Lengkap <span class="req">*</span></label>
                <textarea name="alamat" id="f_alamat" class="form-control" rows="3" placeholder="Jl. Raya Utama No. 123, Kel. Merdeka, Kec. Padalarang" required oninput="validateContainsLetter(this,'f_alamat_err','Alamat wajib mengandung unsur huruf.')">{{ old('alamat') }}</textarea>
                <div class="field-error" id="f_alamat_err"><i class="fa-solid fa-circle-exclamation"></i><span></span></div>
            </div>
            <div class="form-grid-2">
                <div class="form-group">
                    <label class="form-label">NISN <span class="req">*</span></label>
                    <input type="text" name="nisn" id="f_nisn" class="form-control" placeholder="Contoh: 0123456789" maxlength="10" inputmode="numeric" required oninput="this.value=this.value.replace(/\D/g,''); validateNisn(this)">
                    <div class="field-error" id="f_nisn_err"><i class="fa-solid fa-circle-exclamation"></i><span></span></div>
                    <p class="form-help">Wajib <strong>10 digit angka</strong> saja.</p>
                </div>
                <div class="form-group">
                    <label class="form-label">Nomor HP / WhatsApp <span class="req">*</span></label>
                    <input type="text" name="no_hp" id="f_nohp" class="form-control" placeholder="Contoh: 081234567890" maxlength="13" inputmode="numeric" required oninput="this.value=this.value.replace(/\D/g,''); validateNoHp(this)">
                    <div class="field-error" id="f_nohp_err"><i class="fa-solid fa-circle-exclamation"></i><span></span></div>
                    <p class="form-help">Antara <strong>10 sampai 13 digit angka</strong> saja.</p>
                </div>
            </div>
            <div class="form-grid-2">
                <div class="form-group">
                    <label class="form-label">Asal Sekolah <span class="req">*</span></label>
                    <input type="text" name="asal_sekolah" id="f_asal" class="form-control" placeholder="Contoh: SMP Negeri 1 Bandung" required oninput="validateContainsLetter(this,'f_asal_err','Asal sekolah wajib mengandung unsur huruf.')">
                    <div class="field-error" id="f_asal_err"><i class="fa-solid fa-circle-exclamation"></i><span></span></div>
                </div>
                <div class="form-group">
                    <label class="form-label">Rata-rata Nilai Rapor <span class="req">*</span></label>
                    <input type="number" step="0.01" name="nilai_rapor" id="f_rapor_val" class="form-control" placeholder="Contoh: 90.00" max="100" min="0" required oninput="validateRapor(this)">
                    <div class="field-error" id="f_rapor_val_err"><i class="fa-solid fa-circle-exclamation"></i><span></span></div>
                    <p class="form-help">Maksimal <strong>100.</strong> Gunakan desimal (titik).</p>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Jurusan Pilihan <span class="req">*</span></label>
                <select name="jurusan_id" class="form-control" required>
                    <option value="">-- Pilih Jurusan --</option>
                    @foreach($jurusans as $j)
                        <option value="{{ $j->id }}" {{ old('jurusan_id')==$j->id?'selected':'' }}>{{ $j->nama }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Berkas Wajib --}}
        <h2 class="upload-section-title">Upload Berkas Wajib</h2>
        <div class="alert-custom alert-warning">
            <strong><i class="fa-solid fa-triangle-exclamation"></i> Penting:</strong>
            <ul style="margin:.5rem 0 0 1.25rem;padding:0;">
                <li>Ukuran maks. 2 MB per file.</li>
                <li>Gunakan "Lihat File" untuk memastikan berkas benar.</li>
            </ul>
        </div>
        <div class="upload-item">
            <div class="upload-header"><span class="upload-title">1. Scan SKL / Ijazah *</span><span class="badge badge-pending" id="badge-skl">Belum Dipilih</span></div>
            <p class="form-help" style="margin-top:-0.5rem; margin-bottom:0.75rem; color:#dc2626;">* Wajib format <strong>PDF</strong></p>
            <input type="file" name="skl" class="form-control" accept=".pdf,application/pdf" required onchange="handleFile(this,'skl','SKL')">
            <div class="review-box" id="review-skl"><div id="thumb-skl"></div><div class="doc-info"><div class="review-name" id="name-skl"></div></div><button type="button" class="btn-lihat" onclick="bukaPreview('skl')">Lihat</button></div>
        </div>
        <div class="upload-item">
            <div class="upload-header"><span class="upload-title">2. Scan Rapor *</span><span class="badge badge-pending" id="badge-rapor">Belum Dipilih</span></div>
            <p class="form-help" style="margin-top:-0.5rem; margin-bottom:0.75rem; color:#dc2626;">* Wajib format <strong>PDF</strong></p>
            <input type="file" name="rapor" class="form-control" accept=".pdf,application/pdf" required onchange="handleFile(this,'rapor','Rapor')">
            <div class="review-box" id="review-rapor"><div style="color:#ef4444;"><i class="fa-solid fa-file-pdf fa-xl"></i></div><div class="doc-info"><div class="review-name" id="name-rapor"></div></div><button type="button" class="btn-lihat" onclick="bukaPreview('rapor')">Lihat</button></div>
        </div>
        <div class="upload-item">
            <div class="upload-header"><span class="upload-title">3. Pas Foto *</span><span class="badge badge-pending" id="badge-pasfoto">Belum Dipilih</span></div>
            <p class="form-help" style="margin-top:-0.5rem; margin-bottom:0.75rem; color:#dc2626;">* Wajib format <strong>JPG, JPEG, atau PNG</strong></p>
            <input type="file" name="pasfoto" class="form-control" accept=".jpg,.jpeg,.png,image/jpeg,image/png" required onchange="handleFile(this,'pasfoto','Foto')">
            <div class="review-box" id="review-pasfoto"><div id="thumb-pasfoto"></div><div class="doc-info"><div class="review-name" id="name-pasfoto"></div></div><button type="button" class="btn-lihat" onclick="bukaPreview('pasfoto')">Lihat</button></div>
        </div>

        {{-- Sertifikat --}}
        <h2 class="upload-section-title">4. Sertifikat (Opsional)</h2>
        <div id="cert-container">
            <div class="cert-entry" id="cert-0">
                <div class="form-grid-2">
                    <select name="sertifikat_jenis[]" class="form-control"><option value="">Jenis</option><option>Akademik</option><option>Olahraga</option><option>Seni</option><option>Tahfidz</option></select>
                    <select name="sertifikat_tingkat[]" class="form-control"><option value="">Tingkat</option><option>Provinsi</option><option>Nasional</option><option>Internasional</option></select>
                </div>
                <input type="file" name="sertifikat_file[]" class="form-control" onchange="handleCertFile(this,0)">
            </div>
        </div>
        <button type="button" class="btn-add-cert" onclick="addCert()"><i class="fa-solid fa-plus"></i> Tambah Prestasi</button>
        <button type="submit" class="btn-submit"><i class="fa-solid fa-paper-plane"></i> Kirim Pendaftaran</button>
    </form>
@else
@php
    /* ── determine overall status ── */
    $hasRevisi   = $pendaftaran && $pendaftaran->status === 'revisi';
    $isLolosPost = $pendaftaran && in_array($pendaftaran->status,
                    ['lolos_admin','sudah_ujian','siap_finalisasi','siap_diumumkan','diterima','tidak_diterima']);

    /* timeline steps */
    $tSteps = [
        ['label'=>'Registrasi Akun',  'sub'=>'Akun berhasil dibuat',    'icon'=>'fa-user-check',   'date'=>Auth::user()->created_at],
        ['label'=>'Isi Formulir',     'sub'=>'Data berhasil disubmit',   'icon'=>'fa-file-lines',   'date'=>$pendaftaran?->created_at],
        ['label'=>'Upload Berkas',    'sub'=>'Berkas berhasil diupload', 'icon'=>'fa-cloud-arrow-up','date'=>$sudahUploadSemua?($pendaftaran?->updated_at):null],
        ['label'=>'Verifikasi Admin', 'sub'=>$hasRevisi?'Berkas perlu revisi':($isLolosPost?'Berkas diverifikasi':'Menunggu verifikasi'),
         'icon'=>$hasRevisi?'fa-triangle-exclamation':($isLolosPost?'fa-shield-check':'fa-hourglass-half'),
         'date'=>($isLolosPost||$hasRevisi)?$pendaftaran?->updated_at:null],
        ['label'=>'CBT Online',       'sub'=>'Menunggu jadwal ujian',    'icon'=>'fa-desktop',      'date'=>null],
        ['label'=>'Hasil Seleksi',    'sub'=>'Menunggu hasil seleksi',   'icon'=>'fa-award',        'date'=>null],
    ];
    $activeIdx = 0;
    if($pendaftaran) $activeIdx=1;
    if($sudahUploadSemua) $activeIdx=2;
    if($isMenunggu||$hasRevisi) $activeIdx=3;
    if($isLolosPost) $activeIdx=4;

    $anyRevisi = collect($berkasAktif)->contains(fn($b)=>$b->status_verifikasi==='tidak_valid');
    $allValid2 = collect($berkasAktif)->every(fn($b)=>$b->status_verifikasi==='valid');
@endphp

<style>
/* ── Summary-only styles ── */
.sum-page-title  { font-size:1.6rem; font-weight:900; color:#1e293b; margin-bottom:.2rem; }
.sum-page-sub    { font-size:.9rem;  font-weight:500; color:#64748b; margin-bottom:2rem; }

.sum-banner {
    display:flex; align-items:center; gap:1.5rem;
    padding:1.25rem 1.75rem; border-radius:16px; margin-bottom:2rem;
    border:1px solid;
}
.sum-banner.revisi  { background:#fff7ed; border-color:#fed7aa; }
.sum-banner.ok      { background:#f0fdf4; border-color:#bbf7d0; }
.sum-banner.pending { background:#eff6ff; border-color:#bfdbfe; }
.sum-banner-icon     { width:52px;height:52px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.5rem;flex-shrink:0; }
.sum-banner.revisi  .sum-banner-icon { background:#ffedd5; color:#ea580c; }
.sum-banner.ok      .sum-banner-icon { background:#dcfce7; color:#16a34a; }
.sum-banner.pending .sum-banner-icon { background:#dbeafe; color:#2563eb; }
.sum-banner-title   { font-size:1.05rem; font-weight:900; margin-bottom:.2rem; }
.sum-banner.revisi  .sum-banner-title { color:#c2410c; }
.sum-banner.ok      .sum-banner-title { color:#15803d; }
.sum-banner.pending .sum-banner-title { color:#1d4ed8; }
.sum-banner-desc    { font-size:.83rem; font-weight:500; color:#64748b; }
.sum-banner-btn     {
    padding:.7rem 1.4rem; border-radius:12px; font-weight:800; font-size:.82rem;
    white-space:nowrap; cursor:pointer; border:2px solid; flex-shrink:0;
    display:flex; align-items:center; gap:.5rem; text-decoration:none;
}
.sum-banner.revisi  .sum-banner-btn { color:#ea580c; border-color:#fdba74; background:white; }
.sum-banner.ok      .sum-banner-btn { color:#16a34a; border-color:#86efac; background:white; }
.sum-banner.pending .sum-banner-btn { color:#2563eb; border-color:#93c5fd; background:white; }

.sum-grid { display:grid; grid-template-columns:1fr 1fr; gap:1.5rem; margin-bottom:2rem; align-items:start; }
.sum-card { background:white; border:1px solid #e2e8f0; border-radius:16px; overflow:hidden; }
.sum-card-hdr { display:flex; align-items:center; justify-content:space-between; padding:1.1rem 1.4rem; border-bottom:1px solid #f1f5f9; }
.sum-card-hdr-left { display:flex; align-items:center; gap:.75rem; }
.sum-card-icon { width:36px;height:36px;border-radius:9px;background:#eff6ff;color:#3b82f6;display:flex;align-items:center;justify-content:center; }
.sum-card-title { font-size:.95rem; font-weight:900; color:#1e293b; }
.sum-card-sub   { font-size:.7rem;  font-weight:500; color:#94a3b8; }
.sum-readonly   { display:flex;align-items:center;gap:.3rem;background:#f1f5f9;color:#64748b;padding:.3rem .7rem;border-radius:7px;font-size:.65rem;font-weight:800; }

.sum-row { display:grid;grid-template-columns:22px 1fr auto;align-items:start;padding:.65rem 1.4rem;border-bottom:1px solid #f8fafc;gap:.6rem; }
.sum-row:last-child { border-bottom:none; }
.sum-row-icon  { color:#94a3b8; font-size:.8rem; padding-top:.15rem; }
.sum-row-label { font-size:.82rem; color:#64748b; font-weight:600; }
.sum-row-value { font-size:.85rem; color:#1e293b; font-weight:800; text-align:right; }

.doc-item-sum { padding:1rem 1.4rem; border-bottom:1px solid #f8fafc; }
.doc-item-sum:last-child { border-bottom:none; }
.doc-row      { display:flex; align-items:center; gap:.85rem; }
.doc-file-icon{ width:38px;height:38px;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:1rem;flex-shrink:0; }
.doc-info     { flex:1; min-width:0; }
.doc-fname    { font-size:.83rem; font-weight:800; color:#1e293b; white-space:nowrap;overflow:hidden;text-overflow:ellipsis; }
.doc-fmeta    { font-size:.7rem; color:#94a3b8; font-weight:600; margin-top:.1rem; }
.doc-actions  { display:flex; align-items:center; gap:.5rem; flex-shrink:0; }
.doc-badge    { padding:.25rem .7rem; border-radius:999px; font-size:.65rem; font-weight:900; white-space:nowrap; }
.doc-badge.valid   { background:#dcfce7; color:#166534; }
.doc-badge.revisi  { background:#fee2e2; color:#ef4444; }
.doc-badge.pending { background:#fef3c7; color:#92400e; }
.doc-btn { padding:.4rem .9rem; border-radius:9px; font-size:.73rem; font-weight:800; cursor:pointer; display:flex;align-items:center;gap:.35rem; border:none; text-decoration:none; white-space:nowrap; }
.doc-btn.lihat   { background:#eff6ff; color:#2563eb; }
.doc-btn.reupload{ background:#fff7ed; color:#ea580c; border:1.5px solid #fdba74; }
.doc-admin-note  { margin-top:.6rem; padding:.55rem .9rem; background:#fff5f5; border-left:3px solid #ef4444; border-radius:0 8px 8px 0; font-size:.75rem; color:#b91c1c; font-weight:600; }
.doc-admin-note i{ margin-right:.35rem; }

.tl-wrap  { background:white; border:1px solid #e2e8f0; border-radius:16px; padding:1.75rem 2rem; margin-bottom:1.5rem; }
.tl-title { font-size:1.05rem; font-weight:900; color:#1e293b; margin-bottom:.25rem; }
.tl-sub   { font-size:.8rem; color:#94a3b8; font-weight:500; margin-bottom:2rem; }
.tl-inner { display:flex; justify-content:space-between; position:relative; padding:0 1rem; }
.tl-inner::before { content:''; position:absolute; top:28px; left:1rem; right:1rem; height:2px; background:#e2e8f0; z-index:0; }
.tl-item  { display:flex; flex-direction:column; align-items:center; text-align:center; position:relative; z-index:1; flex:1; }
.tl-dot   { width:52px;height:52px;border-radius:50%;border:2.5px solid #e2e8f0;background:white;display:flex;align-items:center;justify-content:center;font-size:1rem;color:#cbd5e1;transition:all .3s;position:relative;z-index:2; }
.tl-item.done  .tl-dot { background:#dcfce7; border-color:#22c55e; color:#22c55e; }
.tl-item.active .tl-dot { background:var(--primary,#3b82f6); border-color:var(--primary,#3b82f6); color:white; box-shadow:0 0 0 5px rgba(59,130,246,.12); }
.tl-item.warn  .tl-dot { background:#fff7ed; border-color:#f59e0b; color:#f59e0b; box-shadow:0 0 0 5px rgba(245,158,11,.12); }
.tl-label  { font-size:.75rem; font-weight:800; color:#1e293b; margin-top:.6rem; }
.tl-lsub   { font-size:.65rem; color:#94a3b8; font-weight:500; margin-top:.15rem; }
.tl-date   { font-size:.65rem; font-weight:700; color:#22c55e; background:#f0fdf4; padding:.15rem .5rem; border-radius:5px; margin-top:.4rem; }
.tl-date.warn { color:#f59e0b; background:#fffbeb; }

.tl-line-done { position:absolute; top:28px; left:1rem; height:2px; background:#22c55e; z-index:1; transition:width 1s; }

.sum-footer { background:#fffbeb; border:1px solid #fde68a; border-radius:14px; padding:1rem 1.5rem; display:flex; align-items:center; justify-content:space-between; gap:1rem; }
.sum-footer-text { font-size:.85rem; color:#92400e; font-weight:600; display:flex; align-items:center; gap:.5rem; }
.sum-footer-deadline { text-align:right; font-size:.75rem; color:#94a3b8; font-weight:600; }
.sum-footer-deadline strong { display:block; font-size:.9rem; color:#dc2626; font-weight:900; }

@media(max-width:860px){ .sum-grid{ grid-template-columns:1fr; } .tl-inner{ flex-direction:column; gap:1.5rem; } .tl-inner::before{ display:none; } }
</style>

{{-- ── Page Title ── --}}
<h1 class="sum-page-title">Data &amp; Dokumen Pendaftaran</h1>
<p class="sum-page-sub">Berikut adalah informasi pendaftaran Anda</p>

{{-- ── Status Banner ── --}}
@php
    $bannerClass = $hasRevisi ? 'revisi' : ($allValid2 ? 'ok' : 'pending');
    $bannerIcon  = $hasRevisi ? 'fa-triangle-exclamation' : ($allValid2 ? 'fa-circle-check' : 'fa-clock-rotate-left');
    $bannerTitle = $hasRevisi ? 'Perlu Revisi Dokumen'
                 : ($allValid2 ? 'Berkas Lengkap &amp; Terverifikasi'
                 : 'Menunggu Verifikasi');
    $bannerDesc  = $hasRevisi ? 'Beberapa dokumen Anda perlu diperbaiki sesuai catatan dari admin. Silakan upload ulang dokumen sebelum batas waktu berakhir.'
                 : ($allValid2 ? 'Selamat! Seluruh berkas Anda telah diverifikasi. Pantau terus dashboard Anda.'
                 : 'Berkas Anda sedang dalam antrian verifikasi oleh panitia. Mohon menunggu.');
@endphp
<div class="sum-banner {{ $bannerClass }}">
    <div class="sum-banner-icon"><i class="fa-solid {{ $bannerIcon }}"></i></div>
    <div style="flex:1;">
        <div class="sum-banner-title">{!! $bannerTitle !!}</div>
        <div class="sum-banner-desc">{{ $bannerDesc }}</div>
    </div>
</div>

<div class="sum-card" style="margin-bottom: 2rem;">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); align-items: stretch;">
        
        {{-- SECTION LEFT: Ringkasan Biodata --}}
        <div style="border-right: 1px solid #f1f5f9;">
            <div class="sum-card-hdr">
                <div class="sum-card-hdr-left">
                    <div class="sum-card-icon"><i class="fa-solid fa-address-card"></i></div>
                    <div>
                        <div class="sum-card-title">Ringkasan Data Pendaftaran</div>
                    </div>
                </div>
                <div class="sum-readonly"><i class="fa-solid fa-lock"></i> Read Only</div>
            </div>

            @php
                $bioRows = [
                    ['icon'=>'fa-user',          'label'=>'Nama Lengkap',        'value'=> Auth::user()->name],
                    ['icon'=>'fa-calendar-days', 'label'=>'Tempat, Tanggal Lahir',
                        'value'=> ($pendaftaran->tempat_lahir??'-').', '.($pendaftaran->tanggal_lahir?\Carbon\Carbon::parse($pendaftaran->tanggal_lahir)->translatedFormat('d M Y'):'-')],
                    ['icon'=>'fa-fingerprint',   'label'=>'NISN',               'value'=> $pendaftaran->nisn??'-'],
                    ['icon'=>'fa-school',        'label'=>'Asal Sekolah',       'value'=> $pendaftaran->asal_sekolah??'-'],
                    ['icon'=>'fa-graduation-cap','label'=>'Jurusan Pilihan',    'value'=> $pendaftaran->jurusan->nama??'-'],
                    ['icon'=>'fa-location-dot',  'label'=>'Alamat Rumah',       'value'=> $pendaftaran->alamat??'-'],
                    ['icon'=>'fa-phone',         'label'=>'Nomor HP / WhatsApp','value'=> $pendaftaran->no_hp??'-'],
                    ['icon'=>'fa-chart-line',    'label'=>'Rata-rata Nilai Rapor','value'=> $pendaftaran->nilai_rapor??'-'],
                ];
            @endphp

            @foreach($bioRows as $r)
            <div class="sum-row">
                <div class="sum-row-icon"><i class="fa-solid {{ $r['icon'] }}"></i></div>
                <div class="sum-row-label">{{ $r['label'] }}</div>
                <div class="sum-row-value">{{ $r['value'] }}</div>
            </div>
            @endforeach
        </div>

        {{-- SECTION RIGHT: Dokumen Terupload --}}
        <div style="background: #fafcfd;">
            <div class="sum-card-hdr">
                <div class="sum-card-hdr-left">
                    <div class="sum-card-icon"><i class="fa-solid fa-folder-open"></i></div>
                    <div>
                        <div class="sum-card-title">Dokumen Terupload</div>
                        <div class="sum-card-sub">Berkas yang telah Anda upload</div>
                    </div>
                </div>
            </div>

            @if($hasRevisi)
            <form action="{{ route('siswa.pendaftaran.reuploadMass') }}" method="POST" enctype="multipart/form-data" id="main-reupload-form">
                @csrf
            @endif

            @forelse($berkasAktif as $b)
            @php
                $bIsPdf    = strtolower($b->file_type)==='pdf';
                $bIsValid  = $b->status_verifikasi==='valid';
                $bIsRevisi = $b->status_verifikasi==='tidak_valid';
                
                $accept = '';
                if($b->jenis_berkas === 'skl')     $accept = '.pdf,application/pdf';
                elseif($b->jenis_berkas === 'rapor')   $accept = '.pdf,application/pdf';
                elseif($b->jenis_berkas === 'pasfoto') $accept = '.jpg,.jpeg,.png,image/jpeg,image/png';
            @endphp
            <div class="doc-item-sum" style="background: white;">
                <div class="doc-row">
                    <div class="doc-file-icon" style="background:{{ $bIsPdf?'#fff0f0':'#f0fdf4' }}; color:{{ $bIsPdf?'#ef4444':'#22c55e' }};">
                        <i class="fa-solid {{ $bIsPdf?'fa-file-pdf':'fa-file-image' }}"></i>
                    </div>
                    <div class="doc-info">
                        <div class="doc-fname">{{ $b->nama_file }}</div>
                        <div class="doc-fmeta">{{ strtoupper($b->file_type) }} • {{ $b->jenis_berkas }}
                            @if($bIsRevisi && $hasRevisi)
                                <br><span style="color:#ef4444; font-weight:700; font-size:0.65rem;">(Wajib format {{ $b->jenis_berkas === 'pasfoto' ? 'JPG/JPEG/PNG' : 'PDF' }})</span>
                            @endif
                        </div>
                        
                        <div id="selection-status-{{ $b->jenis_berkas }}" style="display:none; margin-top:.5rem; align-items:center; gap:.6rem; background:#f0fdf4; padding:.4rem .7rem; border-radius:8px; border:1px solid #bbf7d0;">
                            <i class="fa-solid fa-circle-check" style="color:#10b981;"></i>
                            <div style="flex:1; min-width:0;">
                                <div id="selected-name-{{ $b->jenis_berkas }}" style="font-size:.73rem; color:#166534; font-weight:800; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;"></div>
                                <div style="font-size:.65rem; color:#15803d; font-weight:600;">File siap diunggah</div>
                            </div>
                            <button type="button" class="doc-btn lihat" style="padding:.25rem .5rem; font-size:.65rem; background:white; border:1px solid #bbf7d0;" onclick="previewNewRevision('{{ $b->jenis_berkas }}')">
                                <i class="fa-solid fa-eye"></i> Lihat
                            </button>
                        </div>
                    </div>
                    <div class="doc-actions">
                        <span class="doc-badge {{ $bIsValid?'valid':($bIsRevisi?'revisi':'pending') }}">
                            {{ $bIsValid?'Terverifikasi':($bIsRevisi?'Perlu Revisi':'Pending') }}
                        </span>
                        @if($bIsRevisi && $hasRevisi)
                            <input type="file" name="{{ $b->jenis_berkas }}" id="input-select-{{ $b->jenis_berkas }}" 
                                style="display:none;" accept="{{ $accept }}"
                                onchange="showSelectedFile('{{ $b->jenis_berkas }}')">
                            <label for="input-select-{{ $b->jenis_berkas }}" class="doc-btn reupload" style="cursor:pointer;">
                                <i class="fa-solid fa-file-circle-plus"></i> Pilih File
                            </label>
                        @else
                            <button type="button" class="doc-btn lihat" onclick="previewExistingFile('{{ asset('storage/'.$b->file_path) }}', '{{ strtolower($b->file_type) }}')">
                                <i class="fa-solid fa-eye"></i> Lihat
                            </button>
                        @endif
                    </div>
                </div>
                @if($bIsRevisi && $b->catatan_admin)
                <div class="doc-admin-note">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <strong>Catatan Admin:</strong> {{ $b->catatan_admin }}
                </div>
                @endif
            </div>
            @empty
            <div style="padding:2rem; text-align:center; color:#94a3b8; font-size:.85rem;">
                <i class="fa-solid fa-folder-open fa-2x" style="display:block;margin-bottom:.75rem;"></i>
                Belum ada berkas yang diunggah.
            </div>
            @endforelse

            {{-- Submit button for all revisions --}}
            @if($hasRevisi)
                <div style="padding:1.4rem; background:#f8fafc; border-top:1px solid #e2e8f0; text-align:center;">
                    <button type="submit" class="sum-banner-btn" style="width:100%; height:48px; border-radius:12px; background:#6366f1; color:white; border:none; box-shadow:0 10px 15px -3px rgba(99,102,241,0.25); justify-content:center;">
                        <i class="fa-solid fa-paper-plane"></i> Submit Reupload
                    </button>
                </div>
            </form>
            @endif

            {{-- Footer note --}}
            @if($hasRevisi)
            <div style="padding:.9rem 1.4rem; background:#fffbeb; border-top:1px solid #fde68a; display:flex; align-items:center; gap:.6rem;">
                <i class="fa-solid fa-circle-info" style="color:#f59e0b;"></i>
                <span style="font-size:.78rem; color:#92400e; font-weight:700;">Silakan pilih berkas revisi, lalu klik submit.</span>
            </div>
            @elseif($allValid2 && count($berkasAktif)>0)
            <div style="padding:.9rem 1.4rem; background:#f0fdf4; border-top:1px solid #bbf7d0; display:flex; align-items:center; gap:.6rem;">
                <i class="fa-solid fa-circle-check" style="color:#22c55e;"></i>
                <span style="font-size:.78rem; color:#166534; font-weight:700;">Semua dokumen telah terverifikasi.</span>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- ── Timeline ── --}}
<div class="tl-wrap">
    <div class="tl-title">Alur Pendaftaran</div>
    <div class="tl-sub">Tahapan pendaftaran yang telah Anda lalui</div>

    <div style="position:relative;">
        <div style="position:absolute;top:26px;left:calc(100%/12);right:calc(100%/12);height:2px;background:#e2e8f0;z-index:0;"></div>
        <div style="position:absolute;top:26px;left:calc(100%/12);height:2px;background:#22c55e;z-index:1;width:{{ min(($activeIdx/5)*100*(1-1/6), (1-1/6)*100) }}%;"></div>

        <div style="display:flex;justify-content:space-between;position:relative;z-index:2;">
        @foreach($tSteps as $si => $step)
            @php
                $isDone    = $si < $activeIdx;
                $isCurrent = $si == $activeIdx;
                $isWarn    = $hasRevisi && $isCurrent;
                $dotClass  = $isDone ? 'done' : ($isCurrent ? ($isWarn?'warn':'active') : '');
            @endphp
            <div class="tl-item {{ $dotClass }}" style="flex:1;">
                <div class="tl-dot">
                    @if($isDone) <i class="fa-solid fa-check"></i>
                    @elseif($isWarn) <i class="fa-solid fa-triangle-exclamation"></i>
                    @else <i class="fa-solid {{ $step['icon'] }}"></i>
                    @endif
                </div>
                <div class="tl-label">{{ $step['label'] }}</div>
                <div class="tl-lsub">{{ $step['sub'] }}</div>
                @if($step['date'])
                <div class="tl-date {{ $isWarn?'warn':'' }}">
                    {{ \Carbon\Carbon::parse($step['date'])->translatedFormat('d M Y H:i') }} WIB
                </div>
                @endif
            </div>
        @endforeach
        </div>
    </div>
</div>

{{-- ── Footer bar ── --}}
@if($hasRevisi)
<div class="sum-footer">
    <div class="sum-footer-text">
        <i class="fa-solid fa-circle-info" style="color:#f59e0b;font-size:1rem;"></i>
        <span><strong>Perhatian!</strong> Silakan upload ulang dokumen yang ditandai "Perlu Revisi".</span>
    </div>
    <div class="sum-footer-deadline">
        <span>Batas Waktu Revisi</span>
        <strong>30 Mei 2026</strong>
    </div>
</div>
@endif

<script>
const revFilesStore = {};

function showSelectedFile(type) {
    const input = document.getElementById('input-select-' + type);
    const status = document.getElementById('selection-status-' + type);
    const name = document.getElementById('selected-name-' + type);
    if (input.files.length > 0) {
        const file = input.files[0];
        const fileName = file.name.toLowerCase();
        let isValid = true;
        if (type === 'skl' || type === 'rapor') {
            if (!fileName.endsWith('.pdf')) isValid = false;
        } else if (type === 'pasfoto') {
            if (!fileName.endsWith('.jpg') && !fileName.endsWith('.jpeg') && !fileName.endsWith('.png')) isValid = false;
        }

        if (!isValid) {
            input.value = '';
            Swal.fire({
                icon: 'error',
                title: 'Format File Tidak Sesuai',
                text: type === 'pasfoto' ? 'Mohon unggah file dengan format JPG, JPEG, atau PNG.' : 'Mohon unggah file dengan format PDF.',
                confirmButtonColor: '#ef4444'
            });
            return;
        }

        revFilesStore[type] = file;
        name.textContent = file.name;
        status.style.display = 'flex';
    }
}

function previewNewRevision(type) {
    const file = revFilesStore[type];
    if (file) openLightboxFile(file);
}

function previewExistingFile(url, type) {
    const lb   = document.getElementById('previewLightbox');
    const img  = document.getElementById('lbImg');
    const ifr  = document.getElementById('lbIframe');

    img.style.display = 'none';
    ifr.style.display = 'none';

    const isImg = ['jpg','jpeg','png'].includes(type);
    if (isImg) {
        img.src = url;
        img.style.display = 'block';
    } else {
        ifr.src = url;
        ifr.style.display = 'block';
    }
    lb.classList.add('open');
}

document.getElementById('main-reupload-form')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const inputs = this.querySelectorAll('input[type="file"]');
    let hasFile = false;
    inputs.forEach(i => { if(i.files.length > 0) hasFile = true; });

    if (!hasFile) {
        Swal.fire({ icon:'warning', title:'Tidak Ada Berkas', text:'Silakan pilih minimal satu berkas baru untuk diunggah.', confirmButtonColor:'#6366f1' });
        return;
    }

    Swal.fire({
        title: 'Submit Berkas Revisi?',
        text: 'Berkas yang dipilih akan dikirim ke admin untuk diverifikasi ulang.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#6366f1',
        cancelButtonColor:  '#94a3b8',
        confirmButtonText:  'Ya, Submit Sekarang',
        cancelButtonText:   'Batal',
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({ title:'Mengirim...', allowOutsideClick:false, didOpen:()=>{Swal.showLoading()} });
            this.submit();
        }
    });
});
</script>
@endif
</div>

{{-- Lightbox --}}
<div id="previewLightbox"><span id="lbClose" onclick="tutupLightbox()">&times;</span><img id="lbImg"><iframe id="lbIframe"></iframe></div>

<script>
/* ───────────────────────────────
   FILE OBJECT STORE (key => File)
   ─────────────────────────────── */
const fileStore = {};
const certFiles = {};

/* ── HANDLE MANDATORY FILES ── */
function handleFile(input, type, label) {
    const file = input.files[0];
    if (!file) return;

    const fileName = file.name.toLowerCase();
    let isValid = true;
    if (type === 'skl' || type === 'rapor') {
        if (!fileName.endsWith('.pdf')) isValid = false;
    } else if (type === 'pasfoto') {
        if (!fileName.endsWith('.jpg') && !fileName.endsWith('.jpeg') && !fileName.endsWith('.png')) isValid = false;
    }

    if (!isValid) {
        input.value = '';
        Swal.fire({
            icon: 'error',
            title: 'Format File Tidak Sesuai',
            text: type === 'pasfoto' ? 'Mohon unggah file dengan format JPG, JPEG, atau PNG (Tidak bisa All Files).' : 'Mohon unggah file dengan format PDF (Tidak bisa All Files).',
            confirmButtonColor: '#ef4444'
        });
        return;
    }

    fileStore[type] = file;

    // Badge
    const badge = document.getElementById('badge-' + type);
    badge.textContent = 'Sudah Dipilih';
    badge.className = 'badge badge-ok';

    // Name labels
    document.getElementById('name-' + type).textContent = file.name;

    // Thumbnail
    const thumbEl = document.getElementById('thumb-' + type);
    if (thumbEl) {
        thumbEl.innerHTML = '';
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = e => {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'review-thumb';
                thumbEl.appendChild(img);
            };
            reader.readAsDataURL(file);
        } else {
            thumbEl.innerHTML = '<div style="width:52px;height:52px;background:#fff1f0;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#ef4444;"><i class="fa-solid fa-file-pdf fa-xl"></i></div>';
        }
    }

    document.getElementById('review-' + type).style.display = 'flex';
}

/* ── HANDLE CERT FILES ── */
function handleCertFile(input, idx) {
    const file = input.files[0];
    if (!file) return;
    certFiles[idx] = file;

    document.querySelector(`.cert-name-${idx}`).textContent = file.name;
    const reviewEl = document.getElementById(`cert-review-${idx}`);
    if (reviewEl) reviewEl.style.display = 'flex';

    const thumbEl = document.querySelector(`.cert-thumb-${idx}`);
    if (thumbEl) {
        thumbEl.innerHTML = '';
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = e => {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'review-thumb';
                thumbEl.appendChild(img);
            };
            reader.readAsDataURL(file);
        } else {
            thumbEl.innerHTML = '<div style="width:52px;height:52px;background:#fff1f0;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#ef4444;"><i class="fa-solid fa-file-pdf fa-xl"></i></div>';
        }
    }
}

/* ── LIGHTBOX ── */
function bukaPreview(type) {
    const file = fileStore[type];
    if (!file) return;
    openLightboxFile(file);
}
function bukaCertPreview(idx) {
    const file = certFiles[idx];
    if (!file) return;
    openLightboxFile(file);
}
function openLightboxFile(file) {
    const lb   = document.getElementById('previewLightbox');
    const img  = document.getElementById('lbImg');
    const ifr  = document.getElementById('lbIframe');
    const url  = URL.createObjectURL(file);

    img.style.display = 'none';
    ifr.style.display = 'none';

    if (file.type.startsWith('image/')) {
        img.src = url;
        img.style.display = 'block';
    } else {
        ifr.src = url;
        ifr.style.display = 'block';
    }
    lb.classList.add('open');
}
function tutupLightbox() {
    const lb  = document.getElementById('previewLightbox');
    const ifr = document.getElementById('lbIframe');
    const img = document.getElementById('lbImg');
    lb.classList.remove('open');
    ifr.src = '';
    img.src = '';
}
document.getElementById('previewLightbox')?.addEventListener('click', function(e) {
    if (e.target === this) tutupLightbox();
});

/* ── ADD CERTIFICATE ENTRY ── */
let certCount = 1;
function addCert() {
    const container = document.getElementById('cert-container');
    const idx = certCount++;
    const div = document.createElement('div');
    div.className = 'cert-entry';
    div.id = `cert-${idx}`;
    div.innerHTML = `
        <button type="button" onclick="this.closest('.cert-entry').remove()"
            style="position:absolute;top:14px;right:14px;background:#fee2e2;color:#ef4444;border:none;width:28px;height:28px;border-radius:50%;cursor:pointer;font-size:.85rem;display:flex;align-items:center;justify-content:center;">
            <i class="fa-solid fa-times"></i>
        </button>
        <div class="form-grid-2" style="margin-bottom:1rem;">
            <div>
                <label class="form-label">Jenis Prestasi</label>
                <select name="sertifikat_jenis[]" class="form-control">
                    <option value="">-- Pilih Jenis --</option>
                    <option>Akademik</option><option>Olahraga</option>
                    <option>Seni</option><option>Organisasi</option><option>Tahfidz</option>
                </select>
            </div>
            <div>
                <label class="form-label">Tingkat Prestasi</label>
                <select name="sertifikat_tingkat[]" class="form-control">
                    <option value="">-- Pilih Tingkat --</option>
                    <option>Kabupaten/Kota</option><option>Provinsi</option>
                    <option>Nasional</option><option>Internasional</option>
                </select>
            </div>
        </div>
        <div>
            <label class="form-label">File Sertifikat (PDF/JPG/PNG)</label>
            <input type="file" name="sertifikat_file[]" class="form-control"
                accept=".pdf,.jpg,.jpeg,.png"
                onchange="handleCertFile(this,${idx})">
            <div class="review-box cert-review" id="cert-review-${idx}" style="display:none;">
                <div class="cert-thumb-${idx}" style="flex-shrink:0;"></div>
                <div style="flex:1;min-width:0;">
                    <div class="review-name cert-name-${idx}"></div>
                    <div style="font-size:.72rem;color:#10b981;font-weight:700;"><i class="fa-solid fa-check-circle"></i> Sertifikat siap diunggah</div>
                </div>
                <button type="button" class="btn-lihat" onclick="bukaCertPreview(${idx})">
                    <i class="fa-solid fa-eye"></i> Lihat File
                </button>
            </div>
        </div>`;
    container.appendChild(div);
}

/* ────────────────────────────
   REAL-TIME VALIDATION
   ──────────────────────────── */
function showErr(errId, msg) {
    const el = document.getElementById(errId);
    if (!el) return;
    el.querySelector('span').textContent = msg;
    el.classList.add('show');
}
function hideErr(errId) {
    const el = document.getElementById(errId);
    if (el) el.classList.remove('show');
}
function setValid(input)   { input.classList.remove('is-invalid'); input.classList.add('is-valid'); }
function setInvalid(input) { input.classList.remove('is-valid');   input.classList.add('is-invalid'); }

// Hanya huruf dan spasi
function validateLettersOnly(input, errId, msg) {
    const val = input.value.trim();
    if (!val) { setInvalid(input); showErr(errId, 'Kolom ini wajib diisi.'); return false; }
    if (/[^a-zA-Z\s]/.test(val)) { setInvalid(input); showErr(errId, msg); return false; }
    setValid(input); hideErr(errId); return true;
}

// Wajib mengandung huruf
function validateContainsLetter(input, errId, msg) {
    const val = input.value.trim();
    if (!val) { setInvalid(input); showErr(errId, 'Kolom ini wajib diisi.'); return false; }
    if (!/[a-zA-Z]/.test(val)) { setInvalid(input); showErr(errId, msg); return false; }
    setValid(input); hideErr(errId); return true;
}

function validateNisn(input) {
    const val = input.value;
    if (val.length !== 10) { setInvalid(input); showErr('f_nisn_err','NISN harus tepat 10 digit angka.'); return false; }
    setValid(input); hideErr('f_nisn_err'); return true;
}

function validateNoHp(input) {
    const val = input.value;
    if (val.length < 10 || val.length > 13) { setInvalid(input); showErr('f_nohp_err','Nomor HP harus 10–13 digit angka.'); return false; }
    setValid(input); hideErr('f_nohp_err'); return true;
}

function validateRapor(input) {
    const val = parseFloat(input.value);
    if (isNaN(val) || val < 0 || val > 100) { setInvalid(input); showErr('f_rapor_val_err','Nilai rapor 0–100, boleh desimal.'); return false; }
    setValid(input); hideErr('f_rapor_val_err'); return true;
}

/* ── SUBMIT GUARD ── */
document.getElementById('mainForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    let valid = true;

    valid &= validateLettersOnly(document.getElementById('f_nama'),    'f_nama_err', 'Nama hanya boleh huruf dan spasi.');
    valid &= validateLettersOnly(document.getElementById('f_tl'),      'f_tl_err',   'Tempat lahir hanya boleh huruf dan spasi.');
    valid &= validateContainsLetter(document.getElementById('f_alamat'),'f_alamat_err','Alamat wajib mengandung unsur huruf.');
    valid &= validateNisn(document.getElementById('f_nisn'));
    valid &= validateNoHp(document.getElementById('f_nohp'));
    valid &= validateContainsLetter(document.getElementById('f_asal'), 'f_asal_err', 'Asal sekolah wajib mengandung unsur huruf.');
    valid &= validateRapor(document.getElementById('f_rapor_val'));

    // Jurusan
    const jur = document.querySelector('[name="jurusan_id"]');
    if (!jur.value) { jur.classList.add('is-invalid'); valid = false; }
    else jur.classList.remove('is-invalid');

    if (!valid) {
        Swal.fire({ icon:'warning', title:'Periksa Data Anda', text:'Ada kolom yang belum diisi dengan benar. Cek kembali isian di atas.', confirmButtonColor:'#6366f1' });
        // scroll to first invalid
        const firstInvalidEl = document.querySelector('.is-invalid, .field-error.show');
        if (firstInvalidEl) firstInvalidEl.scrollIntoView({ behavior:'smooth', block:'center' });
        return;
    }

    Swal.fire({
        title: 'Kirim Pendaftaran?',
        html: 'Pastikan seluruh data dan berkas sudah benar.<br><small style="color:#64748b;">Data yang dikirim tidak dapat diubah tanpa persetujuan admin.</small>',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#6366f1',
        cancelButtonColor:  '#94a3b8',
        confirmButtonText:  'Ya, Kirim!',
        cancelButtonText:   'Batal, Cek Dulu',
        reverseButtons: true
    }).then(res => { if (res.isConfirmed) this.submit(); });
});
</script>
@endsection
