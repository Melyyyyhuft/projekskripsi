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
            @foreach($soal->shuffled_opsi as $key => $value)
            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; padding: 0.5rem; border: 1px solid rgba(0,0,0,0.1); border-radius: 8px;">
                <input type="radio" name="jawaban[{{ $soal->id }}]" value="{{ $key }}" style="transform: scale(1.2);"> 
                <span>{{ $value }}</span>
            </label>
            @endforeach
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
