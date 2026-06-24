<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Surat Hasil Seleksi PPDB - {{ $pendaftaran->user->name }}</title>
    <style>
        @page { margin: 0; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #1e293b; line-height: 1.6; margin: 0; padding: 0; }
        .page-wrapper { padding: 40px 60px; position: relative; }
        
        /* Decoration */
        .top-accent { height: 8px; background: linear-gradient(to right, #3b82f6, #10b981); position: absolute; top: 0; left: 0; right: 0; }
        
        /* Header */
        .header { border-bottom: 2px solid #e2e8f0; padding-bottom: 20px; margin-bottom: 30px; }
        .school-logo { width: 60px; height: 60px; float: left; margin-right: 20px; background: #3b82f6; border-radius: 12px; }
        .school-name { font-size: 20px; font-weight: bold; color: #0f172a; text-transform: uppercase; margin: 0; }
        .school-info { font-size: 11px; color: #64748b; margin: 2px 0; }
        .clear { clear: both; }

        /* Title */
        .doc-title { text-align: center; margin: 40px 0; }
        .doc-title h1 { font-size: 22px; font-weight: 900; color: #0f172a; margin: 0; text-transform: uppercase; letter-spacing: 1px; }
        .doc-title p { font-size: 12px; color: #64748b; margin: 5px 0; font-weight: bold; }

        /* Content */
        .intro { font-size: 13px; margin-bottom: 25px; }
        .table-data { width: 100%; margin-bottom: 30px; font-size: 14px; }
        .table-data td { padding: 8px 0; vertical-align: top; }
        .label { width: 180px; color: #64748b; font-weight: bold; font-size: 12px; text-transform: uppercase; }
        .value { color: #0f172a; font-weight: bold; }

        /* Result Section */
        .result-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 20px; padding: 30px; text-align: center; margin: 30px 0; }
        .status-label { font-size: 12px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 10px; }
        .status-value { font-size: 28px; font-weight: 900; color: #10b981; margin: 0; }
        .placement-value { font-size: 16px; font-weight: bold; color: #3b82f6; margin-top: 5px; }

        /* Decision Table */
        .score-table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 12px; }
        .score-table th, .score-table td { padding: 12px; border: 1px solid #e2e8f0; text-align: center; }
        .score-table th { background: #f1f5f9; color: #475569; font-weight: bold; }

        /* Footer & Signature */
        .footer-section { margin-top: 50px; }
        .qr-section { float: left; width: 120px; text-align: center; }
        .qr-code { width: 100px; height: 100px; padding: 5px; border: 1px solid #e2e8f0; border-radius: 10px; margin-bottom: 8px; }
        .qr-text { font-size: 9px; color: #94a3b8; font-weight: bold; }
        
        .sign-section { float: right; width: 220px; text-align: center; }
        .sign-title { font-size: 12px; color: #1e293b; margin-bottom: 60px; font-weight: bold; }
        .sign-name { font-size: 14px; font-weight: 900; color: #0f172a; margin-bottom: 5px; text-decoration: underline; }
        .sign-nip { font-size: 11px; color: #64748b; }
        .digital-sign { 
            position: absolute; right: 60px; bottom: 120px; width: 150px; opacity: 0.1;
            transform: rotate(-10deg); color: #10b981; font-weight: bold; border: 3px solid #10b981;
            padding: 10px; font-size: 12px; border-radius: 10px; text-align: center;
        }
    </style>
</head>
<body>
    <div class="top-accent"></div>
    <div class="page-wrapper">
        <div class="header">
            <div class="school-logo"></div>
            <div style="float: left;">
                <h2 class="school-name">{{ $settings['nama_sekolah'] ?? 'PPDB SEKOLAH MENENGAH' }}</h2>
                <p class="school-info">{{ $settings['alamat_sekolah'] ?? 'Jl. Pendidikan No. 123, Kota Cerdas' }}</p>
                <p class="school-info">Website: {{ url('/') }} | Email: info@sekolah.sch.id</p>
            </div>
            <div class="clear"></div>
        </div>

        <div class="doc-title">
            <h1>SURAT HASIL SELEKSI PPDB</h1>
            <p>Nomor: {{ $pendaftaran->nomor_pendaftaran }}/PPDB/{{ date('Y') }}</p>
        </div>

        <div class="intro">
            Berdasarkan hasil seleksi Penerimaan Peserta Didik Baru (PPDB) Tahun Pelajaran {{ $settings['tahun_ajaran'] ?? '2026/2027' }}, Panitia Seleksi menetapkan bahwa calon siswa berikut:
        </div>

        <table class="table-data">
            <tr>
                <td class="label">Nama Lengkap</td>
                <td class="value">: {{ $pendaftaran->user->name }}</td>
            </tr>
            <tr>
                <td class="label">NISN</td>
                <td class="value">: {{ $pendaftaran->nisn }}</td>
            </tr>
            <tr>
                <td class="label">Asal Sekolah</td>
                <td class="value">: {{ $pendaftaran->asal_sekolah }}</td>
            </tr>
            <tr>
                <td class="label">Jurusan Pilihan</td>
                <td class="value">: {{ $pendaftaran->jurusan->nama }}</td>
            </tr>
        </table>

        <div class="result-box">
            <div class="status-label">Menyatakan Bahwa Anda Dinyatakan:</div>
            <h2 class="status-value">LULUS / DITERIMA</h2>
            <div class="placement-value">Penempatan: {{ $hasil->penempatan_kelas ?? 'Kelas Reguler' }}</div>
            
            <table class="score-table">
                <thead>
                    <tr>
                        <th>Nilai Rapor</th>
                        <th>Nilai CBT</th>
                        <th>Skor Akhir</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ number_format($pendaftaran->nilai_rapor, 1) }}</td>
                        <td>{{ number_format($pendaftaran->hasilUjian->skor ?? 0, 1) }}</td>
                        <td style="font-weight: 900; color: #3b82f6;">{{ number_format($hasil->skor_akhir, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="intro" style="margin-top: 30px;">
            Demikian surat pengumuman ini disampaikan untuk dipergunakan sebagaimana mestinya. Calon siswa yang dinyatakan diterima wajib melakukan <strong>Daftar Ulang</strong> sesuai dengan ketentuan yang berlaku di sekolah.
        </div>

        <div class="footer-section">
            <div class="qr-section">
                <!-- Fallback to placeholder if QR library not configured -->
                <div class="qr-code">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode(route('admin.penempatan.detail', $pendaftaran->id)) }}" width="100" height="100">
                </div>
                <div class="qr-text">VERIFIKASI DIGITAL</div>
                <div class="qr-text">{{ $pendaftaran->nomor_pendaftaran }}</div>
            </div>

            <div class="sign-section">
                <div class="sign-title">Ditetapkan di Kota Cerdas,<br>{{ now()->translatedFormat('d F Y') }}</div>
                <div class="sign-name">DR. KEPALA SEKOLAH, M.PD</div>
                <div class="sign-nip">NIP. 19800101 200501 1 001</div>
            </div>
            <div class="clear"></div>
        </div>

        <div class="digital-sign">
            DIGITALLY SIGNED<br>AUTHENTIC DOCUMENT
        </div>
    </div>
</body>
</html>
