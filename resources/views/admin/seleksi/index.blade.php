@extends('layouts.admin')
@section('title', 'Hasil Seleksi & Pemeringkatan')

@section('content')
<div class="glass-card" style="margin-bottom: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h3 style="margin-bottom: 0.5rem; color: var(--primary);">Eksekusi Algoritma Seleksi</h3>
            <p style="color: var(--gray-text); font-size: 0.875rem;">Sistem akan mengurutkan Pendaftar berdasarkan Rumus Skor Akhir: <br>(Bobot Ujian × Skor Ujian) + (Bobot Rapor × Nilai Rapor).</p>
        </div>
        <form action="{{ route('admin.seleksi.run') }}" method="POST">
            @csrf
            <button type="submit" class="btn-primary" style="font-size: 1.125rem; padding: 1rem 2rem; background: linear-gradient(135deg, #10b981, #059669);" onclick="return confirm('Jalankan algoritma seleksi? Data yang sebelumnya akan ditimpa dengan peringkat terbaru.');">
                ⚡ Jalankan Seleksi Otomatis
            </button>
        </form>
    </div>
</div>

@if(session('success'))
    <div style="background: #d1fae5; color: #059669; padding: 1rem; border-radius: var(--radius-sm); margin-bottom: 1.5rem; font-weight: 500;">
        {{ session('success') }}
    </div>
@endif

<div class="glass-card">
    <h3 style="margin-bottom: 1.5rem;">Daftar Kelulusan Peserta Didik Baru</h3>
    <div class="table-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th>Ranking</th>
                    <th>Nama</th>
                    <th>Jurusan</th>
                    <th>Skor Akhir</th>
                    <th>Status Kelulusan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($hasil as $h)
                <tr style="{{ $h->status_kelulusan ? 'background: #f0fdf4;' : 'background: #fef2f2;' }}">
                    <td>
                        <span style="font-size: 1.25rem; font-weight: 800; color: {{ $h->status_kelulusan ? '#059669' : '#dc2626' }};">
                            #{{ $h->ranking }}
                        </span>
                    </td>
                    <td><strong>{{ $h->pendaftaran->user->name }}</strong></td>
                    <td>{{ $h->pendaftaran->jurusan->nama }}</td>
                    <td><strong>{{ $h->skor_akhir }}</strong></td>
                    <td>
                        @if($h->status_kelulusan)
                            <span style="background: #10b981; color: white; padding: 0.25rem 0.75rem; border-radius: 999px; font-size: 0.875rem; font-weight: 600;">Diterima</span>
                        @else
                            <span style="background: #ef4444; color: white; padding: 0.25rem 0.75rem; border-radius: 999px; font-size: 0.875rem; font-weight: 600;">Tidak Diterima</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; color: var(--gray-text); padding: 2rem;">Algoritma seleksi belum dijalankan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
