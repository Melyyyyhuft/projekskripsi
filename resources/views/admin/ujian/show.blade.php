@extends('layouts.admin')
@section('title', 'Kelola Modul: ' . $ujian->judul)

@section('content')

{{-- ─── Back & Header ─── --}}
<div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem;">
    <div>
        <a href="{{ route('admin.ujian.index') }}" style="color:var(--primary);font-weight:600;text-decoration:none;font-size:.875rem;display:inline-flex;align-items:center;gap:.35rem;">
            ← Kembali ke Daftar Modul
        </a>
        <h1 style="font-size:1.4rem;font-weight:800;color:#0f172a;margin:.5rem 0 .25rem;">{{ $ujian->judul }}</h1>
        <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.5rem;">
            <span style="background:#e0f2fe; color:#0369a1; padding:.2rem .6rem; border-radius:6px; font-size:.75rem; font-weight:700; text-transform:uppercase;">
                📍 Jurusan: {{ $ujian->jurusan->nama ?? 'Umum / Semua' }}
            </span>
        </div>
        <p style="color:#64748b;font-size:.875rem;margin:0;">Kelola soal dan pengaturan sesi ujian ini.</p>
    </div>

</div>

@if(session('success'))
    <div style="background:#d1fae5;color:#059669;padding:.875rem 1.25rem;border-radius:12px;margin-bottom:1.5rem;font-weight:600;border:1px solid #a7f3d0;">
        ✅ {{ session('success') }}
    </div>
@endif

@if(!$ujian->is_tutup && $ujian->jadwal_selesai && now()->gt($ujian->jadwal_selesai))
    <div style="background:#fff7ed;color:#9a3412;padding:1rem 1.5rem;border-radius:12px;margin-bottom:1.5rem;font-weight:700;border:1px solid #fed7aa;display:flex;align-items:center;gap:.75rem;animation:pulse 2s infinite;">
        <span style="font-size:1.5rem;">⚠️</span>
        <div>
            Waktu ujian telah terlewati! 
            <span style="font-weight:500;display:block;font-size:.85rem;margin-top:.2rem;">Siswa tidak lagi dapat memulai ujian. Silakan perpanjang waktu atau tutup ujian untuk memproses hasil.</span>
        </div>
    </div>
    <style> @keyframes pulse { 0% { opacity: 1; } 50% { opacity: 0.8; } 100% { opacity: 1; } } </style>
@endif

