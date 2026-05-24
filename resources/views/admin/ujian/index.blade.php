@extends('layouts.admin')
@section('title', 'Manajemen Ujian CBT')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if(session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: "{{ session('success') }}",
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    </script>
@endif

{{-- ─── Page Header ─── --}}
<div class="animate-slide-up" style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: flex-end;">
    <div>
        <h1 style="font-size: 1.75rem; font-weight: 800; color: #0f172a; margin: 0 0 .25rem; letter-spacing: -0.02em;">Manajemen Ujian CBT</h1>
        <p style="color: #64748b; font-size: .95rem; margin: 0; font-weight: 500;">Kelola semua modul ujian, durasi, dan pengaturan global CBT PPDB.</p>
    </div>
    <button onclick="toggleForm()" class="app-btn" style="background: var(--primary); color: white; padding: 0.75rem 1.5rem; border-radius: 12px; display: flex; align-items: center; gap: 8px; box-shadow: var(--shadow-glow);">
        <i class="fa-solid fa-plus-circle"></i>
        <span>Buat Modul Baru</span>
    </button>
</div>

{{-- ─── Card Pengaturan Global ─── --}}
<div class="premium-card animate-slide-up" style="background: linear-gradient(135deg, rgba(255,255,255,0.9) 0%, rgba(243,232,255,0.4) 100%); margin-bottom: 2.5rem; border: 1px solid rgba(255,255,255,0.6);">
    <div style="display:flex; align-items:center; gap:1rem; margin-bottom:1.5rem;">
        <div style="background:white; width:44px; height:44px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:1.25rem; box-shadow: var(--shadow-sm);">⚙️</div>
        <div>
            <h3 style="margin:0; font-size:1.15rem; font-weight:800; color:#1e1b4b;">Pengaturan Global CBT</h3>
            <p style="margin:0; font-size:.85rem; color:#4338ca; font-weight:500;">Konfigurasi periode aktif dan parameter dasar ujian.</p>
        </div>
    </div>

    <form action="{{ route('admin.ujian.cbt_settings') }}" method="POST">
        @csrf
        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:1.25rem;">
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label" style="font-weight:700; font-size:0.8rem; color:#475569;">📅 TANGGAL MULAI</label>
                <input type="datetime-local" name="cbt_tgl_mulai" class="form-control" style="background:rgba(255,255,255,0.8);" value="{{ \Carbon\Carbon::parse($settings['cbt_tgl_mulai'] ?? now())->format('Y-m-d\TH:i') }}" required>
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label" style="font-weight:700; font-size:0.8rem; color:#475569;">📅 TANGGAL SELESAI</label>
                <input type="datetime-local" name="cbt_tgl_selesai" class="form-control" style="background:rgba(255,255,255,0.8);" value="{{ \Carbon\Carbon::parse($settings['cbt_tgl_selesai'] ?? now()->addDays(3))->format('Y-m-d\TH:i') }}" required>
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label" style="font-weight:700; font-size:0.8rem; color:#475569;">⏱️ DURASI DEFAULT</label>
                <input type="number" name="cbt_durasi_default" class="form-control" style="background:rgba(255,255,255,0.8);" value="{{ $settings['cbt_durasi_default'] ?? 60 }}" required>
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label" style="font-weight:700; font-size:0.8rem; color:#475569;">🔄 MAKS. PERCOBAAN</label>
                <input type="number" name="cbt_max_percobaan" class="form-control" style="background:rgba(255,255,255,0.8);" value="{{ $settings['cbt_max_percobaan'] ?? 1 }}" required>
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label" style="font-weight:700; font-size:0.8rem; color:#475569;">🚦 STATUS CBT</label>
                <select name="cbt_status" class="form-control" style="background:rgba(255,255,255,0.8); font-weight:600;">
                    <option value="aktif" {{ ($settings['cbt_status'] ?? '') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="ditutup" {{ ($settings['cbt_status'] ?? '') == 'ditutup' ? 'selected' : '' }}>Ditutup</option>
                </select>
            </div>
        </div>

        <div style="margin-top:1.5rem; display:flex; justify-content:flex-end;">
            <button type="submit" class="app-btn" style="padding:0.6rem 2rem; background: #6366f1; color: white; border-radius: 10px; font-size: 0.9rem;">
                <i class="fa-solid fa-save" style="margin-right:6px;"></i> Simpan Pengaturan
            </button>
        </div>
    </form>
</div>

{{-- ─── Section Daftar Ujian ─── --}}
<div style="margin-bottom:1.5rem;">
    <h2 style="font-size:1.25rem; font-weight:800; color:#1e293b; margin:0;">📋 Daftar Ujian Per Jurusan</h2>
</div>

@if($ujians->isEmpty())
    {{-- ── Empty State ── --}}
    <div class="premium-card animate-slide-up" style="text-align: center; padding: 4rem 2rem; display: flex; flex-direction: column; align-items: center; justify-content: center; background: rgba(255,255,255,0.5);">
        <div style="width: 100px; height: 100px; background: #f1f5f9; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 2rem; font-size: 3rem; color: #94a3b8;">
            <i class="fa-solid fa-folder-open"></i>
        </div>
        <h3 style="font-size: 1.5rem; font-weight: 800; color: #1e293b; margin-bottom: 0.5rem;">Belum Ada Modul Ujian</h3>
        <p style="color: #64748b; max-width: 450px; margin-bottom: 2rem; line-height: 1.6;">Database ujian Anda masih kosong. Silakan buat modul baru per jurusan untuk mulai mengatur pelaksanaan CBT PPDB.</p>
        <button onclick="toggleForm()" class="app-btn" style="background: var(--primary); color: white; padding: 0.8rem 2rem; border-radius: 12px;">
            <i class="fa-solid fa-plus-circle" style="margin-right:8px;"></i> Buat Modul Sekarang
        </button>
    </div>
@else
    {{-- ── Responsive Grid ── --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1.5rem;">
        @foreach($ujians as $u)
        <div class="premium-card animate-slide-up" style="padding: 0; overflow: hidden; display: flex; flex-direction: column; height: 100%; border: 1px solid rgba(0,0,0,0.05); transition: transform 0.3s ease;">
            {{-- Card Header Gradient --}}
            <div style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); padding: 1.25rem; color: white; position: relative;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
                    <span style="background: rgba(255,255,255,0.2); backdrop-filter: blur(4px); padding: 0.3rem 0.75rem; border-radius: 8px; font-size: 0.65rem; text-transform: uppercase; font-weight: 800; letter-spacing: 0.05em;">
                       {{ $u->jurusan->nama ?? 'Umum' }}
                    </span>
                    <form action="{{ route('admin.ujian.toggle', $u->id) }}" method="POST">
                        @csrf
                        <button type="submit" style="background: {{ $u->is_active ? '#22c55e' : '#94a3b8' }}; color: white; border: none; padding: 0.25rem 0.6rem; border-radius: 6px; font-size: 0.6rem; font-weight: 800; cursor: pointer; text-transform: uppercase;">
                            {{ $u->is_active ? 'PUBLISHED' : 'DRAFT' }}
                        </button>
                    </form>
                </div>
                <h4 style="font-size: 1.15rem; font-weight: 800; margin: 0; line-height: 1.2;">{{ $u->judul }}</h4>
                <div style="margin-top: 1rem; display: flex; gap: 1rem; align-items: center; opacity: 0.9; font-size: 0.8rem;">
                    <span><i class="fa-solid fa-clock"></i> {{ $u->durasi_menit }} Menit</span>
                    <span><i class="fa-solid fa-list-check"></i> {{ $u->soals_count }} Soal</span>
                </div>
            </div>

            {{-- Card Body --}}
            <div style="padding: 1.25rem; flex: 1; background: white;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 1.5rem;">
                    <div style="background: #f8fafc; padding: 0.75rem; border-radius: 10px; text-align: center;">
                        <span style="display: block; font-size: 0.6rem; color: #64748b; font-weight: 700; text-transform: uppercase;">Acak Soal</span>
                        <span style="font-weight: 800; color: #1e293b; font-size: 0.85rem;">{{ $u->acak_soal ? 'ON' : 'OFF' }}</span>
                    </div>
                    <div style="background: #f8fafc; padding: 0.75rem; border-radius: 10px; text-align: center;">
                        <span style="display: block; font-size: 0.6rem; color: #64748b; font-weight: 700; text-transform: uppercase;">Acak Jawaban</span>
                        <span style="font-weight: 800; color: #1e293b; font-size: 0.85rem;">{{ $u->acak_jawaban ? 'ON' : 'OFF' }}</span>
                    </div>
                </div>

                <div style="display: flex; gap: 0.5rem;">
                    <a href="{{ route('admin.ujian.show', $u->id) }}" class="app-btn" style="flex: 1; background: #eff6ff; color: #1d4ed8; font-size: 0.8rem; padding: 0.6rem; border-radius: 8px; justify-content: center; border: 1px solid #dbeafe;">
                        <i class="fa-solid fa-pen-to-square"></i> Kelola
                    </a>
                    <form action="{{ route('admin.ujian.destroy', $u->id) }}" method="POST" style="flex: 0 0 auto;" onsubmit="return confirmDelete(event)">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="app-btn" style="background: #fef2f2; color: #ef4444; border: 1px solid #fee2e2; padding: 0.6rem; border-radius: 8px; aspect-ratio: 1/1; justify-content: center;">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
            
            {{-- Card Footer --}}
            <div style="padding: 0.75rem 1.25rem; background: #fcfcfd; border-top: 1px solid #f1f5f9; font-size: 0.65rem; color: #94a3b8; font-weight: 600;">
                <i class="fa-solid fa-calendar"></i> Dibuat pada {{ $u->created_at->format('d M Y') }}
            </div>
        </div>
        @endforeach
    </div>
@endif

{{-- ─── Form Tambah Modul (Modern Form Layout) ─── --}}
<div id="formContainer" class="premium-card animate-slide-up" style="display:none; margin-top:3rem; border-top:4px solid var(--primary); background: #ffffff;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:2rem;">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <div style="background: var(--primary-light); color: var(--primary); width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                <i class="fa-solid fa-folder-plus"></i>
            </div>
            <h3 style="margin:0; font-weight:800; color:#1e293b; font-size: 1.25rem;">Buat Modul Ujian Baru</h3>
        </div>
        <button onclick="toggleForm()" style="background: #f1f5f9; border: none; cursor: pointer; color: #64748b; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; transition: all 0.2s;" onmouseover="this.style.background='#e2e8f0'">✕</button>
    </div>

    <form action="{{ route('admin.ujian.store') }}" method="POST">
        @csrf
        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap:1.5rem;">
            <div class="form-group">
                <label class="form-label">Nama/Judul Modul</label>
                <input type="text" name="judul" class="form-control" placeholder="Contoh: Seleksi Jalur Prestasi 2024" required>
            </div>
            <div class="form-group">
                <label class="form-label">Tentukan Jurusan</label>
                <select name="jurusan_id" class="form-control" required>
                    <option value="">-- Pilih Jurusan --</option>
                    @foreach($jurusans as $j)
                        <option value="{{ $j->id }}">{{ $j->nama }}</option>
                    @endforeach
                </select>
                <small style="color:#64748b; font-size:.7rem; display: block; margin-top: 4px;">Modul ini hanya akan dapat diakses oleh siswa yang mendaftar ke jurusan terpilih.</small>
            </div>
            <div class="form-group">
                <label class="form-label">Durasi Ujian (Menit)</label>
                <input type="number" name="durasi_menit" class="form-control" value="{{ $settings['cbt_durasi_default'] ?? 60 }}" required>
            </div>
        </div>

        <div style="background:#f8fafc; padding:1.25rem; border-radius:15px; border:1px solid #e2e8f0; margin-top:1.5rem; display:flex; gap:2.5rem;">
            <label style="display:flex; align-items:center; gap:0.6rem; cursor:pointer; font-weight:700; font-size:.9rem; color:#334155;">
                <input type="checkbox" name="acak_soal" value="1" style="width:20px; height:20px; accent-color:var(--primary);"> Acak Urutan Soal
            </label>
            <label style="display:flex; align-items:center; gap:0.6rem; cursor:pointer; font-weight:700; font-size:.9rem; color:#334155;">
                <input type="checkbox" name="acak_jawaban" value="1" style="width:20px; height:20px; accent-color:var(--primary);"> Acak Urutan Jawaban
            </label>
        </div>

        <div style="display:flex; justify-content:flex-end; gap:1rem; margin-top:2.5rem;">
            <button type="button" onclick="toggleForm()" class="app-btn" style="background: #f1f5f9; color: #64748b; padding: 0.75rem 2rem; border-radius: 12px;">Batal</button>
            <button type="submit" class="app-btn" style="background: var(--primary); color: white; padding: 0.75rem 2.5rem; border-radius: 12px; box-shadow: var(--shadow-glow);">
                🚀 Konfirmasi & Simpan
            </button>
        </div>
    </form>
</div>

<style>
    .form-label { display: block; margin-bottom: 0.5rem; font-weight: 700; color: #334155; font-size: 0.85rem; }
    .form-control { width: 100%; padding: 0.75rem 1rem; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 0.9rem; transition: all 0.2s; }
    .form-control:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 4px rgba(30, 64, 175, 0.1); }
    .app-btn { display: inline-flex; align-items: center; border: none; font-weight: 700; cursor: pointer; transition: all 0.2s; gap: 8px; }
    .app-btn:hover { transform: translateY(-2px); opacity: 0.9; }
    .app-btn:active { transform: translateY(0); }
    .premium-card:hover { transform: translateY(-5px); box-shadow: var(--shadow-lg); }
</style>

<script>
    function toggleForm() {
        const fc = document.getElementById('formContainer');
        if(fc.style.display === 'none') {
            fc.style.display = 'block';
            fc.classList.add('animate-slide-up');
            fc.scrollIntoView({ behavior: 'smooth', block: 'center' });
        } else {
            fc.style.display = 'none';
        }
    }

    function confirmDelete(e) {
        e.preventDefault();
        const form = e.target;
        Swal.fire({
            title: 'Hapus Modul?',
            text: "Data soal yang terkait tetap ada di Bank Soal, namun modul ini akan dihapus permanen.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            border: 'none',
            borderRadius: '20px'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        })
    }
</script>

@endsection
