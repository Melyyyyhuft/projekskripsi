@extends('layouts.siswa')
@section('title', 'Profil Saya')

@section('content')
<style>
    /* ── Profile Page Styles ── */
    .profile-page { max-width: 1100px; margin: 0 auto; }

    .profile-grid {
        display: grid;
        grid-template-columns: 380px 1fr;
        gap: 1.5rem;
        align-items: start;
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

    /* PPDB Card */
    .ppdb-info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
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

    /* Responsive */
    @media (max-width: 860px) {
        .profile-grid {
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

    <div class="profile-grid">

        {{-- ═══ LEFT: Profile Card ═══ --}}
        <div style="display:flex;flex-direction:column;gap:1.5rem;">

            {{-- Avatar & Info Card --}}
            <div class="profile-card anim-in anim-d1">
                <div class="profile-avatar-section">
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

                    {{-- Hidden photo form --}}
                    <form action="{{ route('siswa.profile.photo') }}" method="POST" enctype="multipart/form-data" id="photoForm">
                        @csrf
                        @method('PUT')
                        <input type="file" id="photoInput" name="foto" accept="image/jpeg,image/png,image/webp"
                               style="display:none;" onchange="document.getElementById('photoForm').submit();">
                    </form>

                    <p class="profile-user-name">{{ $user->name }}</p>
                    <span class="profile-user-role">
                        <i class="fa-solid fa-graduation-cap"></i> Calon Siswa PPDB
                    </span>
                    <p style="font-size:.72rem;color:#94a3b8;margin:.75rem 0 0;cursor:pointer;" onclick="document.getElementById('photoInput').click()">
                        <i class="fa-solid fa-camera"></i> Klik untuk ganti foto (JPG/PNG/WebP, maks 2MB)
                    </p>
                    @if($errors->has('foto'))
                        <p style="color:#ef4444;font-size:.75rem;margin:.35rem 0 0;font-weight:600;">{{ $errors->first('foto') }}</p>
                    @endif
                </div>

                {{-- Info rows --}}
                <div class="profile-info-row">
                    <div class="profile-info-icon" style="background:#eff6ff;color:#3b82f6;"><i class="fa-solid fa-user"></i></div>
                    <div>
                        <p class="profile-info-label">Nama Lengkap</p>
                        <p class="profile-info-value">{{ $user->name }}</p>
                    </div>
                </div>
                <div class="profile-info-row">
                    <div class="profile-info-icon" style="background:#f0fdf4;color:#059669;"><i class="fa-solid fa-envelope"></i></div>
                    <div style="min-width:0;">
                        <p class="profile-info-label">Email</p>
                        <p class="profile-info-value">{{ $user->email }}</p>
                    </div>
                </div>
                <div class="profile-info-row">
                    <div class="profile-info-icon" style="background:#fef9c3;color:#b45309;"><i class="fa-solid fa-phone"></i></div>
                    <div>
                        <p class="profile-info-label">Nomor Telepon</p>
                        <p class="profile-info-value">{{ $user->no_hp ?: ($pendaftaran->no_hp ?? '—') }}</p>
                    </div>
                </div>
                <div class="profile-info-row">
                    <div class="profile-info-icon" style="background:#fce7f3;color:#db2777;"><i class="fa-solid fa-location-dot"></i></div>
                    <div>
                        <p class="profile-info-label">Alamat</p>
                        <p class="profile-info-value">{{ $user->alamat ?: ($pendaftaran->alamat ?? '—') }}</p>
                    </div>
                </div>
                <div class="profile-info-row">
                    <div class="profile-info-icon" style="background:#f5f3ff;color:#7c3aed;"><i class="fa-solid fa-calendar-check"></i></div>
                    <div>
                        <p class="profile-info-label">Tanggal Bergabung</p>
                        <p class="profile-info-value">{{ $user->created_at->format('d F Y') }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══ RIGHT: Forms ═══ --}}
        <div class="profile-right-stack">

            {{-- Edit Profile --}}
            <div class="profile-card anim-in anim-d2">
                <h3 class="profile-card-title">
                    <i style="background:#eff6ff;color:#3b82f6;"><span class="fa-solid fa-pen-to-square"></span></i>
                    Edit Profil
                </h3>

                <form action="{{ route('siswa.profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="pf-form-group">
                        <label class="pf-form-label">Nama Lengkap</label>
                        <input type="text" name="name" class="pf-form-input @error('name') is-invalid @enderror"
                               value="{{ old('name', $user->name) }}" placeholder="Masukkan nama lengkap">
                        @error('name') <div class="pf-form-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="pf-form-group">
                        <label class="pf-form-label">Email</label>
                        <input type="email" name="email" class="pf-form-input @error('email') is-invalid @enderror"
                               value="{{ old('email', $user->email) }}" placeholder="contoh@email.com">
                        @error('email') <div class="pf-form-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="pf-form-group">
                        <label class="pf-form-label">Nomor Telepon</label>
                        <input type="text" name="no_hp" class="pf-form-input @error('no_hp') is-invalid @enderror"
                               value="{{ old('no_hp', $user->no_hp ?: ($pendaftaran->no_hp ?? '')) }}" placeholder="08xxxxxxxxxx">
                        @error('no_hp') <div class="pf-form-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="pf-form-group">
                        <label class="pf-form-label">Alamat</label>
                        <textarea name="alamat" class="pf-form-input pf-form-textarea @error('alamat') is-invalid @enderror"
                                  placeholder="Masukkan alamat lengkap">{{ old('alamat', $user->alamat ?: ($pendaftaran->alamat ?? '')) }}</textarea>
                        @error('alamat') <div class="pf-form-error">{{ $message }}</div> @enderror
                    </div>

                    <button type="submit" class="pf-btn-gradient">
                        <i class="fa-solid fa-check"></i> Simpan Perubahan
                    </button>
                </form>
            </div>

            {{-- Change Password --}}
            <div class="profile-card anim-in anim-d3">
                <h3 class="profile-card-title">
                    <i style="background:#fef2f2;color:#ef4444;"><span class="fa-solid fa-shield-halved"></span></i>
                    Ubah Password
                </h3>

                <form action="{{ route('siswa.profile.password') }}" method="POST">
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

                    <button type="submit" class="pf-btn-gradient" style="background:linear-gradient(135deg, #ef4444, #f97316);">
                        <i class="fa-solid fa-lock"></i> Perbarui Password
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- ═══ PPDB Information Card ═══ --}}
    <div class="profile-card anim-in anim-d3" style="margin-top:1.5rem;">
        <h3 class="profile-card-title">
            <i style="background:#f0fdf4;color:#059669;"><span class="fa-solid fa-file-invoice"></span></i>
            Informasi Pendaftaran PPDB
        </h3>

        @if($pendaftaran)
        <div class="ppdb-info-grid">
            <div class="ppdb-info-item">
                <p class="ppdb-label"><i class="fa-solid fa-hashtag" style="margin-right:.3rem;"></i> No. Pendaftaran</p>
                <p class="ppdb-value" style="color:var(--primary);">{{ $pendaftaran->nomor_pendaftaran ?? '—' }}</p>
            </div>
            <div class="ppdb-info-item">
                <p class="ppdb-label"><i class="fa-solid fa-id-card" style="margin-right:.3rem;"></i> NISN</p>
                <p class="ppdb-value">{{ $pendaftaran->nisn ?? '—' }}</p>
            </div>
            <div class="ppdb-info-item">
                <p class="ppdb-label"><i class="fa-solid fa-building-columns" style="margin-right:.3rem;"></i> Jurusan Dipilih</p>
                <p class="ppdb-value">{{ $pendaftaran->jurusan->nama ?? '—' }}</p>
            </div>
            <div class="ppdb-info-item">
                <p class="ppdb-label"><i class="fa-solid fa-school" style="margin-right:.3rem;"></i> Asal Sekolah</p>
                <p class="ppdb-value">{{ $pendaftaran->asal_sekolah ?? '—' }}</p>
            </div>
            <div class="ppdb-info-item">
                <p class="ppdb-label"><i class="fa-solid fa-circle-check" style="margin-right:.3rem;"></i> Status Verifikasi</p>
                @php
                    $statusMap = [
                        'draft'                 => ['label' => 'Draft',                 'bg' => '#f1f5f9', 'color' => '#64748b', 'icon' => '📝'],
                        'menunggu_verifikasi'   => ['label' => 'Menunggu Verifikasi',   'bg' => '#fef9c3', 'color' => '#92400e', 'icon' => '⏳'],
                        'perlu_revisi'          => ['label' => 'Perlu Revisi',          'bg' => '#ffedd5', 'color' => '#c2410c', 'icon' => '🔄'],
                        'lolos_admin'           => ['label' => 'Lolos Administrasi',    'bg' => '#dcfce7', 'color' => '#166534', 'icon' => '✅'],
                        'ditolak_admin'         => ['label' => 'Ditolak',               'bg' => '#fee2e2', 'color' => '#991b1b', 'icon' => '❌'],
                        'sudah_ujian'           => ['label' => 'Sudah Ujian',           'bg' => '#dbeafe', 'color' => '#1e40af', 'icon' => '📋'],
                        'siap_finalisasi'       => ['label' => 'Proses Seleksi',        'bg' => '#e0e7ff', 'color' => '#3730a3', 'icon' => '⚙️'],
                        'siap_diumumkan'        => ['label' => 'Siap Diumumkan',        'bg' => '#dcfce7', 'color' => '#166534', 'icon' => '📢'],
                        'gugur'                 => ['label' => 'Gugur',                 'bg' => '#fee2e2', 'color' => '#991b1b', 'icon' => '🚫'],
                    ];
                    $st = $statusMap[$pendaftaran->status] ?? ['label' => ucfirst(str_replace('_',' ',$pendaftaran->status)), 'bg' => '#f1f5f9', 'color' => '#64748b', 'icon' => '📄'];
                @endphp
                <p class="ppdb-value">
                    <span class="ppdb-status-badge" style="background:{{ $st['bg'] }};color:{{ $st['color'] }};">
                        {{ $st['icon'] }} {{ $st['label'] }}
                    </span>
                </p>
            </div>
        </div>
        @else
        <div style="text-align:center;padding:2rem 1rem;color:#94a3b8;">
            <div style="font-size:2.5rem;margin-bottom:.75rem;">📋</div>
            <p style="font-weight:700;color:#64748b;margin:0 0 .35rem;">Belum Ada Pendaftaran</p>
            <p style="font-size:.85rem;margin:0 0 1rem;">Anda belum mengisi form pendaftaran PPDB.</p>
            <a href="{{ route('siswa.pendaftaran') }}" class="pf-btn-gradient" style="display:inline-flex;width:auto;">
                <i class="fa-solid fa-plus"></i> Daftar Sekarang
            </a>
        </div>
        @endif
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
        showToast('{{ session("success_password") }}', 'success');
    @endif
    @if(session('success_photo'))
        showToast('{{ session("success_photo") }}', 'success');
    @endif
</script>
@endsection
