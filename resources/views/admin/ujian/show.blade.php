@extends('layouts.admin')
@section('title', 'Soal Ujian: ' . $ujian->judul)

@section('content')
<div style="margin-bottom: 1.5rem;">
    <a href="{{ route('admin.ujian.index') }}" style="color: var(--primary); font-weight: 500;">&larr; Kembali ke Daftar Ujian</a>
</div>

<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 2rem;">
    <!-- Form Tambah Soal -->
    <div class="glass-card">
        <h3 style="margin-bottom: 1.5rem; color: var(--primary);">Tambah Soal Pilihan Ganda</h3>
        
        @if(session('success'))
            <div style="background: #d1fae5; color: #059669; padding: 1rem; border-radius: var(--radius-sm); margin-bottom: 1.5rem; font-weight: 500;">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('admin.ujian.soal.store', $ujian->id) }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label class="form-label" for="teks_soal">Pertanyaan</label>
                <textarea name="teks_soal" id="teks_soal" class="form-control" rows="3" required placeholder="Tuliskan soal di sini..."></textarea>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="opsi_a">Opsi A</label>
                <input type="text" name="opsi_a" id="opsi_a" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="opsi_b">Opsi B</label>
                <input type="text" name="opsi_b" id="opsi_b" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="opsi_c">Opsi C</label>
                <input type="text" name="opsi_c" id="opsi_c" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="opsi_d">Opsi D</label>
                <input type="text" name="opsi_d" id="opsi_d" class="form-control" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="jawaban_benar">Jawaban Benar</label>
                <select name="jawaban_benar" id="jawaban_benar" class="form-control" required>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                    <option value="D">D</option>
                </select>
            </div>
            
            <button type="submit" class="btn-primary" style="width: 100%;">Simpan Soal</button>
        </form>
    </div>

    <!-- Daftar Soal Tersimpan -->
    <div class="glass-card">
        <h3 style="margin-bottom: 1.5rem;">Soal Tersimpan ({{ $soals->count() }})</h3>
        <div style="display: flex; flex-direction: column; gap: 1rem;">
            @forelse($soals as $index => $soal)
            <div style="padding: 1rem; border: 1px solid #e2e8f0; border-radius: var(--radius-md); background: var(--light-bg);">
                <p style="font-weight: 600; margin-bottom: 0.5rem;">{{ $index + 1 }}. {{ $soal->teks_soal }}</p>
                <ul style="list-style: none; padding-left: 1rem; color: var(--gray-text); margin-bottom: 0.5rem;">
                    <li>A. {{ $soal->opsi_a }}</li>
                    <li>B. {{ $soal->opsi_b }}</li>
                    <li>C. {{ $soal->opsi_c }}</li>
                    <li>D. {{ $soal->opsi_d }}</li>
                </ul>
                <div style="font-size: 0.875rem; font-weight: 600; color: #059669;">
                    Jawaban Kunci: {{ $soal->jawaban_benar }}
                </div>
            </div>
            @empty
            <div style="text-align: center; color: var(--gray-text); padding: 2rem;">Belum ada soal yang tersimpan untuk ujian ini.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
