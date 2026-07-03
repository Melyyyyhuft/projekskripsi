$word = New-Object -ComObject Word.Application
$word.Visible = $false
$doc = $word.Documents.Add()
$selection = $word.Selection

# Set font default
$selection.Font.Name = "Times New Roman"
$selection.Font.Size = 12

function SetCellText($cell, $text, $bold = $false, $center = $false) {
    $cell.Range.Text = $text
    $cell.Range.Font.Name = "Times New Roman"
    $cell.Range.Font.Size = 11
    $cell.Range.Font.Bold = $bold
    if ($center) {
        $cell.Range.ParagraphFormat.Alignment = 1
    } else {
        $cell.Range.ParagraphFormat.Alignment = 0
    }
}

function AddSectionHeader($text) {
    $selection.Font.Name = "Times New Roman"
    $selection.Font.Size = 12
    $selection.Font.Bold = $true
    $selection.ParagraphFormat.Alignment = 0
    $selection.TypeText($text)
    $selection.Font.Bold = $false
    $selection.TypeParagraph()
}

function AddTableSubtitle($text) {
    $selection.Font.Name = "Times New Roman"
    $selection.Font.Size = 12
    $selection.Font.Bold = $false
    $selection.ParagraphFormat.Alignment = 0
    $selection.TypeText($text)
    $selection.TypeParagraph()
}

function AddBlackboxTable($rows_data) {
    $headers = ,@("No", "Fitur", "Skenario Uji", "Proses yang Diharapkan", "Output yang Diharapkan", "Tampilan", "Hasil")
    $allRows = @($headers) + $rows_data
    $numRows = $allRows.Count
    $numCols = 7

    $range = $selection.Range
    $table = $doc.Tables.Add($range, $numRows, $numCols)
    $table.Borders.Enable = $true
    $table.Style = "Table Grid"

    # Column widths (in cm * 567 = twips; Word unit is points 72/inch, 1cm = 28.35pt)
    # Total page width ~16cm
    $colWidths = @(50, 110, 160, 160, 160, 80, 60) # in points
    for ($c = 1; $c -le $numCols; $c++) {
        $table.Columns($c).Width = $colWidths[$c-1]
    }

    for ($r = 0; $r -lt $numRows; $r++) {
        $isHeader = ($r -eq 0)
        for ($c = 0; $c -lt $numCols; $c++) {
            $cell = $table.Cell($r + 1, $c + 1)
            $text = $allRows[$r][$c]
            $bold = $isHeader
            $center = ($isHeader -or $c -eq 0 -or $c -eq 5 -or $c -eq 6)
            SetCellText $cell $text $bold $center

            if ($isHeader) {
                $cell.Shading.BackgroundPatternColor = [Microsoft.Office.Interop.Word.WdColor]::wdColorGray25
            }
        }
    }

    $selection.EndKey(6)
    $selection.TypeParagraph()
}

#----------- TITLE ------------
$selection.Style = $doc.Styles["Heading 2"]
$selection.Font.Name = "Times New Roman"
$selection.ParagraphFormat.Alignment = 0
$selection.TypeText("4.5 Pengujian Sistem")
$selection.TypeParagraph()
$selection.Style = $doc.Styles["Heading 3"]
$selection.TypeText("4.5.1 Pengujian Black Box")
$selection.TypeParagraph()
$selection.Style = $doc.Styles["Normal"]
$selection.Font.Name = "Times New Roman"
$selection.Font.Size = 12

#===== TABEL 4.1 - Halaman Landing & Autentikasi ====
AddSectionHeader "a.	Pengujian Fitur Halaman Beranda dan Autentikasi"
AddTableSubtitle "Tabel 4.1 Pengujian Fitur Halaman Beranda dan Autentikasi"

