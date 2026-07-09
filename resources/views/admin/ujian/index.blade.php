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

@if(session('error'))
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: "{{ session('error') }}",
            confirmButtonColor: '#6366f1',
            confirmButtonText: 'OK'
        });
    </script>
@endif

{{-- ─── Page Header ─── --}}
<div class="animate-slide-up" style="margin-bottom: 2rem;">
    <h1 style="font-size: 1.75rem; font-weight: 800; color: #0f172a; margin: 0 0 .25rem; letter-spacing: -0.02em;">Manajemen Ujian CBT</h1>
    <p style="color: #64748b; font-size: .95rem; margin: 0; font-weight: 500;">Kelola semua modul ujian, durasi, dan pengaturan CBT PPDB.</p>
</div>

{{-- ─── Card Pengaturan CBT ─── --}}
<div style="max-width: 1000px; margin-bottom: 3rem;">
    <div class="premium-card animate-slide-up" style="background: white; border: 1px solid #eef2f6; box-shadow: 0 10px 30px rgba(0,0,0,0.02); padding: 1.75rem 2rem; border-radius: 20px;">
        <div style="display:flex; align-items:center; gap:0.75rem; margin-bottom:1.75rem;">
            <div style="background:#f5f3ff; width:38px; height:38px; border-radius:12px; display:flex; align-items:center; justify-content:center; color:#6366f1; font-size:1rem; border:1px solid #e2e8f0;">
                <i class="fa-solid fa-cog"></i>
            </div>
            <h3 style="margin:0; font-size:1.05rem; font-weight:800; color:#1e293b; letter-spacing:-0.01em;">Pengaturan CBT</h3>
        </div>
    
        <form action="{{ route('admin.ujian.cbt_settings') }}" method="POST">
            @csrf
            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:1.25rem;">
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label" style="font-weight:700; font-size:0.7rem; color:#94a3b8; text-transform:uppercase; letter-spacing:0.05em;">Tanggal Mulai</label>
                    <input type="datetime-local" name="cbt_tgl_mulai" class="form-control" style="background:#f8fafc; border-color:#e2e8f0; height:42px; font-size:0.85rem;" value="{{ \Carbon\Carbon::parse($settings['cbt_tgl_mulai'] ?? now())->format('Y-m-d\TH:i') }}" required>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label" style="font-weight:700; font-size:0.7rem; color:#94a3b8; text-transform:uppercase; letter-spacing:0.05em;">Tanggal Selesai</label>
                    <input type="datetime-local" name="cbt_tgl_selesai" class="form-control" style="background:#f8fafc; border-color:#e2e8f0; height:42px; font-size:0.85rem;" value="{{ \Carbon\Carbon::parse($settings['cbt_tgl_selesai'] ?? now()->addDays(3))->format('Y-m-d\TH:i') }}" required>
                </div>

                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label" style="font-weight:700; font-size:0.7rem; color:#94a3b8; text-transform:uppercase; letter-spacing:0.05em;">Maks. Percobaan</label>
                    <input type="number" name="cbt_max_percobaan" class="form-control" style="background:#f8fafc; border-color:#e2e8f0; height:42px; font-size:0.85rem;" value="{{ $settings['cbt_max_percobaan'] ?? 1 }}" required>
                </div>
            </div>
    
            <div style="margin-top:1.5rem; display:flex; justify-content:space-between; align-items:flex-end; gap:1.5rem; flex-wrap:wrap;">
                <div class="form-group" style="margin-bottom:0; flex: 0 1 300px;">
                    <label class="form-label" style="font-weight:700; font-size:0.7rem; color:#94a3b8; text-transform:uppercase; letter-spacing:0.05em;">Status CBT</label>
                    <select name="cbt_status" class="form-control" style="background:#f8fafc; border-color:#e2e8f0; font-weight:700; height:42px; font-size:0.85rem;">
                        <option value="aktif" {{ ($settings['cbt_status'] ?? '') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="ditutup" {{ ($settings['cbt_status'] ?? '') == 'ditutup' ? 'selected' : '' }}>Ditutup</option>
                    </select>
                </div>
                <button type="submit" class="app-btn" style="padding:0 2rem; height:42px; background: #6366f1; color: white; border-radius: 10px; font-size: 0.85rem; box-shadow: 0 4px 15px rgba(99,102,241,0.2); display: flex; align-items: center; justify-content: center; font-weight: 700;">
                    <i class="fa-solid fa-save" style="margin-right:8px;"></i> Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ─── Section Daftar Ujian ─── --}}
