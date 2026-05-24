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
                
                <div style="display:flex; flex-direction:column; gap:.75rem; padding-left:3rem; margin-bottom: 1.5rem;">
                    @foreach($soal->shuffled_opsi as $key => $value)
                    <label class="opsi-label" style="display:flex; align-items:center; gap:1rem; padding:0.875rem 1.25rem; border:1px solid #e2e8f0; border-radius:12px; cursor:pointer; transition:all .2s; background:#f8fafc; min-height: 52px;">
                        <input type="radio" name="jawaban[{{ $soal->id }}]" value="{{ $key }}" onchange="handleAnswerChange({{ $index }})" style="margin:0; transform:scale(1.2); accent-color:var(--primary);">
                        <span style="font-size:1rem; color:#334155; font-weight: 500;">{{ $value }}</span>
                    </label>
                    @endforeach
                </div>

                <div style="padding-left:3rem;">
                   <label style="display:inline-flex; align-items:center; gap:.5rem; padding:.5rem 1rem; background:#fffbeb; border:1px solid #fde68a; border-radius:8px; cursor:pointer; transition:all .2s; font-size:.85rem; font-weight:700; color:#92400e;">
                       <input type="checkbox" name="ragu[{{ $index }}]" id="ragu-{{ $index }}" onchange="handleRaguChange({{ $index }})" style="accent-color:#f59e0b;">
                       🤔 Ragu-Ragu
                   </label>
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

            <div style="margin-top:1.5rem; display:flex; flex-direction:column; gap:.6rem; font-size:.8rem; color:#475569;">
                <div style="display:flex; align-items:center; gap:.5rem;"><div style="width:14px; height:14px; background:var(--primary); border-radius:4px; box-shadow:0 2px 4px rgba(30,64,175,0.2);"></div> <span style="font-weight:600;">Sedang Dilihat</span></div>
                <div style="display:flex; align-items:center; gap:.5rem;"><div style="width:14px; height:14px; background:#10b981; border-radius:4px; box-shadow:0 2px 4px rgba(16,185,129,0.2);"></div> <span style="font-weight:600;">Sudah Dijawab</span></div>
                <div style="display:flex; align-items:center; gap:.5rem;"><div style="width:14px; height:14px; background:#f59e0b; border-radius:4px; box-shadow:0 2px 4px rgba(245,158,11,0.2);"></div> <span style="font-weight:600;">Ragu-Ragu</span></div>
                <div style="display:flex; align-items:center; gap:.5rem;"><div style="width:14px; height:14px; border:1px solid #cbd5e1; background:white; border-radius:4px;"></div> <span style="font-weight:600;">Belum Dijawab</span></div>
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
    .grid-btn:hover { border-color:var(--primary); color:var(--primary); transform: translateY(-2px); }
    .grid-btn.active { background:var(--primary)!important; border-color:var(--primary)!important; color:white!important; box-shadow: 0 4px 10px rgba(30, 64, 175, 0.3); }
    .grid-btn.answered { background:#10b981!important; border-color:#10b981!important; color:white!important; }
    .grid-btn.doubtful { background:#f59e0b!important; border-color:#d97706!important; color:white!important; }
    .grid-btn.doubtful.active { box-shadow: 0 4px 10px rgba(245, 158, 11, 0.4); }
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

    function handleAnswerChange(index) {
        updateGridStatus(index);
    }

    function handleRaguChange(index) {
        updateGridStatus(index);
    }

    function updateGridStatus(index) {
        const btn = document.getElementById(`grid-btn-${index}`);
        const isRagu = document.getElementById(`ragu-${index}`).checked;
        const soalId = btn.dataset.soalId;
        const radioChecked = !!document.querySelector(`input[name="jawaban[${soalId}]"]:checked`);

        btn.classList.remove('answered', 'doubtful');
        
        if (isRagu) {
            btn.classList.add('doubtful');
        } else if (radioChecked) {
            btn.classList.add('answered');
        }
    }

    // Helper to setup datasets for JS lookup
    document.querySelectorAll('.grid-btn').forEach((btn, idx) => {
        // Find the soal ID from the input names in the corresponding soal div
        const soalContainer = document.querySelector(`#soal-${idx}`);
        const firstInput = soalContainer.querySelector('input[type="radio"]');
        if (firstInput) {
            const matches = firstInput.name.match(/\[(\d+)\]/);
            if (matches) {
                btn.dataset.soalId = matches[1];
            }
        }
    });

    function markAnswered(index) {
        updateGridStatus(index);
    }

    function confirmSubmit() {
        // Cek jawaban yang memiliki pilihan (baik answered biasa maupun doubtful)
        const chosenCount = document.querySelectorAll('.grid-btn.answered, .grid-btn.doubtful').length;
        let msg = `Anda yakin ingin mengakhiri ujian?\n\nTotal Terjawab: ${chosenCount} dari ${totalSoal}`;
        
        if (chosenCount < totalSoal) {
            msg += `\n\nPERINGATAN: Masih ada ${totalSoal - chosenCount} soal yang BELUM diisi!`;
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
    let time = Math.floor({{ isset($sisaDetik) ? $sisaDetik : ($ujian->durasi_menit * 60) }});
    const timerDisplay = document.getElementById('timer');

    const countdown = setInterval(() => {
        if(time <= 0) {
            clearInterval(countdown);
            timerDisplay.innerText = "00:00";
            alert('Waktu Habis! Ujian akan dikumpulkan secara otomatis.');
            document.getElementById('ujianForm').submit();
        } else {
            let minutes = Math.floor(time / 60);
            let seconds = Math.floor(time % 60);
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