$t1 = @(
    ,@("1.", "Tampilan Landing Page", "Pengguna membuka URL http://ppdb-sekolah.test/", "Sistem merender halaman beranda dengan daftar jurusan, informasi sekolah, dan status PPDB dari database.", "Halaman beranda tampil dengan daftar jurusan (nama & kuota), tombol 'Daftar Sekarang' aktif jika PPDB dibuka, atau nonaktif jika ditutup.", "(Sistem)", "Sesuai"),
    ,@("2.", "Status PPDB Ditutup", "Period PPDB belum atau sudah lewat dari konfigurasi admin.", "Sistem membaca kolom tanggal periode dari tabel pengaturans dan membandingkannya dengan tanggal hari ini.", "Tombol 'Daftar Sekarang' berubah menjadi abu-abu dengan label 'Pendaftaran Ditutup' dan tidak dapat diklik.", "(Sistem)", "Sesuai"),
    ,@("3.", "Login Kolom Kosong", "Pengguna mengeklik tombol 'Masuk' tanpa mengisi email dan password.", "Sistem mendeteksi field kosong sebelum form dikirim ke AuthController.", "Browser menampilkan peringatan bawaan: 'Harap isi bidang ini' pada field yang kosong.", "(Sistem)", "Sesuai"),
    ,@("4.", "Login Email Tidak Terdaftar", "Pengguna mengisi email yang tidak ada di database, lalu klik 'Masuk'.", "Sistem memverifikasi email ke tabel users dan gagal menemukan akun.", "Halaman login kembali dengan pesan error: 'These credentials do not match our records.'", "(Sistem)", "Sesuai"),
    ,@("5.", "Login Password Salah", "Pengguna mengisi email yang terdaftar namun password salah.", "Sistem mencocokkan hash password dengan data di database dan menemukan ketidakcocokan.", "Halaman login kembali dengan pesan alert merah berisi pesan error kredensial.", "(Sistem)", "Sesuai"),
    ,@("6.", "Login Berhasil (Siswa)", "Siswa mengisi email dan password yang benar lalu klik 'Masuk'.", "Sistem memverifikasi kredensial, membuat sesi login, dan memeriksa role akun sebagai siswa.", "Pengguna diarahkan ke halaman /siswa/dashboard.", "(Sistem)", "Sesuai"),
    ,@("7.", "Login Berhasil (Admin)", "Admin mengisi email dan password yang benar lalu klik 'Masuk'.", "Sistem memverifikasi kredensial, membuat sesi login, dan memeriksa role akun sebagai admin.", "Pengguna diarahkan ke halaman /admin/dashboard.", "(Sistem)", "Sesuai"),
    ,@("8.", "Lupa Password", "Pengguna mengeklik link 'Lupa Password?' di halaman login.", "Sistem menavigasi ke halaman lupa-password.", "Halaman /lupa-password tampil dengan instruksi pemulihan akun.", "(Sistem)", "Sesuai"),
    ,@("9.", "Registrasi Akun Kosong", "Pengguna mengeklik 'Daftar Akun' tanpa mengisi form pendaftaran.", "Sistem mendeteksi field kosong dari validasi HTML5 sebelum form dikirim.", "Browser menampilkan peringatan pada field Nama Lengkap yang wajib diisi.", "(Sistem)", "Sesuai"),
    ,@("10.", "Registrasi Email Tidak Valid", "Pengguna mengisi email tidak berformat @gmail.com, lalu klik 'Daftar'.", "Sistem mengecek pattern validasi email sebelum submit form.", "Input email berwarna merah dan tampil pesan: 'Email harus diawali huruf dan menggunakan domain @gmail.com'.", "(Sistem)", "Sesuai"),
    ,@("11.", "Registrasi Password Tidak Cocok", "Pengguna mengisi password berbeda di kolom password dan konfirmasi password.", "Sistem memvalidasi kecocokan dua field password di AuthController.", "Halaman kembali dengan error validasi: 'The password confirmation does not match'.", "(Sistem)", "Sesuai"),
    ,@("12.", "Registrasi Berhasil", "Pengguna mengisi semua field registrasi dengan format yang benar.", "Sistem menyimpan akun baru ke tabel users, membuat sesi, dan mengarahkan ke dashboard siswa.", "Pengguna berhasil masuk dan diarahkan ke /siswa/dashboard sebagai akun siswa baru.", "(Sistem)", "Sesuai"),
    ,@("13.", "Logout", "Pengguna mengeklik tombol 'Logout' dari menu.", "Sistem menghapus sesi login yang aktif dan menutup akses ke halaman terproteksi.", "Pengguna diarahkan kembali ke halaman login /login.", "(Sistem)", "Sesuai")
)
AddBlackboxTable $t1

