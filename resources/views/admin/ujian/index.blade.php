@extends('layouts.admin')
@section('title', 'Manajemen Ujian CBT')

@section('content')
<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 2rem;">
    <!-- Form Tambah Ujian -->
    <div class="glass-card">
        <h3 style="margin-bottom: 1.5rem; color: var(--primary);">Buat Sesi Ujian Baru</h3>
        
        @if(session('success'))
            <div style="background: #d1fae5; color: #059669; padding: 1rem; border-radius: var(--radius-sm); margin-bottom: 1.5rem; font-weight: 500;">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('admin.ujian.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label" for="judul">Judul/Materi Ujian</label>
                <input type="text" name="judul" id="judul" class="form-control" required placeholder="Contoh: Tes Skolastik & Jurusan">
            </div>
            
            <div class="form-group">
                <label class="form-label" for="durasi_menit">Durasi (Menit)</label>
                <input type="number" name="durasi_menit" id="durasi_menit" class="form-control" required placeholder="Contoh: 60">
            </div>
            
            <button type="submit" class="btn-primary" style="width: 100%;">Setup Ujian</button>
        </form>
    </div>

    <!-- Tabel Ujian -->
    <div class="glass-card">
        <h3 style="margin-bottom: 1.5rem;">Daftar Ujian Aktif</h3>
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>Judul Ujian</th>
                        <th>Durasi</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ujians as $u)
                    <tr>
                        <td><strong>{{ $u->judul }}</strong></td>
                        <td>{{ $u->durasi_menit }} Menit</td>
                        <td>
                            @if($u->is_active)
                                <span style="background: #e0f2fe; color: var(--primary); padding: 0.25rem 0.5rem; border-radius: 999px; font-size: 0.875rem; font-weight: 600;">Aktif</span>
                            @else
                                <span style="color: var(--gray-text);">Nonaktif</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.ujian.show', $u->id) }}" class="btn-primary" style="font-size: 0.8rem; padding: 0.4rem 0.8rem;">Manajemen Soal</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align: center; color: var(--gray-text); padding: 2rem;">Belum ada ujian. Silakan buat di form sebelah kiri.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
