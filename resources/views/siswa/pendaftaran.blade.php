@extends('layouts.siswa')
@section('title', 'Form Pendaftaran')

@section('content')
@php
    $sudahUploadSemua = !empty($berkasAktif['skl']) && !empty($berkasAktif['rapor']) && !empty($berkasAktif['pasfoto']);
@endphp
<div class="glass-card" style="max-width: 800px; margin: 0 auto;">
    <h2 style="color: var(--primary); margin-bottom: 2rem;">
        {{ $sudahUploadSemua ? '📋 Data & Dokumen Pendaftaran' : 'Lengkapi Data Pendaftaran' }}
    </h2>

    @if($pendaftaran)
        @php
            $statusColor = match($pendaftaran->status) {
                'lolos_admin'         => ['bg'=>'#d1fae5','color'=>'#065f46','icon'=>'✅'],
                'ditolak_admin'       => ['bg'=>'#fee2e2','color'=>'#991b1b','icon'=>'❌'],
                'menunggu_verifikasi' => ['bg'=>'#fef3c7','color'=>'#92400e','icon'=>'⏳'],
                default               => ['bg'=>'#e0f2fe','color'=>'#0284c7','icon'=>'ℹ️'],
            };
        @endphp
        <div style="background:{{ $statusColor['bg'] }};color:{{ $statusColor['color'] }};padding:1rem 1.25rem;border-radius:10px;margin-bottom:1.5rem;display:flex;align-items:center;gap:.75rem;">
            <span style="font-size:1.25rem;">{{ $statusColor['icon'] }}</span>
            <div>
                <strong>Status Pendaftaran:</strong>
                <span style="text-transform:uppercase;font-weight:700;"> {{ str_replace('_',' ',$pendaftaran->status) }}</span>
                @if($sudahUploadSemua)<br><span style="font-size:.82rem;opacity:.85;">Semua berkas sudah diunggah. Data tidak dapat diubah lagi.</span>@endif
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div style="background: #fee2e2; color: #ef4444; padding: 1rem; border-radius: var(--radius-sm); margin-bottom: 1.5rem;">
            <ul style="padding-left: 1.5rem; margin: 0;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

@if($sudahUploadSemua)
    {{-- ═══ MODE READ-ONLY: semua berkas sudah diunggah ═══ --}}
    <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;padding:1.25rem;margin-bottom:1.5rem;">
        <div style="font-size:.72rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;margin-bottom:1rem;">📄 Ringkasan Data Pendaftaran</div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem 1.5rem;">
            <div>
                <div style="font-size:.78rem;color:#94a3b8;margin-bottom:.15rem;">NISN</div>
                <div style="font-weight:700;color:#0f172a;">{{ $pendaftaran->nisn ?? '-' }}</div>
            </div>
            <div>
                <div style="font-size:.78rem;color:#94a3b8;margin-bottom:.15rem;">Asal Sekolah</div>
                <div style="font-weight:700;color:#0f172a;">{{ $pendaftaran->asal_sekolah ?? '-' }}</div>
            </div>
            <div>
                <div style="font-size:.78rem;color:#94a3b8;margin-bottom:.15rem;">Rata-rata Nilai Rapor</div>
                <div style="font-weight:700;color:#0f172a;">{{ $pendaftaran->nilai_rapor ?? '-' }}</div>
            </div>
            <div>
                <div style="font-size:.78rem;color:#94a3b8;margin-bottom:.15rem;">Nomor HP / WhatsApp</div>
                <div style="font-weight:700;color:#0f172a;">{{ $pendaftaran->no_hp ?? '-' }}</div>
            </div>
            <div>
                <div style="font-size:.78rem;color:#94a3b8;margin-bottom:.15rem;">Tempat, Tanggal Lahir</div>
                <div style="font-weight:700;color:#0f172a;">
                    {{ $pendaftaran->tempat_lahir ?? '-' }},
                    {{ $pendaftaran->tanggal_lahir ? \Carbon\Carbon::parse($pendaftaran->tanggal_lahir)->translatedFormat('d F Y') : '-' }}
                </div>
            </div>
            <div>
                <div style="font-size:.78rem;color:#94a3b8;margin-bottom:.15rem;">Jurusan Pilihan</div>
                <div style="font-weight:700;color:#0f172a;">{{ $pendaftaran->jurusan->nama ?? '-' }}</div>
            </div>
            <div style="grid-column:1/-1;">
                <div style="font-size:.78rem;color:#94a3b8;margin-bottom:.15rem;">Alamat Rumah</div>
                <div style="font-weight:700;color:#0f172a;">{{ $pendaftaran->alamat ?? '-' }}</div>
            </div>
        </div>
    </div>