<div style="margin-bottom:1.5rem; display:flex; justify-content:space-between; align-items:center;">
    <h2 style="font-size:1.25rem; font-weight:800; color:#1e293b; margin:0;">📋 Daftar Ujian Per Jurusan</h2>
    <button onclick="toggleForm()" class="app-btn" style="background: var(--primary); color: white; padding: 0.6rem 1.25rem; border-radius: 10px; font-size: 0.8rem; box-shadow: var(--shadow-sm);">
        <i class="fa-solid fa-plus-circle"></i>
        <span>Modul Baru</span>
    </button>
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
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.25rem;">
        @foreach($ujians as $u)
        <div class="premium-card animate-slide-up" style="padding: 0; overflow: hidden; display: flex; flex-direction: column; height: 100%; border: 1px solid rgba(0,0,0,0.05); transition: transform 0.3s ease; border-radius: 18px;">
            {{-- Card Header Gradient --}}
            <div style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); padding: 1rem 1.25rem; color: white; position: relative;">
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
            <div style="padding: 1rem 1.25rem; flex: 1; background: white;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; margin-bottom: 1.25rem;">
                    <div style="background: #f8fafc; padding: 0.6rem; border-radius: 10px; text-align: center; border: 1px solid #f1f5f9;">
                        <span style="display: block; font-size: 0.55rem; color: #94a3b8; font-weight: 800; text-transform: uppercase;">Acak Soal</span>
                        <span style="font-weight: 800; color: #1e293b; font-size: 0.8rem;">{{ $u->acak_soal ? 'ON' : 'OFF' }}</span>
                    </div>
                    <div style="background: #f8fafc; padding: 0.6rem; border-radius: 10px; text-align: center; border: 1px solid #f1f5f9;">
                        <span style="display: block; font-size: 0.55rem; color: #94a3b8; font-weight: 800; text-transform: uppercase;">Acak Urutan</span>
                        <span style="font-weight: 800; color: #1e293b; font-size: 0.8rem;">{{ $u->acak_jawaban ? 'ON' : 'OFF' }}</span>
                    </div>
                </div>

                <div style="display: flex; gap: 0.4rem; margin-top: 0.25rem;">
                    <a href="{{ route('admin.ujian.show', $u->id) }}" class="app-btn" style="flex: 1; background: #eff6ff; color: #1d4ed8; font-size: 0.75rem; padding: 0.5rem; border-radius: 8px; justify-content: center; border: 1px solid #dbeafe;">
                        <i class="fa-solid fa-pen-to-square"></i> Kelola
                    </a>
                    <form action="{{ route('admin.ujian.destroy', $u->id) }}" method="POST" style="flex: 0 0 auto;" onsubmit="return confirmDelete(event)">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="app-btn" style="background: #fef2f2; color: #ef4444; border: 1px solid #fee2e2; padding: 0.5rem; border-radius: 8px; aspect-ratio: 1/1; justify-content: center;">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
            
            {{-- Card Footer --}}
            <div style="padding: 0.6rem 1.25rem; background: #fcfcfd; border-top: 1px solid #f1f5f9; font-size: 0.6rem; color: #94a3b8; font-weight: 700;">
                <i class="fa-solid fa-calendar"></i> Dibuat pada {{ $u->created_at->format('d M Y') }}
            </div>
        </div>
        @endforeach
    </div>
@endif

