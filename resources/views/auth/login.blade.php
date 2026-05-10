@extends('layouts.app')
@section('title', 'Login')

@section('content')
<div class="auth-wrapper">
    <div class="auth-card">
        <div class="text-center mb-4">
            <h2 style="font-size: 2rem; margin-bottom: 0.5rem;" class="text-gradient">Selamat Datang!</h2>
            <p style="color: var(--gray-text);">Silakan masuk untuk melanjutkan.</p>
        </div>
        
        @if(session('error'))
            <div style="background: rgba(254, 226, 226, 0.9); color: #dc2626; padding: 1rem; border-radius: var(--radius-sm); margin-bottom: 1.5rem; text-align: center; font-weight: 500; border: 1px solid #fca5a5;">
                <i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}
            </div>
        @endif

        <form action="{{ url('/login') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label" for="email">E-mail</label>
                <div style="position: relative;">
                    <i class="fa-solid fa-envelope" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--gray-text);"></i>
                    <input type="email" name="email" id="email" class="form-control" style="padding-left: 2.75rem;" required placeholder="Masukkan email anda">
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <div style="position: relative;">
                    <i class="fa-solid fa-lock" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--gray-text);"></i>
                    <input type="password" name="password" id="password" class="form-control" style="padding-left: 2.75rem;" required placeholder="Masukkan password">
                </div>
            </div>
            
            <button type="submit" class="btn-primary" style="width: 100%; margin-top: 1rem; font-size: 1.125rem;">
                Masuk <i class="fa-solid fa-right-to-bracket"></i>
            </button>
        </form>
        
        <div style="text-align: center; margin-top: 2rem; color: var(--gray-text);">
            Belum punya akun? <a href="{{ url('/register') }}" style="color: var(--primary); font-weight: 600;">Daftar Sekarang</a>
        </div>
    </div>
</div>
@endsection
