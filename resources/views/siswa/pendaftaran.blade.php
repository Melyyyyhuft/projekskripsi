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
        </div>

        <div class="form-group" style="margin-top: 1rem; padding: 1.5rem; border: 2px dashed #cbd5e1; border-radius: var(--radius-md); text-align: center;">
            <label class="form-label" for="berkas" style="font-size: 1.125rem;">Unggah Berkas (Rapor & Sertifikat Pendukung)</label>
            <p style="color: var(--gray-text); font-size: 0.875rem; margin-bottom: 1rem;">Format: PDF/JPG/PNG. Maks: 2MB per file. Anda dapat memilih lebih dari satu file (Multiple)</p>
            <input type="file" name="berkas[]" id="berkas" class="form-control" accept=".pdf,.jpg,.jpeg,.png" multiple style="border: none; padding: 0;">
        </div>

        <button type="submit" class="btn-primary" style="width: 100%; margin-top: 1rem; font-size: 1.125rem; padding: 1rem;">Kirim Pendaftaran</button>
    </form>
</div>
@endsection
