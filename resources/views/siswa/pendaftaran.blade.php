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
                    <option value="{{ $jurusan->id }}" {{ (isset($pendaftaran) && $pendaftaran->jurusan_id == $jurusan->id) ? 'selected' : '' }}>
                        {{ $jurusan->nama }} (Kuota: {{ $jurusan->kuota }})
                    </option>
                @endforeach
            </select>
        </div>

        <h3 style="margin-top: 2rem; margin-bottom: 1rem; color: var(--primary);">Upload Berkas Pendukung</h3>
        <div style="background: #fff8f1; padding: 1rem; border-radius: var(--radius-sm); border-left: 4px solid #f59e0b; margin-bottom: 1rem; font-size: 0.9rem;">
            <strong>Perhatian:</strong> Berkas wajib harus diisi agar pendaftaran dapat dikirim. Maksimal 2MB per file.
        </div>
        <div style="background: #f0fdf4; padding: 1rem; border-radius: var(--radius-sm); border-left: 4px solid #22c55e; margin-bottom: 1.5rem; font-size: 0.9rem;">
            <i class="fa-solid fa-shield-halved"></i> <strong>Privasi:</strong> Dokumen hanya digunakan untuk keperluan verifikasi dan tidak disebarluaskan.
        </div>

        <div class="form-group">
            <label class="form-label" for="skl">1. Scan SKL / Ijazah (Wajib)</label>
            <input type="file" name="skl" id="file_skl" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
            <small style="color: var(--gray-text);">Format: PDF, JPG, PNG.</small>
        </div>

        <div class="form-group">
            <label class="form-label" for="rapor">2. Scan Rapor Terakhir (Wajib)</label>
            <input type="file" name="rapor" id="file_rapor" class="form-control" accept=".pdf" required>
            <small style="color: var(--gray-text);">Format: PDF.</small>
        </div>

        <div class="form-group">
            <label class="form-label" for="pasfoto">3. Pas Foto Terkini (Wajib)</label>
            <input type="file" name="pasfoto" id="file_pasfoto" class="form-control" accept=".jpg,.jpeg,.png" required>
            <small style="color: var(--gray-text);">Format: JPG, PNG.</small>
        </div>

        <div class="form-group">
            <label class="form-label" for="sertifikat">4. Sertifikat Pendukung (Opsional)</label>
            <input type="file" name="sertifikat[]" id="file_sertifikat" class="form-control" accept=".pdf,.jpg,.jpeg,.png" multiple>
            <small style="color: var(--gray-text);">Boleh pilih lebih dari satu file.</small>
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
    });
</script>
@endsection
