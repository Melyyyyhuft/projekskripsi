@extends('layouts.admin')
@section('title', 'Ganti Password')

@section('content')
<style>
    .pf-form-group {
        margin-bottom: 1.25rem;
    }
    .pf-form-label {
        display: block;
        font-size: .85rem;
        font-weight: 700;
        color: #475569;
        margin-bottom: .5rem;
    }
    .pf-form-input {
        width: 100%;
        padding: .7rem 1rem;
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        font-size: .95rem;
        color: #1e293b;
        background: #f8fafc;
        outline: none;
        transition: border-color .3s, box-shadow .3s;
    }
    .pf-form-input:focus {
        border-color: #818cf8;
        box-shadow: 0 0 0 3px rgba(129,140,248,.15);
    }
    .pf-form-input.is-invalid {
        border-color: #f87171;
        box-shadow: 0 0 0 3px rgba(248,113,113,.1);
    }
    .pf-form-error {
        font-size: .75rem;
        color: #ef4444;
        margin-top: .35rem;
        font-weight: 600;
    }
    .pf-password-wrap {
        position: relative;
    }
    .pf-password-wrap .pf-form-input {
        padding-right: 2.75rem;
    }
    .pf-password-toggle {
        position: absolute;
        right: .75rem;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #94a3b8;
        cursor: pointer;
        font-size: 1rem;
        padding: .25rem;
        transition: color .3s;
    }
    .pf-password-toggle:hover { color: #6366f1; }
    .pf-btn-gradient {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .5rem;
        padding: .85rem 1.5rem;
        background: linear-gradient(135deg, #ef4444, #f97316);
        color: white;
        border: none;
        border-radius: 12px;
        font-size: .95rem;
        font-weight: 700;
        cursor: pointer;
        transition: all .3s ease;
        box-shadow: 0 4px 14px rgba(239,68,68,.25);
    }
    .pf-btn-gradient:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(239,68,68,.35);
    }
</style>

<div style="max-width:600px; margin:0 auto; padding-top:2rem;">
    <div style="margin-bottom:1.5rem;">
        <h1 style="font-size:1.75rem;font-weight:800;color:var(--dark);margin:0 0 .5rem;">Pengaturan Akun</h1>
        <p style="color:var(--gray-text);font-size:.95rem;margin:0;">Di sini administrator dapat mengganti kata sandi demi keamanan.</p>
    </div>

    <div class="glass-card" style="padding:2rem;">
        <h3 style="font-size:1.15rem;font-weight:800;color:var(--dark);margin:0 0 1.5rem;display:flex;align-items:center;gap:.6rem;">
            <div style="width:36px;height:36px;border-radius:10px;background:#fef2f2;color:#ef4444;display:flex;align-items:center;justify-content:center;">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            Ubah Password
        </h3>

        <form action="{{ route('admin.profile.password') }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="pf-form-group">
                <label class="pf-form-label">Password Lama</label>
                <div class="pf-password-wrap">
                    <input type="password" name="current_password" class="pf-form-input @error('current_password') is-invalid @enderror"
                           placeholder="Masukkan password saat ini" id="pw_old">
                    <button type="button" class="pf-password-toggle" onclick="togglePw('pw_old', this)">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                </div>
                @error('current_password') <div class="pf-form-error">{{ $message }}</div> @enderror
            </div>

            <div class="pf-form-group">
                <label class="pf-form-label">Password Baru</label>
                <div class="pf-password-wrap">
                    <input type="password" name="password" class="pf-form-input @error('password') is-invalid @enderror"
                           placeholder="Minimal 8 karakter" id="pw_new">
                    <button type="button" class="pf-password-toggle" onclick="togglePw('pw_new', this)">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                </div>
                @error('password') <div class="pf-form-error">{{ $message }}</div> @enderror
            </div>

            <div class="pf-form-group">
                <label class="pf-form-label">Konfirmasi Password Baru</label>
                <div class="pf-password-wrap">
                    <input type="password" name="password_confirmation" class="pf-form-input"
                           placeholder="Ketik ulang password baru" id="pw_confirm">
                    <button type="button" class="pf-password-toggle" onclick="togglePw('pw_confirm', this)">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                </div>
            </div>

            <div style="margin-top:2rem;">
                <button type="submit" class="pf-btn-gradient" style="width:100%;">
                    <i class="fa-solid fa-lock"></i> Perbarui Password
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function togglePw(id, btn) {
        const input = document.getElementById(id);
        const icon = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'fa-solid fa-eye-slash';
        } else {
            input.type = 'password';
            icon.className = 'fa-solid fa-eye';
        }
    }

    @if(session('success'))
        document.addEventListener('DOMContentLoaded', () => {
            Swal.fire({
                title: 'Berhasil!',
                text: '{{ session("success") }}',
                icon: 'success',
                showConfirmButton: false,
                timer: 2000,
                background: localStorage.getItem('theme') === 'dark' ? '#1e293b' : '#ffffff',
                color: localStorage.getItem('theme') === 'dark' ? '#f8fafc' : '#0f172a',
                borderRadius: '20px'
            });
        });
    @endif
</script>
@endsection
