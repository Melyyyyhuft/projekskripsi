@extends('layouts.admin')
@section('title', 'Manajemen Pendaftaran')

@section('content')
<div class="glass-card">
    <h3 style="margin-bottom: 1.5rem; color: var(--primary);">Daftar Calon Siswa Baru (Menunggu Verifikasi)</h3>
    
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
                        @if($p->status == 'menunggu_verifikasi')
                        <div style="display: flex; gap: 0.5rem;">
                            <!-- Lolos -->
                            <form action="{{ route('admin.pendaftaran.verifikasi', $p->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="lolos_admin">
                                <button type="submit" class="btn-primary" style="padding: 0.4rem 0.8rem; font-size: 0.8rem; background: #10b981;">Luluskan</button>
                            </form>
                            
                            <!-- Tolak -->
                            <form action="{{ route('admin.pendaftaran.verifikasi', $p->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="ditolak_admin">
                                <button type="submit" class="btn-primary" style="padding: 0.4rem 0.8rem; font-size: 0.8rem; background: #ef4444;" onclick="return confirm('Yakin ingin menolak siswa ini?');">Tolak</button>
                            </form>
                        </div>
                        @else
                            <span style="font-size: 0.875rem; color: var(--gray-text);">Selesai Tinjauan</span>
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