#===== TABEL 4.2 - Dashboard & Pendaftaran Siswa ====
AddSectionHeader "b.	Pengujian Fitur Dashboard dan Pendaftaran Siswa"
AddTableSubtitle "Tabel 4.2 Pengujian Fitur Dashboard dan Pendaftaran Siswa"

$t2 = @(
    ,@("1.", "Akses Dashboard Siswa", "Siswa yang sudah login mengakses /siswa/dashboard.", "Sistem memuat data pendaftaran, status, hasil ujian, dan progress langkah PPDB milik siswa.", "Dashboard menampilkan kartu status pendaftaran, progress bar (0-100%), dan card aksi sesuai tahap yang sedang dilalui.", "(Sistem)", "Sesuai"),
    ,@("2.", "Notifikasi Perlu Revisi", "Status pendaftaran siswa adalah 'revisi' (berkas ditolak admin).", "Sistem mendeteksi status revisi dan menampilkan alert khusus di bagian atas dashboard.", "Muncul banner merah bertuliskan 'Perlu Revisi' beserta tombol 'Lihat Revisi' dan popup notifikasi dari SweetAlert2.", "(Sistem)", "Sesuai"),
    ,@("3.", "Memulai Isi Data Pendaftaran", "Siswa baru yang belum mendaftar mengeklik 'Mulai Isi Data'.", "Sistem menavigasi ke halaman formulir pendaftaran /siswa/pendaftaran.", "Formulir pendaftaran multitab tampil dengan field data diri, data orang tua, asal sekolah, pilihan jurusan, dan unggah berkas.", "(Sistem)", "Sesuai"),
    ,@("4.", "Simpan Data Pendaftaran", "Siswa mengisi seluruh form pendaftaran dan mengeklik 'Simpan Pendaftaran'.", "Sistem memvalidasi semua field, menyimpan data ke tabel pendaftarans, dan mengubah status menjadi 'menunggu_verifikasi'.", "Halaman kembali ke dashboard dengan status berubah menjadi 'Menunggu Verifikasi' dan muncul notifikasi sukses.", "(Sistem)", "Sesuai"),
    ,@("5.", "Unggah Berkas Wajib", "Siswa memilih file dokumen (KK, Ijazah, Akta) dan mengunggah lewat form.", "Sistem menerima file, memvalidasi tipe dan ukuran, lalu menyimpannya di storage server dan mencatat ke tabel berkas.", "Berkas tampil di daftar lampiran dengan pratinjau link dan status 'Menunggu Verifikasi'.", "(Sistem)", "Sesuai"),
    ,@("6.", "Re-upload Berkas Revisi", "Admin menolak berkas tertentu; siswa mengunggah ulang berkas yang ditolak.", "Sistem mengganti file lama dengan file baru di storage dan memperbarui status berkas di database.", "Status pendaftaran kembali ke 'menunggu_verifikasi' dan berkas terbaru tampil tanggal perubahan terkini.", "(Sistem)", "Sesuai"),
    ,@("7.", "Lihat Hasil Seleksi", "Siswa yang telah diterima mengeklik 'Lihat Detail' di card Hasil Seleksi.", "Sistem menavigasi ke halaman /siswa/hasil dan memuat data hasilSeleksi dari database.", "Halaman hasil menampilkan status kelulusan, nama jurusan yang diterima, dan skor akhir seleksi.", "(Sistem)", "Sesuai"),
    ,@("8.", "Unduh Surat Hasil (PDF)", "Siswa mengeklik tombol 'Unduh PDF' di dashboard atau halaman hasil.", "Sistem merender template PDF dari data pendaftaran dan hasil seleksi siswa menggunakan library PDF.", "File surat penerimaan/penolakan berformat PDF otomatis terunduh ke perangkat siswa.", "(Sistem)", "Sesuai")
)
AddBlackboxTable $t2

#===== TABEL 4.3 - Ujian CBT Siswa ====
AddSectionHeader "c.	Pengujian Fitur Ujian CBT (Computer Based Test)"
AddTableSubtitle "Tabel 4.3 Pengujian Fitur Ujian CBT"