@else
    {{-- ═══ MODE FORM: belum semua berkas diunggah ═══ --}}
    <form action="{{ url('siswa/pendaftaran') }}" method="POST" enctype="multipart/form-data" id="formPendaftaran">
        @csrf

        {{-- ── Baris 1: NISN & Asal Sekolah ── --}}
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;">
            <div class="form-group">
                <label class="form-label" for="nisn">Nomor Induk Siswa Nasional (NISN) <span style="color:#ef4444;">*</span></label>
                <input type="text" name="nisn" id="nisn" class="form-control"
                    value="{{ $pendaftaran->nisn ?? old('nisn') }}"
                    required minlength="10" maxlength="10" pattern="[0-9]{10}"
                    placeholder="Contoh: 0123456789"
                    oninput="this.value=this.value.replace(/\D/g,'')"
                    title="NISN harus tepat 10 digit angka">
                <small style="color:#94a3b8;">Wajib tepat <strong>10 digit angka</strong>.</small>
            </div>
            <div class="form-group">
                <label class="form-label" for="asal_sekolah">Asal Sekolah <span style="color:#ef4444;">*</span></label>
                <input type="text" name="asal_sekolah" id="asal_sekolah" class="form-control"
                    value="{{ $pendaftaran->asal_sekolah ?? old('asal_sekolah') }}"
                    required placeholder="Contoh: SMA Negeri 1 Bandung">
                <small style="color:#94a3b8;">Awali dengan jenis sekolah: <strong>SMA / SMK / MAN / SMP</strong>, dst.</small>
            </div>
        </div>

        {{-- ── Baris 2: Nilai Rapor & Nomor HP ── --}}
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;">
            <div class="form-group">
                <label class="form-label" for="nilai_rapor">Rata-rata Nilai Rapor <span style="color:#ef4444;">*</span></label>
                <input type="text" inputmode="decimal" name="nilai_rapor" id="nilai_rapor" class="form-control"
                    value="{{ $pendaftaran->nilai_rapor ?? old('nilai_rapor') }}"
                    required placeholder="Contoh: 90.00"
                    pattern="^(100(\.0{1,2})?|[0-9]{1,2}(\.[0-9]{1,2})?)$"
                    title="Angka 0–100, gunakan titik (.) untuk desimal">
                <small style="color:#94a3b8;">Maksimal <strong>100</strong>. Gunakan titik (.) untuk desimal, contoh: <strong>90.00</strong>.</small>
            </div>
            <div class="form-group">
                <label class="form-label" for="no_hp">Nomor HP / WhatsApp <span style="color:#ef4444;">*</span></label>
                <input type="text" name="no_hp" id="no_hp" class="form-control"
                    value="{{ $pendaftaran->no_hp ?? old('no_hp') }}"
                    required minlength="10" maxlength="15" pattern="[0-9]{10,15}"
                    placeholder="Contoh: 081234567890"
                    oninput="this.value=this.value.replace(/\D/g,'')"
                    title="Nomor HP harus berupa angka tanpa spasi atau simbol">
                <small style="color:#94a3b8;">Hanya <strong>angka</strong>, tanpa spasi, +, atau tanda lain.</small>
            </div>
        </div>

        {{-- ── Baris 3: Jurusan ── --}}
        <div class="form-group">
            <label class="form-label" for="jurusan_id">Pilih Jurusan <span style="color:#ef4444;">*</span></label>
            <select name="jurusan_id" id="jurusan_id" class="form-control" required>
                <option value="">-- Pilih Jurusan --</option>
                @foreach($jurusans as $jurusan)
                    @php $sisa = $jurusan->sisa_kuota; @endphp
                    <option value="{{ $jurusan->id }}"
                        {{ (isset($pendaftaran) && $pendaftaran->jurusan_id == $jurusan->id) ? 'selected' : '' }}
                        {{ $sisa <= 0 ? 'disabled' : '' }}
                        style="{{ $sisa <= 0 ? 'color:#ef4444;font-weight:bold;' : '' }}">
                        {{ $jurusan->nama }} &ndash; {{ $sisa <= 0 ? 'Penuh' : 'Tersedia' }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- ── Data Tambahan ── --}}
        <div style="margin-top:.5rem;padding:1.25rem;background:#f0f9ff;border:1px solid #bae6fd;border-radius:12px;">
            <h4 style="margin:0 0 1rem;font-size:.9rem;font-weight:700;color:#0369a1;"><i class="fa-solid fa-circle-info" style="margin-right:.4rem;"></i>Data Tambahan</h4>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;">
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label" for="tempat_lahir">Tempat Lahir <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="tempat_lahir" id="tempat_lahir" class="form-control"
                        value="{{ $pendaftaran->tempat_lahir ?? old('tempat_lahir') }}"
                        required placeholder="Contoh: Bandung">
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label" for="tanggal_lahir">Tanggal Lahir <span style="color:#ef4444;">*</span></label>
                    <input type="date" name="tanggal_lahir" id="tanggal_lahir" class="form-control"
                        value="{{ $pendaftaran->tanggal_lahir ?? old('tanggal_lahir') }}"
                        required max="{{ date('Y-m-d', strtotime('-5 years')) }}">
                </div>
            </div>
            <div class="form-group" style="margin-top:1.25rem;margin-bottom:0;">
                <label class="form-label" for="alamat">Alamat Rumah <span style="color:#ef4444;">*</span></label>
                <textarea name="alamat" id="alamat" class="form-control" rows="3"
                    required placeholder="Contoh: Jl. Merdeka No. 5, Kel. Sukajadi, Kec. Bandung Utara"
                    style="resize:vertical;">{{ $pendaftaran->alamat ?? old('alamat') }}</textarea>
            </div>
        </div>

        @if(!empty($berkasAktif))
        <div style="margin-top:1.5rem;border:1px solid #e2e8f0;border-radius:12px;background:white;overflow:hidden;">
            <div style="background:#f8fafc;padding:1rem 1.25rem;border-bottom:1px solid #e2e8f0;">
                <h3 style="margin:0;font-size:1rem;font-weight:700;color:#334155;">📋 Daftar Dokumen Aktif</h3>
                <p style="margin:.2rem 0 0;font-size:.8rem;color:#64748b;">Dokumen terbaru yang Anda unggah untuk verifikasi.</p>
            </div>
            <div style="display: flex; flex-direction: column;">
                @foreach($berkasAktif as $key => $berkas)
                <div style="padding: 1rem; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
                    <div>
                        <strong style="text-transform: capitalize; color: var(--primary);">{{ $berkas->jenis_berkas }} {{ $berkas->jenis_prestasi ? '('.$berkas->jenis_prestasi.')' : '' }}</strong>
                        <div style="font-size: 0.8rem; color: #64748b; margin-top: 0.25rem;">{{ $berkas->nama_file }}</div>
                        @if($berkas->status_verifikasi == 'tidak_valid' && $berkas->catatan_admin)
                        <div style="margin-top: 0.5rem; background: #fef2f2; color: #991b1b; padding: 0.5rem; border-radius: 4px; font-size: 0.85rem; border: 1px solid #fca5a5;">
                            <strong>Catatan Penolakan:</strong> {{ $berkas->catatan_admin }}
                        </div>
                        @endif
                    </div>
                    <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 0.5rem;">
                        <div style="display: flex; gap: 0.5rem; align-items: center;">
                            @if($berkas->status_verifikasi == 'valid')
                                <span style="background: #d1fae5; color: #059669; padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.75rem; font-weight: bold;">✅ DITERIMA</span>
                            @elseif($berkas->status_verifikasi == 'tidak_valid')
                                <span style="background: #fee2e2; color: #dc2626; padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.75rem; font-weight: bold;">❌ DITOLAK</span>
                            @else
                                <span style="background: #fef3c7; color: #d97706; padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.75rem; font-weight: bold;">⏳ PENDING</span>
                            @endif
                            <button type="button" class="btn-outline" style="padding: 0.2rem 0.5rem; font-size: 0.75rem;" onclick="openLightbox('{{ asset('storage/'.$berkas->file_path) }}', '{{ strtolower($berkas->file_type) == 'pdf' ? 'pdf' : 'img' }}')">👁️ Lihat File</button>
                        </div>
                        @if($berkas->status_verifikasi == 'tidak_valid')
                        <button type="button" class="btn-primary" style="background: #ef4444; border: none; padding: 0.3rem 0.75rem; font-size: 0.8rem; border-radius: 4px;" onclick="document.getElementById('reupload-form-{{ $berkas->id }}').style.display='block'; this.style.display='none';">🔄 Upload Ulang Ini Saja</button>
                        
                        <form action="{{ route('siswa.pendaftaran.reupload') }}" method="POST" enctype="multipart/form-data" id="reupload-form-{{ $berkas->id }}" style="display: none; background: #f8fafc; padding: 0.75rem; border-radius: 6px; border: 1px dashed #cbd5e1; margin-top: 0.5rem;">
                            @csrf
                            <input type="hidden" name="berkas_id_lama" value="{{ $berkas->id }}">
                            <input type="hidden" name="jenis_berkas" value="{{ $berkas->jenis_berkas }}">
                            <label style="font-size: 0.8rem; font-weight: bold; margin-bottom: 0.25rem; display: block;">Pilih File Baru (PDF/JPG/PNG max 2MB):</label>
                            <input type="file" name="file_reupload" class="form-control" style="padding: 0.4rem; font-size: 0.8rem; margin-bottom: 0.5rem;" required accept=".pdf,.jpg,.jpeg,.png">
                            <div style="display: flex; gap: 0.5rem;">
                                <button type="submit" class="btn-primary" style="padding: 0.3rem 0.75rem; font-size: 0.8rem; flex: 1;">Kirim Revisi</button>
                                <button type="button" class="btn-outline" style="padding: 0.3rem 0.75rem; font-size: 0.8rem;" onclick="document.getElementById('reupload-form-{{ $berkas->id }}').style.display='none'; this.closest('div.display\\:flex').parentElement.previousElementSibling.style.display='block';">Batal</button>
                            </div>
                        </form>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        @if(!empty($riwayatBerkas))
        <details style="margin-top: 1rem; border: 1px solid #e2e8f0; border-radius: var(--radius-md); background: #f8fafc;">
            <summary style="padding: 1rem; cursor: pointer; font-weight: bold; color: #64748b;">📂 Tampilkan Riwayat Dokumen Terdahulu (Ditolak / Diganti)</summary>
            <div style="padding: 0 1rem 1rem 1rem;">
                <ul style="margin: 0; padding-left: 1rem; font-size: 0.85rem; color: #475569;">
                    @foreach($riwayatBerkas as $r)
                        <li style="margin-bottom: 0.5rem;">
                            <strong>{{ ucfirst($r->jenis_berkas) }}</strong> - {{ $r->nama_file }} 
                            <span style="color: #94a3b8; font-size: 0.75rem;">(Diunggah: {{ $r->created_at->format('d M Y H:i') }})</span>
                            <button type="button" style="background:none; border:none; color:var(--primary); cursor:pointer; text-decoration:underline; font-size:0.75rem;" onclick="openLightbox('{{ asset('storage/'.$r->file_path) }}', '{{ strtolower($r->file_type) == 'pdf' ? 'pdf' : 'img' }}')">Lihat</button>
                        </li>
                    @endforeach
                </ul>
            </div>
        </details>
        @endif

        @if(!$sudahUploadSemua)
        <h3 style="margin-top:2rem;margin-bottom:1rem;color:var(--primary);">Upload Berkas Pendukung</h3>

        {{-- Ketentuan Upload --}}
        <div style="background:#fff8f1;padding:1.1rem 1.25rem;border-radius:12px;border-left:4px solid #f59e0b;margin-bottom:1rem;font-size:.875rem;">
            <p style="font-weight:700;color:#92400e;margin:0 0 .5rem;">⚠️ Ketentuan Upload Berkas</p>
            <ul style="margin:0;padding-left:1.25rem;color:#78350f;line-height:1.75;">
                <li>Maksimal ukuran file <strong>2 MB per berkas</strong>.</li>
                <li>Pastikan seluruh dokumen terlihat <strong>jelas, tidak blur, dan dapat dibaca</strong>.</li>
                <li>Dokumen hanya digunakan untuk <strong>proses verifikasi pendaftaran</strong> dan tidak disebarluaskan.</li>
            </ul>
        </div>
        <div style="background:#f0fdf4;padding:1rem 1.25rem;border-radius:12px;border-left:4px solid #22c55e;margin-bottom:1.5rem;font-size:.875rem;">
            <i class="fa-solid fa-shield-halved"></i> <strong>Privasi:</strong> Dokumen hanya digunakan untuk keperluan verifikasi dan tidak disebarluaskan.
        </div>
        @endif

        @php
            $berkasSkl    = $berkasAktif['skl']    ?? null;
            $berkasRapor  = $berkasAktif['rapor']  ?? null;
            $berkasPasfoto = $berkasAktif['pasfoto'] ?? null;

            $statusBadge = fn($v) => match($v) {
                'valid'       => ['bg'=>'#d1fae5','color'=>'#059669','label'=>'✅ Diterima'],
                'tidak_valid' => ['bg'=>'#fee2e2','color'=>'#dc2626','label'=>'❌ Ditolak'],
                default       => ['bg'=>'#fef3c7','color'=>'#d97706','label'=>'⏳ Pending'],
            };
        @endphp

        {{-- Helper macro: satu berkas wajib --}}
        @foreach([
            ['key'=>'skl',     'no'=>1, 'label'=>'Scan SKL / Ijazah',    'name'=>'skl',     'accept'=>'.pdf,.jpg,.jpeg,.png', 'hint'=>'Format: PDF, JPG, PNG.'],
            ['key'=>'rapor',   'no'=>2, 'label'=>'Scan Rapor Terakhir',  'name'=>'rapor',   'accept'=>'.pdf',                 'hint'=>'Format: PDF.'],
            ['key'=>'pasfoto', 'no'=>3, 'label'=>'Pas Foto Terkini',     'name'=>'pasfoto', 'accept'=>'.jpg,.jpeg,.png',      'hint'=>'Format: JPG, PNG.'],
        ] as $item)
        @php
            $existing = $berkasAktif[$item['key']] ?? null;
            $badge    = $existing ? $statusBadge($existing->status_verifikasi) : null;
            $fileId   = 'file_' . $item['key'];
        @endphp
        <div class="form-group" style="border:1px solid #e2e8f0;border-radius:12px;padding:1rem 1.25rem;background:#f8fafc;">
            <label class="form-label" style="font-weight:700;color:#0f172a;margin-bottom:.75rem;display:block;">
                {{ $item['no'] }}. {{ $item['label'] }}
                @if($existing)
                    <span style="background:{{ $badge['bg'] }};color:{{ $badge['color'] }};font-size:.72rem;padding:.2rem .55rem;border-radius:999px;font-weight:700;margin-left:.5rem;">{{ $badge['label'] }}</span>
                @else
                    <span style="background:#fee2e2;color:#dc2626;font-size:.72rem;padding:.2rem .55rem;border-radius:999px;font-weight:700;margin-left:.5rem;">Belum Upload</span>
                @endif
            </label>

            @if($existing)
                {{-- Sudah ada berkas: tampilkan preview, sembunyikan input --}}
                <div style="display:flex;align-items:center;gap:1rem;flex-wrap:wrap;">
                    <div style="display:flex;align-items:center;gap:.6rem;background:white;border:1px solid #e2e8f0;border-radius:8px;padding:.6rem 1rem;flex:1;min-width:0;">
                        <span style="font-size:1.25rem;">
                            {{ strtolower($existing->file_type ?? '') === 'pdf' ? '📄' : '🖼️' }}
                        </span>
                        <span style="font-size:.82rem;color:#334155;font-weight:600;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $existing->nama_file }}</span>
                    </div>
                    <button type="button" class="btn-outline" style="padding:.45rem 1rem;font-size:.82rem;white-space:nowrap;"
                        onclick="openLightbox('{{ asset('storage/'.$existing->file_path) }}', '{{ strtolower($existing->file_type ?? '') === 'pdf' ? 'pdf' : 'img' }}')">
                        👁️ Preview
                    </button>
                    @if($existing->status_verifikasi !== 'valid')
                    <button type="button" style="background:#fff7ed;color:#ea580c;border:1px solid #fed7aa;border-radius:8px;padding:.45rem 1rem;font-size:.82rem;font-weight:600;cursor:pointer;white-space:nowrap;"
                        onclick="toggleReplaceInput('{{ $fileId }}')">
                        🔄 Ganti File
                    </button>
                    @endif
                </div>
                @if($existing->status_verifikasi !== 'valid')
                <div id="{{ $fileId }}_replace" style="display:none;margin-top:.75rem;padding:.875rem;background:#fff7ed;border:1px dashed #fb923c;border-radius:8px;">
                    <p style="font-size:.8rem;color:#9a3412;margin:0 0 .5rem;font-weight:600;">⚠️ File lama akan diganti. Pilih file baru:</p>
                    <input type="file" name="{{ $item['name'] }}" id="{{ $fileId }}" class="form-control" accept="{{ $item['accept'] }}">
                    <small style="color:var(--gray-text);">{{ $item['hint'] }}</small>
                </div>
                @endif
            @else
                {{-- Belum ada berkas: tampilkan input upload --}}
                <input type="file" name="{{ $item['name'] }}" id="{{ $fileId }}" class="form-control"
                    accept="{{ $item['accept'] }}"
                    {{ !isset($pendaftaran) ? 'required' : '' }}
                    onchange="showPreview(this, '{{ $fileId }}_preview')">
                <small style="color:var(--gray-text);">{{ $item['hint'] }}</small>
                {{-- Preview sebelum upload --}}
                <div id="{{ $fileId }}_preview" style="display:none;margin-top:.75rem;"></div>
            @endif
        </div>
        @endforeach


        @if(!$sudahUploadSemua)
        <div class="form-group" style="margin-top: 2rem;">
            <label class="form-label">4. Sertifikat Pendukung (Opsional)</label>
            
            <div style="background: #eff6ff; padding: 1.25rem; border-radius: var(--radius-md); border-left: 4px solid #3b82f6; margin-bottom: 1rem;">
                <h4 style="margin: 0 0 0.5rem; color: #1e40af; font-size: 0.95rem;">Ketentuan Upload Prestasi:</h4>
                <ul style="margin: 0; padding-left: 1.25rem; color: #1e3a8a; font-size: 0.85rem; line-height: 1.6;">
                    <li>Jenis Sertifikat yang diakui: <strong>Akademik, Olahraga, Seni, Organisasi, dan Tahfidz</strong>.</li>
                    <li><strong style="color: #dc2626;">Sertifikat Webinar atau partisipasi peserta umum TIDAK DITERIMA.</strong></li>
                    <li>Siswa dapat mengupload lebih dari 1 sertifikat.</li>
                    <li>Pilih jenis prestasi dan tingkat pencapaian dengan benar sebelum upload file.</li>
                    <li>Sertifikat yang diupload akan masuk tahap verifikasi oleh admin.</li>
                </ul>
            </div>

            <div id="prestasi-container">
                <div class="prestasi-item glass-card" style="padding: 1rem; margin-bottom: 1rem; border: 1px solid #e2e8f0; background: #f8fafc;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" style="font-size: 0.85rem;">Jenis Prestasi</label>
                            <select name="sertifikat_jenis[]" class="form-control" style="padding: 0.5rem; font-size: 0.9rem;">
                                <option value="">-- Pilih Jenis --</option>
                                <option value="Akademik">Akademik (Olimpiade, Lomba Cerdas Cermat, dll)</option>
                                <option value="Olahraga">Olahraga (O2SN, Popda, dll)</option>
                                <option value="Seni">Seni (FLS2N, Lomba Tari, Musik, dll)</option>
                                <option value="Organisasi">Organisasi (OSIS, Pramuka, PMR, dll)</option>
                                <option value="Tahfidz">Tahfidz / Keagamaan</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label" style="font-size: 0.85rem;">Tingkat Prestasi</label>
                            <select name="sertifikat_tingkat[]" class="form-control" style="padding: 0.5rem; font-size: 0.9rem;">
                                <option value="">-- Pilih Tingkat --</option>
                                <option value="Sekolah">Sekolah / Antar Kelas</option>
                                <option value="Kecamatan">Kecamatan</option>
                                <option value="Kabupaten/Kota">Kabupaten/Kota</option>
                                <option value="Provinsi">Provinsi</option>
                                <option value="Nasional">Nasional / Internasional</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="form-label" style="font-size: 0.85rem;">File Sertifikat (PDF/JPG/PNG)</label>
                        <input type="file" name="sertifikat_file[]" class="form-control" accept=".pdf,.jpg,.jpeg,.png" style="padding: 0.5rem; font-size: 0.9rem;">
                    </div>
                </div>
            </div>

            <button type="button" id="btn_add_prestasi" class="btn-outline" style="font-size: 0.85rem; padding: 0.5rem 1rem; border: 1px dashed var(--primary); color: var(--primary);">
                + Tambah Prestasi Lainnya
            </button>
        </div>

        <button type="button" id="btn_submit" class="btn-primary"
            style="width:100%;margin-top:1rem;font-size:1.125rem;padding:1rem;"
            onclick="konfirmasiKirim()">
            📨 Kirim Pendaftaran
        </button>

        @endif {{-- end !$sudahUploadSemua --}}
    </form>
