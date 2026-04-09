@extends('layouts.app')
@section('title', 'Login')

@section('content')
<div style="min-height: 100vh; display: flex; align-items: center; justify-content: center; background: url('https://images.unsplash.com/photo-1497366216548-37526070297c?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80') center/cover; padding-top: 80px;">
    <div style="position: absolute; inset: 0; background: linear-gradient(135deg, rgba(79, 70, 229, 0.9), rgba(14, 165, 233, 0.9)); z-index: 1;"></div>
    
    <div class="glass-card" style="width: 100%; max-width: 450px; z-index: 2; position: relative;">
        <h2 style="text-align: center; font-size: 2rem; margin-bottom: 2rem; background: linear-gradient(to right, var(--primary), var(--secondary)); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Selamat Datang!</h2>
        
        @if(session('error'))
            <div style="background: #fee2e2; color: #ef4444; padding: 1rem; border-radius: var(--radius-sm); margin-bottom: 1.5rem; text-align: center; font-weight: 500;">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ url('/login') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label" for="email">E-mail</label>
                <input type="email" name="email" id="email" class="form-control" required placeholder="Masukkan email anda">
            </div>
            
            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <input type="password" name="password" id="password" class="form-control" required placeholder="Masukkan password">
            </div>
            
            <button type="submit" class="btn-primary" style="width: 100%; margin-top: 1rem; font-size: 1.125rem;">Masuk</button>
        </form>
        
        <div style="text-align: center; margin-top: 2rem; color: var(--gray-text);">
            Belum punya akun? <a href="{{ url('/register') }}" style="color: var(--primary); font-weight: 600;">Daftar Sekarang</a>
        </div>
    </div>
</div>
@endsection
