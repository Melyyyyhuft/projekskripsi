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
                    <td style="font-weight: 600;">Status Pendaftaran</td>
                    <td>
                        @php
                            $isRevision = $pendaftaran->status == 'menunggu_verifikasi' && $pendaftaran->berkas->where('status_verifikasi', 'valid')->count() > 0;
                            
                            $bg = '#e0f2fe';
                            $text = '#0284c7';
                            $label = str_replace('_', ' ', strtoupper($pendaftaran->status));
                            
                            if ($isRevision) {
                                $bg = '#fef3c7'; // Yellow
                                $text = '#d97706';
                                $label = 'REVISI MASUK';
                            } elseif ($pendaftaran->status == 'menunggu_verifikasi' || $pendaftaran->status == 'pending') {
                                $bg = '#e0f2fe'; // Blue
                                $text = '#0284c7';
                                $label = 'PENDING (BARU)';
                            } elseif ($pendaftaran->status == 'revisi') {
                                $bg = '#fef3c7'; // Yellow
                                $text = '#d97706';
                                $label = 'PERLU REVISI';
                            } elseif ($pendaftaran->status == 'lolos_admin' || $pendaftaran->status == 'lolos_administrasi') {
                                $bg = '#d1fae5'; // Green
                                $text = '#059669';
                                $label = 'LOLOS ADMINISTRASI';
                            }
                        @endphp
                        <span style="padding: 0.3rem 0.75rem; border-radius: 99px; font-weight: 800; font-size: 0.75rem; background: {{ $bg }}; color: {{ $text }}; letter-spacing: 0.02em;">
                            {{ $label }}
                        </span>
                    </td>
                </tr>
            </tbody>
        </table>

        @php
            $berkasList = $pendaftaran->berkas;
            $adaPending = $berkasList->contains('status_verifikasi', 'pending');
            $adaDitolak = $berkasList->contains('status_verifikasi', 'tidak_valid');
            
            $wajibs = ['skl', 'rapor', 'pasfoto'];
            $berkasWajib = $berkasList->whereIn('jenis_berkas', $wajibs);
            $semuaWajibAda = collect($wajibs)->every(fn($w) => $berkasList->contains('jenis_berkas', $w));
            $semuaWajibValid = $semuaWajibAda && $berkasWajib->every('status_verifikasi', 'valid');
            
            // Sertifikat juga harus valid jika ada
            $sertifikatValid = $berkasList->where('jenis_berkas', 'sertifikat')->every('status_verifikasi', 'valid');
            
            $bisaLolos = $semuaWajibValid && $sertifikatValid && !$adaPending && !$adaDitolak;
            $bisaRevisi = $adaDitolak;
        @endphp

        @if(in_array($pendaftaran->status, ['menunggu_verifikasi', 'revisi']))
        <div style="margin-top: 2rem; border-top: 1px solid rgba(0,0,0,0.1); padding-top: 1.5rem;">
            <h4 style="margin-bottom: 1rem; color: var(--dark);">Keputusan Verifikasi</h4>
            
            @if($adaPending)
                <div style="background: #fff9db; color: #856404; padding: 0.75rem; border-radius: 8px; font-size: 0.85rem; margin-bottom: 1rem; border-left: 4px solid #fcc419; font-weight: 500;">
                    Masih ada berkas yang berstatus Pending. Harap periksa semua berkas sebelum meloloskan pendaftaran.
                </div>
            @elseif($adaDitolak)
                <div style="background: #fff5f5; color: #c92a2a; padding: 0.75rem; border-radius: 8px; font-size: 0.85rem; margin-bottom: 1rem; border-left: 4px solid #ff6b6b; font-weight: 500;">
                    Ada berkas yang Ditolak. Silakan klik tombol "Minta Revisi" agar siswa dapat memperbaiki berkas tersebut.
                </div>
            @elseif(!$semuaWajibAda)
                <div style="background: #fff5f5; color: #c92a2a; padding: 0.75rem; border-radius: 8px; font-size: 0.85rem; margin-bottom: 1rem; border-left: 4px solid #ff6b6b; font-weight: 500;">
                    Dokumen wajib (SKL, Rapor, atau Pas Foto) belum diunggah secara lengkap.
                </div>
            @endif

            <div style="display: flex; gap: 1rem; flex-direction: column;">
                <form action="{{ route('admin.pendaftaran.verifikasi', $pendaftaran->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="status" value="lolos_admin">
                    <button type="submit" class="btn-primary" style="width: 100%; background: {{ $bisaLolos ? '#10b981' : '#94a3b8' }};" 
                        {{ !$bisaLolos ? 'disabled' : '' }}
                        onclick="return confirm('Loloskan siswa ini?');">
                        Loloskan
                    </button>
                </form>
                
                <form action="{{ route('admin.pendaftaran.verifikasi', $pendaftaran->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="status" value="revisi">
                    <button type="submit" class="btn-primary" style="width: 100%; background: {{ $bisaRevisi ? '#f59e0b' : '#94a3b8' }};" 
                        {{ !$bisaRevisi ? 'disabled' : '' }}
                        onclick="return confirm('Minta siswa untuk revisi berkas?');">
                        Minta Revisi
                    </button>
                </form>
            </div>
        </div>
        @endif
    </div>

    <!-- Berkas -->
    <div class="glass-card">
        <h3 style="margin-bottom: 1.5rem; color: var(--primary);">Berkas Pendukung</h3>
        
        @php
            // Filter agar tidak ganda: ambil yang terbaru untuk tiap jenis (kecuali sertifikat)
            $mandatoryKeys = ['skl', 'rapor', 'pasfoto'];
            $berkasMandatori = $pendaftaran->berkas->whereIn('jenis_berkas', $mandatoryKeys)->sortByDesc('id')->unique('jenis_berkas');
            $sertifikats = $pendaftaran->berkas->where('jenis_berkas', 'sertifikat');
            $displayBerkas = $berkasMandatori->concat($sertifikats)->sortBy('id');
        @endphp

        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            @forelse($displayBerkas as $berkas)
                @php
                    // Logika "Read Only": Berkas valid yang sudah ada SEBELUM revisi terakhir/submission terakhir
                    // Kita anggap "telah diloloskan sebelumnya" jika valid DAN updated_at nya lebih tua dari pendaftaran->updated_at
                    // ATAU jika status pendaftaran sedang 'revisi' dan berkas sudah 'valid'
                    $isOldValid = ($berkas->status_verifikasi == 'valid' && ($pendaftaran->status == 'revisi' || $berkas->updated_at < $pendaftaran->updated_at));
                @endphp
                <div style="border: 1px solid rgba(0,0,0,0.1); border-radius: var(--radius-md); overflow: hidden; background: white;">
                    <div style="background: {{ $berkas->status_verifikasi == 'pending' ? '#eff6ff' : '#f8fafc' }}; padding: 0.75rem 1rem; border-bottom: 1px solid rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                        <div style="display: flex; flex-direction: column;">
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <strong style="text-transform: capitalize; color: var(--primary);">{{ $berkas->jenis_berkas }}</strong>
                                @if($berkas->status_verifikasi == 'pending')
                                    <span style="background: #fef3c7; color: #d97706; padding: 0.15rem 0.6rem; border-radius: 99px; font-size: 0.65rem; font-weight: 800; letter-spacing: 0.05em; border: 1px solid #fde68a;">📌 REVISI MASUK</span>
                                @endif
                            </div>
                            <span style="font-size: 0.8rem; color: var(--gray-text);">{{ $berkas->nama_file }}</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            @if($isOldValid)
                                <span style="background: #f0fdf4; color: #166534; padding: 0.3rem 0.75rem; border-radius: 8px; font-size: 0.65rem; font-weight: 800; border: 1px solid #dcfce7;">📜 TERVERIFIKASI (SISTEM)</span>
                            @elseif($berkas->status_verifikasi == 'valid')
                                <span style="background: #d1fae5; color: #059669; padding: 0.3rem 0.75rem; border-radius: 8px; font-size: 0.65rem; font-weight: 800;">✅ TERVERIFIKASI</span>
                            @elseif($berkas->status_verifikasi == 'tidak_valid')
                                <span style="background: #fee2e2; color: #dc2626; padding: 0.3rem 0.75rem; border-radius: 8px; font-size: 0.65rem; font-weight: 800;">❌ DITOLAK</span>
                            @else
                                <span style="background: #dbeafe; color: #1e40af; padding: 0.3rem 0.75rem; border-radius: 8px; font-size: 0.65rem; font-weight: 800;">⏳ MENUNGGU VERIFIKASI</span>
                            @endif
                        </div>
                    </div>
                    
                    @if($berkas->jenis_berkas == 'sertifikat' && $berkas->jenis_prestasi)
                    <div style="padding: 0.75rem 1rem; background: #fefce8; border-bottom: 1px solid rgba(0,0,0,0.1); display: flex; gap: 2rem;">
                        <div><span style="font-size:0.8rem; color:#854d0e;">Jenis Prestasi:</span> <strong style="color:#a16207;">{{ $berkas->jenis_prestasi }}</strong></div>
                        <div><span style="font-size:0.8rem; color:#854d0e;">Tingkat:</span> <strong style="color:#a16207;">{{ $berkas->tingkat_prestasi }}</strong></div>
                    </div>
                    @endif

                    @if($berkas->status_verifikasi == 'tidak_valid' && $berkas->catatan_admin)
                    <div style="padding: 0.75rem 1rem; background: #fef2f2; border-bottom: 1px solid rgba(0,0,0,0.1);">
                        <span style="font-size:0.8rem; color:#b91c1c;">Alasan Ditolak:</span> <strong style="color:#991b1b;">{{ $berkas->catatan_admin }}</strong>
                    </div>
                    @endif
                    
                    <div style="background: #e2e8f0; display: flex; justify-content: center; align-items: center; min-height: 300px; padding: 1rem;">
                        @if(in_array(strtolower($berkas->file_type), ['jpg', 'jpeg', 'png']))
                            <img src="{{ asset('storage/' . $berkas->file_path) }}" alt="{{ $berkas->jenis_berkas }}" style="max-width: 100%; max-height: 500px; border-radius: var(--radius-sm); box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); cursor: pointer;" onclick="openLightbox(this.src, 'img')">
                        @elseif(strtolower($berkas->file_type) == 'pdf')
                            <div style="position:relative; width:100%; border-radius: var(--radius-sm); overflow:hidden;">
                                <iframe src="{{ asset('storage/' . $berkas->file_path) }}" width="100%" height="400px" style="border: none; pointer-events: none;"></iframe>
                                <div style="position:absolute; top:0; left:0; width:100%; height:100%; display:flex; align-items:center; justify-content:center; background:rgba(0,0,0,0.05); cursor:pointer; transition: background 0.3s;" onmouseover="this.style.background='rgba(0,0,0,0.2)'" onmouseout="this.style.background='rgba(0,0,0,0.05)'" onclick="openLightbox('{{ asset('storage/' . $berkas->file_path) }}', 'pdf')">
                                    <span style="background:var(--primary); color:white; padding:0.6rem 1.2rem; border-radius:999px; font-weight:bold; font-size: 0.9rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">🔍 Klik untuk Fullscreen PDF</span>
                                </div>
                            </div>
                        @else
                            <a href="{{ asset('storage/' . $berkas->file_path) }}" target="_blank" class="btn-primary">Unduh File</a>
                        @endif
                    </div>

                    @if(in_array($pendaftaran->status, ['menunggu_verifikasi', 'revisi']))
                        @if($berkas->status_verifikasi != 'valid')
                        <div style="padding: 1rem; background: #fff; display: flex; gap: 0.5rem; border-top: 1px solid rgba(0,0,0,0.1);">
                            <form action="{{ route('admin.pendaftaran.verifikasi_berkas', $berkas->id) }}" method="POST" style="flex: 1;">
                                @csrf
                                <input type="hidden" name="status_verifikasi" value="valid">
                                <button type="submit" class="btn-outline" style="width: 100%; border-color: #10b981; color: #10b981; padding: 0.5rem;" onclick="return confirm('Terima berkas ini?');">✅ Terima File</button>
                            </form>
                            <button type="button" class="btn-outline btn-tolak-berkas" style="flex: 1; border-color: #ef4444; color: #ef4444; padding: 0.5rem;" data-id="{{ $berkas->id }}">❌ Tolak File</button>
                        </div>
                        @elseif($isOldValid)
                        <div style="padding: 1rem; background: #f0fdf4; display: flex; align-items: center; justify-content: center; gap: 0.5rem; border-top: 1px solid #dcfce7; color: #166534; font-size: 0.85rem; font-weight: 600;">
                            <i class="fa-solid fa-circle-check"></i> Berkas ini sebelumnya sudah diloloskan (Read-only)
                        </div>
                        @endif

                        <!-- Form Tolak Tersembunyi -->
                        <form action="{{ route('admin.pendaftaran.verifikasi_berkas', $berkas->id) }}" method="POST" id="form-tolak-{{ $berkas->id }}" style="display: none; padding: 1rem; background: #f8fafc; border-top: 1px solid rgba(0,0,0,0.1);">
                            @csrf
                            <input type="hidden" name="status_verifikasi" value="tidak_valid">
                            <div style="margin-bottom: 0.5rem;">
                                <label style="font-size: 0.85rem; font-weight: 600;">Alasan Penolakan:</label>
                                <textarea name="catatan_admin" class="form-control" rows="2" placeholder="Contoh: Sertifikat webinar tidak diakui" required style="margin-top: 0.25rem; font-size: 0.85rem; padding: 0.5rem;"></textarea>
                            </div>
                            <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                                <button type="button" class="btn-outline btn-batal-tolak" data-id="{{ $berkas->id }}" style="padding: 0.3rem 0.75rem; font-size: 0.8rem;">Batal</button>
                                <button type="submit" class="btn-primary" style="background: #ef4444; padding: 0.3rem 0.75rem; font-size: 0.8rem;">Simpan Penolakan</button>
                            </div>
                        </form>
                    @endif
                </div>
            @empty
                <div style="text-align: center; color: var(--gray-text); padding: 3rem;">
                    Siswa ini belum mengunggah berkas apapun.
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Lightbox Modal -->
<div id="lightbox" style="display:none; position:fixed; z-index:9999; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.85); align-items:center; justify-content:center; backdrop-filter: blur(4px);">
    <span onclick="closeLightbox()" style="position:absolute; top:20px; right:30px; color:white; font-size:40px; cursor:pointer; font-weight: bold; text-shadow: 0 2px 4px rgba(0,0,0,0.5);">&times;</span>
    <img id="lightbox-img" style="max-width:90%; max-height:90%; border-radius:8px; box-shadow: 0 10px 25px rgba(0,0,0,0.5); display:none;">
    <iframe id="lightbox-pdf" style="width:80%; height:90%; border-radius:8px; border:none; box-shadow: 0 10px 25px rgba(0,0,0,0.5); display:none; background:white;"></iframe>