$t3 = @(
    ,@("1.", "Halaman Info Ujian", "Siswa yang berstatus 'lolos_admin' mengakses menu Ujian.", "Sistem memeriksa status siswa dan ketersediaan ujian aktif dalam periode berlaku.", "Halaman /siswa/ujian menampilkan informasi jadwal ujian, durasi, dan tombol 'Mulai Ujian Sekarang'.", "(Sistem)", "Sesuai"),
    ,@("2.", "Akses Ditolak (Belum Verifikasi)", "Siswa yang belum 'lolos_admin' mencoba mengakses halaman ujian.", "Sistem memeriksa status pendaftaran dan mendeteksi belum lolos verifikasi admin.", "Halaman ujian menampilkan tombol 'Belum Tersedia' yang nonaktif beserta keterangan status.", "(Sistem)", "Sesuai"),
    ,@("3.", "Ujian Belum Dimulai (Period)", "Status CBT aktif namun tanggal hari ini sebelum tanggal mulai ujian.", "Sistem membandingkan tanggal sekarang dengan cbt_tgl_mulai dari tabel pengaturans.", "Tombol ujian menampilkan ikon jam: 'Belum Dimulai' dan informasi tanggal mulai ujian.", "(Sistem)", "Sesuai"),
    ,@("4.", "Mulai Ujian", "Siswa mengeklik 'Mulai Ujian Sekarang' dan mengkonfirmasi.", "Sistem POST ke /siswa/ujian/mulai, mencatat waktu_mulai_ujian di tabel pendaftarans, dan membagikan soal secara acak.", "Halaman ujian CBT tampil dengan soal pertama, navigator nomor soal, dan countdown timer.", "(Sistem)", "Sesuai"),
    ,@("5.", "Timer Countdown", "Siswa sedang mengerjakan soal dan timer berjalan.", "Sistem menghitung mundur durasi ujian setiap detik di sisi klien (JavaScript).", "Countdown tampil di pojok kanan atas, warna berubah menjadi merah saat sisa waktu kurang dari 5 menit.", "(Sistem)", "Sesuai"),
    ,@("6.", "Simpan Jawaban Per Soal", "Siswa memilih salah satu opsi jawaban (A/B/C/D/E).", "Sistem mengirim request AJAX untuk menyimpan pilihan ke tabel jawabans.", "Nomor soal pada navigator berubah menjadi warna hijau sebagai penanda soal sudah dijawab.", "(Sistem)", "Sesuai"),
    ,@("7.", "Navigasi Antar Soal", "Siswa mengeklik nomor soal di panel navigator atau tombol Sebelumnya/Berikutnya.", "Sistem memuat soal yang dipilih tanpa reload halaman.", "Soal berganti sesuai nomor yang dipilih dan jawaban sebelumnya tetap tersimpan.", "(Sistem)", "Sesuai"),
    ,@("8.", "Submit Ujian Manual", "Siswa mengeklik tombol 'Selesaikan Ujian' sebelum waktu habis.", "Sistem POST ke /siswa/ujian/submit, menghitung total jawaban benar, dan menyimpan skor ke tabel hasil_ujians.", "Halaman beralih ke halaman konfirmasi selesai dan status pendaftaran siswa berubah menjadi 'sudah_ujian'.", "(Sistem)", "Sesuai"),
    ,@("9.", "Submit Otomatis (Waktu Habis)", "Waktu ujian habis sebelum siswa mengeklik submit.", "Sistem JavaScript otomatis mengirimkan form submit saat timer mencapai nol.", "Ujian tersubmit otomatis dan siswa melihat halaman konfirmasi selesai.", "(Sistem)", "Sesuai"),
    ,@("10.", "Ujian Sudah Dikerjakan", "Siswa yang sudah mengerjakan ujian mencoba mengakses halaman ujian kembali.", "Sistem mendeteksi bahwa hasilUjian sudah ada di database untuk siswa ini.", "Tombol ujian berubah menjadi 'Ujian Selesai' (nonaktif) dan nilai CBT ditampilkan di dashboard.", "(Sistem)", "Sesuai")
)
AddBlackboxTable $t3

#===== TABEL 4.4 - Dashboard Admin ====
AddSectionHeader "d.	Pengujian Fitur Dashboard Admin"
AddTableSubtitle "Tabel 4.4 Pengujian Fitur Dashboard Admin"

