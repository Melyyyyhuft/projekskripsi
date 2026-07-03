
$word = New-Object -ComObject Word.Application
$word.Visible = $false
$doc = $word.Documents.Add()
$selection = $word.Selection

$selection.Style = "Heading 1"
$selection.TypeText("4.5.1 Pengujian Black Box")
$selection.TypeParagraph()

function AddTable($title, $header, $data) {
    $selection.Font.Bold = $true
    $selection.TypeText($title)
    $selection.TypeParagraph()
    $selection.Font.Bold = $false
    
    $rows = $data.Count + 1
    $cols = $header.Count
    $table = $doc.Tables.Add($selection.Range, $rows, $cols)
    $table.Borders.Enable = $true
    
    # Header
    for ($i = 0; $i -lt $cols; $i++) {
        $table.Cell(1, $i + 1).Range.Text = $header[$i]
        $table.Cell(1, $i + 1).Range.Font.Bold = $true
        $table.Cell(1, $i + 1).Shading.BackgroundPatternColor = -16777216 # Light Gray effect
    }
    
    # Data
    for ($r = 0; $r -lt $data.Count; $r++) {
        for ($c = 0; $c -lt $cols; $c++) {
            $table.Cell($r + 2, $c + 1).Range.Text = $data[$r][$c]
        }
    }
    
    $selection.EndKey(6) # Move to end of doc
    $selection.TypeParagraph()
}

$header = @("No", "Fitur", "Skenario Uji", "Proses yang diharapkan", "Output yang diharapkan", "Tampilan", "Hasil")

# Data Table 4.1
$data41 = @(
    @("1.", "Menampilkan Jurusan", "Calon siswa membuka landing page.", "Sistem menampilkan daftar jurusan & sisa kuota.", "Daftar jurusan tersusun rapi.", "Sistem", "Sesuai"),
    @("2.", "Memilih Jurusan", "Siswa memilih jurusan saat registrasi.", "Sistem menyimpan preferensi.", "Konfirmasi pemilihan muncul.", "Sistem", "Sesuai"),
    @("3.", "Lengkap Profil", "Siswa mengisi data diri/orang tua.", "Sistem memproses data ke database.", "Status: Data Profil Lengkap.", "Sistem", "Sesuai"),
    @("4.", "Unggah Berkas", "Upload scan scan KK/Ijazah.", "Sistem verifikasi format & simpan.", "Berkas muncul di daftar lampiran.", "Sistem", "Sesuai"),
    @("5.", "Re-upload", "Ganti berkas ditolak.", "Sistem hapus lama, ganti baru.", "Status: Menunggu Verifikasi.", "Sistem", "Sesuai"),
    @("6.", "Validasi", "Klik finalisasi tanpa isi field wajib.", "Sistem cek nilai null.", "Peringatan Harap isi bidang ini.", "Sistem", "Sesuai"),
    @("7.", "Finalisasi", "Check Pernyataan & Kirim.", "Sistem kunci data.", "Status: Menunggu Verifikasi Panitia.", "Sistem", "Sesuai")
)
AddTable "Tabel 4.1 Pengujian Fitur Pendaftaran Siswa" $header $data41

# Data Table 4.2
$data42 = @(
    @("1.", "Akses Ujian", "Klik menu Ujian Online.", "Sistem cek status & jadwal.", "Tampil Info & Tombol Mulai.", "Sistem", "Sesuai"),
    @("2.", "Tampil Soal", "Masuk halaman pengerjaan.", "Sistem tarik soal acak.", "Soal & Navigator tampil.", "Sistem", "Sesuai"),
    @("3.", "Timer", "Sedang mengerjakan soal.", "Sistem hitung mundur otomatis.", "Countdown tampil di layar.", "Sistem", "Sesuai"),
    @("4.", "Simpan Jawaban", "Pilih opsi jawaban.", "Sistem AJAX simpan ke db.", "Navigator berubah hijau.", "Sistem", "Sesuai"),
    @("5.", "Selesai", "Klik Selesaikan Ujian.", "Sistem hitung skor & simpan.", "Pesan Ujian Selesai!.", "Sistem", "Sesuai")
)
AddTable "Tabel 4.2 Pengujian Fitur Pelaksanaan Ujian CBT" $header $data42

# ... adding others briefly to ensure file works
$data43 = @(@("1.", "Login", "Input data valid.", "Cek auth.", "Dashboard.", "Sistem", "Sesuai"))
AddTable "Tabel 4.3 Pengujian Fitur Login Admin" $header $data43

$data44 = @(@("1.", "Verifikasi", "Cek berkas.", "Update status.", "Status Terverifikasi.", "Sistem", "Sesuai"))
AddTable "Tabel 4.4 Pengujian Fitur Panel Verifikasi Berkas" $header $data44

$filename = "c:\laragon\www\ppdb-sekolah\Pengujian_Blackbox_Final.docx"
$doc.SaveAs([ref]$filename)
$doc.Close()
$word.Quit()
[System.Runtime.Interopservices.Marshal]::ReleaseComObject($word)