{{-- ─── Info Modul ─── --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:1rem;margin-bottom:2rem;">

    <div style="background:white;border-radius:14px;padding:1.25rem;border:1px solid #e2e8f0;box-shadow:0 1px 4px rgba(0,0,0,.04);">
        <div style="font-size:.72rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;margin-bottom:.4rem;">Durasi</div>
        <div style="font-weight:800;font-size:1.3rem;color:#0f172a;">{{ $ujian->durasi_menit }} <span style="font-size:.85rem;font-weight:500;color:#64748b;">menit</span></div>
    </div>

    @if($ujian->jadwal_mulai)
    <div style="background:white;border-radius:14px;padding:1.25rem;border:1px solid #e2e8f0;box-shadow:0 1px 4px rgba(0,0,0,.04);">
        <div style="font-size:.72rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;margin-bottom:.4rem;">Jadwal Mulai</div>
        <div style="font-weight:700;font-size:.9rem;color:#0f172a;">{{ \Carbon\Carbon::parse($ujian->jadwal_mulai)->format('d M Y, H:i') }}</div>
    </div>
    <div style="background:white;border-radius:14px;padding:1.25rem;border:1px solid #e2e8f0;box-shadow:0 1px 4px rgba(0,0,0,.04);">
        <div style="font-size:.72rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;margin-bottom:.4rem;">Jadwal Selesai</div>
        <div style="font-weight:700;font-size:.9rem;color:#0f172a;">{{ \Carbon\Carbon::parse($ujian->jadwal_selesai)->format('d M Y, H:i') }}</div>
    </div>
    @endif

    <div style="background:white;border-radius:14px;padding:1.25rem;border:1px solid #e2e8f0;box-shadow:0 1px 4px rgba(0,0,0,.04);">
        <div style="font-size:.72rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;margin-bottom:.4rem;">Total Soal</div>
        <div style="font-weight:800;font-size:1.3rem;color:var(--primary);">{{ $soals->count() }} <span style="font-size:.85rem;font-weight:500;color:#64748b;">soal</span></div>
    </div>

    <div style="background:white;border-radius:14px;padding:1.25rem;border:1px solid #e2e8f0;box-shadow:0 1px 4px rgba(0,0,0,.04);">
        <div style="font-size:.72rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;margin-bottom:.6rem;">Status & Acak</div>
        <div style="display:flex;flex-wrap:wrap;gap:.35rem;">
            @if($ujian->is_tutup)
                <span style="background:#fee2e2;color:#dc2626;padding:.2rem .6rem;border-radius:999px;font-size:.75rem;font-weight:700;">🔒 Ditutup</span>
            @else
                <span style="background:#d1fae5;color:#059669;padding:.2rem .6rem;border-radius:999px;font-size:.75rem;font-weight:700;">✅ Aktif</span>
            @endif
            @if($ujian->acak_soal) <span style="background:#eef2ff;color:#4f46e5;padding:.2rem .6rem;border-radius:999px;font-size:.75rem;font-weight:600;">🔀 Soal</span> @endif
            @if($ujian->acak_jawaban) <span style="background:#eef2ff;color:#4f46e5;padding:.2rem .6rem;border-radius:999px;font-size:.75rem;font-weight:600;">🔀 Jwbn</span> @endif
        </div>
    </div>
</div>

{{-- ─── Soal dalam Modul ─── --}}
<div class="glass-card" style="margin-bottom:2rem;">
    <h3 style="margin:0 0 1.5rem;font-size:1.1rem;font-weight:700;color:#0f172a;">📝 Soal dalam Modul Ini <span style="background:#e0f2fe;color:var(--primary);padding:.2rem .6rem;border-radius:999px;font-size:.8rem;margin-left:.5rem;">{{ $soals->count() }}</span></h3>

    <div style="overflow-x:auto;border-radius:12px;border:1px solid #e2e8f0;max-height:420px;overflow-y:auto;">
        <table style="width:100%;border-collapse:collapse;min-width:500px;">
            <thead>
                <tr style="background:#f8fafc;position:sticky;top:0;z-index:1;">
                    <th style="padding:.75rem 1rem;text-align:center;font-size:.75rem;font-weight:700;color:#475569;text-transform:uppercase;border-bottom:1px solid #e2e8f0;width:50px;background:#f8fafc;">#</th>
                    <th style="padding:.75rem 1rem;text-align:left;font-size:.75rem;font-weight:700;color:#475569;text-transform:uppercase;border-bottom:1px solid #e2e8f0;background:#f8fafc;">Pertanyaan</th>
                    <th style="padding:.75rem 1rem;text-align:center;font-size:.75rem;font-weight:700;color:#475569;text-transform:uppercase;border-bottom:1px solid #e2e8f0;background:#f8fafc;">Kunci</th>
                    <th style="padding:.75rem 1rem;text-align:right;font-size:.75rem;font-weight:700;color:#475569;text-transform:uppercase;border-bottom:1px solid #e2e8f0;background:#f8fafc;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($soals as $index => $soal)
                <tr style="border-bottom:1px solid #f1f5f9;">
                    <td style="padding:.75rem 1rem;text-align:center;color:#94a3b8;font-size:.875rem;">{{ $index + 1 }}</td>
                    <td style="padding:.75rem 1rem;">
                        <div style="font-weight:600;color:#0f172a;margin-bottom:.35rem;font-size:.9rem;">{{ Str::limit($soal->teks_soal, 80) }}</div>
                        <div style="display:flex;gap:.75rem;flex-wrap:wrap;font-size:.75rem;color:#94a3b8;">
                            <span>A. {{ Str::limit($soal->opsi_a, 20) }}</span>
                            <span>B. {{ Str::limit($soal->opsi_b, 20) }}</span>
                            <span>C. {{ Str::limit($soal->opsi_c, 20) }}</span>
                            <span>D. {{ Str::limit($soal->opsi_d, 20) }}</span>
                        </div>
                    </td>
                    <td style="padding:.75rem 1rem;text-align:center;">
                        <span style="background:#d1fae5;color:#059669;padding:.25rem .65rem;border-radius:8px;font-weight:800;font-size:.9rem;">{{ $soal->jawaban_benar }}</span>
                    </td>
                    <td style="padding:.75rem 1rem;text-align:right;">
                        <form action="{{ route('admin.ujian.soal.detach', ['ujian' => $ujian->id, 'soal' => $soal->id]) }}" method="POST" onsubmit="return confirm('Keluarkan soal ini dari modul?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="background:#fef2f2;color:#ef4444;padding:.3rem .75rem;border-radius:8px;font-size:.8rem;font-weight:600;border:1px solid #fca5a5;cursor:pointer;">
                                Keluarkan
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="padding:3rem;text-align:center;color:#94a3b8;">
                        <div style="font-size:2rem;margin-bottom:.5rem;">📭</div>
                        Belum ada soal. Tambahkan dari Bank Soal di bawah.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ─── Pilih dari Bank Soal ─── --}}
