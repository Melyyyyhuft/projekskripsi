@extends('layouts.admin')
@section('title', 'Manajemen Pendaftaran')

@section('content')

{{-- ─── Page Header ─── --}}
<div style="margin-bottom:2rem;">
    <h1 style="font-size:1.5rem;font-weight:800;color:#0f172a;margin:0 0 .25rem;">Data Pendaftaran</h1>
    <p style="color:#64748b;font-size:.9rem;margin:0;">Verifikasi dan kelola berkas pendaftar calon siswa.</p>
</div>

@if(session('success'))
    <div style="background:#d1fae5;color:#059669;padding:.875rem 1.25rem;border-radius:12px;margin-bottom:1.5rem;font-weight:600;border:1px solid #a7f3d0;">
        ✅ {{ session('success') }}
    </div>
@endif

<div class="glass-card">
    {{-- ─── Toolbar: Tabs + Filter ─── --}}
    <div style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem;border-bottom:1px solid #f1f5f9;padding-bottom:1.25rem;">
        {{-- Tabs --}}
        <div style="display:flex;gap:.5rem;">
            <a href="{{ route('admin.pendaftaran.index', ['tab' => 'baru', 'status' => $filterStatus]) }}"
               style="padding:.55rem 1.25rem;border-radius:10px;font-weight:700;font-size:.875rem;text-decoration:none;transition:all .2s;
               {{ $tab == 'baru' ? 'background:linear-gradient(135deg,var(--primary),#6366f1);color:white;box-shadow:0 3px 10px rgba(59,130,246,.3);' : 'background:#f1f5f9;color:#475569;' }}">
                🆕 Baru Mendaftar
            </a>
            <a href="{{ route('admin.pendaftaran.index', ['tab' => 'arsip', 'status' => $filterStatus]) }}"
               style="padding:.55rem 1.25rem;border-radius:10px;font-weight:700;font-size:.875rem;text-decoration:none;transition:all .2s;
               {{ $tab == 'arsip' ? 'background:linear-gradient(135deg,var(--primary),#6366f1);color:white;box-shadow:0 3px 10px rgba(59,130,246,.3);' : 'background:#f1f5f9;color:#475569;' }}">
                📁 Arsip
            </a>
        </div>

        {{-- Filter Status --}}
        <form action="{{ route('admin.pendaftaran.index') }}" method="GET" style="display:flex;gap:.75rem;align-items:center;">
            <input type="hidden" name="tab" value="{{ $tab }}">
            
            <div style="position:relative;">
                <i class="fa-solid fa-magnifying-glass" style="position:absolute;left:.75rem;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:.8rem;"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, NISN, asal sekolah..." class="form-control" style="padding:.45rem .75rem .45rem 2rem;height:auto;font-size:.85rem;border-radius:8px;min-width:200px;">
                <button type="submit" style="display:none;"></button>
            </div>

            <label style="font-size:.8rem;font-weight:600;color:#475569;white-space:nowrap;">Filter Status:</label>
            <select name="status" class="form-control" style="padding:.45rem .75rem;height:auto;font-size:.85rem;border-radius:8px;" onchange="this.form.submit()">
                <option value="">Semua Status</option>
                <option value="menunggu_verifikasi" {{ $filterStatus == 'menunggu_verifikasi' ? 'selected' : '' }}>⏳ Pending</option>
                <option value="revisi" {{ $filterStatus == 'revisi' ? 'selected' : '' }}>⚠️ Revisi</option>
                <option value="lolos_admin" {{ $filterStatus == 'lolos_admin' ? 'selected' : '' }}>✅ Lolos Administrasi</option>
            </select>
        </form>
    </div>

    {{-- ─── Tabel ─── --}}
    <div style="overflow-x:auto;border-radius:12px;border:1px solid #e2e8f0;">
        <table style="width:100%;border-collapse:collapse;min-width:640px;">
            <thead>
                <tr style="background:#f8fafc;">
                    <th style="padding:.875rem 1rem;text-align:center;font-size:.75rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;border-bottom:1px solid #e2e8f0;width:40px;">No</th>
                    <th style="padding:.875rem 1rem;text-align:left;font-size:.75rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;border-bottom:1px solid #e2e8f0;">Peserta</th>
                    <th style="padding:.875rem 1rem;text-align:left;font-size:.75rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;border-bottom:1px solid #e2e8f0;">NISN</th>
                    <th style="padding:.875rem 1rem;text-align:left;font-size:.75rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;border-bottom:1px solid #e2e8f0;">Asal Sekolah</th>
                    <th style="padding:.875rem 1rem;text-align:center;font-size:.75rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;border-bottom:1px solid #e2e8f0;">Rapor</th>
                    <th style="padding:.875rem 1rem;text-align:left;font-size:.75rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;border-bottom:1px solid #e2e8f0;">Jurusan</th>
                    <th style="padding:.875rem 1rem;text-align:center;font-size:.75rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;border-bottom:1px solid #e2e8f0;">Status</th>
                    <th style="padding:.875rem 1rem;text-align:right;font-size:.75rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;border-bottom:1px solid #e2e8f0;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pendaftarans as $p)
                <tr style="border-bottom:1px solid #f1f5f9;transition:background .15s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                    <td style="padding:.875rem 1rem;text-align:center;font-size:.875rem;color:#64748b;font-weight:700;">{{ $loop->iteration }}</td>
                    <td style="padding:.875rem 1rem;">
                        <div style="font-weight:700;color:#0f172a;">{{ $p->user->name }}</div>
                        <div style="font-size:.75rem;color:#94a3b8;">{{ $p->user->email }}</div>
                    </td>
                    <td style="padding:.875rem 1rem;">
                        <span style="font-family:monospace;font-size:.875rem;color:#475569;">{{ $p->nisn }}</span>
                    </td>
                    <td style="padding:.875rem 1rem;font-size:.875rem;color:#475569;max-width:160px;">
                        {{ $p->asal_sekolah }}
                    </td>
                    <td style="padding:.875rem 1rem;text-align:center;">
                        <span style="background:#e0f2fe;color:var(--primary);padding:.3rem .75rem;border-radius:999px;font-size:.875rem;font-weight:700;">{{ $p->nilai_rapor }}</span>
                    </td>
                    <td style="padding:.875rem 1rem;font-size:.875rem;color:#475569;white-space:nowrap;">{{ $p->jurusan->nama }}</td>
                    <td style="padding:.875rem 1rem;text-align:center;">
                        @php
                            $isRevision = $p->status == 'menunggu_verifikasi' && $p->berkas->where('status_verifikasi', 'valid')->count() > 0;
                        @endphp

                        @if($isRevision)
                             <span style="background:#fef3c7;color:#d97706;padding:.3rem .75rem;border-radius:999px;font-size:.75rem;font-weight:700;">🔄 REVISI MASUK</span>
                        @elseif(in_array($p->status, ['menunggu_verifikasi', 'pending']))
                            <span style="background:#e0f2fe;color:#0284c7;padding:.3rem .75rem;border-radius:999px;font-size:.75rem;font-weight:700;">⏳ PENDING</span>
                        @elseif($p->status == 'revisi')
                            <span style="background:#fef3c7;color:#d97706;padding:.3rem .75rem;border-radius:999px;font-size:.75rem;font-weight:700;">⚠️ PERLU REVISI</span>
                        @elseif(in_array($p->status, ['lolos_admin', 'lolos_administrasi']))
                            <span style="background:#d1fae5;color:#059669;padding:.3rem .75rem;border-radius:999px;font-size:.75rem;font-weight:700;">✅ LOLOS</span>
                        @else
                            <span style="background:#f1f5f9;color:#64748b;padding:.3rem .75rem;border-radius:999px;font-size:.75rem;font-weight:600;">{{ strtoupper(str_replace('_', ' ', $p->status)) }}</span>
                        @endif
                    </td>
                    <td style="padding:.875rem 1rem;text-align:right;">
                        @if(in_array($p->status, ['menunggu_verifikasi', 'revisi']))
                            <a href="{{ route('admin.pendaftaran.show', $p->id) }}" style="background:linear-gradient(135deg,var(--primary),#6366f1);color:white;padding:.45rem 1rem;border-radius:8px;font-size:.8rem;font-weight:700;text-decoration:none;white-space:nowrap;">
                                Verifikasi →
                            </a>
                        @else
                            <a href="{{ route('admin.pendaftaran.show', $p->id) }}" style="background:#f1f5f9;color:#475569;padding:.45rem 1rem;border-radius:8px;font-size:.8rem;font-weight:600;text-decoration:none;white-space:nowrap;">
                                Lihat Detail
                            </a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="padding:3rem;text-align:center;color:#94a3b8;">
                        <div style="font-size:2rem;margin-bottom:.5rem;">📭</div>
                        Tidak ada data pendaftaran untuk ditampilkan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