@endif {{-- end else (read-only vs form) --}}
</div>

<script>
    // Preview file sebelum upload (untuk berkas yang belum ada)
    function showPreview(input, previewId) {
        const preview = document.getElementById(previewId);
        if (!input.files || !input.files[0]) { preview.style.display = 'none'; return; }
        const file = input.files[0];
        const isPdf = file.type === 'application/pdf';
        const url = URL.createObjectURL(file);
        
        preview.style.display = 'block';
        if (isPdf) {
            preview.innerHTML = `<div style="background:#f0f9ff;border:1px solid #bae6fd;border-radius:8px;padding:.75rem 1rem;display:flex;align-items:center;gap:.75rem;">
                <span style="font-size:1.5rem;">📄</span>
                <div>
                    <div style="font-weight:700;font-size:.85rem;color:#0369a1;word-break:break-all;">${file.name}</div>
                    <div style="font-size:.75rem;color:#64748b;">${(file.size/1024).toFixed(1)} KB · PDF</div>
                </div>
                <div style="margin-left:auto; display:flex; flex-direction:column; gap:.3rem; align-items:flex-end;">
                    <span style="background:#dcfce7;color:#166534;font-size:.7rem;padding:.15rem .5rem;border-radius:999px;font-weight:700;">✓ Siap Upload</span>
                    <button type="button" class="btn-outline" style="padding:.2rem .5rem;font-size:.75rem;white-space:nowrap;" onclick="openLightbox('${url}', 'pdf')">👁️ Lihat File</button>
                </div>
            </div>`;
        } else {
            preview.innerHTML = `<div style="display:flex; align-items:center; gap:1rem; background:#f0f9ff; border:1px solid #bae6fd; border-radius:8px; padding:.5rem;">
                <div style="position:relative; cursor:pointer;" onclick="openLightbox('${url}', 'img')">
                    <img src="${url}" style="max-height:80px;border-radius:6px;display:block;box-shadow:0 2px 4px rgba(0,0,0,0.1);transition:transform .2s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                    <div style="position:absolute;inset:0;background:rgba(0,0,0,0.3);border-radius:6px;display:flex;align-items:center;justify-content:center;opacity:0;transition:opacity .2s;" onmouseover="this.style.opacity=1" onmouseout="this.style.opacity=0">
                        <i class="fa-solid fa-eye" style="color:white;font-size:1.2rem;"></i>
                    </div>
                </div>
                <div>
                    <div style="font-weight:700;font-size:.85rem;color:#0369a1;word-break:break-all;">${file.name}</div>
                    <div style="font-size:.75rem;color:#64748b;margin-bottom:.3rem;">${(file.size/1024).toFixed(1)} KB · Gambar</div>
                    <span style="background:#dcfce7;color:#166534;font-size:.7rem;padding:.15rem .5rem;border-radius:999px;font-weight:700;">✓ Siap Upload</span>
                </div>
            </div>`;
        }
    }

    // Toggle form ganti file (untuk berkas yang sudah ada tapi belum valid)
    function toggleReplaceInput(fileId) {
        const box = document.getElementById(fileId + '_replace');
        if (!box) return;
        box.style.display = box.style.display === 'none' ? 'block' : 'none';
    }

    // Konfirmasi sebelum kirim
    function konfirmasiKirim() {
        if (confirm('📨 Yakin ingin mengirim pendaftaran?\n\nPastikan semua data dan berkas sudah benar sebelum dikirim.')) {
            document.getElementById('formPendaftaran').submit();
        }
    }

    // Script Tambah Prestasi
    document.addEventListener('DOMContentLoaded', function() {
        const btnAddPrestasi = document.getElementById('btn_add_prestasi');
        const prestasiContainer = document.getElementById('prestasi-container');
        
        if(btnAddPrestasi && prestasiContainer) {
            btnAddPrestasi.addEventListener('click', function() {
                const itemHtml = `
                <div class="prestasi-item glass-card" style="padding: 1rem; margin-bottom: 1rem; border: 1px solid #e2e8f0; background: #f8fafc; position: relative;">
                    <button type="button" class="btn-remove-prestasi" style="position: absolute; top: 0.5rem; right: 0.5rem; background: #fee2e2; color: #dc2626; border: none; border-radius: 4px; padding: 0.2rem 0.5rem; cursor: pointer; font-size: 0.75rem; font-weight: bold;">X Hapus</button>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" style="font-size: 0.85rem;">Jenis Prestasi</label>
                            <select name="sertifikat_jenis[]" class="form-control" style="padding: 0.5rem; font-size: 0.9rem;">
                                <option value="">-- Pilih Jenis --</option>
                                <option value="Akademik">Akademik</option>
                                <option value="Olahraga">Olahraga</option>
                                <option value="Seni">Seni</option>
                                <option value="Organisasi">Organisasi</option>
                                <option value="Tahfidz">Tahfidz</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label" style="font-size: 0.85rem;">Tingkat Prestasi</label>
                            <select name="sertifikat_tingkat[]" class="form-control" style="padding: 0.5rem; font-size: 0.9rem;">
                                <option value="">-- Pilih Tingkat --</option>
                                <option value="Sekolah">Sekolah / Antar Kelas</option>
                                <option value="Kecamatan">Kecamatan</option>
                                <option value="Kabupaten/Kota">Kabupaten/Kota</option>
                                <option value="Provinsi">Provinsi</option>
                                <option value="Nasional">Nasional / Internasional</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="form-label" style="font-size: 0.85rem;">File Sertifikat (PDF/JPG/PNG)</label>
                        <input type="file" name="sertifikat_file[]" class="form-control" accept=".pdf,.jpg,.jpeg,.png" style="padding: 0.5rem; font-size: 0.9rem;">
                    </div>
                </div>`;
                prestasiContainer.insertAdjacentHTML('beforeend', itemHtml);
            });

            prestasiContainer.addEventListener('click', function(e) {
                if(e.target.classList.contains('btn-remove-prestasi')) {
                    e.target.closest('.prestasi-item').remove();
                }
            });
        }
    });