$t4 = @(
    ,@("1.", "Statistik Pendaftar", "Admin membuka halaman /admin/dashboard.", "Sistem melakukan query ke tabel pendaftarans untuk menghitung jumlah berdasarkan status.", "Tampil 5 kartu statistik: Total Pendaftar, Menunggu, Lolos Admin, Sudah Ujian, dan Hasil Final.", "(Sistem)", "Sesuai"),
    ,@("2.", "Kuota per Jurusan", "Admin melihat panel 'Kuota per Jurusan' di dashboard.", "Sistem menghitung jumlah siswa diterima per jurusan vs kuota total.", "Setiap jurusan tampil progress bar: hijau jika sisa banyak, kuning jika mendekati penuh, merah jika penuh.", "(Sistem)", "Sesuai"),
    ,@("3.", "Tabel Pendaftar Terbaru", "Admin melihat tabel 5 pendaftar paling baru.", "Sistem mengambil 5 data terbaru dari tabel pendaftarans dengan relasi ke user dan jurusan.", "Tabel menampilkan Nama, Asal Sekolah, Jurusan, Status badge berwarna, dan tombol 'Detail'.", "(Sistem)", "Sesuai"),
    ,@("4.", "Aksi Cepat (Quick Actions)", "Admin mengeklik salah satu shortcut di panel 'Aksi Cepat'.", "Sistem menavigasi ke halaman sesuai menu yang dipilih.", "Halaman tujuan terbuka: Data Pendaftaran, Modul Ujian, Proses Seleksi, Bank Soal, atau Pengaturan.", "(Sistem)", "Sesuai"),
    ,@("5.", "Notifikasi Bell", "Admin mengeklik ikon notifikasi di navbar.", "Sistem membaca tabel notifications untuk notifikasi yang belum dilihat oleh admin.", "Dropdown notifikasi tampil berisi daftar notifikasi terbaru dengan badge jumlah notifikasi belum dibaca.", "(Sistem)", "Sesuai"),
    ,@("6.", "Proteksi Middleware Admin", "Pengguna yang bukan admin mencoba akses langsung ke /admin/dashboard.", "Middleware 'is.admin' memeriksa role pengguna yang login.", "Pengguna diarahkan ke halaman login atau menerima respon 403 Forbidden.", "(Sistem)", "Sesuai")
)
AddBlackboxTable $t4

#===== TABEL 4.5 - Verifikasi Pendaftaran ====
AddSectionHeader "e.	Pengujian Fitur Verifikasi Pendaftaran Admin"
AddTableSubtitle "Tabel 4.5 Pengujian Fitur Verifikasi Pendaftaran Admin"

$t5 = @(
    ,@("1.", "Daftar Pendaftar", "Admin membuka /admin/pendaftaran.", "Sistem mengambil data dari tabel pendaftarans beserta relasi user, jurusan, dan berkas.", "Tabel pendaftar tampil dengan kolom No. Daftar, Nama, Jurusan, Status, dan tombol aksi.", "(Sistem)", "Sesuai"),
    ,@("2.", "Filter Berdasarkan Status", "Admin memilih tab atau filter status tertentu (Baru/Arsip).", "Sistem memfilter data query berdasarkan nilai tab yang dipilih.", "Tabel hanya menampilkan data sesuai filter yang dipilih.", "(Sistem)", "Sesuai"),
    ,@("3.", "Detail Pendaftaran", "Admin mengeklik tombol 'Detail' pada salah satu pendaftar.", "Sistem menavigasi ke /admin/pendaftaran/{id} dan memuat semua data pendaftaran siswa.", "Halaman detail menampilkan biodata lengkap, data orang tua, daftar berkas, dan riwayat status.", "(Sistem)", "Sesuai"),
    ,@("4.", "Pratinjau Berkas Dokumen", "Admin mengeklik tautan pratinjau berkas pada halaman detail.", "Sistem memanggil file dari storage dan menampilkannya dalam jendela baru atau modal lightbox.", "Berkas dokumen (KK, Ijazah, dll) tampil dengan jelas di layar untuk diverifikasi.", "(Sistem)", "Sesuai"),
    ,@("5.", "Setujui Berkas (Per Berkas)", "Admin mengeklik 'Setujui' pada satu berkas tertentu.", "Sistem POST ke /admin/berkas/{id}/verifikasi dan memperbarui status berkas menjadi diterima.", "Status berkas berubah jadi 'Diterima' (hijau) dan tanda verifikasi muncul.", "(Sistem)", "Sesuai"),
    ,@("6.", "Tolak Berkas dengan Alasan", "Admin mengeklik 'Tolak' pada berkas tertentu dan mengisi alasan penolakan.", "Sistem menyimpan alasan di tabel berkas dan memperbarui status berkas menjadi ditolak.", "Status berkas berubah menjadi 'Ditolak' (merah) dan catatan alasan penolakan tampil.", "(Sistem)", "Sesuai"),
    ,@("7.", "Loloskan Pendaftaran Admin", "Admin mengeklik 'Loloskan' pada halaman detail setelah semua berkas diverifikasi.", "Sistem memperbarui status pendaftaran di tabel pendaftarans menjadi 'lolos_admin'.", "Status pendaftar di tabel berubah menjadi 'Lolos Admin' dan siswa mendapat akses ujian CBT.", "(Sistem)", "Sesuai"),
    ,@("8.", "Minta Revisi Berkas", "Admin mengeklik 'Revisi' dan mengisi catatan revisi.", "Sistem memperbarui status pendaftaran menjadi 'revisi' dan menyimpan catatan revisi.", "Di sisi siswa muncul notifikasi alert merah berisi instruksi perbaikan yang harus dilakukan.", "(Sistem)", "Sesuai")
)
AddBlackboxTable $t5

