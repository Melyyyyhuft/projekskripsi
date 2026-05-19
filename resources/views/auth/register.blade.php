@extends('layouts.app')
@section('title', 'Daftar Siswa')

@section('content')
<style>
    /* Jika input tidak kosong dan isinya tidak sesuai format/pattern, beri warna merah */
    input.form-control:not(:placeholder-shown):invalid {
        border-color: #ef4444 !important;
        background-color: #fef2f2 !important;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.2);
    }
    
    /* Jika input tidak kosong dan isinya sudah benar, beri warna hijau */
    input.form-control:not(:placeholder-shown):valid {
        border-color: #22c55e !important;
        background-color: #f0fdf4 !important;
    }
</style>
<div style="min-height: 100vh; display: flex; align-items: center; justify-content: center; background: url('https://images.unsplash.com/photo-1522202176988-66273c2fd55f?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80') center/cover; padding: 2rem 1rem;">
    <div style="position: absolute; inset: 0; background: linear-gradient(135deg, rgba(14, 165, 233, 0.9), rgba(79, 70, 229, 0.9)); z-index: 1;"></div>
    
    <div class="glass-card" style="width: 100%; max-width: 500px; z-index: 2; position: relative;">
        <h2 style="text-align: center; font-size: 2rem; margin-bottom: 0.5rem; background: linear-gradient(to right, var(--primary), var(--secondary)); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Buat Akun Baru</h2>
        <p style="text-align: center; color: var(--gray-text); margin-bottom: 2rem;">Bergabunglah menjadi calon siswa berprestasi.</p>
        
        @if ($errors->any())
            <div style="background: #fee2e2; color: #ef4444; padding: 1rem; border-radius: var(--radius-sm); margin-bottom: 1.5rem; font-weight: 500;">
                <ul style="padding-left: 1rem;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ url('/register') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label" for="name">Nama Lengkap</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required placeholder="Sesuai Ijazah / Akta" pattern="[a-zA-Z\s]+" title="Nama hanya boleh berisi huruf dan spasi">
            </div>

            <div class="form-group">
                <label class="form-label" for="email">E-mail Aktif</label>
                <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required placeholder="Contoh: siswa@gmail.com" pattern="[a-zA-Z][a-zA-Z0-9._]*@gmail\.com$" title="Email harus diawali huruf dan menggunakan domain @gmail.com">
            </div>
            
            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <input type="password" name="password" id="password" class="form-control" required placeholder="Minimal 8 Karakter">
            </div>

            <div class="form-group">
                <label class="form-label" for="password_confirmation">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required placeholder="Ulangi Password">
            </div>
            
            <button type="submit" class="btn-primary" style="width: 100%; margin-top: 1rem; font-size: 1.125rem;">Daftar Akun</button>
        </form>
        
        <div style="text-align: center; margin-top: 2rem; color: var(--gray-text);">
            Sudah punya akun? <a href="{{ url('/login') }}" style="color: var(--primary); font-weight: 600;">Masuk di sini</a>
        </div>
    </div>
</div>
@endsection
