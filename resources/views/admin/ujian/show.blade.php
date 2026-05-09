@extends('layouts.admin')
@section('title', 'Kelola Ujian: ' . $ujian->judul)

@section('content')
<div style="margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center;">
    <a href="{{ route('admin.ujian.index') }}" style="color: var(--primary); font-weight: 500;">&larr; Kembali ke Daftar Ujian</a>

    {{-- Tombol Tutup Ujian --}}
    @if(!$ujian->is_tutup)
        <form action="{{ route('admin.ujian.tutup', $ujian->id) }}" method="POST"
              onsubmit="return confirm('🔒 Tutup Ujian?\n\nSiswa yang belum mengikuti ujian akan otomatis berstatus \"Tidak Mengikuti Ujian\" dan tidak dapat mengerjakan soal lagi.');">
            @csrf
            <button type="submit" class="btn-primary" style="background: #ef4444; padding: 0.6rem 1.25rem;">
                🔒 Tutup Ujian
            </button>
        </form>
    @else
        <span style="background: #fee2e2; color: #dc2626; padding: 0.5rem 1rem; border-radius: var(--radius-sm); font-weight: 600; font-size: 0.9rem;">
            🔒 Ujian Sudah Ditutup
        </span>
    @endif
</div>

@if(session('success'))
    <div style="background: #d1fae5; color: #059669; padding: 1rem 1.25rem; border-radius: var(--radius-sm); margin-bottom: 1.5rem; font-weight: 500;">
        ✅ {{ session('success') }}
    </div>
@endif

{{-- Info Ujian --}}
<div class="glass-card" style="margin-bottom: 2rem;">
    <div style="display: flex; gap: 3rem; flex-wrap: wrap;">
        <div>
            <div style="font-size: 0.8rem; color: var(--gray-text);">Judul Ujian</div>
            <div style="font-weight: 700; font-size: 1.1rem;">{{ $ujian->judul }}</div>
        </div>
        <div>
            <div style="font-size: 0.8rem; color: var(--gray-text);">Durasi</div>
            <div style="font-weight: 700;">{{ $ujian->durasi_menit }} menit</div>
        </div>
        @if($ujian->jadwal_mulai)
        <div>
            <div style="font-size: 0.8rem; color: var(--gray-text);">Jadwal Mulai</div>
            <div style="font-weight: 700;">{{ \Carbon\Carbon::parse($ujian->jadwal_mulai)->format('d M Y H:i') }}</div>
        </div>
        <div>
            <div style="font-size: 0.8rem; color: var(--gray-text);">Jadwal Selesai</div>
            <div style="font-weight: 700;">{{ \Carbon\Carbon::parse($ujian->jadwal_selesai)->format('d M Y H:i') }}</div>
        </div>
        @endif
        <div>
            <div style="font-size: 0.8rem; color: var(--gray-text);">Status</div>
            @if($ujian->is_tutup)
                <span style="background: #fee2e2; color: #dc2626; padding: 0.2rem 0.6rem; border-radius: 4px; font-weight: 600; font-size: 0.85rem;">🔒 Ditutup</span>
            @else
                <span style="background: #d1fae5; color: #059669; padding: 0.2rem 0.6rem; border-radius: 4px; font-weight: 600; font-size: 0.85rem;">✅ Aktif</span>
            @endif
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 2rem;">
    <!-- Form Tambah Soal -->
    <div class="glass-card" style="align-self: start;">
        <h3 style="margin-bottom: 1.5rem; color: var(--primary);">Tambah Soal Pilihan Ganda</h3>

        <form action="{{ route('admin.ujian.soal.store', $ujian->id) }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label" for="teks_soal">Pertanyaan</label>
                <textarea name="teks_soal" id="teks_soal" class="form-control" rows="3" required placeholder="Tuliskan soal di sini..."></textarea>
            </div>
            <div class="form-group">
                <label class="form-label" for="opsi_a">Opsi A</label>
                <input type="text" name="opsi_a" id="opsi_a" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label" for="opsi_b">Opsi B</label>
                <input type="text" name="opsi_b" id="opsi_b" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label" for="opsi_c">Opsi C</label>
                <input type="text" name="opsi_c" id="opsi_c" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label" for="opsi_d">Opsi D</label>
                <input type="text" name="opsi_d" id="opsi_d" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label" for="jawaban_benar">Jawaban Benar</label>
                <select name="jawaban_benar" id="jawaban_benar" class="form-control" required>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                    <option value="D">D</option>
                </select>
            </div>
            <button type="submit" class="btn-primary" style="width: 100%;">Simpan Soal</button>
        </form>
    </div>

    <!-- Kanan: Soal + Peserta -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">

        <!-- Daftar Peserta -->
        <div class="glass-card">
            <h3 style="margin-bottom: 1rem;">👥 Status Peserta Ujian ({{ $peserta->count() }} siswa)</h3>
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Jurusan</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($peserta as $p)
                        <tr>
                            <td><strong>{{ $p->user->name }}</strong></td>
                            <td>{{ $p->jurusan->nama }}</td>
                            <td>
                                @if($p->status === 'lolos_admin')
                                    <span style="background: #dbeafe; color: #1d4ed8; padding: 0.2rem 0.5rem; border-radius: 4px; font-weight: 600; font-size: 0.78rem;">⏳ Menunggu Ujian</span>
                                @elseif($p->status === 'sudah_ujian')
                                    <span style="background: #d1fae5; color: #059669; padding: 0.2rem 0.5rem; border-radius: 4px; font-weight: 600; font-size: 0.78rem;">✅ Sudah Ujian</span>
                                @elseif($p->status === 'tidak_mengikuti_ujian')
                                    <span style="background: #fee2e2; color: #dc2626; padding: 0.2rem 0.5rem; border-radius: 4px; font-weight: 600; font-size: 0.78rem;">✗ Tidak Mengikuti</span>
                                @else
                                    <span style="background: #f1f5f9; color: #64748b; padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.78rem;">{{ str_replace('_', ' ', strtoupper($p->status)) }}</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" style="text-align: center; color: var(--gray-text); padding: 1.5rem;">Belum ada peserta yang lolos administrasi.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Daftar Soal Tersimpan -->
        <div class="glass-card">
            <h3 style="margin-bottom: 1.5rem;">📝 Soal Tersimpan ({{ $soals->count() }})</h3>
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                @forelse($soals as $index => $soal)
                <div style="padding: 1rem; border: 1px solid #e2e8f0; border-radius: var(--radius-md); background: var(--light-bg);">
                    <p style="font-weight: 600; margin-bottom: 0.5rem;">{{ $index + 1 }}. {{ $soal->teks_soal }}</p>
                    <ul style="list-style: none; padding-left: 1rem; color: var(--gray-text); margin-bottom: 0.5rem;">
                        <li>A. {{ $soal->opsi_a }}</li>
                        <li>B. {{ $soal->opsi_b }}</li>
                        <li>C. {{ $soal->opsi_c }}</li>
                        <li>D. {{ $soal->opsi_d }}</li>
                    </ul>
                    <div style="font-size: 0.875rem; font-weight: 600; color: #059669;">Jawaban Kunci: {{ $soal->jawaban_benar }}</div>
                </div>
                @empty
                <div style="text-align: center; color: var(--gray-text); padding: 2rem;">Belum ada soal yang tersimpan untuk ujian ini.</div>
                @endforelse
            </div>
        </div>

    </div>
</div>
@endsection
