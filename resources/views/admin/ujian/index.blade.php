@extends('layouts.admin')
@section('title', 'Manajemen Ujian CBT')

@section('content')
@if(session('success'))
    <div style="background: #d1fae5; color: #059669; padding: 1rem 1.25rem; border-radius: var(--radius-sm); margin-bottom: 1.5rem; font-weight: 500;">
        ✅ {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div style="background: #fee2e2; color: #dc2626; padding: 1rem 1.25rem; border-radius: var(--radius-sm); margin-bottom: 1.5rem; font-weight: 500;">
        ⚠️ {{ session('error') }}
    </div>
@endif

<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 2rem;">
    <!-- Form Tambah Ujian -->
    <div class="glass-card" style="align-self: start;">
        <h3 style="margin-bottom: 1.5rem; color: var(--primary);">Buat Sesi Ujian Baru</h3>

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

            <div class="form-group">
                <label class="form-label" for="jadwal_mulai">📅 Tanggal Mulai Ujian</label>
                <input type="datetime-local" name="jadwal_mulai" id="jadwal_mulai" class="form-control">
                <small style="color: var(--gray-text);">Kosongkan jika tidak ada pembatasan waktu.</small>
            </div>

            <div class="form-group">
                <label class="form-label" for="jadwal_selesai">📅 Tanggal Selesai Ujian</label>
                <input type="datetime-local" name="jadwal_selesai" id="jadwal_selesai" class="form-control">
                <small style="color: var(--gray-text);">Setelah tanggal ini, siswa tidak bisa mengakses ujian.</small>
            </div>

            <button type="submit" class="btn-primary" style="width: 100%;">Setup Ujian</button>
        </form>
    </div>

    <!-- Tabel Ujian -->
    <div class="glass-card">
        <h3 style="margin-bottom: 1.5rem;">Daftar Sesi Ujian</h3>
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>Judul Ujian</th>
                        <th>Durasi</th>
                        <th>Jadwal</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ujians as $u)
                    <tr>
                        <td><strong>{{ $u->judul }}</strong></td>
                        <td>{{ $u->durasi_menit }} menit</td>
                        <td style="font-size: 0.8rem;">
                            @if($u->jadwal_mulai)
                                <div>▶ {{ \Carbon\Carbon::parse($u->jadwal_mulai)->format('d M Y H:i') }}</div>
                                <div>⏹ {{ \Carbon\Carbon::parse($u->jadwal_selesai)->format('d M Y H:i') }}</div>
                            @else
                                <span style="color: var(--gray-text);">—</span>
                            @endif
                        </td>
                        <td>
                            @if($u->is_tutup)
                                <span style="background: #fee2e2; color: #dc2626; padding: 0.2rem 0.5rem; border-radius: 4px; font-weight: 600; font-size: 0.8rem;">🔒 Ditutup</span>
                            @elseif($u->is_active)
                                <span style="background: #d1fae5; color: #059669; padding: 0.2rem 0.5rem; border-radius: 4px; font-weight: 600; font-size: 0.8rem;">✅ Aktif</span>
                            @else
                                <span style="background: #f1f5f9; color: #94a3b8; padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.8rem;">Nonaktif</span>
                            @endif
                        </td>
                        <td style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                            <a href="{{ route('admin.ujian.show', $u->id) }}" class="btn-primary" style="font-size: 0.8rem; padding: 0.4rem 0.8rem; background: #3b82f6;">Kelola Soal</a>
                            @if(!$u->is_tutup)
                                <form action="{{ route('admin.ujian.tutup', $u->id) }}" method="POST" onsubmit="return confirm('Tutup ujian? Siswa yang belum ujian akan otomatis berstatus Tidak Mengikuti Ujian.');">
                                    @csrf
                                    <button type="submit" class="btn-primary" style="font-size: 0.8rem; padding: 0.4rem 0.8rem; background: #ef4444;">🔒 Tutup Ujian</button>
                                </form>
                            @else
                                <span style="font-size: 0.8rem; color: var(--gray-text);">Sudah ditutup</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align: center; color: var(--gray-text); padding: 2rem;">Belum ada ujian. Silakan buat di form sebelah kiri.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