#===== TABEL 4.6 - Bank Soal & Modul Ujian ====
AddSectionHeader "f.	Pengujian Fitur Bank Soal dan Modul Ujian"
AddTableSubtitle "Tabel 4.6 Pengujian Fitur Bank Soal dan Modul Ujian"

$t6 = @(
    ,@("1.", "Daftar Soal", "Admin membuka /admin/bank_soal.", "Sistem mengambil data dari tabel soals beserta filter kategori dan sumber.", "Tabel soal tampil dengan kolom nomor, isi soal (ditruncate), kategori, sumber, status, dan tombol aksi.", "(Sistem)", "Sesuai"),
    ,@("2.", "Tambah Soal Baru", "Admin mengisi form tambah soal (pertanyaan, 5 opsi, jawaban benar, kategori).", "Sistem menyimpan data soal baru ke tabel soals dengan semua field yang diperlukan.", "Soal baru muncul di daftar tabel dan muncul notifikasi sukses.", "(Sistem)", "Sesuai"),
    ,@("3.", "Toggle Status Soal (Aktif/Nonaktif)", "Admin mengeklik toggle status pada salah satu soal.", "Sistem memperbarui kolom status soal di database menjadi aktif atau nonaktif.", "Tampilan toggle berubah, soal nonaktif tidak akan diikutsertakan dalam ujian.", "(Sistem)", "Sesuai"),
    ,@("4.", "Hapus Soal", "Admin mengeklik tombol hapus dan mengkonfirmasi penghapusan.", "Sistem menghapus data soal dari tabel soals secara permanen.", "Soal hilang dari daftar dan muncul notifikasi sukses penghapusan.", "(Sistem)", "Sesuai"),
    ,@("5.", "Import Soal dari Excel", "Admin memilih file Excel template dan mengeklik 'Import'.", "Sistem membaca file Excel, memvalidasi kolom, dan meng-insert data soal ke tabel soals secara massal.", "Semua soal dari file Excel masuk ke database dan ditampilkan di tabel bank soal.", "(Sistem)", "Sesuai"),
    ,@("6.", "Download Template Excel", "Admin mengeklik 'Unduh Template Excel'.", "Sistem menghasilkan file Excel kosong berformat template sesuai kolom yang dibutuhkan.", "File template Excel otomatis terunduh ke perangkat admin.", "(Sistem)", "Sesuai"),
    ,@("7.", "Tambah Modul Ujian", "Admin mengisi form buat ujian baru (nama, durasi, tanggal, jurusan).", "Sistem menyimpan data ujian ke tabel ujians.", "Modul ujian baru muncul di daftar /admin/ujian.", "(Sistem)", "Sesuai"),
    ,@("8.", "Assign Soal ke Modul Ujian", "Admin memilih soal dari bank soal dan mengeklik 'Tambahkan ke Modul'.", "Sistem menyimpan relasi soal-ujian ke tabel modul_ujian_soal (pivot table).", "Soal terpilih muncul di daftar soal dalam modul ujian yang dipilih.", "(Sistem)", "Sesuai"),
    ,@("9.", "Buka/Tutup Ujian", "Admin mengeklik tombol 'Buka Ujian' atau 'Tutup Ujian'.", "Sistem mengubah flag is_tutup pada tabel ujians.", "Status ujian berubah; jika tutup, siswa tidak bisa mengakses ujian meskipun dalam periode aktif.", "(Sistem)", "Sesuai"),
    ,@("10.", "Perpanjang Durasi Ujian", "Admin mengeklik 'Perpanjang' dan mengisi menit tambahan.", "Sistem menambah nilai menit perpanjangan ke field durasi ujian di database.", "Durasi ujian diperbarui dan siswa mendapat tambahan waktu.", "(Sistem)", "Sesuai")
)
AddBlackboxTable $t6