{{-- ─── Form Tambah Modul (Modern Form Layout) ─── --}}
<div style="max-width: 1000px; margin: 3rem 0;">
    <div id="formContainer" class="premium-card animate-slide-up" style="display:none; background: #ffffff; border: 1px solid #eef2f6; box-shadow: 0 20px 50px rgba(0,0,0,0.05); border-radius: 24px; padding: 2rem;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:2rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div style="background: #eff6ff; color: #3b82f6; width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; border: 1px solid #dbeafe;">
                    <i class="fa-solid fa-folder-plus"></i>
                </div>
                <h3 style="margin:0; font-weight:800; color:#1e293b; font-size: 1.15rem; letter-spacing: -0.01em;">Buat Modul Ujian Baru</h3>
            </div>
            <button onclick="toggleForm()" style="background: #f8fafc; border: 1px solid #e2e8f0; cursor: pointer; color: #94a3b8; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; transition: all 0.2s;" onmouseover="this.style.background='#f1f5f9'">✕</button>
        </div>
    
        <form action="{{ route('admin.ujian.store') }}" method="POST">
            @csrf
            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap:1.5rem;">
                <div class="form-group">
                    <label class="form-label" style="font-weight:700; color:#64748b; font-size:0.75rem; text-transform:uppercase; letter-spacing:0.02em;">Nama/Judul Modul</label>
                    <input type="text" name="judul" class="form-control" style="background:#f8fafc; border-color:#e2e8f0; height:45px;" placeholder="Contoh: Seleksi Jalur Prestasi 2024" required>
                </div>
                <div class="form-group">
                    <label class="form-label" style="font-weight:700; color:#64748b; font-size:0.75rem; text-transform:uppercase; letter-spacing:0.02em;">Tentukan Jurusan</label>
                    <select name="jurusan_id" class="form-control" style="background:#f8fafc; border-color:#e2e8f0; height:45px; font-weight:600;" required>
                        <option value="">-- Pilih Jurusan --</option>
                        @foreach($jurusans as $j)
                            <option value="{{ $j->id }}">{{ $j->nama }}</option>
                        @endforeach
                    </select>
                    <small style="color:#94a3b8; font-size:.65rem; display: block; margin-top: 6px; font-weight:600;">Modul ini hanya akan diakses siswa jurusan terpilih.</small>
                </div>
                <div class="form-group">
                    <label class="form-label" style="font-weight:700; color:#64748b; font-size:0.75rem; text-transform:uppercase; letter-spacing:0.02em;">Durasi Ujian (Menit)</label>
                    <input type="number" name="durasi_menit" class="form-control" style="background:#f8fafc; border-color:#e2e8f0; height:45px;" value="{{ $settings['cbt_durasi_default'] ?? 60 }}" required>
                </div>
            </div>
    
            <div style="background:#f8fafc; padding:1.25rem; border-radius:18px; border:1px solid #eef2f6; margin-top:2rem; display:flex; gap:3rem; flex-wrap:wrap;">
                <label style="display:flex; align-items:center; gap:0.75rem; cursor:pointer; font-weight:700; font-size:.85rem; color:#475569;">
                    <input type="checkbox" name="acak_soal" value="1" style="width:18px; height:18px; accent-color:#3b82f6;"> Acak Urutan Soal
                </label>
                <label style="display:flex; align-items:center; gap:0.75rem; cursor:pointer; font-weight:700; font-size:.85rem; color:#475569;">
                    <input type="checkbox" name="acak_jawaban" value="1" style="width:18px; height:18px; accent-color:#3b82f6;"> Acak Urutan Jawaban
                </label>
            </div>
    
            <div style="display:flex; justify-content:flex-end; gap:1rem; margin-top:2.5rem;">
                <button type="button" onclick="toggleForm()" class="app-btn" style="background: #f1f5f9; color: #64748b; padding: 0.75rem 2rem; border-radius: 12px; font-weight:700;">Batal</button>
                <button type="submit" class="app-btn" style="background: #6366f1; color: white; padding: 0.75rem 2.5rem; border-radius: 12px; box-shadow: 0 4px 15px rgba(99,102,241,0.3); font-weight:700;">
                    <i class="fa-solid fa-rocket" style="margin-right:8px;"></i> Simpan Modul Baru
                </button>
            </div>
        </form>
    </div>
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
