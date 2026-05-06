@extends('layouts.admin')
@section('title', 'Dashboard Admin')

@section('content')
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    <!-- Stat Cards -->
    <div class="glass-card" style="border-left: 4px solid var(--primary);">
        <h3 style="color: var(--gray-text); font-size: 1rem; font-weight: 500;">Total Pendaftar</h3>
        <p style="font-size: 2rem; font-weight: 700; color: var(--dark); margin-top: 0.5rem;">{{ $totalPendaftar }}</p>
    </div>
    
    <div class="glass-card" style="border-left: 4px solid var(--secondary);">
        <h3 style="color: var(--gray-text); font-size: 1rem; font-weight: 500;">Menunggu Verifikasi</h3>
        <p style="font-size: 2rem; font-weight: 700; color: var(--dark); margin-top: 0.5rem;">{{ $menungguVerifikasi }}</p>
    </div>
    
    <div class="glass-card" style="border-left: 4px solid #10b981;">
        <h3 style="color: var(--gray-text); font-size: 1rem; font-weight: 500;">Total Diterima</h3>
        <p style="font-size: 2rem; font-weight: 700; color: var(--dark); margin-top: 0.5rem;">{{ $totalDiterima }}</p>
    </div>
</div>

<div class="glass-card" style="margin-bottom: 2rem;">
    <h3 style="margin-top: 0; margin-bottom: 1.5rem;">Statistik Kuota Jurusan</h3>
    <div class="table-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th>Nama Jurusan</th>
                    <th style="text-align: center;">Kuota Total</th>
                    <th style="text-align: center;">Jumlah Diterima</th>
                    <th style="text-align: center;">Sisa Kuota</th>
                </tr>
            </thead>
            <tbody>
                @foreach($jurusans as $jurusan)
                <tr>
                    <td><strong>{{ $jurusan->nama }}</strong></td>
                    <td style="text-align: center;">{{ $jurusan->kuota }}</td>
                    <td style="text-align: center;">
                        <span style="background: rgba(16, 185, 129, 0.1); color: #10b981; padding: 0.25rem 0.75rem; border-radius: 999px; font-weight: 600;">
                            {{ $jurusan->diterima_count }}
                        </span>
                    </td>
                    <td style="text-align: center;">
                        @if($jurusan->sisa_kuota <= 0)
                            <span style="color: #ef4444; font-weight: 700;">Penuh (0)</span>
                        @else
                            <span style="color: var(--primary); font-weight: 700;">{{ $jurusan->sisa_kuota }}</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="glass-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h3 style="margin: 0;">Pendaftar Terbaru</h3>
        <a href="{{ route('admin.pendaftaran.index') }}" style="color: var(--primary); font-size: 0.875rem; font-weight: 600;">Lihat Semua &rarr;</a>
    </div>
    
    <div class="table-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Asal Sekolah</th>
                    <th>Pilihan Jurusan</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pendaftarTerbaru as $p)
                <tr>
                    <td><strong>{{ $p->user->name }}</strong></td>
                    <td>{{ $p->asal_sekolah }}</td>
                    <td>{{ $p->jurusan->nama }}</td>
                    <td>
                        @if($p->status == 'menunggu_verifikasi')
                            <span style="color: #d97706; font-weight: 600;">Menunggu</span>
                        @elseif($p->status == 'lolos_admin')
                            <span style="color: #059669; font-weight: 600;">Lolos</span>
                        @elseif($p->status == 'ditolak_admin')
                            <span style="color: #dc2626; font-weight: 600;">Ditolak</span>
                        @else
                            <span style="color: var(--gray-text); font-weight: 600; text-transform: uppercase;">{{ $p->status }}</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align: center; color: var(--gray-text);">Belum ada data pendaftar.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
