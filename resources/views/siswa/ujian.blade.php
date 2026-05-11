@extends('layouts.siswa')
@section('title', 'Ujian Seleksi CBT')

@section('content')
<div style="display:flex; justify-content:space-between; align-items:flex-end; margin-bottom:1.5rem;">
    <div>
        <h2 style="font-size:1.4rem; color:var(--dark); margin:0 0 .25rem;">{{ $ujian->judul }}</h2>
        <p style="color:var(--gray-text); font-size:.9rem; margin:0;">Kerjakan soal dengan teliti. Waktu akan berjalan mundur.</p>
    </div>
</div>

<form action="{{ route('siswa.ujian.submit') }}" method="POST" id="ujianForm" style="display:flex; gap:1.5rem; align-items:flex-start; flex-wrap:wrap;">
    @csrf
    <input type="hidden" name="ujian_id" value="{{ $ujian->id }}">
    
    {{-- Kolom Kiri: Area Soal --}}
    <div style="flex:1; min-width:300px; display:flex; flex-direction:column; gap:1.5rem;">
        <div class="glass-card" style="padding:2rem;">
            @foreach($soals as $index => $soal)
            <div class="soal-item" id="soal-{{ $index }}" style="display:none;">
                <div style="margin-bottom:1.5rem; display:flex; gap:.75rem;">
                    <div style="width:36px; height:36px; border-radius:50%; background:linear-gradient(135deg,var(--primary),#8b5cf6); color:white; display:flex; align-items:center; justify-content:center; font-weight:700; flex-shrink:0;">
                        {{ $index + 1 }}
                    </div>
                    <div style="font-size:1.1rem; font-weight:600; color:var(--dark); line-height:1.6; padding-top:.2rem;">
                        {{ $soal->teks_soal }}
                    </div>
                </div>
                
                <div style="display:flex; flex-direction:column; gap:.75rem; padding-left:3rem;">
                    @foreach($soal->shuffled_opsi as $key => $value)
                    <label class="opsi-label" style="display:flex; align-items:flex-start; gap:1rem; padding:1rem; border:1px solid #e2e8f0; border-radius:12px; cursor:pointer; transition:all .2s; background:#f8fafc;">
                        <input type="radio" name="jawaban[{{ $soal->id }}]" value="{{ $key }}" onchange="markAnswered({{ $index }})" style="margin-top:.2rem; transform:scale(1.2); accent-color:var(--primary);">
                        <span style="font-size:.95rem; color:#334155;">{{ $value }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>

        {{-- Navigasi Bawah --}}
        <div style="display:flex; justify-content:space-between; gap:1rem;">
            <button type="button" class="btn-outline" id="btnPrev" onclick="prevSoal()" style="width:120px;">
                &laquo; Sebelum
            </button>
            <button type="button" class="btn-primary" id="btnNext" onclick="nextSoal()" style="width:120px;">
                Lanjut &raquo;
            </button>
        </div>
    </div>

    {{-- Kolom Kanan: Timer & Grid --}}
    <div style="width:280px; flex-shrink:0; position:sticky; top:2rem; display:flex; flex-direction:column; gap:1.5rem;">
        
        <div class="glass-card" style="padding:1.5rem; text-align:center; border:2px solid #e2e8f0;">
            <p style="font-size:.8rem; font-weight:700; color:#64748b; text-transform:uppercase; margin:0 0 .5rem; letter-spacing:.05em;">Sisa Waktu</p>
            <div id="timer" style="font-family:monospace; font-size:2.2rem; font-weight:800; color:var(--dark); line-height:1; background:#f8fafc; padding:1rem; border-radius:12px;">
                {{ $ujian->durasi_menit }}:00
            </div>
        </div>

        <div class="glass-card" style="padding:1.5rem;">
            <p style="font-size:.85rem; font-weight:700; color:var(--dark); margin:0 0 1rem;">Navigasi Soal</p>
            <div style="display:grid; grid-template-columns:repeat(5, 1fr); gap:.5rem;">
                @foreach($soals as $index => $soal)
                <button type="button" class="grid-btn" id="grid-btn-{{ $index }}" onclick="goToSoal({{ $index }})"
                    style="width:100%; aspect-ratio:1; border-radius:8px; border:1px solid #cbd5e1; background:white; font-weight:600; font-size:.85rem; color:#64748b; cursor:pointer; transition:all .2s;">
                    {{ $index + 1 }}
                </button>
                @endforeach
            </div>

            <div style="margin-top:1.5rem; display:flex; flex-direction:column; gap:.5rem; font-size:.75rem; color:#64748b;">
                <div style="display:flex; align-items:center; gap:.5rem;"><div style="width:12px; height:12px; background:var(--primary); border-radius:3px;"></div> Sedang Dilihat</div>
                <div style="display:flex; align-items:center; gap:.5rem;"><div style="width:12px; height:12px; background:#10b981; border-radius:3px;"></div> Sudah Dijawab</div>
                <div style="display:flex; align-items:center; gap:.5rem;"><div style="width:12px; height:12px; border:1px solid #cbd5e1; background:white; border-radius:3px;"></div> Belum Dijawab</div>
            </div>

            <hr style="border:none; border-top:1px dashed #e2e8f0; margin:1.5rem 0;">

            <button type="button" class="btn-primary" style="width:100%; background:linear-gradient(135deg,#10b981,#059669);" onclick="confirmSubmit()">
                Akhiri Ujian
            </button>
        </div>
    </div>
</form>

<style>
    .opsi-label:hover { border-color:var(--primary)!important; background:white!important; }
    .opsi-label:has(input:checked) {
        border-color:var(--primary)!important;
        background:#eff6ff!important;
        box-shadow:0 0 0 2px rgba(59,130,246,.2);
    }
    .grid-btn:hover { border-color:var(--primary); color:var(--primary); }
    .grid-btn.active { background:var(--primary)!important; border-color:var(--primary)!important; color:white!important; }
    .grid-btn.answered { background:#10b981; border-color:#10b981; color:white; }
</style>

<script>
    const totalSoal = {{ count($soals) }};
    let currentIndex = 0;

    function updateNavButtons() {
        document.getElementById('btnPrev').disabled = (currentIndex === 0);
        document.getElementById('btnPrev').style.opacity = (currentIndex === 0) ? '0.5' : '1';
        
        if (currentIndex === totalSoal - 1) {
            document.getElementById('btnNext').style.display = 'none';
        } else {
            document.getElementById('btnNext').style.display = 'block';
        }

        // Update grid visual
        document.querySelectorAll('.grid-btn').forEach((btn, idx) => {
            btn.classList.remove('active');
            if (idx === currentIndex) {
                btn.classList.add('active');
            }
        });
    }

    function goToSoal(index) {
        document.getElementById(`soal-${currentIndex}`).style.display = 'none';
        currentIndex = index;
        document.getElementById(`soal-${currentIndex}`).style.display = 'block';
        updateNavButtons();
    }

    function prevSoal() {
        if (currentIndex > 0) goToSoal(currentIndex - 1);
    }

    function nextSoal() {
        if (currentIndex < totalSoal - 1) goToSoal(currentIndex + 1);
    }

    function markAnswered(index) {
        // Hapus class active dulu biar gak ketimpa warnanya saat sedang dilihat
        const btn = document.getElementById(`grid-btn-${index}`);
        btn.classList.add('answered');
    }

    function confirmSubmit() {
        // Cek jawaban kosong
        const answeredCount = document.querySelectorAll('.grid-btn.answered').length;
        let msg = `Anda yakin ingin mengakhiri ujian?\n\nSoal Terjawab: ${answeredCount} dari ${totalSoal}`;
        if (answeredCount < totalSoal) {
            msg += `\nMasih ada ${totalSoal - answeredCount} soal yang BELUM dijawab!`;
        }

        if(confirm(msg)) {
            document.getElementById('ujianForm').submit();
        }
    }

    // Init
    if (totalSoal > 0) {
        goToSoal(0);
    }

    // Timer Logic tersinkronisasi dengan server
    let time = {{ isset($sisaDetik) ? $sisaDetik : ($ujian->durasi_menit * 60) }};
    const timerDisplay = document.getElementById('timer');

    setInterval(() => {
        if(time <= 0) {
            alert('Waktu Habis! Ujian akan dikumpulkan secara otomatis.');
            document.getElementById('ujianForm').submit();
        } else {
            let minutes = Math.floor(time / 60);
            let seconds = time % 60;
            timerDisplay.innerText = 
                (minutes < 10 ? "0" + minutes : minutes) + ":" + 
                (seconds < 10 ? "0" + seconds : seconds);
            
            // Warning style if < 5 minutes
            if(time < 300) {
                timerDisplay.style.color = '#ef4444';
                timerDisplay.style.animation = 'pulse 1s infinite';
            }
            time--;
        }
    }, 1000);
</script>
@endsection
