@extends('layouts.admin')
@section('title', 'Seleksi & Penempatan')

@section('content')
<style>
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes pulse-glow { 0%, 100% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.4); } 50% { box-shadow: 0 0 0 10px rgba(59, 130, 246, 0); } }
    
    .fade-up { animation: fadeInUp 0.5s ease forwards; }
    .delay-1 { animation-delay: 0.1s; opacity: 0; }
    .delay-2 { animation-delay: 0.2s; opacity: 0; }
    
    .glass-card { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.5); border-radius: 24px; box-shadow: 0 8px 32px rgba(31, 38, 135, 0.07); padding: 1.5rem; transition: all 0.3s ease; }
    .glass-card:hover { transform: translateY(-3px); box-shadow: 0 12px 40px rgba(31, 38, 135, 0.12); }
    
    .badge-modern { display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.4rem 0.9rem; border-radius: 999px; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; }
    
    .btn-modern { display: inline-flex; align-items: center; gap: 0.6rem; padding: 0.8rem 1.5rem; border-radius: 16px; font-weight: 800; font-size: 0.875rem; border: none; cursor: pointer; transition: all 0.2s; color: white; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
    .btn-modern:hover { transform: scale(1.02); filter: brightness(1.1); box-shadow: 0 6px 20px rgba(0,0,0,0.15); }
    .btn-modern:active { transform: scale(0.98); }
    .btn-modern:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }
    
    .table-modern { width: 100%; border-collapse: separate; border-spacing: 0 0.75rem; }
    .table-modern th { padding: 0.75rem 1rem; color: #94a3b8; font-size: 0.7rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em; }
    .table-modern tbody tr { background: white; border-radius: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.02); transition: all 0.2s; }
    .table-modern tbody tr:hover { transform: scale(1.01); box-shadow: 0 4px 12px rgba(0,0,0,0.05); z-index: 10; position: relative; }
    .table-modern td { padding: 1.1rem 1rem; border: none; font-size: 0.875rem; vertical-align: middle; }
    .table-modern td:first-child { border-radius: 16px 0 0 16px; padding-left: 1.5rem; }
    .table-modern td:last-child { border-radius: 0 16px 16px 0; padding-right: 1.5rem; }
    
    .filter-group { display: flex; align-items: center; gap: 0.75rem; background: #f1f5f9; padding: 0.5rem 1rem; border-radius: 14px; border: 1px solid #e2e8f0; }
    .filter-group select { background: transparent; border: none; font-weight: 700; color: #1e293b; outline: none; font-size: 0.875rem; cursor: pointer; }
    
    .process-badge { display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1.25rem; border-radius: 12px; font-weight: 700; font-size: 0.8rem; }
</style>

<div class="fade-up">
    {{-- Header Section --}}
    <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 1.5rem; margin-bottom: 2rem; flex-wrap: wrap;">
        <div>
            <h1 style="font-size: 1.75rem; font-weight: 900; color: #0f172a; margin: 0 0 0.4rem; letter-spacing: -0.02em;">🏫 Seleksi & Penempatan Kelas</h1>
            <p style="color: #64748b; font-size: 0.95rem; margin: 0;">Hitung skor akhir, tentukan kelas, lalu publish hasil ke siswa.</p>
        </div>
        
        <div style="display: flex; gap: 1rem; align-items: center;">
            @if($sudahPublish)
                <div class="process-badge" style="background: #ecfdf5; color: #059669; border: 1px solid #10b981;">
                    🔒 Hasil Sudah Dipublish
                </div>
            @else
                <div class="process-badge" style="background: #fffbeb; color: #d97706; border: 1px solid #fbbf24;">
                    ⏳ Menunggu Publikasi
                </div>
                
                <form action="{{ route('admin.penempatan.proses') }}" method="POST" id="fHitung">@csrf</form>
                <button type="button" onclick="confirmCalc()" form="fHitung" class="btn-modern" style="background: linear-gradient(135deg, #3b82f6, #2563eb);">
                    ⚡ Hitung Semua
                </button>
                
                <form action="{{ route('admin.penempatan.publish') }}" method="POST" id="fPublish">@csrf</form>
                <button type="button" onclick="confirmPublish()" form="fPublish" class="btn-modern" style="background: linear-gradient(135deg, #10b981, #059669);" {{ $rows->where('hasil_seleksi', '!=', null)->count() == 0 ? 'disabled' : '' }}>
                    📢 Publish Hasil
                </button>
            @endif
        </div>
    </div>

    {{-- Formula Section --}}
    <div class="glass-card delay-1" style="background: linear-gradient(135deg, #1e293b, #0f172a); border: none; margin-bottom: 2rem;">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1.5rem;">
            <div style="display: flex; align-items: center; gap: 1.25rem;">
                <div style="width: 48px; height: 48px; background: rgba(255,255,255,0.05); border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">📐</div>
                <div>
                    <div style="font-size: 0.7rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 0.25rem;">Formula Penilaian</div>
                    <code style="font-size: 1.1rem; color: #f8fafc; font-weight: 700;">Skor = ({{ ($settings['bobot_rapor'] ?? 70) / 100 }} × Rapor) + ({{ ($settings['bobot_ujian'] ?? 30) / 100 }} × CBT) + Bonus Cert</code>
                </div>
            </div>
            
            <div style="display: flex; gap: 0.75rem;">
                <div style="background: rgba(234, 179, 8, 0.1); border: 1px solid rgba(234, 179, 8, 0.2); padding: 0.5rem 1rem; border-radius: 12px;">
                    <span style="color: #facc15; font-weight: 800; font-size: 0.85rem;">⭐ ≥{{ $settings['ambang_unggulan'] ?? 70 }} → Unggulan</span>
                </div>
                <div style="background: rgba(59, 130, 246, 0.1); border: 1px solid rgba(59, 130, 246, 0.2); padding: 0.5rem 1rem; border-radius: 12px;">
                    <span style="color: #60a5fa; font-weight: 800; font-size: 0.85rem;">📘 <{{ $settings['ambang_unggulan'] ?? 70 }} → Reguler</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats Grid --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2.5rem;" class="delay-1">
        @php
            $stats = [
                ['label' => 'Total Siswa', 'val' => $totalSiswa, 'icon' => '👥', 'bg' => '#f8fafc', 'color' => '#1e293b'],
                ['label' => 'Diterima', 'val' => $totalDiterima, 'icon' => '✅', 'bg' => '#f0fdf4', 'color' => '#166534'],
                ['label' => 'Tidak Diterima', 'val' => $totalTidakDiterima, 'icon' => '❌', 'bg' => '#fff7ed', 'color' => '#9a3412'],
                ['label' => 'Tidak Hadir CBT', 'val' => $totalGugur, 'icon' => '🚫', 'bg' => '#fef2f2', 'color' => '#991b1b'],
                ['label' => 'Sudah Dihitung', 'val' => $sudahDihitung, 'icon' => '🏆', 'bg' => '#f5f3ff', 'color' => '#5b21b6'],
            ];
        @endphp
        @foreach($stats as $s)
        <div class="glass-card" style="background: white;">
            <div style="display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 0.75rem;">
                <div style="width: 44px; height: 44px; border-radius: 14px; background: {{ $s['bg'] }}; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">{{ $s['icon'] }}</div>
            </div>
            <div style="font-size: 2.25rem; font-weight: 900; color: {{ $s['color'] }}; line-height: 1; margin-bottom: 0.4rem;">{{ $s['val'] }}</div>
            <div style="font-size: 0.8rem; font-weight: 700; color: #94a3b8; text-transform: uppercase;">{{ $s['label'] }}</div>
        </div>
        @endforeach
    </div>

    {{-- Main Table Area --}}
    <div class="glass-card delay-2">
        <div style="display: flex; align-items: center; justify-content: space-between; gap: 1.5rem; margin-bottom: 1.5rem; flex-wrap: wrap;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <h2 style="font-size: 1.1rem; font-weight: 900; color: #0f172a; margin: 0;">📋 Daftar Siswa</h2>
                <span style="background: #f1f5f9; color: #64748b; padding: 0.25rem 0.75rem; border-radius: 8px; font-weight: 800; font-size: 0.75rem;">{{ $rows->count() }} siswa</span>
            </div>
            
            <div style="display: flex; align-items: center; gap: 1rem;">
                <form action="{{ route('admin.penempatan.index') }}" method="GET" class="filter-group">
                    <span style="color: #94a3b8; font-size: 0.8rem; font-weight: 700;">Jurusan:</span>
                    <select name="jurusan_id" onchange="this.form.submit()">
                        <option value="">Semua Jurusan</option>
                        @foreach($jurusans as $j)
                            <option value="{{ $j->id }}" {{ Request::get('jurusan_id') == $j->id ? 'selected' : '' }}>{{ $j->nama }}</option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>

        <div style="overflow-x: auto;">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th style="text-align: left;">Siswa</th>
                        <th style="text-align: left;">Jurusan</th>
                        <th style="text-align: center;">Rapor</th>
                        <th style="text-align: center;">CBT</th>
                        <th style="text-align: center;">Bonus</th>
                        <th style="text-align: center;">Skor Akhir</th>
                        <th style="text-align: center;">Penempatan</th>
                        <th style="text-align: center;">Status</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $row)
                        @php
                            $hs = $row['hasil_seleksi'];
                            $p = $row['pendaftaran'];
                            
                            // Badge Styles
                            $statusMap = [
                                'Diterima' => ['bg' => '#dcfce7', 'color' => '#166534', 'label' => 'DITERIMA'],
                                'Tidak Diterima' => ['bg' => '#fee2e2', 'color' => '#991b1b', 'label' => 'TIDAK DITERIMA'],
                                'Tidak Hadir CBT' => ['bg' => '#f1f5f9', 'color' => '#475569', 'label' => 'GUGUR'],
                                'Belum Dihitung' => ['bg' => '#f5f3ff', 'color' => '#5b21b6', 'label' => 'BELUM DIHITUNG'],
                                'Belum CBT' => ['bg' => '#fffbeb', 'color' => '#d97706', 'label' => 'BELUM CBT'],
                            ];
                            $s = $statusMap[$row['status']] ?? $statusMap['Belum Dihitung'];
                            
                            $placeMap = [
                                'Unggulan' => ['bg' => '#fef9c3', 'color' => '#854d0e', 'icon' => '⭐'],
                                'Reguler' => ['bg' => '#dbeafe', 'color' => '#1e40af', 'icon' => '📘'],
                            ];
                            $pl = $placeMap[$row['penempatan']] ?? null;
                        @endphp
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 1rem;">
                                    <div style="width: 40px; height: 40px; background: #f8fafc; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-weight: 900; color: #64748b;">{{ substr($row['nama'], 0, 1) }}</div>
                                    <div>
                                        <div style="font-weight: 800; color: #1e293b; font-size: 0.9rem;">{{ $row['nama'] }}</div>
                                        <div style="font-size: 0.75rem; color: #94a3b8;">{{ $p->nik }}</div>
                                    </div>
                                </div>
                            </td>
                            <td style="font-weight: 700; color: #475569; font-size: 0.85rem;">{{ $row['jurusan'] }}</td>
                            <td style="text-align: center; font-weight: 800; color: #1e293b;">{{ number_format($row['nilai_rapor'], 1) }}</td>
                            <td style="text-align: center; font-weight: 800; color: #1e293b;">{{ $row['nilai_cbt'] !== null ? number_format($row['nilai_cbt'], 1) : '—' }}</td>
                            <td style="text-align: center;">
                                @if($row['bonus_sertifikat'] > 0)
                                    <span style="background: #fef9c3; color: #854d0e; padding: 0.2rem 0.5rem; border-radius: 6px; font-weight: 900; font-size: 0.75rem;">+{{ $row['bonus_sertifikat'] }}</span>
                                @else
                                    <span style="color: #cbd5e1; font-weight: 800;">—</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                @if($row['skor_akhir'] !== null)
                                    <div style="font-size: 1rem; font-weight: 900; color: #0f172a;">{{ number_format($row['skor_akhir'], 2) }}</div>
                                @else
                                    <span style="color: #cbd5e1;">—</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                @if($pl)
                                    <span class="badge-modern" style="background: {{ $pl['bg'] }}; color: {{ $pl['color'] }};">
                                        {{ $pl['icon'] }} {{ $row['penempatan'] }}
                                    </span>
                                @else
                                    <span style="color: #cbd5e1; font-size: 0.75rem; font-weight: 800;">BELUM</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <span class="badge-modern" style="background: {{ $s['bg'] }}; color: {{ $s['color'] }};">
                                    {{ $s['label'] }}
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <a href="{{ route('admin.pendaftaran.show', $p->id) }}" style="width: 32px; height: 32px; background: #f1f5f9; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; color: #64748b; text-decoration: none; transition: all 0.2s;">
                                    👁️
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 4rem 2rem;">
                                <div style="font-size: 3rem; margin-bottom: 1rem;">🔍</div>
                                <h3 style="font-size: 1.25rem; font-weight: 900; color: #1e293b; margin: 0 0 0.5rem;">Tidak ada data ditemukan</h3>
                                <p style="color: #94a3b8; margin: 0;">Siswa yang sudah lolos tahap seleksi administrasi akan muncul di sini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmCalc() {
        Swal.fire({
            title: '<strong>⚡ Hitung Hasil Seleksi?</strong>',
            icon: 'info',
            html: `Sistem akan otomatis menghitung:<br><br>
                   <ul style="text-align: left; font-size: 0.9rem; line-height: 1.8;">
                     <li>✅ Bonus sertifikat tertinggi</li>
                     <li>✅ Skor akhir ({{ $settings['bobot_rapor'] ?? 70 }}% Rapor + {{ $settings['bobot_ujian'] ?? 30 }}% CBT)</li>
                     <li>✅ Penempatan kelas (Threshold {{ $settings['ambang_unggulan'] ?? 70 }})</li>
                     <li>✅ Status kelulusan awal</li>
                   </ul><br>
                   Hasil perhitungan ini <strong>tidak dapat dilihat siswa</strong> sebelum dipublish.`,
            showCancelButton: true,
            confirmButtonText: 'Ya, Hitung Sekarang',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#3b82f6',
            cancelButtonColor: '#94a3b8',
            borderRadius: '24px',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Memproses data...',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading() }
                });
                document.getElementById('fHitung').submit();
            }
        })
    }

    function confirmPublish() {
        Swal.fire({
            title: '<strong>📢 Publish Hasil Seleksi?</strong>',
            icon: 'warning',
            html: `PENTING: Setelah dipublish, hasil akan <strong>langsung tampil di dashboard siswa</strong>.<br><br>
                   Siswa dapat mulai mendownload surat pengumuman resmi. Pastikan semua data sudah benar!`,
            showCancelButton: true,
            confirmButtonText: 'Ya, Publish Sekarang',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#94a3b8',
            borderRadius: '24px',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Memproses publikasi...',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading() }
                });
                document.getElementById('fPublish').submit();
            }
        })
    }
</script>
@endsection
