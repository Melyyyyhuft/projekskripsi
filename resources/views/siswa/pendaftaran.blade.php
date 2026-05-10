@extends('layouts.siswa')
@section('title', 'Form Pendaftaran')

@section('content')
<div class="glass-card" style="max-width: 800px; margin: 0 auto;">
    <h2 style="color: var(--primary); margin-bottom: 2rem;">Lengkapi Data Pendaftaran</h2>
    
    @if($pendaftaran)
        <div style="background: #e0f2fe; color: #0284c7; padding: 1rem; border-radius: var(--radius-sm); margin-bottom: 1.5rem;">
            <strong>Informasi:</strong> Anda sudah mensubmit pendaftaran. Status saat ini: <span style="text-transform: uppercase;">{{ $pendaftaran->status }}</span>. Anda dapat memperbarui data jika belum diuji.
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

    <form action="{{ url('siswa/pendaftaran') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
            <div class="form-group">
                <label class="form-label" for="nisn">Nomor Induk Siswa Nasional (NISN)</label>
                <input type="text" name="nisn" id="nisn" class="form-control" value="{{ $pendaftaran->nisn ?? old('nisn') }}" required>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="asal_sekolah">Asal Sekolah</label>
                <input type="text" name="asal_sekolah" id="asal_sekolah" class="form-control" value="{{ $pendaftaran->asal_sekolah ?? old('asal_sekolah') }}" required>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
            <div class="form-group">
                <label class="form-label" for="nilai_rapor">Rata-rata Nilai Rapor</label>
                <input type="number" step="0.01" max="100" name="nilai_rapor" id="nilai_rapor" class="form-control" value="{{ $pendaftaran->nilai_rapor ?? old('nilai_rapor') }}" required>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="no_hp">Nomor HP / WhatsApp</label>
                <input type="text" name="no_hp" id="no_hp" class="form-control" value="{{ $pendaftaran->no_hp ?? old('no_hp') }}" required placeholder="Contoh: 081234567890">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label" for="jurusan_id">Pilih Jurusan</label>
            <select name="jurusan_id" id="jurusan_id" class="form-control" required>
                <option value="">-- Pilih Jurusan --</option>
                @foreach($jurusans as $jurusan)
                    @php $sisa = $jurusan->sisa_kuota; @endphp
                    <option value="{{ $jurusan->id }}" 
                        {{ (isset($pendaftaran) && $pendaftaran->jurusan_id == $jurusan->id) ? 'selected' : '' }}
                        {{ $sisa <= 0 ? 'disabled' : '' }}
                        style="{{ $sisa <= 0 ? 'color: #ef4444; font-weight: bold;' : '' }}">
                        {{ $jurusan->nama }} &ndash; {{ $sisa <= 0 ? 'Penuh' : 'Tersedia' }}
                    </option>
                @endforeach
            </select>
        </div>

        @if(!empty($berkasAktif))
        <div style="margin-top: 2rem; border: 1px solid #e2e8f0; border-radius: var(--radius-md); background: white; overflow: hidden;">
            <div style="background: #f8fafc; padding: 1rem; border-bottom: 1px solid #e2e8f0;">
                <h3 style="margin: 0; font-size: 1.1rem; color: #334155;">📋 Daftar Dokumen Aktif</h3>
                <p style="margin: 0; font-size: 0.8rem; color: #64748b;">Dokumen terbaru yang Anda unggah untuk verifikasi.</p>
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

        <h3 style="margin-top: 2rem; margin-bottom: 1rem; color: var(--primary);">Upload Berkas Pendukung</h3>
        <div style="background: #fff8f1; padding: 1rem; border-radius: var(--radius-sm); border-left: 4px solid #f59e0b; margin-bottom: 1rem; font-size: 0.9rem;">
            <strong>Perhatian:</strong> Berkas wajib harus diisi agar pendaftaran dapat dikirim. Maksimal 2MB per file.
        </div>
        <div style="background: #f0fdf4; padding: 1rem; border-radius: var(--radius-sm); border-left: 4px solid #22c55e; margin-bottom: 1.5rem; font-size: 0.9rem;">
            <i class="fa-solid fa-shield-halved"></i> <strong>Privasi:</strong> Dokumen hanya digunakan untuk keperluan verifikasi dan tidak disebarluaskan.
        </div>

        <div class="form-group">
            <label class="form-label" for="skl">1. Scan SKL / Ijazah {{ isset($pendaftaran) ? '(Opsional jika sudah ada)' : '(Wajib)' }}</label>
            <input type="file" name="skl" id="file_skl" class="form-control" accept=".pdf,.jpg,.jpeg,.png" {{ isset($pendaftaran) ? '' : 'required' }}>
            <small style="color: var(--gray-text);">Format: PDF, JPG, PNG.</small>
        </div>

        <div class="form-group">
            <label class="form-label" for="rapor">2. Scan Rapor Terakhir {{ isset($pendaftaran) ? '(Opsional jika sudah ada)' : '(Wajib)' }}</label>
            <input type="file" name="rapor" id="file_rapor" class="form-control" accept=".pdf" {{ isset($pendaftaran) ? '' : 'required' }}>
            <small style="color: var(--gray-text);">Format: PDF.</small>
        </div>

        <div class="form-group">
            <label class="form-label" for="pasfoto">3. Pas Foto Terkini {{ isset($pendaftaran) ? '(Opsional jika sudah ada)' : '(Wajib)' }}</label>
            <input type="file" name="pasfoto" id="file_pasfoto" class="form-control" accept=".jpg,.jpeg,.png" {{ isset($pendaftaran) ? '' : 'required' }}>
            <small style="color: var(--gray-text);">Format: JPG, PNG.</small>
        </div>

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

        <button type="submit" id="btn_submit" class="btn-primary" style="width: 100%; margin-top: 1rem; font-size: 1.125rem; padding: 1rem;" disabled>Kirim Pendaftaran</button>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const skl = document.getElementById('file_skl');
        const rapor = document.getElementById('file_rapor');
        const pasfoto = document.getElementById('file_pasfoto');
        const btnSubmit = document.getElementById('btn_submit');

        function checkFiles() {
            // Cek apakah ketiganya memiliki setidaknya 1 file
            if (skl.files.length > 0 && rapor.files.length > 0 && pasfoto.files.length > 0) {
                btnSubmit.disabled = false;
                btnSubmit.style.opacity = '1';
                btnSubmit.style.cursor = 'pointer';
            } else {
                btnSubmit.disabled = true;
                btnSubmit.style.opacity = '0.5';
                btnSubmit.style.cursor = 'not-allowed';
            }
        }

        skl.addEventListener('change', checkFiles);
        rapor.addEventListener('change', checkFiles);
        pasfoto.addEventListener('change', checkFiles);
        
        // Panggil sekali saat load
        checkFiles();
        // Script Tambah Prestasi
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
