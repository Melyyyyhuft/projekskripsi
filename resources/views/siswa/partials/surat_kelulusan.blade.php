{{-- ═══════════════════════════════════════════════════
     TEMPLATE SURAT KELULUSAN RESMI
     Digunakan untuk: Print CSS & html2pdf Download
═══════════════════════════════════════════════════ --}}
<div style="font-family:'Times New Roman',Times,serif;color:#0f172a;background:white;line-height:1.6;font-size:12pt;">

    {{-- ─── Kop Surat ─── --}}
    <div style="border-bottom:3px solid #1e3a5f;padding-bottom:16px;margin-bottom:24px;display:flex;align-items:center;gap:20px;">
        @if($logoSekolah)
            <img src="{{ $logoSekolah }}" alt="Logo" style="width:72px;height:72px;object-fit:contain;">
        @else
            <div style="width:72px;height:72px;background:linear-gradient(135deg,#1e3a5f,#3b82f6);border-radius:50%;display:flex;align-items:center;justify-content:center;">
                <span style="color:white;font-size:28px;font-weight:900;">S</span>
            </div>
        @endif
        <div>
            <div style="font-size:10pt;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#475569;margin-bottom:2px;">PANITIA PENERIMAAN PESERTA DIDIK BARU (PPDB)</div>
            <div style="font-size:18pt;font-weight:900;text-transform:uppercase;color:#0f172a;letter-spacing:-.01em;">{{ strtoupper($namaSekolah) }}</div>
            <div style="font-size:9pt;color:#64748b;">Tahun Ajaran {{ $tahunAjaran }}</div>
        </div>
    </div>

    {{-- ─── Judul Surat ─── --}}
    <div style="text-align:center;margin-bottom:24px;">
        <div style="font-size:14pt;font-weight:900;text-transform:uppercase;text-decoration:underline;letter-spacing:.05em;color:#0f172a;">SURAT KEPUTUSAN KELULUSAN</div>
        <div style="font-size:10pt;color:#475569;margin-top:6px;">Nomor: <strong>{{ $nomorSurat }}</strong></div>
    </div>

    {{-- ─── Kalimat Pembuka ─── --}}
    <p style="text-align:justify;margin-bottom:16px;">
        Berdasarkan hasil proses seleksi administrasi dan ujian online (CBT) Penerimaan Peserta Didik Baru (PPDB) <strong>{{ $namaSekolah }}</strong> Tahun Ajaran <strong>{{ $tahunAjaran }}</strong>, Panitia PPDB menetapkan bahwa:
    </p>

    {{-- ─── Data Siswa ─── --}}
    <table style="width:100%;border-collapse:collapse;margin:20px 0 20px 16px;">
        <tr>
            <td style="width:30%;padding:5px 0;vertical-align:top;">Nama Lengkap</td>
            <td style="width:4%;padding:5px 0;vertical-align:top;text-align:center;">:</td>
            <td style="padding:5px 0;vertical-align:top;font-weight:700;">{{ Auth::user()->name }}</td>
        </tr>
        <tr>
            <td style="padding:5px 0;vertical-align:top;">NISN</td>
            <td style="padding:5px 0;vertical-align:top;text-align:center;">:</td>
            <td style="padding:5px 0;vertical-align:top;font-weight:700;font-family:monospace;">{{ $pendaftaran->nisn }}</td>
        </tr>
        <tr>
            <td style="padding:5px 0;vertical-align:top;">Asal Sekolah</td>
            <td style="padding:5px 0;vertical-align:top;text-align:center;">:</td>
            <td style="padding:5px 0;vertical-align:top;">{{ $pendaftaran->asal_sekolah }}</td>
        </tr>
        <tr>
            <td style="padding:5px 0;vertical-align:top;">Dinyatakan</td>
            <td style="padding:5px 0;vertical-align:top;text-align:center;">:</td>
            <td style="padding:5px 0;vertical-align:top;font-weight:900;font-size:14pt;color:#059669;">LULUS ✓</td>
        </tr>
        <tr>
            <td style="padding:5px 0;vertical-align:top;">Jurusan</td>
            <td style="padding:5px 0;vertical-align:top;text-align:center;">:</td>
            <td style="padding:5px 0;vertical-align:top;font-weight:700;">{{ $pendaftaran->jurusan->nama }}</td>
        </tr>
        <tr>
            <td style="padding:5px 0;vertical-align:top;">Jalur Penerimaan</td>
            <td style="padding:5px 0;vertical-align:top;text-align:center;">:</td>
            <td style="padding:5px 0;vertical-align:top;font-weight:700;">{{ $badgeText }}</td>
        </tr>
        <tr>
            <td style="padding:5px 0;vertical-align:top;">Nilai CBT</td>
            <td style="padding:5px 0;vertical-align:top;text-align:center;">:</td>
            <td style="padding:5px 0;vertical-align:top;">{{ $hasilUjian ? $hasilUjian->skor : '—' }}</td>
        </tr>
        <tr>
            <td style="padding:5px 0;vertical-align:top;">Nilai Rapor</td>
            <td style="padding:5px 0;vertical-align:top;text-align:center;">:</td>
            <td style="padding:5px 0;vertical-align:top;">{{ $pendaftaran->nilai_rapor }}</td>
        </tr>
        <tr>
            <td style="padding:5px 0;vertical-align:top;">Skor Akhir Seleksi</td>
            <td style="padding:5px 0;vertical-align:top;text-align:center;">:</td>
            <td style="padding:5px 0;vertical-align:top;font-weight:700;">{{ $hasil->skor_akhir }}</td>
        </tr>
    </table>

    {{-- ─── Kalimat Penutup ─── --}}
    <p style="text-align:justify;margin-bottom:8px;">
        Peserta yang dinyatakan lulus diwajibkan hadir untuk melakukan <strong>daftar ulang</strong> dengan membawa berkas asli dan kelengkapan administrasi. Ketidakhadiran dalam batas waktu yang ditentukan dapat mengakibatkan pembatalan status kelulusan.
    </p>
    <p style="text-align:justify;margin-bottom:32px;">
        Demikian surat keputusan ini dibuat dengan sebenarnya untuk digunakan sebagaimana mestinya.
    </p>

    {{-- ─── Tanda Tangan ─── --}}
    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-top:40px;">
        {{-- Kode Verifikasi --}}
        <div style="max-width:240px;">
            <div style="background:#f8fafc;border:1.5px solid #e2e8f0;border-radius:10px;padding:12px 14px;">
                <div style="font-size:8pt;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px;font-family:Arial,sans-serif;">Kode Verifikasi</div>
                <div style="font-family:monospace;font-size:13pt;font-weight:900;color:#0f172a;letter-spacing:.12em;">{{ $kodeVerif }}</div>
                <div style="font-size:7.5pt;color:#94a3b8;margin-top:6px;font-family:Arial,sans-serif;line-height:1.4;">Dokumen ini dapat diverifikasi melalui sistem PPDB {{ $namaSekolah }}</div>
            </div>
        </div>

        {{-- Tanda Tangan Kepala Sekolah --}}
        <div style="text-align:center;min-width:200px;">
            <div style="font-size:10pt;margin-bottom:4px;">{{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }},</div>
            <div style="font-size:10pt;margin-bottom:60px;">Kepala Sekolah / Ketua Panitia PPDB</div>
            <div style="border-bottom:1.5px solid #0f172a;margin-bottom:4px;"></div>
            <div style="font-weight:700;font-size:10pt;">Panitia PPDB {{ $namaSekolah }}</div>
        </div>
    </div>

    {{-- ─── Footer ─── --}}
    <div style="border-top:2px solid #e2e8f0;margin-top:40px;padding-top:12px;display:flex;justify-content:space-between;align-items:center;">
        <div style="font-size:8pt;color:#94a3b8;font-family:Arial,sans-serif;">
            Diterbitkan oleh Sistem PPDB {{ $namaSekolah }} | Nomor: {{ $nomorSurat }}
        </div>
        <div style="font-size:8pt;color:#94a3b8;font-family:Arial,sans-serif;">
            Dokumen sah jika memiliki kode verifikasi yang valid.
        </div>
    </div>

</div>