<div class="glass-card" style="border-top:3px solid var(--primary);">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem;">
        <div>
            <h3 style="margin:0;color:var(--primary);font-size:1.1rem;font-weight:700;">📚 Pilih dari Bank Soal</h3>
            <p style="font-size:.8rem;color:var(--gray-text);margin:.35rem 0 0;">Centang soal yang ingin dimasukkan ke modul ini.</p>
        </div>

        <form action="{{ route('admin.ujian.show', $ujian->id) }}" method="GET" style="display:flex;gap:.75rem;align-items:center;flex-wrap:wrap;">
            <div style="display:flex;gap:0;align-items:center;border:1px solid #e2e8f0;border-radius:8px;overflow:hidden;">
                <input type="text" name="search" id="searchBankSoal" placeholder="Cari soal..." value="{{ request('search') }}" style="padding:.4rem .75rem;font-size:.85rem;height:auto;border:none;width:150px;outline:none;" onkeyup="if(event.keyCode===13) this.form.submit()">
                <button type="submit" style="padding:.4rem .6rem;background:#6366f1;border:none;cursor:pointer;color:white;display:flex;align-items:center;justify-content:center;" title="Cari">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                </button>
            </div>
            
            <div style="display:flex;gap:.4rem;align-items:center;">
                <label style="font-size:.75rem;font-weight:700;color:#64748b;text-transform:uppercase;">Thn Ajaran:</label>
                <select name="tahun_ajaran" class="form-control" style="padding:.4rem .75rem;font-size:.85rem;height:auto;border-radius:8px;width:120px;" onchange="this.form.submit()">
                    <option value="">Semua</option>
                    @foreach($tahunAjarans as $ta)
                        <option value="{{ $ta }}" {{ request('tahun_ajaran') == $ta ? 'selected' : '' }}>{{ $ta }}</option>
                    @endforeach
                </select>
            </div>
            
            <div style="display:flex;gap:.4rem;align-items:center;">
                <label style="font-size:.75rem;font-weight:700;color:#64748b;text-transform:uppercase;">Sumber:</label>
                <select name="sumber" class="form-control" style="padding:.4rem .75rem;font-size:.85rem;height:auto;border-radius:8px;width:160px;" onchange="this.form.submit()">
                    <option value="">Semua</option>
                    @foreach($sumbers as $smb)
                        <option value="{{ $smb }}" {{ request('sumber') == $smb ? 'selected' : '' }}>{{ $smb }}</option>
                    @endforeach
                </select>
            </div>
            @if(request('tahun_ajaran') || request('sumber') || request('search'))
                <a href="{{ route('admin.ujian.show', $ujian->id) }}" style="text-decoration:none;color:#ef4444;font-size:.85rem;font-weight:700;">✕ Reset</a>
            @endif
        </form>
    </div>

    <form action="{{ route('admin.ujian.soal.assign', $ujian->id) }}" method="POST">
        @csrf
        <div style="overflow-x:auto;border-radius:12px;border:1px solid #e2e8f0;max-height:420px;overflow-y:auto;">
            <table style="width:100%;border-collapse:collapse;min-width:400px;">
                <thead>
                    <tr style="background:#f8fafc;position:sticky;top:0;z-index:1;">
                        <th style="padding:.75rem 1rem;text-align:center;font-size:.75rem;font-weight:700;color:#475569;text-transform:uppercase;border-bottom:1px solid #e2e8f0;width:50px;">
                            <input type="checkbox" id="checkAll" style="width:16px;height:16px;accent-color:var(--primary);cursor:pointer;">
                        </th>
                        <th style="padding:.75rem 1rem;text-align:center;font-size:.75rem;font-weight:700;color:#475569;text-transform:uppercase;border-bottom:1px solid #e2e8f0;width:40px;">No</th>
                        <th style="padding:.75rem 1rem;text-align:left;font-size:.75rem;font-weight:700;color:#475569;text-transform:uppercase;border-bottom:1px solid #e2e8f0;">Pertanyaan</th>
                        <th style="padding:.75rem 1rem;text-align:center;font-size:.75rem;font-weight:700;color:#475569;text-transform:uppercase;border-bottom:1px solid #e2e8f0;white-space:nowrap;">Tahun Ajaran</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bankSoals as $idx => $bs)
                    <tr style="border-bottom:1px solid #f1f5f9;cursor:pointer;" onclick="this.querySelector('input[type=checkbox]').click()">
                        <td style="padding:.75rem 1rem;text-align:center;" onclick="event.stopPropagation()">
                            <input type="checkbox" name="soal_ids[]" value="{{ $bs->id }}" class="checkSoal" style="width:16px;height:16px;accent-color:var(--primary);">
                        </td>
                        <td style="padding:.75rem 1rem;text-align:center;color:#94a3b8;font-size:.875rem;">
                            {{ $idx + 1 }}
                        </td>
                        <td style="padding:.75rem 1rem;">
                            <div style="font-size:.9rem;font-weight:500;color:#0f172a;">{{ Str::limit($bs->teks_soal, 100) }}</div>
                            <div style="font-size:.7rem;color:#94a3b8;margin-top:.2rem;">
                                📁 Sumber: {{ $bs->sumber ?? 'Input Manual' }}
                            </div>
                        </td>
                        <td style="padding:.75rem 1rem;text-align:center;">
                            <span style="background:#f1f5f9;color:#475569;padding:.2rem .6rem;border-radius:6px;font-size:.75rem;font-weight:600;">{{ $bs->tahun_ajaran }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" style="padding:3rem;text-align:center;color:#94a3b8;">
                            <div style="font-size:2rem;margin-bottom:.5rem;">✅</div>
                            @if(request('tahun_ajaran'))
                                Tidak ada soal tersedia untuk tahun {{ request('tahun_ajaran') }}.
                            @else
                                Semua soal di Bank Soal sudah ada di modul ini.
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($bankSoals->count() > 0)
        <div style="margin-top:1.25rem;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem;">
            <span id="selectedCount" style="font-size:.875rem;color:#64748b;font-weight:600;">0 soal dipilih</span>
            <button type="submit" class="btn-primary" style="padding:.7rem 1.5rem;">
                ➕ Tambahkan Soal Terpilih ke Modul
            </button>
        </div>
        @endif
    </form>
</div>

<script>
    const checkAll = document.getElementById('checkAll');
    if (checkAll) {
        checkAll.addEventListener('change', function() {
            document.querySelectorAll('.checkSoal').forEach(cb => cb.checked = this.checked);
            updateCount();
        });
        document.querySelectorAll('.checkSoal').forEach(cb => {
            cb.addEventListener('change', updateCount);
        });
    }

    function updateCount() {
        const count = document.querySelectorAll('.checkSoal:checked').length;
        const el = document.getElementById('selectedCount');
        if (el) el.textContent = count + ' soal dipilih';
    }
</script>
@endsection
