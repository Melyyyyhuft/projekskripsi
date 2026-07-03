
$word = New-Object -ComObject Word.Application
$word.Visible = $false
$doc = $word.Documents.Add()
$selection = $word.Selection

$selection.Font.Name = "Times New Roman"
$selection.Font.Size = 14
$selection.ParagraphFormat.Alignment = 1 # Center
$selection.Font.Bold = $true
$selection.TypeText("4.5.1 Pengujian Black Box")
$selection.TypeParagraph()
$selection.TypeParagraph()

function AddTable($title, $rows_data) {
    $selection.Font.Size = 12
    $selection.ParagraphFormat.Alignment = 0 # Left
    $selection.Font.Bold = $true
    $selection.TypeText($title)
    $selection.TypeParagraph()
    $selection.Font.Bold = $false
    
    $table = $doc.Tables.Add($selection.Range, ($rows_data.Count + 1), 7)
    $table.Borders.Enable = $true
    
    # Header
    $headers = "No", "Fitur", "Skenario Uji", "Proses yang diharapkan", "Output yang diharapkan", "Tampilan", "Hasil"
    for ($i = 1; $i -le 7; $i++) {
        $cell = $table.Cell(1, $i)
        $cell.Range.Text = $headers[$i-1]
        $cell.Range.Font.Bold = $true
        $cell.Shading.BackgroundPatternColor = -16777216 # Gray
    }
    
    # Data
    $r_idx = 2
    foreach ($row in $rows_data) {
        for ($c_idx = 1; $c_idx -le 7; $c_idx++) {
            $table.Cell($r_idx, $c_idx).Range.Text = $row[$c_idx-1]
        }
        $r_idx++
    }
    
    $selection.EndKey(6)
    $selection.TypeParagraph()
}

$data41 = @(
    ,@("1.", "Menampilkan Jurusan", "Buka landing page.", "Tampil daftar jurusan & kuota.", "Daftar tersusun rapi.", "Sistem", "Sesuai")
    ,@("2.", "Memilih Jurusan", "Siswa memilih saat registrasi.", "Simpan preferensi.", "Konfirmasi muncul.", "Sistem", "Sesuai")
)

AddTable "Tabel 4.1 Pengujian Fitur Pendaftaran Siswa" $data41

$filename = "c:\laragon\www\ppdb-sekolah\Pengujian_Blackbox_PPDB.docx"
$doc.SaveAs([ref]$filename)
$doc.Close()
$word.Quit()