#===== TABEL 4.7 - Seleksi & Penempatan ====
AddSectionHeader "g.	Pengujian Fitur Seleksi dan Penempatan"
AddTableSubtitle "Tabel 4.7 Pengujian Fitur Seleksi dan Penempatan"

$t7 = @(
    ,@("1.", "Halaman Proses Seleksi", "Admin membuka /admin/seleksi.", "Sistem memuat daftar semua siswa yang berstatus sudah ujian beserta nilai ujian masing-masing.", "Tabel seleksi tampil dengan kolom Nama, Jurusan Pilihan, Nilai Ujian, dan Status.", "(Sistem)", "Sesuai"),
    ,@("2.", "Jalankan Seleksi Otomatis", "Admin mengeklik tombol 'Jalankan Seleksi'.", "Sistem menjalankan algoritma perangkingan berdasarkan skor ujian dan kuota jurusan, lalu menulis hasilnya ke tabel hasil_seleksis.", "Data perangkingan muncul di tabel penempatan dan status siswa diperbarui.", "(Sistem)", "Sesuai"),
    ,@("3.", "Tunda Seleksi Siswa", "Admin mengeklik 'Tunda' pada siswa tertentu.", "Sistem mengubah flag ditunda_seleksi pada tabel pendaftarans.", "Siswa tidak ikut diperhitungkan pada proses seleksi otomatis berikutnya.", "(Sistem)", "Sesuai"),
    ,@("4.", "Tandai Tidak Ikut Ujian", "Admin mengeklik tombol 'Tandai Tidak Ujian' untuk siswa absent.", "Sistem memperbarui status pendaftaran menjadi 'tidak_mengikuti_ujian'.", "Status siswa berubah dan tidak diikutsertakan dalam proses seleksi.", "(Sistem)", "Sesuai"),
    ,@("5.", "Halaman Penempatan", "Admin membuka /admin/penempatan.", "Sistem memuat data hasil seleksi dari tabel hasil_seleksis beserta data siswa.", "Tabel penempatan menampilkan ranking siswa, jurusan, skor akhir, dan status kelulusan per jurusan.", "(Sistem)", "Sesuai"),
    ,@("6.", "Preview Hitung Seleksi", "Admin mengeklik 'Preview Hitung' sebelum finalisasi.", "Sistem menghitung simulasi perangkingan tanpa menyimpan ke database.", "Tampil tabel simulasi ranking yang bisa dikonfirmasi sebelum benar-benar disimpan.", "(Sistem)", "Sesuai"),
    ,@("7.", "Hitung & Simpan Seleksi", "Admin mengeklik 'Hitung & Simpan' setelah mengkonfirmasi preview.", "Sistem menyimpan hasil perangkingan final ke tabel hasil_seleksis dan memperbarui status pendaftaran.", "Data seleksi tersimpan, status siswa berubah menjadi 'siap_diumumkan'.", "(Sistem)", "Sesuai"),
    ,@("8.", "Update Manual Hasil", "Admin mengubah status kelulusan siswa secara manual lewat tabel penempatan.", "Sistem POST ke /admin/penempatan/update/{id} dan memperbarui data di tabel hasil_seleksis.", "Perubahan tersimpan dan tampil di tabel tanpa perlu reload penuh.", "(Sistem)", "Sesuai"),
    ,@("9.", "Finalisasi & Publikasi Hasil", "Admin mengeklik 'Publish Hasil' untuk mengumumkan hasil ke publik.", "Sistem mengubah flag is_finalisasi dan is_published di tabel hasil_seleksis menjadi true.", "Hasil seleksi muncul di dashboard siswa yang bersangkutan; siswa diterima dapat unduh surat.", "(Sistem)", "Sesuai"),
    ,@("10.", "Unduh PDF Surat Siswa (Admin)", "Admin mengeklik 'Generate PDF' pada detail penempatan siswa.", "Sistem merender template surat menggunakan data siswa dan hasil seleksi menjadi PDF.", "File PDF surat penerimaan/penolakan siswa terunduh ke perangkat admin.", "(Sistem)", "Sesuai")
)
AddBlackboxTable $t7

