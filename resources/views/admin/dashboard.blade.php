@extends('layouts.admin')
@section('title', 'Dashboard Admin')

@section('content')

@php
    // Hitung tambahan statistik dari data yang sudah ada
    $totalSudahUjian   = \App\Models\Pendaftaran::where('status', 'sudah_ujian')->count()
                       + \App\Models\Pendaftaran::where('status', 'siap_finalisasi')->count()
                       + \App\Models\Pendaftaran::where('status', 'siap_diumumkan')->count();
    $totalLolosAdmin   = \App\Models\Pendaftaran::where('status', 'lolos_admin')->count();
@endphp

{{-- ─── Greeting ─── --}}
<div style="margin-bottom:2rem;">
    <h1 style="font-size:1.6rem;font-weight:800;color:#0f172a;margin:0 0 .25rem;">Selamat Datang, {{ Auth::user()->name }} 👋</h1>
    <p style="color:#64748b;font-size:.9rem;margin:0;">Pantau dan kelola proses PPDB dari dashboard ini.</p>
</div>

{{-- ─── Stats Cards ─── --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:1.25rem;margin-bottom:2rem;">

    {{-- Card 1 --}}
    <a href="{{ route('admin.pendaftaran.index', ['tab' => 'arsip']) }}" style="text-decoration:none; display:block; background:white;border-radius:16px;padding:1.5rem;box-shadow:0 1px 3px rgba(0,0,0,.06),0 4px 16px rgba(99,102,241,.06);border:1px solid #f1f5f9;position:relative;overflow:hidden;transition:transform 0.2s,box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-5px)';this.style.boxShadow='0 10px 25px rgba(99,102,241,0.15)'" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 1px 3px rgba(0,0,0,.06),0 4px 16px rgba(99,102,241,.06)'">
        <div style="position:absolute;top:0;right:0;width:80px;height:80px;background:linear-gradient(135deg,#6366f1,#8b5cf6);border-radius:0 16px 0 100%;opacity:.08;"></div>
        <div style="width:40px;height:40px;border-radius:10px;background:linear-gradient(135deg,#6366f1,#8b5cf6);display:flex;align-items:center;justify-content:center;margin-bottom:1rem;font-size:1.1rem;">👥</div>
        <p style="color:#64748b;font-size:.8rem;font-weight:600;margin:0 0 .35rem;text-transform:uppercase;letter-spacing:.05em;">Total Pendaftar</p>
        <p style="font-size:2.2rem;font-weight:900;color:#0f172a;margin:0;line-height:1;">{{ $totalPendaftar }}</p>
        <p style="font-size:.75rem;color:#94a3b8;margin:.35rem 0 0;">Seluruh pendaftar PPDB</p>
    </a>

    {{-- Card 2 --}}
    <a href="{{ route('admin.pendaftaran.index', ['tab' => 'baru', 'status' => 'menunggu_verifikasi']) }}" style="text-decoration:none; display:block; background:white;border-radius:16px;padding:1.5rem;box-shadow:0 1px 3px rgba(0,0,0,.06),0 4px 16px rgba(245,158,11,.06);border:1px solid #f1f5f9;position:relative;overflow:hidden;transition:transform 0.2s,box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-5px)';this.style.boxShadow='0 10px 25px rgba(245,158,11,0.15)'" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 1px 3px rgba(0,0,0,.06),0 4px 16px rgba(245,158,11,.06)'">
        <div style="position:absolute;top:0;right:0;width:80px;height:80px;background:linear-gradient(135deg,#f59e0b,#fbbf24);border-radius:0 16px 0 100%;opacity:.08;"></div>
        <div style="width:40px;height:40px;border-radius:10px;background:linear-gradient(135deg,#f59e0b,#fbbf24);display:flex;align-items:center;justify-content:center;margin-bottom:1rem;font-size:1.1rem;">⏳</div>
        <p style="color:#64748b;font-size:.8rem;font-weight:600;margin:0 0 .35rem;text-transform:uppercase;letter-spacing:.05em;">Menunggu Verifikasi</p>
        <p style="font-size:2.2rem;font-weight:900;color:#0f172a;margin:0;line-height:1;">{{ $menungguVerifikasi }}</p>
        <p style="font-size:.75rem;color:#94a3b8;margin:.35rem 0 0;">Perlu tindakan admin</p>
    </a>

    {{-- Card 3 --}}
    <a href="{{ route('admin.pendaftaran.index', ['tab' => 'arsip', 'status' => 'lolos_admin']) }}" style="text-decoration:none; display:block; background:white;border-radius:16px;padding:1.5rem;box-shadow:0 1px 3px rgba(0,0,0,.06),0 4px 16px rgba(16,185,129,.06);border:1px solid #f1f5f9;position:relative;overflow:hidden;transition:transform 0.2s,box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-5px)';this.style.boxShadow='0 10px 25px rgba(16,185,129,0.15)'" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 1px 3px rgba(0,0,0,.06),0 4px 16px rgba(16,185,129,.06)'">
        <div style="position:absolute;top:0;right:0;width:80px;height:80px;background:linear-gradient(135deg,#10b981,#34d399);border-radius:0 16px 0 100%;opacity:.08;"></div>
        <div style="width:40px;height:40px;border-radius:10px;background:linear-gradient(135deg,#10b981,#34d399);display:flex;align-items:center;justify-content:center;margin-bottom:1rem;font-size:1.1rem;">✅</div>
        <p style="color:#64748b;font-size:.8rem;font-weight:600;margin:0 0 .35rem;text-transform:uppercase;letter-spacing:.05em;">Lolos Administrasi</p>
        <p style="font-size:2.2rem;font-weight:900;color:#0f172a;margin:0;line-height:1;">{{ $totalLolosAdmin }}</p>
        <p style="font-size:.75rem;color:#94a3b8;margin:.35rem 0 0;">Siap mengikuti ujian</p>
    </a>

    {{-- Card 4 --}}
    <a href="{{ route('admin.pendaftaran.index', ['tab' => 'arsip', 'status' => 'sudah_ujian']) }}" style="text-decoration:none; display:block; background:white;border-radius:16px;padding:1.5rem;box-shadow:0 1px 3px rgba(0,0,0,.06),0 4px 16px rgba(59,130,246,.06);border:1px solid #f1f5f9;position:relative;overflow:hidden;transition:transform 0.2s,box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-5px)';this.style.boxShadow='0 10px 25px rgba(59,130,246,0.15)'" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 1px 3px rgba(0,0,0,.06),0 4px 16px rgba(59,130,246,.06)'">
        <div style="position:absolute;top:0;right:0;width:80px;height:80px;background:linear-gradient(135deg,#3b82f6,#60a5fa);border-radius:0 16px 0 100%;opacity:.08;"></div>
        <div style="width:40px;height:40px;border-radius:10px;background:linear-gradient(135deg,#3b82f6,#60a5fa);display:flex;align-items:center;justify-content:center;margin-bottom:1rem;font-size:1.1rem;">📝</div>
        <p style="color:#64748b;font-size:.8rem;font-weight:600;margin:0 0 .35rem;text-transform:uppercase;letter-spacing:.05em;">Sudah Ujian</p>
        <p style="font-size:2.2rem;font-weight:900;color:#0f172a;margin:0;line-height:1;">{{ $totalSudahUjian }}</p>
        <p style="font-size:.75rem;color:#94a3b8;margin:.35rem 0 0;">Selesai CBT</p>
    </a>

</div>

{{-- ─── Main Grid ─── --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;margin-bottom:1.25rem;">

    {{-- Kuota Jurusan --}}
    <div style="background:white;border-radius:16px;padding:1.5rem;box-shadow:0 1px 3px rgba(0,0,0,.06),0 4px 16px rgba(0,0,0,.04);border:1px solid #f1f5f9;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.25rem;">
            <div>
                <h3 style="font-size:1rem;font-weight:700;color:#0f172a;margin:0 0 .2rem;">Kuota per Jurusan</h3>
                <p style="font-size:.78rem;color:#94a3b8;margin:0;">Status penerimaan jurusan</p>
            </div>
            <div style="width:36px;height:36px;border-radius:10px;background:#f0fdf4;display:flex;align-items:center;justify-content:center;font-size:1rem;">🏫</div>
        </div>

        @foreach($jurusans as $jurusan)
        @php
            $persen = $jurusan->kuota > 0 ? min(100, round(($jurusan->diterima_count / $jurusan->kuota) * 100)) : 0;
            $barColor = $persen >= 90 ? '#ef4444' : ($persen >= 60 ? '#f59e0b' : '#10b981');
        @endphp
        <div style="margin-bottom:1rem;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.35rem;">
                <span style="font-size:.85rem;font-weight:600;color:#334155;">{{ $jurusan->nama }}</span>
                <span style="font-size:.78rem;color:#64748b;">{{ $jurusan->diterima_count }} / {{ $jurusan->kuota }}
                    @if($jurusan->sisa_kuota <= 0)
                        <span style="color:#ef4444;font-weight:700;"> • Penuh</span>
                    @else
                        <span style="color:#10b981;font-weight:600;"> • Sisa {{ $jurusan->sisa_kuota }}</span>
                    @endif
                </span>
            </div>
            <div style="height:6px;background:#f1f5f9;border-radius:999px;overflow:hidden;">
                <div style="height:100%;width:{{ $persen }}%;background:{{ $barColor }};border-radius:999px;transition:width .6s ease;"></div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Aksi Cepat --}}
    <div style="background:white;border-radius:16px;padding:1.5rem;box-shadow:0 1px 3px rgba(0,0,0,.06),0 4px 16px rgba(0,0,0,.04);border:1px solid #f1f5f9;">
        <div style="margin-bottom:1.25rem;">
            <h3 style="font-size:1rem;font-weight:700;color:#0f172a;margin:0 0 .2rem;">Aksi Cepat</h3>
            <p style="font-size:.78rem;color:#94a3b8;margin:0;">Shortcut menu utama admin</p>
        </div>

        @php
            $menus = [
                ['href' => route('admin.pendaftaran.index'), 'ikon' => '📋', 'label' => 'Data Pendaftaran',  'sub' => 'Verifikasi berkas siswa',    'bg' => '#eff6ff', 'c' => '#3b82f6'],
                ['href' => route('admin.ujian.index'),       'ikon' => '🖥️', 'label' => 'Modul Ujian',        'sub' => 'Kelola soal & ujian CBT',    'bg' => '#fdf4ff', 'c' => '#9333ea'],
                ['href' => route('admin.seleksi.index'),     'ikon' => '⚡', 'label' => 'Proses Seleksi',     'sub' => 'Jalankan & finalisasi',      'bg' => '#fff7ed', 'c' => '#f59e0b'],
                ['href' => route('admin.bank_soal.index'),   'ikon' => '📚', 'label' => 'Bank Soal',          'sub' => 'Import & kelola soal',       'bg' => '#f0fdf4', 'c' => '#10b981'],
                ['href' => route('admin.pengaturan.index'),  'ikon' => '⚙️', 'label' => 'Pengaturan',         'sub' => 'Konfigurasi sistem PPDB',    'bg' => '#f8fafc', 'c' => '#475569'],
            ];
        @endphp

        <div style="display:flex;flex-direction:column;gap:.6rem;">
            @foreach($menus as $m)
            <a href="{{ $m['href'] }}" style="display:flex;align-items:center;gap:.85rem;padding:.75rem 1rem;border-radius:12px;background:{{ $m['bg'] }};text-decoration:none;border:1px solid transparent;transition:all .2s;" onmouseover="this.style.transform='translateX(4px)';this.style.boxShadow='0 2px 8px rgba(0,0,0,.08)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
                <div style="width:36px;height:36px;border-radius:8px;background:white;display:flex;align-items:center;justify-content:center;font-size:1rem;box-shadow:0 1px 4px rgba(0,0,0,.08);flex-shrink:0;">{{ $m['ikon'] }}</div>
                <div>
                    <p style="margin:0;font-size:.875rem;font-weight:700;color:#0f172a;">{{ $m['label'] }}</p>
                    <p style="margin:0;font-size:.75rem;color:#64748b;">{{ $m['sub'] }}</p>
                </div>
                <div style="margin-left:auto;color:#cbd5e1;font-size:.85rem;">›</div>
            </a>
            @endforeach
        </div>
    </div>
</div>

{{-- ─── Pendaftar Terbaru ─── --}}
<div style="background:white;border-radius:16px;padding:1.5rem;box-shadow:0 1px 3px rgba(0,0,0,.06),0 4px 16px rgba(0,0,0,.04);border:1px solid #f1f5f9;">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.25rem;">
        <div>
            <h3 style="font-size:1rem;font-weight:700;color:#0f172a;margin:0 0 .2rem;">Pendaftar Terbaru</h3>
            <p style="font-size:.78rem;color:#94a3b8;margin:0;">5 pendaftaran paling baru</p>
        </div>
        <a href="{{ route('admin.pendaftaran.index') }}" style="font-size:.8rem;font-weight:600;color:#6366f1;text-decoration:none;padding:.4rem .9rem;background:#eef2ff;border-radius:999px;">Lihat Semua →</a>
    </div>

    @php
        $statusConfig = [
            'menunggu_verifikasi' => ['label' => 'Menunggu',  'bg' => '#fef3c7', 'color' => '#92400e'],
            'lolos_admin'         => ['label' => 'Lolos',     'bg' => '#d1fae5', 'color' => '#065f46'],
            'ditolak_admin'       => ['label' => 'Ditolak',   'bg' => '#fee2e2', 'color' => '#991b1b'],
            'sudah_ujian'         => ['label' => 'Sdh Ujian', 'bg' => '#dbeafe', 'color' => '#1e40af'],
            'siap_finalisasi'     => ['label' => 'Diproses',  'bg' => '#ede9fe', 'color' => '#5b21b6'],
            'siap_diumumkan'      => ['label' => 'Diumumkan', 'bg' => '#cffafe', 'color' => '#164e63'],
            'gugur'               => ['label' => 'Gugur',     'bg' => '#fce7f3', 'color' => '#9d174d'],
        ];
    @endphp

    <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="border-bottom:2px solid #f1f5f9;">
                    <th style="text-align:left;padding:.6rem 1rem;font-size:.72rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;">Nama Siswa</th>
                    <th style="text-align:left;padding:.6rem 1rem;font-size:.72rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;">Asal Sekolah</th>
                    <th style="text-align:left;padding:.6rem 1rem;font-size:.72rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;">Jurusan</th>
                    <th style="text-align:center;padding:.6rem 1rem;font-size:.72rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;">Status</th>
                    <th style="text-align:center;padding:.6rem 1rem;font-size:.72rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pendaftarTerbaru as $p)
                @php $sc = $statusConfig[$p->status] ?? ['label' => ucfirst($p->status), 'bg' => '#f1f5f9', 'color' => '#475569']; @endphp
                <tr style="border-bottom:1px solid #f8fafc;transition:background .15s;" onmouseover="this.style.background='#fafbff'" onmouseout="this.style.background=''">
                    <td style="padding:.85rem 1rem;">
                        <div style="display:flex;align-items:center;gap:.6rem;">
                            <div style="width:30px;height:30px;border-radius:50%;background:linear-gradient(135deg,#6366f1,#8b5cf6);color:white;font-size:.75rem;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                {{ strtoupper(substr($p->user->name, 0, 1)) }}
                            </div>
                            <span style="font-size:.875rem;font-weight:600;color:#0f172a;">{{ $p->user->name }}</span>
                        </div>
                    </td>
                    <td style="padding:.85rem 1rem;font-size:.85rem;color:#475569;">{{ $p->asal_sekolah }}</td>
                    <td style="padding:.85rem 1rem;font-size:.85rem;color:#475569;">{{ $p->jurusan->nama }}</td>
                    <td style="padding:.85rem 1rem;text-align:center;">
                        <span style="display:inline-block;padding:.25rem .75rem;border-radius:999px;font-size:.72rem;font-weight:700;background:{{ $sc['bg'] }};color:{{ $sc['color'] }};">{{ $sc['label'] }}</span>
                    </td>
                    <td style="padding:.85rem 1rem;text-align:center;">
                        <a href="{{ route('admin.pendaftaran.show', $p->id) }}" style="display:inline-flex;align-items:center;gap:.3rem;font-size:.78rem;font-weight:600;color:#6366f1;text-decoration:none;padding:.3rem .7rem;border-radius:8px;background:#eef2ff;">
                            Detail →
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="padding:3rem;text-align:center;color:#94a3b8;font-size:.875rem;">Belum ada data pendaftar.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
