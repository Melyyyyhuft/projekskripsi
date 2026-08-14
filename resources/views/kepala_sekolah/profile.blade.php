@extends('layouts.kepala-sekolah')
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
        color: var(--gray-text);
        margin-bottom: .5rem;
    }
    .pf-form-input {
        width: 100%;
        padding: .75rem 1rem;
        border: 1.5px solid var(--border-color);
        border-radius: 12px;
        font-size: .95rem;
        color: var(--dark);
        background: var(--light-bg);
        outline: none;
        transition: border-color .3s, box-shadow .3s;
        box-sizing: border-box;
    }
    .pf-form-input:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99,102,241,.15);
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
    }
    .pf-password-toggle:hover { color: #6366f1; }
</style>

<div style="max-width: 600px; margin: 0 auto;">
    @if(session('success'))
        <div style="background:#dcfce7; border:1px solid #86efac; color:#15803d; padding:1rem 1.25rem; border-radius:14px; margin-bottom:1.5rem; font-weight:700; display:flex; align-items:center; gap:0.6rem;">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="background:#fee2e2; border:1px solid #fca5a5; color:#b91c1c; padding:1rem 1.25rem; border-radius:14px; margin-bottom:1.5rem; font-weight:700; display:flex; align-items:center; gap:0.6rem;">
            <i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}
        </div>
    @endif

    @if(isset($errors) && $errors->any())
        <div style="background:#fee2e2; border:1px solid #fca5a5; color:#b91c1c; padding:1rem 1.25rem; border-radius:14px; margin-bottom:1.5rem; font-size:0.85rem;">
            <ul style="margin:0; padding-left:1.25rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div style="background:var(--white); border:1px solid var(--border-color); border-radius:24px; padding:2.5rem; box-shadow:var(--shadow-sm);">
        <div style="display:flex; align-items:center; gap:1rem; margin-bottom:2rem; padding-bottom:1.5rem; border-bottom:1px solid var(--border-color);">
            <div style="width:54px; height:54px; border-radius:16px; background:linear-gradient(135deg, #4f46e5, #7c3aed); color:white; display:flex; align-items:center; justify-content:center; font-size:1.5rem;">
                <i class="fa-solid fa-user-shield"></i>
            </div>
            <div>
                <h3 style="margin:0 0 0.25rem; font-size:1.2rem; font-weight:900; color:var(--dark);">Akun Kepala Sekolah</h3>
                <p style="margin:0; font-size:0.85rem; color:var(--gray-text);">{{ $user->email }}</p>
            </div>
        </div>

        <form action="{{ route('kepala_sekolah.profile.password') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="pf-form-group">
                <label class="pf-form-label">Password Saat Ini</label>
                <div class="pf-password-wrap">
                    <input type="password" name="current_password" id="cur_pass" class="pf-form-input" required placeholder="Masukkan password saat ini">
                    <button type="button" class="pf-password-toggle" onclick="togglePass('cur_pass', this)">
                        <i class="fa-regular fa-eye"></i>
                    </button>
                </div>
            </div>

            <div class="pf-form-group">
                <label class="pf-form-label">Password Baru</label>
                <div class="pf-password-wrap">
                    <input type="password" name="password" id="new_pass" class="pf-form-input" required placeholder="Minimal 8 karakter">
                    <button type="button" class="pf-password-toggle" onclick="togglePass('new_pass', this)">
                        <i class="fa-regular fa-eye"></i>
                    </button>
                </div>
            </div>

            <div class="pf-form-group">
                <label class="pf-form-label">Konfirmasi Password Baru</label>
                <div class="pf-password-wrap">
                    <input type="password" name="password_confirmation" id="conf_pass" class="pf-form-input" required placeholder="Ulangi password baru">
                    <button type="button" class="pf-password-toggle" onclick="togglePass('conf_pass', this)">
                        <i class="fa-regular fa-eye"></i>
                    </button>
                </div>
            </div>

            <div style="margin-top:2rem;">
                <button type="submit" class="btn-primary" style="width:100%; padding:0.85rem; font-size:0.95rem; font-weight:800; justify-content:center;">
                    <i class="fa-solid fa-save"></i> Perbarui Password
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function togglePass(inputId, btn) {
        const input = document.getElementById(inputId);
        const icon = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'fa-regular fa-eye-slash';
        } else {
            input.type = 'password';
            icon.className = 'fa-regular fa-eye';
        }
    }
</script>
@endsection
