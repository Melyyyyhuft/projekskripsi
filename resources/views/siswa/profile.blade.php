@extends('layouts.siswa')
@section('title', 'Profil Saya')

@section('content')
<style>
    /* ── Profile Page Styles ── */
    .profile-page { max-width: 1100px; margin: 0 auto; }

    .profile-grid {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .profile-card {
        background: rgba(255,255,255,.75);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255,255,255,.6);
        border-radius: 20px;
        box-shadow: 0 8px 32px rgba(0,0,0,.06), 0 2px 8px rgba(0,0,0,.03);
        padding: 2rem;
        transition: transform .3s ease, box-shadow .3s ease;
    }
    .profile-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 40px rgba(0,0,0,.08), 0 4px 12px rgba(0,0,0,.04);
    }

    .profile-card-title {
        font-size: 1rem;
        font-weight: 700;
        color: #0f172a;
        margin: 0 0 1.5rem;
        display: flex;
        align-items: center;
        gap: .6rem;
    }
    .profile-card-title i {
        width: 32px; height: 32px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: .8rem;
    }

    /* Avatar Section */
    .profile-avatar-section {
        text-align: center;
        padding-bottom: 1.75rem;
        border-bottom: 1px solid #f1f5f9;
        margin-bottom: 1.75rem;
    }
    .profile-avatar-wrap {
        position: relative;
        display: inline-block;
        margin-bottom: 1rem;
    }
    .profile-avatar-img {
        width: 110px; height: 110px;
        border-radius: 50%;
        background: linear-gradient(135deg, #6366f1, #3b82f6, #8b5cf6);
        padding: 3px;
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 8px 24px rgba(99,102,241,.25);
        transition: transform .3s ease, box-shadow .3s ease;
    }
    .profile-avatar-img:hover {
        transform: scale(1.05);
        box-shadow: 0 12px 32px rgba(99,102,241,.35);
    }
    .profile-avatar-img .avatar-inner {
        width: 100%; height: 100%;
        border-radius: 50%;
        background: white;
        display: flex; align-items: center; justify-content: center;
        font-size: 2.5rem;
        font-weight: 900;
        color: #6366f1;
        overflow: hidden;
    }
    .profile-avatar-img .avatar-inner img {
        width: 100%; height: 100%;
        object-fit: cover;
        border-radius: 50%;
    }
    .profile-avatar-camera {
        position: absolute; bottom: 4px; right: 4px;
        width: 34px; height: 34px;
        background: linear-gradient(135deg, #3b82f6, #8b5cf6);
        border: 3px solid white;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        color: white; font-size: .75rem;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(59,130,246,.3);
        transition: transform .3s ease;
    }
    .profile-avatar-camera:hover { transform: scale(1.15); }

    .profile-user-name {
        font-size: 1.25rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 .25rem;
    }
    .profile-user-role {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        background: linear-gradient(135deg, #eff6ff, #f5f3ff);
        color: #6366f1;
        font-size: .75rem;
        font-weight: 700;
        padding: .3rem .85rem;
        border-radius: 999px;
        border: 1px solid rgba(99,102,241,.15);
    }

    /* Info Rows */
    .profile-info-row {
        display: flex;
        align-items: flex-start;
        gap: .875rem;
        padding: .75rem 0;
        border-bottom: 1px solid #f8fafc;
    }
    .profile-info-row:last-child { border-bottom: none; }
    .profile-info-icon {
        width: 36px; height: 36px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: .8rem;
        flex-shrink: 0;
    }
    .profile-info-label {
        font-size: .7rem;
        font-weight: 700;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: .04em;
        margin: 0 0 .15rem;
    }
    .profile-info-value {
        font-size: .9rem;
        font-weight: 600;
        color: #1e293b;
        margin: 0;
        word-break: break-word;
    }

    /* Form Styles */
    .pf-form-group {
        margin-bottom: 1.25rem;
    }
    .pf-form-label {
        display: block;
        font-size: .78rem;
        font-weight: 700;
        color: #475569;
        margin-bottom: .4rem;
    }
    .pf-form-input {
        width: 100%;
        padding: .7rem 1rem;
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        font-size: .9rem;
        color: #1e293b;
        background: rgba(248,250,252,.8);
        transition: border-color .3s, box-shadow .3s;
        outline: none;
        font-family: inherit;
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
        margin-top: .3rem;
        font-weight: 600;
    }
    .pf-form-textarea {
        resize: vertical;
        min-height: 80px;
    }

    /* Password Toggle */
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
        font-size: .9rem;
        padding: .25rem;
        transition: color .3s;
    }
    .pf-password-toggle:hover { color: #6366f1; }

    /* Buttons */
    .pf-btn-gradient {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .5rem;
        padding: .75rem 1.5rem;
        background: linear-gradient(135deg, #3b82f6, #8b5cf6);
        color: white;
        border: none;
        border-radius: 12px;
        font-size: .875rem;
        font-weight: 700;
        cursor: pointer;
        transition: all .3s ease;
        box-shadow: 0 4px 14px rgba(99,102,241,.25);
        width: 100%;
    }
    .pf-btn-gradient:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(99,102,241,.35);
    }
    .pf-btn-gradient:active {
        transform: translateY(0);
    }

    /* PPDB Card — landscape 5 items */
    .ppdb-info-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 1rem;
    }
    .ppdb-info-item {
        background: rgba(248,250,252,.6);
        border: 1px solid #f1f5f9;
        border-radius: 14px;
        padding: 1rem;
        transition: all .3s ease;
    }
    .ppdb-info-item:hover {
        background: rgba(248,250,252,.9);
        border-color: #e2e8f0;
    }
    .ppdb-info-item .ppdb-label {
        font-size: .7rem;
        font-weight: 700;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: .04em;
        margin: 0 0 .35rem;
    }
    .ppdb-info-item .ppdb-value {
        font-size: .9rem;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
    }

    .ppdb-status-badge {
        display: inline-flex;
        align-items: center;
        gap: .3rem;
        padding: .25rem .7rem;
        border-radius: 999px;
        font-size: .78rem;
        font-weight: 700;
    }

    /* Right column stack */
    .profile-right-stack {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    /* Toast notification */
    .pf-toast {
        position: fixed;
        top: 1.5rem;
        right: 1.5rem;
        z-index: 99999;
        padding: 1rem 1.5rem;
        border-radius: 14px;
        font-size: .875rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: .6rem;
        box-shadow: 0 12px 40px rgba(0,0,0,.12);
        animation: toastSlideIn .4s ease forwards;
        max-width: 400px;
    }
    .pf-toast-success {
        background: linear-gradient(135deg, #ecfdf5, #d1fae5);
        color: #065f46;
        border: 1px solid #a7f3d0;
    }
    .pf-toast-error {
        background: linear-gradient(135deg, #fef2f2, #fee2e2);
        color: #991b1b;
        border: 1px solid #fca5a5;
    }
    .pf-toast-close {
        background: none;
        border: none;
        color: inherit;
        cursor: pointer;
        font-size: 1.1rem;
        opacity: .6;
        margin-left: .5rem;
    }
    .pf-toast-close:hover { opacity: 1; }

    @keyframes toastSlideIn {
        from { opacity: 0; transform: translateX(60px); }
        to { opacity: 1; transform: translateX(0); }
    }
    @keyframes toastSlideOut {
        from { opacity: 1; transform: translateX(0); }
        to { opacity: 0; transform: translateX(60px); }
    }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(16px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .anim-in { animation: fadeInUp .5s ease forwards; }
    .anim-d1 { animation-delay: .1s; opacity: 0; }
    .anim-d2 { animation-delay: .2s; opacity: 0; }
    .anim-d3 { animation-delay: .3s; opacity: 0; }

    /* Password card landscape */
    .pw-form-row {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr auto;
        gap: 1rem;
        align-items: end;
    }

    /* Responsive */
    @media (max-width: 900px) {
        .profile-avatar-landscape {
            flex-direction: column;
            text-align: center;
        }
        .profile-avatar-landscape .profile-avatar-section {
            border-right: none !important;
            border-bottom: 1px solid #f1f5f9;
            padding-right: 0 !important;
            padding-bottom: 1.5rem !important;
            margin-right: 0 !important;
            margin-bottom: 1.5rem !important;
        }
        .ppdb-info-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        .pw-form-row {
            grid-template-columns: 1fr;
        }
    }
    @media (max-width: 600px) {
        .ppdb-info-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="profile-page">

    {{-- Header --}}
    <div style="margin-bottom:1.75rem;" class="anim-in">
        <h1 style="font-size:1.5rem;font-weight:800;color:#0f172a;margin:0 0 .35rem;">Profil Saya</h1>
        <p style="color:#64748b;font-size:.9rem;margin:0;">Kelola informasi profil, foto, dan keamanan akun Anda.</p>
    </div>

    {{-- ═══ Dual Info Card: Account & PPDB — Premium Unified Design ═══ --}}
    <div class="profile-card anim-in anim-d1" style="padding: 0; overflow: hidden;">
        <div style="display: flex; flex-wrap: wrap;">
            
            {{-- Left/Top Sidebar: Avatar & Quick Status --}}
            <div style="flex: 1; min-width: 300px; padding: 2.5rem; background: linear-gradient(135deg, rgba(99, 102, 241, 0.05) 0%, rgba(59, 130, 241, 0.05) 100%); border-right: 1px solid #f1f5f9;">
                <div style="text-align: center;">
                    <div class="profile-avatar-wrap">
                        <div class="profile-avatar-img">
                            <div class="avatar-inner">
                                @if($user->foto)
                                    <img src="{{ asset('storage/' . $user->foto) }}" alt="Foto Profil">
                                @else
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                @endif
                            </div>
                        </div>
                        <div class="profile-avatar-camera" onclick="document.getElementById('photoInput').click()" title="Ganti foto profil">
                            <i class="fa-solid fa-camera"></i>
                        </div>
                    </div>

                    <form action="{{ route('siswa.profile.photo') }}" method="POST" enctype="multipart/form-data" id="photoForm">
                        @csrf
                        @method('PUT')
                        <input type="file" id="photoInput" name="foto" accept="image/jpeg,image/png,image/webp"
                            style="display:none;" onchange="confirmPhotoUpload()">
                    </form>

                    <h2 class="profile-user-name" style="margin-top: 1rem; font-size: 1.5rem;">{{ $user->name }}</h2>
                    <span class="profile-user-role" style="margin-bottom: 1.5rem;">
                        <i class="fa-solid fa-graduation-cap"></i> Calon Siswa PPDB
                    </span>

                    <div style="margin-top: 2rem; padding: 1.25rem; background: white; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: var(--shadow-sm);">
                        <p style="font-size: 0.65rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 0.75rem;">Status Pendaftaran</p>
                        @if($pendaftaran)
                            @php
                                $statusMap = [
                                    'draft'                 => ['label' => 'Draft', 'color' => '#64748b', 'bg' => '#f1f5f9', 'icon' => '📝'],
                                    'menunggu_verifikasi'   => ['label' => 'Menunggu Verifikasi', 'color' => '#92400e', 'bg' => '#fef9c3', 'icon' => '⏳'],
                                    'perlu_revisi'          => ['label' => 'Perlu Revisi', 'color' => '#c2410c', 'bg' => '#ffedd5', 'icon' => '🔄'],
                                    'lolos_admin'           => ['label' => 'Lolos Administrasi', 'color' => '#166534', 'bg' => '#dcfce7', 'icon' => '✅'],
                                    'ditolak_admin'         => ['label' => 'Ditolak', 'color' => '#991b1b', 'bg' => '#fee2e2', 'icon' => '❌'],
                                    'sudah_ujian'           => ['label' => 'Sudah Ujian', 'color' => '#1e40af', 'bg' => '#dbeafe', 'icon' => '📋'],
                                    'siap_finalisasi'       => ['label' => 'Proses Seleksi', 'color' => '#3730a3', 'bg' => '#e0e7ff', 'icon' => '⚙️'],
                                    'siap_diumumkan'        => ['label' => 'Siap Diumumkan', 'color' => '#166534', 'bg' => '#dcfce7', 'icon' => '📢'],
                                    'gugur'                 => ['label' => 'Gugur', 'color' => '#991b1b', 'bg' => '#fee2e2', 'icon' => '🚫'],
                                ];
                                $st = $statusMap[$pendaftaran->status] ?? ['label' => ucfirst(str_replace('_',' ',$pendaftaran->status)), 'color' => '#64748b', 'bg' => '#f1f5f9', 'icon' => '📄'];
                            @endphp
                            <span class="ppdb-status-badge" style="background:{{ $st['bg'] }}; color:{{ $st['color'] }}; font-size: 0.85rem; padding: 0.5rem 1rem; width: 100%; justify-content: center;">
                                {{ $st['icon'] }} {{ $st['label'] }}
                            </span>
                        @else
                            <span class="ppdb-status-badge" style="background: #f1f5f9; color: #64748b; font-size: 0.85rem; padding: 0.5rem 1rem; width: 100%; justify-content: center;">
                                📄 Belum Terdaftar
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Right Body: Information Details --}}
            <div style="flex: 2; min-width: 350px; padding: 2.5rem;">
                {{-- Category: Personal Information --}}
                <div style="margin-bottom: 2.5rem;">
                    <h3 style="font-size: 0.9rem; font-weight: 800; color: #1e293b; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fa-solid fa-user-gear" style="color: #6366f1;"></i> INFORMASI PRIBADI
                    </h3>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem;">
                        <div>
                            <p class="profile-info-label">Email Aktif</p>
                            <p class="profile-info-value">{{ $user->email }}</p>
                        </div>
                        <div>
                            <p class="profile-info-label">Nomor WhatsApp</p>
                            <p class="profile-info-value">{{ $user->no_hp ?: ($pendaftaran->no_hp ?? '—') }}</p>
                        </div>
                        <div style="grid-column: 1 / -1;">
                            <p class="profile-info-label">Alamat Lengkap</p>
                            <p class="profile-info-value">{{ $user->alamat ?: ($pendaftaran->alamat ?? '—') }}</p>
                        </div>
                    </div>
                </div>

                {{-- Horizontal Divider --}}
                <div style="height: 1px; background: #f1f5f9; margin-bottom: 2.5rem;"></div>

                {{-- Category: Registration Information --}}
                <div>
                    <h3 style="font-size: 0.9rem; font-weight: 800; color: #1e293b; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fa-solid fa-file-invoice" style="color: #059669;"></i> DETAIL PENDAFTARAN PPDB
                    </h3>
                    @if($pendaftaran)
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1.5rem;">
                            <div>
                                <p class="profile-info-label">ID Pendaftaran</p>
                                <p class="profile-info-value" style="color: var(--primary);">{{ $pendaftaran->nomor_pendaftaran ?? '—' }}</p>
                            </div>
                            <div>
                                <p class="profile-info-label">NISN Siswa</p>
                                <p class="profile-info-value">{{ $pendaftaran->nisn ?? '—' }}</p>
                            </div>
                            <div>
                                <p class="profile-info-label">Pilihan Jurusan</p>
                                <p class="profile-info-value">{{ $pendaftaran->jurusan->nama ?? '—' }}</p>
                            </div>
                            <div>
                                <p class="profile-info-label">Sekolah Asal</p>
                                <p class="profile-info-value">{{ $pendaftaran->asal_sekolah ?? '—' }}</p>
                            </div>
                        </div>
                    @else
                        <div style="padding: 2rem; background: #f8fafc; border-radius: 12px; text-align: center; border: 1px dashed #cbd5e1;">
                            <p style="color: #64748b; font-size: 0.85rem; margin-bottom: 1rem;">Anda belum melengkapi pendaftaran PPDB.</p>
                            <a href="{{ route('siswa.pendaftaran') }}" class="pf-btn-gradient" style="width: auto; padding: 0.6rem 1.5rem;">
                                <i class="fa-solid fa-plus"></i> Lengkapi Sekarang
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ═══ BOTTOM: Ubah Password — Landscape ═══ --}}
    <div class="profile-card anim-in anim-d3" style="margin-top:1.5rem;">
        <h3 class="profile-card-title">
            <i style="background:#fef2f2;color:#ef4444;"><span class="fa-solid fa-shield-halved"></span></i>
            Ubah Password
        </h3>

        <form action="{{ route('siswa.profile.password') }}" method="POST">
            @csrf
            @method('PUT')
            <div class="pw-form-row">
                <div class="pf-form-group" style="margin-bottom:0;">
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

                <div class="pf-form-group" style="margin-bottom:0;">
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

                <div class="pf-form-group" style="margin-bottom:0;">
                    <label class="pf-form-label">Konfirmasi Password Baru</label>
                    <div class="pf-password-wrap">
                        <input type="password" name="password_confirmation" class="pf-form-input"
                               placeholder="Ketik ulang password baru" id="pw_confirm">
                        <button type="button" class="pf-password-toggle" onclick="togglePw('pw_confirm', this)">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div>
                    <button type="submit" class="pf-btn-gradient" style="background:linear-gradient(135deg, #ef4444, #f97316); white-space:nowrap;">
                        <i class="fa-solid fa-lock"></i> Perbarui Password
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Toggle password visibility
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

    // Handle photo confirmation
    function confirmPhotoUpload() {
        const fileInput = document.getElementById('photoInput');
        if (fileInput.files && fileInput.files[0]) {
            Swal.fire({
                title: 'Perbarui Foto Profil?',
                text: "Apakah Anda yakin ingin mengganti foto profil saat ini?",
                icon: 'question',
                showCancelButton: false,
                showCloseButton: true,
                confirmButtonColor: '#3b82f6',
                confirmButtonText: 'Ya',
                reverseButtons: true,
                background: '#ffffff',
                borderRadius: '20px'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading state
                    Swal.fire({
                        title: 'Mengunggah...',
                        text: 'Tunggu sebentar, sedang memproses foto Anda.',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        willOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    document.getElementById('photoForm').submit();
                } else {
                    fileInput.value = ''; // Reset input if cancelled
                }
            });
        }
    }

    // Toast notifications
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `pf-toast pf-toast-${type}`;
        toast.innerHTML = `
            <i class="fa-solid ${type === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation'}"></i>
            <span>${message}</span>
            <button class="pf-toast-close" onclick="this.parentElement.remove()">×</button>
        `;
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.style.animation = 'toastSlideOut .4s ease forwards';
            setTimeout(() => toast.remove(), 400);
        }, 4000);
    }

    // Show toasts from session
    @if(session('success_profile'))
        showToast('{{ session("success_profile") }}', 'success');
    @endif
    @if(session('success_password'))
        Swal.fire({
            title: 'Berhasil!',
            text: '{{ session("success_password") }}',
            icon: 'success',
            showConfirmButton: false,
            timer: 2000,
            background: '#ffffff',
            borderRadius: '20px'
        });
    @endif
    @if(session('success_photo'))
        showToast('{{ session("success_photo") }}', 'success');
    @endif
    @if(session('success_foto'))
        showToast('{{ session("success_foto") }}', 'success');
    @endif
</script>
@endsection
