@extends('layouts.app')
@section('title', 'Lupa Password')

@section('content')
<div class="auth-wrapper" style="min-height: 80vh; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); padding: 2rem;">
    <div class="auth-card" style="background: white; border-radius: 16px; padding: 2.5rem; width: 100%; max-width: 450px; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05), 0 20px 48px rgba(0, 0, 0, 0.05); border: 1px solid #f1f5f9; text-align: center;">
        
        <div style="width: 70px; height: 70px; background: linear-gradient(135deg, #eff6ff, #dbeafe); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; color: var(--primary); font-size: 1.8rem; box-shadow: 0 4px 10px rgba(59, 130, 246, 0.1);">
            <i class="fa-solid fa-lock"></i>
        </div>

        <h2 style="font-size: 1.75rem; font-weight: 800; color: #0f172a; margin-bottom: 0.5rem;">Lupa Password</h2>
        <p style="color: #64748b; font-size: 0.95rem; line-height: 1.5; margin-bottom: 2rem;">Masukkan email atau nomor HP yang terdaftar untuk mengatur ulang password akun PPDB Anda.</p>

        <!-- Alert placeholder -->
        <div id="reset-alert" style="display: none; background: #d1fae5; color: #059669; padding: 0.8rem 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-weight: 600; font-size: 0.9rem; text-align: left; border: 1px solid #a7f3d0; animation: slideDown 0.3s ease-out;">
            <i class="fa-solid fa-circle-check" style="margin-right: 0.5rem;"></i> Link reset password berhasil dikirim.
        </div>

        <form id="forgotPasswordForm" onsubmit="handleReset(event)" style="text-align: left;">
            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label class="form-label" for="contact" style="font-weight: 600; color: #334155; margin-bottom: 0.5rem; display: block;">Email / Nomor HP</label>
                <div style="position: relative;">
                    <i class="fa-solid fa-envelope-open-text" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
                    <input type="text" id="contact" class="form-control" style="padding-left: 2.75rem; border-radius: 8px; border: 1px solid #cbd5e1; padding-top: 0.75rem; padding-bottom: 0.75rem; width: 100%; transition: border-color 0.2s;" required placeholder="Contoh: user@email.com atau 0812...">
                </div>
            </div>
            
            <button type="submit" class="btn-primary" id="btn-reset" style="width: 100%; padding: 0.85rem; font-size: 1.05rem; font-weight: 700; border-radius: 8px; border: none; background: linear-gradient(135deg, var(--primary), #4f46e5); color: white; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.25);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(59, 130, 246, 0.35)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(59, 130, 246, 0.25)';">
                Kirim Link Reset <i class="fa-solid fa-paper-plane" style="margin-left: 0.5rem;"></i>
            </button>
        </form>
        
        <div style="margin-top: 1.75rem;">
            <a href="{{ route('login') }}" style="color: #64748b; font-size: 0.9rem; font-weight: 600; text-decoration: none; transition: color 0.2s ease;" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='#64748b'">
                <i class="fa-solid fa-arrow-left" style="margin-right: 0.25rem;"></i> Kembali ke Login
            </a>
        </div>
    </div>
</div>

<style>
    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<script>
    function handleReset(e) {
        e.preventDefault();
        
        const btn = document.getElementById('btn-reset');
        const alertBox = document.getElementById('reset-alert');
        const input = document.getElementById('contact');
        
        // Simulasikan loading state
        btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Mengirim...';
        btn.style.opacity = '0.8';
        btn.disabled = true;
        
        setTimeout(() => {
            // Tampilkan alert sukses
            alertBox.style.display = 'block';
            
            // Kembalikan tombol ke semula
            btn.innerHTML = 'Kirim Link Reset <i class="fa-solid fa-paper-plane" style="margin-left: 0.5rem;"></i>';
            btn.style.opacity = '1';
            btn.disabled = false;
            
            // Bersihkan input form
            input.value = '';
        }, 1200);
    }
</script>
@endsection
