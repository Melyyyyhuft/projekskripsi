@extends('layouts.app')
@section('title', 'Lupa Password')

@section('content')
<div class="auth-wrapper" style="min-height: 80vh; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); padding: 2rem;">
    <div class="auth-card" style="background: white; border-radius: 16px; padding: 2.5rem; width: 100%; max-width: 450px; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05), 0 20px 48px rgba(0, 0, 0, 0.05); border: 1px solid #f1f5f9; text-align: center;">

        <div style="width: 70px; height: 70px; background: linear-gradient(135deg, #eff6ff, #dbeafe); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; color: var(--primary); font-size: 1.8rem; box-shadow: 0 4px 10px rgba(59, 130, 246, 0.1);">
            <i class="fa-solid fa-lock"></i>
        </div>

        <h2 style="font-size: 1.75rem; font-weight: 800; color: #0f172a; margin-bottom: 0.75rem;">Lupa Password</h2>
        <p style="color: #64748b; font-size: 0.95rem; line-height: 1.6; margin-bottom: 2rem;">
            Apabila Anda lupa password, silakan hubungi<br>
            Admin PPDB melalui kontak resmi sekolah<br>
            untuk melakukan reset password akun.
        </p>

        {{-- Kontak WhatsApp --}}
        <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; padding: 1.25rem 1.5rem; margin-bottom: 1rem; text-align: left; display: flex; align-items: center; gap: 1rem;">
            <div style="width: 44px; height: 44px; background: #22c55e; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; color: white; font-size: 1.2rem;">
                <i class="fa-brands fa-whatsapp"></i>
            </div>
            <div>
                <div style="font-size: 0.8rem; color: #15803d; font-weight: 600; margin-bottom: 0.2rem;">📞 WhatsApp</div>
                <div style="font-size: 1rem; font-weight: 700; color: #166534;">0812-3456-7890</div>
            </div>
        </div>

        {{-- Kontak Email --}}
        <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 12px; padding: 1.25rem 1.5rem; margin-bottom: 2rem; text-align: left; display: flex; align-items: center; gap: 1rem;">
            <div style="width: 44px; height: 44px; background: var(--primary, #3b82f6); border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; color: white; font-size: 1.1rem;">
                <i class="fa-solid fa-envelope"></i>
            </div>
            <div>
                <div style="font-size: 0.8rem; color: #1d4ed8; font-weight: 600; margin-bottom: 0.2rem;">✉️ Email</div>
                <div style="font-size: 0.95rem; font-weight: 700; color: #1e3a8a;">admin@ppdbsekolah.sch.id</div>
            </div>
        </div>

        {{-- Catatan datang langsung --}}
        <div style="background: #fffbeb; border: 1px solid #fde68a; border-radius: 12px; padding: 1.1rem 1.25rem; margin-bottom: 2rem; text-align: left;">
            <p style="margin: 0; color: #92400e; font-size: 0.88rem; line-height: 1.6;">
                <strong>Belum mendapatkan respons?</strong><br>
                Silakan datang langsung ke sekolah agar<br>
                proses reset password dapat dilakukan lebih cepat.
            </p>
        </div>

        <a href="{{ route('login') }}" style="display: inline-block; width: 100%; padding: 0.85rem; font-size: 1rem; font-weight: 700; border-radius: 8px; background: linear-gradient(135deg, var(--primary, #3b82f6), #4f46e5); color: white; text-decoration: none; transition: all 0.3s ease; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.25); text-align: center;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(59, 130, 246, 0.35)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(59, 130, 246, 0.25)';">
            <i class="fa-solid fa-arrow-left" style="margin-right: 0.4rem;"></i> Kembali ke Login
        </a>
    </div>
</div>


@endsection