</script>

<!-- Lightbox Modal -->
<div id="lightbox" style="display:none; position:fixed; z-index:9999; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.85); align-items:center; justify-content:center; backdrop-filter: blur(4px);">
    <span onclick="closeLightbox()" style="position:absolute; top:20px; right:30px; color:white; font-size:40px; cursor:pointer; font-weight: bold; text-shadow: 0 2px 4px rgba(0,0,0,0.5);">&times;</span>
    <img id="lightbox-img" style="max-width:90%; max-height:90%; border-radius:8px; box-shadow: 0 10px 25px rgba(0,0,0,0.5); display:none;">
    <iframe id="lightbox-pdf" style="width:80%; height:90%; border-radius:8px; border:none; box-shadow: 0 10px 25px rgba(0,0,0,0.5); display:none; background:white;"></iframe>
</div>

<script>
    // Lightbox Logic for Siswa
    function openLightbox(src, type = 'img') {
        if(type === 'pdf') {
            document.getElementById('lightbox-img').style.display = 'none';
            document.getElementById('lightbox-pdf').src = src;
            document.getElementById('lightbox-pdf').style.display = 'block';
        } else {
            document.getElementById('lightbox-pdf').style.display = 'none';
            document.getElementById('lightbox-img').src = src;
            document.getElementById('lightbox-img').style.display = 'block';
        }
        document.getElementById('lightbox').style.display = 'flex';
    }
    function closeLightbox() {
        document.getElementById('lightbox').style.display = 'none';
        document.getElementById('lightbox-pdf').src = ''; // Stop PDF loading
    }
    document.getElementById('lightbox').addEventListener('click', function(e) {
        if (e.target === this) closeLightbox();
    });
</script>
@endsection
