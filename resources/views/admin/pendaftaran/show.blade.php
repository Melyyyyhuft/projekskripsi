@extends('layouts.admin')
@section('title', 'Detail & Verifikasi Pendaftaran')

@section('content')
<div style="margin-bottom: 1rem;">
    <a href="{{ route('admin.pendaftaran.index', ['tab' => 'baru']) }}" style="color: var(--primary); text-decoration: none; font-weight: 500;">&larr; Kembali ke Daftar</a>
</div>

<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 2rem;">
    <!-- Info Siswa -->
    <div class="glass-card" style="align-self: start;">
        <h3 style="margin-bottom: 1.5rem; color: var(--primary);">Biodata Siswa</h3>
        <table class="table">
            <tbody>
                <tr><td style="font-weight: 600;">Nama</td><td>{{ $pendaftaran->user->name }}</td></tr>
                <tr><td style="font-weight: 600;">NISN</td><td>{{ $pendaftaran->nisn }}</td></tr>
                <tr><td style="font-weight: 600;">Asal Sekolah</td><td>{{ $pendaftaran->asal_sekolah }}</td></tr>
                <tr><td style="font-weight: 600;">No HP / WA</td><td>{{ $pendaftaran->no_hp ?? '-' }}</td></tr>
                <tr><td style="font-weight: 600;">Email</td><td>{{ $pendaftaran->user->email }}</td></tr>
                <tr><td style="font-weight: 600;">Rata-rata Rapor</td><td>{{ $pendaftaran->nilai_rapor }}</td></tr>
                <tr><td style="font-weight: 600;">Pilihan Jurusan</td><td>{{ $pendaftaran->jurusan->nama }}</td></tr>
                <tr>
                    <td style="font-weight: 600;">Status</td>
                    <td>
                        <span style="padding: 0.2rem 0.5rem; border-radius: 4px; font-weight: 600; font-size: 0.8rem; background: #e0f2fe; color: #0284c7;">
                            {{ str_replace('_', ' ', strtoupper($pendaftaran->status)) }}
                        </span>
                    </td>
                </tr>
            </tbody>
        </table>

        @if(in_array($pendaftaran->status, ['menunggu_verifikasi', 'revisi']))
        <div style="margin-top: 2rem; border-top: 1px solid rgba(0,0,0,0.1); padding-top: 1.5rem;">
            <h4 style="margin-bottom: 1rem; color: var(--dark);">Keputusan Verifikasi</h4>
            <div style="display: flex; gap: 1rem;">
                <form action="{{ route('admin.pendaftaran.verifikasi', $pendaftaran->id) }}" method="POST" style="flex: 1;">
                    @csrf
                    <input type="hidden" name="status" value="lolos_admin">
                    <button type="submit" class="btn-primary" style="width: 100%; background: #10b981;" onclick="return confirm('Apakah Anda yakin meluluskan (verifikasi) siswa ini?');">Luluskan</button>
                </form>
                <form action="{{ route('admin.pendaftaran.verifikasi', $pendaftaran->id) }}" method="POST" style="flex: 1;">
                    @csrf
                    <input type="hidden" name="status" value="ditolak_admin">
                    <button type="submit" class="btn-primary" style="width: 100%; background: #ef4444;" onclick="return confirm('Apakah Anda yakin menolak siswa ini?');">Tolak</button>
                </form>
            </div>
            
            <form action="{{ route('admin.pendaftaran.verifikasi', $pendaftaran->id) }}" method="POST" style="margin-top: 1rem;">
                @csrf
                <input type="hidden" name="status" value="revisi">
                <button type="submit" class="btn-primary" style="width: 100%; background: #f59e0b;" onclick="return confirm('Minta siswa untuk memperbaiki berkasnya?');">Minta Revisi Berkas</button>
            </form>
        </div>
        @endif
    </div>

    <!-- Berkas -->
    <div class="glass-card">
        <h3 style="margin-bottom: 1.5rem; color: var(--primary);">Berkas Pendukung</h3>
        
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            @forelse($pendaftaran->berkas as $berkas)
                <div style="border: 1px solid rgba(0,0,0,0.1); border-radius: var(--radius-md); overflow: hidden;">
                    <div style="background: #f8fafc; padding: 0.75rem 1rem; border-bottom: 1px solid rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center;">
                        <strong style="text-transform: capitalize;">{{ $berkas->jenis_berkas }}</strong>
                        <span style="font-size: 0.8rem; color: var(--gray-text);">{{ $berkas->nama_file }}</span>
                    </div>
                    
                    <div style="background: #e2e8f0; display: flex; justify-content: center; align-items: center; min-height: 300px; padding: 1rem;">
                        @if(in_array(strtolower($berkas->file_type), ['jpg', 'jpeg', 'png']))
                            <img src="{{ asset('storage/' . $berkas->file_path) }}" alt="{{ $berkas->jenis_berkas }}" style="max-width: 100%; max-height: 500px; border-radius: var(--radius-sm); box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
                        @elseif(strtolower($berkas->file_type) == 'pdf')
                            <iframe src="{{ asset('storage/' . $berkas->file_path) }}" width="100%" height="500px" style="border: none;"></iframe>
                        @else
                            <a href="{{ asset('storage/' . $berkas->file_path) }}" target="_blank" class="btn-primary">Unduh File</a>
                        @endif
                    </div>
                </div>
            @empty
                <div style="text-align: center; color: var(--gray-text); padding: 3rem;">
                    Siswa ini belum mengunggah berkas apapun.
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
