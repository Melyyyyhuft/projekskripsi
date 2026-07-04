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
<div class="auth-wrapper">
    <div class="auth-card">
        <div class="text-center mb-4">
            <h2 style="font-size: 2rem; margin-bottom: 0.5rem;" class="text-gradient">Buat Akun Baru</h2>
            <p style="color: var(--gray-text);">Bergabunglah menjadi calon siswa berprestasi.</p>
        </div>

        @if ($errors->any())
            <div style="background: rgba(254, 226, 226, 0.9); color: #dc2626; padding: 0.75rem 1rem; border-radius: var(--radius-sm); margin-bottom: 1rem; font-weight: 500; border: 1px solid #fca5a5;">
                <ul style="padding-left: 1rem; margin: 0;">
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

        <div style="text-align: center; margin-top: 1.5rem; color: var(--gray-text);">
            Sudah punya akun? <a href="{{ url('/login') }}" style="color: var(--primary); font-weight: 600;">Masuk di sini</a>
        </div>
    </div>
</div>
@endsection