</div>

<script>
    // Lightbox Logic
    function openLightbox(src, type = 'img') {
        if(type === 'pdf') {
            document.getElementById('lightbox-img').style.display = 'none';
            document.getElementById('lightbox-pdf').src = src;
            document.getElementById('lightbox-pdf').style.display = 'block';
        } else {
            document.getElementById('lightbox-pdf').style.display = 'none';
            document.getElementById('lightbox-img').src = src;
            document.getElementById('lightbox-img').style.display = 'block';
        }
        document.getElementById('lightbox').style.display = 'flex';
    }
    function closeLightbox() {
        document.getElementById('lightbox').style.display = 'none';
        document.getElementById('lightbox-pdf').src = ''; // Clear iframe src to stop playing/loading
    }
    document.getElementById('lightbox').addEventListener('click', function(e) {
        if (e.target === this) closeLightbox();
    });

    // Form Tolak Toggle
    document.querySelectorAll('.btn-tolak-berkas').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            document.getElementById('form-tolak-' + id).style.display = 'block';
            this.parentElement.style.display = 'none';
        });
    });

    document.querySelectorAll('.btn-batal-tolak').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            document.getElementById('form-tolak-' + id).style.display = 'none';
            this.closest('.glass-card').querySelector('.btn-tolak-berkas').parentElement.style.display = 'flex'; // Wait, closest is wrong because they are siblings
            // The logic: find the parent form, then find the sibling div containing the buttons
            const form = document.getElementById('form-tolak-' + id);
            form.style.display = 'none';
            form.previousElementSibling.style.display = 'flex';
        });
    });
</script>
@endsection
