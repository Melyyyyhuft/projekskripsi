@extends('layouts.siswa')
@section('title', 'Ujian Uji Kompetensi (CBT)')

@section('content')
<div class="glass-card" style="margin-bottom: 2rem; background: linear-gradient(135deg, var(--dark), var(--primary)); color: var(--white); display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h2 style="font-size: 1.5rem; margin-bottom: 0.5rem;">{{ $ujian->judul }}</h2>
        <p style="opacity: 0.9;">Pilihlah jawaban yang paling benar. Waktu berjalan mundur.</p>
    </div>
    <div style="background: rgba(255,255,255,0.2); padding: 1rem 2rem; border-radius: var(--radius-md); font-family: monospace; font-size: 1.5rem; font-weight: bold;">
        <span id="timer">{{ $ujian->durasi_menit }}:00</span>
    </div>
</div>

<form action="{{ route('siswa.ujian.submit') }}" method="POST" id="ujianForm">
    @csrf
    <input type="hidden" name="ujian_id" value="{{ $ujian->id }}">
    
    @foreach($soals as $index => $soal)
    <div class="glass-card" style="margin-bottom: 1.5rem;">
        <h3 style="font-size: 1.125rem; font-weight: 600; margin-bottom: 1rem;">{{ $index + 1 }}. {{ $soal->teks_soal }}</h3>
        
        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                <input type="radio" name="jawaban[{{ $soal->id }}]" value="A"> A. {{ $soal->opsi_a }}
            </label>
            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                <input type="radio" name="jawaban[{{ $soal->id }}]" value="B"> B. {{ $soal->opsi_b }}
            </label>
            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                <input type="radio" name="jawaban[{{ $soal->id }}]" value="C"> C. {{ $soal->opsi_c }}
            </label>
            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                <input type="radio" name="jawaban[{{ $soal->id }}]" value="D"> D. {{ $soal->opsi_d }}
            </label>
        </div>
    </div>
    @endforeach

    <button type="submit" class="btn-primary" style="width: 100%; font-size: 1.25rem; padding: 1rem;" onclick="return confirm('Apakah Anda yakin ingin Mengakhiri Ujian? Jawaban tidak dapat diubah lagi.');">Kirim & Akhiri Ujian</button>
</form>

<script>
    // Simple Timer Logic
    let limitInMinutes = {{ $ujian->durasi_menit }};
    let time = limitInMinutes * 60;
    const timerDisplay = document.getElementById('timer');

    setInterval(() => {
        if(time <= 0) {
            document.getElementById('ujianForm').submit();
        } else {
            let minutes = Math.floor(time / 60);
            let seconds = time % 60;
            timerDisplay.innerText = 
                (minutes < 10 ? "0" + minutes : minutes) + ":" + 
                (seconds < 10 ? "0" + seconds : seconds);
            time--;
        }
    }, 1000);
</script>
@endsection