#===== TABEL 4.8 - Pengaturan & Jurusan ====
AddSectionHeader "h.	Pengujian Fitur Pengaturan Sistem dan Kelola Jurusan"
AddTableSubtitle "Tabel 4.8 Pengujian Fitur Pengaturan dan Kelola Jurusan"

$t8 = @(
    ,@("1.", "Pengaturan Umum Sekolah", "Admin mengisi nama sekolah, deskripsi, logo dan mengeklik 'Simpan'.", "Sistem menyimpan semua nilai ke tabel pengaturans dengan format key-value.", "Pengaturan berhasil disimpan dan muncul notifikasi sukses. Nama sekolah berubah di seluruh halaman.", "(Sistem)", "Sesuai"),
    ,@("2.", "Konfigurasi Periode PPDB", "Admin mengisi tanggal buka dan tutup pendaftaran, lalu klik 'Simpan'.", "Sistem menyimpan periode ke tabel pengaturans dan sistem membaca tanggal ini untuk mengaktifkan/menonaktifkan pendaftaran.", "Tombol daftar di landing page aktif/nonaktif sesuai periode yang telah dikonfigurasi.", "(Sistem)", "Sesuai"),
    ,@("3.", "Konfigurasi Bobot Nilai", "Admin mengatur persentase bobot nilai ujian CBT dan komponen lainnya.", "Sistem menyimpan bobot ke tabel pengaturans yang akan digunakan saat menjalankan seleksi.", "Bobot nilai tersimpan dan akan diaplikasikan pada perhitungan skor akhir di proses seleksi berikutnya.", "(Sistem)", "Sesuai"),
    ,@("4.", "Konfigurasi Periode CBT", "Admin mengisi tanggal mulai/selesai ujian CBT dan mengubah status CBT.", "Sistem menyimpan cbt_tgl_mulai, cbt_tgl_selesai, dan cbt_status ke tabel pengaturans.", "Dashboard siswa menampilkan informasi periode ujian yang baru sesuai konfigurasi.", "(Sistem)", "Sesuai"),
    ,@("5.", "Tambah Jurusan Baru", "Admin mengisi nama jurusan, deskripsi, dan kuota, lalu klik 'Simpan'.", "Sistem menyimpan data jurusan baru ke tabel jurusans.", "Jurusan baru muncul di daftar kelola jurusan, landing page, dan form pendaftaran siswa.", "(Sistem)", "Sesuai"),
    ,@("6.", "Edit Data Jurusan", "Admin mengeklik 'Edit' pada jurusan tertentu dan memperbarui datanya.", "Sistem memperbarui data di tabel jurusans berdasarkan ID.", "Data jurusan berhasil diperbarui dan tampil di semua halaman terkait.", "(Sistem)", "Sesuai"),
    ,@("7.", "Hapus Jurusan", "Admin mengeklik 'Hapus' pada jurusan tertentu.", "Sistem menghapus record jurusan dari tabel jurusans (dengan cek relasi ke pendaftaran).", "Jurusan terhapus dari daftar dan tidak lagi tersedia di form pilihan jurusan.", "(Sistem)", "Sesuai"),
    ,@("8.", "Validasi Kuota 0 atau Negatif", "Admin mengisi kuota jurusan dengan nilai 0 atau angka negatif.", "Sistem memvalidasi input kuota harus bernilai positif.", "Form menampilkan error validasi dan data tidak tersimpan.", "(Sistem)", "Sesuai")
)
AddBlackboxTable $t8

# Save
$filename = "c:\laragon\www\ppdb-sekolah\Pengujian_Blackbox_PPDB_Final.docx"
$doc.SaveAs([ref]$filename)
$doc.Close()
$word.Quit()
[System.Runtime.Interopservices.Marshal]::ReleaseComObject($word) | Out-Null

Write-Host "Selesai! File tersimpan di: $filename"
