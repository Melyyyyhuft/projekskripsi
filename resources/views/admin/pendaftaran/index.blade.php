@extends('layouts.admin')
@section('title', 'Manajemen Pendaftaran')

@section('content')
<div class="glass-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h3 style="color: var(--primary); margin: 0;">Daftar Pendaftar</h3>
        
        <form action="{{ route('admin.pendaftaran.index') }}" method="GET" style="display: flex; gap: 1rem; align-items: center;">
            <input type="hidden" name="tab" value="{{ $tab }}">
            <select name="status" class="form-control" style="padding: 0.4rem 0.8rem; height: auto;" onchange="this.form.submit()">
                <option value="">Semua Status</option>
                <option value="menunggu_verifikasi" {{ $filterStatus == 'menunggu_verifikasi' ? 'selected' : '' }}>Belum Diverifikasi</option>
                <option value="revisi" {{ $filterStatus == 'revisi' ? 'selected' : '' }}>Revisi</option>
                <option value="lolos_admin" {{ $filterStatus == 'lolos_admin' ? 'selected' : '' }}>Lulus (Verifikasi)</option>
                <option value="ditolak_admin" {{ $filterStatus == 'ditolak_admin' ? 'selected' : '' }}>Tidak Lulus</option>
            </select>
        </form>
    </div>

    <!-- Tabs -->
    <div style="display: flex; gap: 1rem; margin-bottom: 1.5rem; border-bottom: 1px solid rgba(0,0,0,0.1); padding-bottom: 0.5rem;">
        <a href="{{ route('admin.pendaftaran.index', ['tab' => 'baru', 'status' => $filterStatus]) }}" style="font-weight: 600; padding: 0.5rem 1rem; border-radius: var(--radius-sm); {{ $tab == 'baru' ? 'background: var(--primary); color: white;' : 'color: var(--gray-text); text-decoration: none;' }}">
            Baru Mendaftar
        </a>
        <a href="{{ route('admin.pendaftaran.index', ['tab' => 'arsip', 'status' => $filterStatus]) }}" style="font-weight: 600; padding: 0.5rem 1rem; border-radius: var(--radius-sm); {{ $tab == 'arsip' ? 'background: var(--primary); color: white;' : 'color: var(--gray-text); text-decoration: none;' }}">
            Arsip / Sudah Diproses
        </a>
    </div>
    
    @if(session('success'))
        <div style="background: #d1fae5; color: #059669; padding: 1rem; border-radius: var(--radius-sm); margin-bottom: 1.5rem; font-weight: 500;">
            {{ session('success') }}
        </div>
    @endif

    <div class="table-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th>Peserta</th>
                    <th>NISN</th>
                    <th>Asal Sekolah</th>
                    <th>Nilai Rapor</th>
                    <th>Jurusan</th>
                    <th>Status</th>
                    <th>Aksi Verifikasi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pendaftarans as $p)
                <tr>
                    <td><strong>{{ $p->user->name }}</strong></td>
                    <td>{{ $p->nisn }}</td>
                    <td>{{ $p->asal_sekolah }}</td>
                    <td><span style="background: #e0f2fe; color: var(--primary); padding: 0.25rem 0.5rem; border-radius: 999px; font-size: 0.875rem; font-weight: 600;">{{ $p->nilai_rapor }}</span></td>
                    <td>{{ $p->jurusan->nama }}</td>
                    <td>
                        @if($p->status == 'menunggu_verifikasi')
                            <span style="color: #d97706; font-weight: 600;">Menunggu</span>
                        @elseif($p->status == 'lolos_admin')
                            <span style="color: #059669; font-weight: 600;">Lolos Verifikasi</span>
                        @elseif($p->status == 'ditolak_admin')
                            <span style="color: #dc2626; font-weight: 600;">Ditolak</span>
                        @else
                            <span style="color: var(--gray-text); font-weight: 600; text-transform: uppercase;">{{ $p->status }}</span>
                        @endif
                    </td>
                    <td>
                        @if($p->status == 'menunggu_verifikasi' || $p->status == 'revisi')
                            <a href="{{ route('admin.pendaftaran.show', $p->id) }}" class="btn-primary" style="background: #3b82f6; padding: 0.4rem 0.8rem; font-size: 0.8rem; text-decoration: none;">Lihat Detail</a>
                        @else
                            <a href="{{ route('admin.pendaftaran.show', $p->id) }}" class="btn-primary" style="background: var(--gray-text); padding: 0.4rem 0.8rem; font-size: 0.8rem; text-decoration: none;">Lihat Arsip</a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; color: var(--gray-text); padding: 2rem;">Tidak ada data pendaftaran.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
