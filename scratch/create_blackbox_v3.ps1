$word = New-Object -ComObject Word.Application
$word.Visible = $false
$doc = $word.Documents.Add()
$sel = $word.Selection

function SetCell($tbl, $row, $col, $txt, $bold=$false, $center=$false) {
    $c = $tbl.Cell($row, $col)
    $c.Range.Text = [string]$txt
    $c.Range.Font.Name = "Times New Roman"
    $c.Range.Font.Size = 10
    $c.Range.Font.Bold = $bold
    if ($center) { $c.Range.ParagraphFormat.Alignment = 1 }
    else { $c.Range.ParagraphFormat.Alignment = 0 }
}

function WriteSection($title) {
    $sel.Font.Name = "Times New Roman"
    $sel.Font.Size = 12
    $sel.Font.Bold = $true
    $sel.ParagraphFormat.Alignment = 0
    $sel.TypeText($title)
    $sel.Font.Bold = $false
    $sel.TypeParagraph()
}

function WriteSubtitle($title) {
    $sel.Font.Name = "Times New Roman"
    $sel.Font.Size = 12
    $sel.Font.Bold = $false
    $sel.ParagraphFormat.Alignment = 0
    $sel.TypeText($title)
    $sel.TypeParagraph()
}

function MakeTable($numRows) {
    $t = $doc.Tables.Add($sel.Range, $numRows, 7)
    $t.Borders.Enable = $true
    # Col widths in points: No=35, Fitur=90, Skenario=145, Proses=145, Output=145, Tampilan=70, Hasil=55
    $w = @(35,90,145,145,145,70,55)
    for ($i=1;$i -le 7;$i++) { $t.Columns($i).Width = $w[$i-1] }
    $hdr = @("No","Fitur","Skenario Uji","Proses yang Diharapkan","Output yang Diharapkan","Tampilan","Hasil")
    for ($i=1;$i -le 7;$i++) {
        SetCell $t 1 $i $hdr[$i-1] $true $true
        $t.Cell(1,$i).Shading.BackgroundPatternColor = -16777012
    }
    return $t
}

function FinishTable() {
    $sel.EndKey(6)
    $sel.TypeParagraph()
}

function FillRow($tbl, $r, $no, $fitur, $skenario, $proses, $output, $tampilan, $hasil) {
    SetCell $tbl $r 1 $no $false $true
    SetCell $tbl $r 2 $fitur $false $false
    SetCell $tbl $r 3 $skenario $false $false
    SetCell $tbl $r 4 $proses $false $false
    SetCell $tbl $r 5 $output $false $false
    SetCell $tbl $r 6 $tampilan $false $true
    SetCell $tbl $r 7 $hasil $false $true
}

# ===== JUDUL =====
$sel.Style = $doc.Styles["Heading 2"]
$sel.Font.Name = "Times New Roman"
$sel.TypeText("4.5 Pengujian Sistem")
$sel.TypeParagraph()
$sel.Style = $doc.Styles["Heading 3"]
$sel.Font.Name = "Times New Roman"
$sel.TypeText("4.5.1 Pengujian Black Box")
$sel.TypeParagraph()
$sel.Style = $doc.Styles["Normal"]

# ===== TABEL 4.1 =====
WriteSection "a.`tPengujian Fitur Halaman Beranda dan Autentikasi"
WriteSubtitle "Tabel 4. 1 Pengujian Fitur Halaman Beranda dan Autentikasi"
$t = MakeTable 14
FillRow $t 2  "1." "Tampilan Beranda"         "Pengguna membuka URL http://ppdb-sekolah.test/"                                          "Sistem merender halaman beranda dengan daftar jurusan dan status PPDB."                           "Halaman beranda tampil: daftar jurusan (nama & kuota) dan tombol Daftar Sekarang aktif jika PPDB terbuka." "(Sistem)" "Sesuai"
FillRow $t 3  "2." "PPDB Ditutup"             "Periode PPDB belum atau sudah lewat tanggal yang dikonfigurasi admin."                    "Sistem membandingkan tanggal hari ini dengan konfigurasi periode PPDB dari tabel pengaturans."    "Tombol Daftar berubah jadi abu-abu dengan label 'Pendaftaran Ditutup' dan tidak bisa diklik."             "(Sistem)" "Sesuai"
FillRow $t 4  "3." "Login Kolom Kosong"       "Pengguna mengeklik 'Masuk' tanpa mengisi email dan password."                             "Sistem mendeteksi field kosong dari validasi HTML5 sebelum form dikirim."                        "Browser menampilkan peringatan: 'Harap isi bidang ini' pada kolom Email."                                 "(Sistem)" "Sesuai"
FillRow $t 5  "4." "Email Tidak Terdaftar"    "Pengguna mengisi email yang tidak ada di database, lalu klik 'Masuk'."                    "Sistem memverifikasi email ke tabel users dan gagal menemukan akun."                             "Halaman login kembali dengan pesan error: 'These credentials do not match our records.'"                   "(Sistem)" "Sesuai"
FillRow $t 6  "5." "Password Salah"           "Pengguna mengisi email terdaftar namun password salah."                                   "Sistem mencocokkan hash password di database dan menemukan ketidakcocokan."                       "Halaman login kembali dengan alert merah berisi pesan error kredensial."                                   "(Sistem)" "Sesuai"
FillRow $t 7  "6." "Login Berhasil (Siswa)"   "Siswa mengisi email dan password yang benar lalu klik 'Masuk'."                          "Sistem verifikasi kredensial, buat sesi, dan cek role akun sebagai siswa."                       "Pengguna diarahkan ke /siswa/dashboard."                                                                  "(Sistem)" "Sesuai"
FillRow $t 8  "7." "Login Berhasil (Admin)"   "Admin mengisi email dan password yang benar lalu klik 'Masuk'."                          "Sistem verifikasi kredensial, buat sesi, dan cek role akun sebagai admin."                       "Pengguna diarahkan ke /admin/dashboard."                                                                  "(Sistem)" "Sesuai"
FillRow $t 9  "8." "Lupa Password"            "Pengguna mengeklik link 'Lupa Password?' di halaman login."                              "Sistem menavigasi ke halaman lupa-password."                                                     "Halaman /lupa-password tampil dengan instruksi pemulihan akun."                                           "(Sistem)" "Sesuai"
FillRow $t 10 "9." "Registrasi Kosong"        "Pengguna mengeklik 'Daftar Akun' tanpa mengisi form."                                    "Sistem mendeteksi field kosong dari validasi HTML5 sebelum form dikirim."                        "Browser menampilkan peringatan pada field Nama Lengkap yang wajib diisi."                                 "(Sistem)" "Sesuai"
FillRow $t 11 "10." "Email Format Salah"      "Pengguna mengisi email tidak berformat @gmail.com lalu klik 'Daftar'."                   "Sistem mengecek pattern validasi sebelum submit."                                                "Input email berwarna merah: 'Email harus diawali huruf dan menggunakan domain @gmail.com'."                "(Sistem)" "Sesuai"
FillRow $t 12 "11." "Password Tidak Cocok"    "Pengguna mengisi password berbeda di kolom password dan konfirmasi password."            "Sistem memvalidasi kecocokan dua field password di AuthController."                              "Halaman kembali dengan error: 'The password confirmation does not match'."                                 "(Sistem)" "Sesuai"
FillRow $t 13 "12." "Registrasi Berhasil"     "Pengguna mengisi semua field registrasi dengan format yang benar."                       "Sistem menyimpan akun baru ke tabel users dan membuat sesi login."                              "Pengguna berhasil masuk dan diarahkan ke /siswa/dashboard sebagai akun siswa baru."                       "(Sistem)" "Sesuai"
FillRow $t 14 "13." "Logout"                  "Pengguna mengeklik tombol 'Logout' dari menu navigasi."                                  "Sistem menghapus sesi login yang aktif."                                                         "Pengguna diarahkan kembali ke halaman login /login."                                                      "(Sistem)" "Sesuai"
FinishTable

# ===== TABEL 4.2 =====
WriteSection "b.`tPengujian Fitur Dashboard dan Pendaftaran Siswa"
WriteSubtitle "Tabel 4. 2 Pengujian Fitur Dashboard dan Pendaftaran Siswa"
$t = MakeTable 9
FillRow $t 2 "1." "Akses Dashboard"         "Siswa login mengakses /siswa/dashboard."                                               "Sistem memuat data pendaftaran, status, hasil ujian, dan progress siswa."                     "Dashboard menampilkan kartu status, progress bar (0-100%), dan card aksi sesuai tahap."                "(Sistem)" "Sesuai"
FillRow $t 3 "2." "Notif Revisi"            "Status pendaftaran siswa adalah 'revisi' (berkas ditolak admin)."                      "Sistem deteksi status revisi dan tampilkan alert khusus di atas dashboard."                   "Muncul banner merah 'Perlu Revisi' beserta tombol 'Lihat Revisi' dan popup SweetAlert2."               "(Sistem)" "Sesuai"
FillRow $t 4 "3." "Mulai Isi Data"          "Siswa baru yang belum mendaftar mengeklik 'Mulai Isi Data'."                          "Sistem navigasi ke halaman formulir pendaftaran /siswa/pendaftaran."                         "Formulir multitab tampil: data diri, orang tua, asal sekolah, pilihan jurusan, dan unggah berkas."     "(Sistem)" "Sesuai"
FillRow $t 5 "4." "Simpan Pendaftaran"      "Siswa mengisi seluruh form dan mengeklik 'Simpan Pendaftaran'."                        "Sistem validasi semua field, simpan ke tabel pendaftarans, ubah status ke menunggu_verifikasi." "Dashboard dengan status 'Menunggu Verifikasi' dan notifikasi sukses muncul."                            "(Sistem)" "Sesuai"
FillRow $t 6 "5." "Unggah Berkas"           "Siswa memilih file dokumen (KK, Ijazah, Akta) dan unggah lewat form."                 "Sistem terima file, validasi tipe & ukuran, simpan di storage dan catat ke tabel berkas."    "Berkas tampil di daftar lampiran dengan pratinjau link dan status 'Menunggu Verifikasi'."               "(Sistem)" "Sesuai"
FillRow $t 7 "6." "Re-upload Revisi"        "Admin tolak berkas; siswa unggah ulang berkas yang ditolak."                          "Sistem ganti file lama dengan file baru di storage dan perbarui status di database."          "Status pendaftaran kembali ke 'menunggu_verifikasi' dan berkas terbaru tampil dengan tanggal baru."    "(Sistem)" "Sesuai"
FillRow $t 8 "7." "Profil Siswa"            "Siswa mengakses menu /siswa/profile."                                                  "Sistem memuat data profil akun siswa yang sedang login."                                     "Halaman profil tampil dengan form update nama, email, password dan foto profil."                        "(Sistem)" "Sesuai"
FillRow $t 9 "8." "Lihat Hasil Seleksi"     "Siswa diterima mengeklik 'Lihat Detail' di card Hasil Seleksi."                       "Sistem navigasi ke /siswa/hasil dan muat data hasilSeleksi dari database."                    "Halaman hasil: status kelulusan, nama jurusan diterima, dan skor akhir seleksi tampil."                "(Sistem)" "Sesuai"
FillRow $t 10 "9." "Unduh PDF Hasil"        "Siswa mengeklik 'Unduh PDF' di dashboard atau halaman hasil."                         "Sistem render template PDF dari data pendaftaran dan hasil seleksi siswa."                    "File surat penerimaan/penolakan PDF otomatis terunduh ke perangkat siswa."                              "(Sistem)" "Sesuai"
FinishTable

# ===== TABEL 4.3 =====
WriteSection "c.`tPengujian Fitur Ujian CBT (Computer Based Test)"
WriteSubtitle "Tabel 4. 3 Pengujian Fitur Ujian CBT"
$t = MakeTable 11
FillRow $t 2  "1."  "Info Ujian"              "Siswa berstatus 'lolos_admin' mengakses menu Ujian."                                   "Sistem cek status siswa dan ketersediaan ujian aktif dalam periode berlaku."                  "Halaman /siswa/ujian menampilkan info jadwal ujian, durasi, dan tombol 'Mulai Ujian Sekarang'."          "(Sistem)" "Sesuai"
FillRow $t 3  "2."  "Akses Ditolak"           "Siswa belum 'lolos_admin' mencoba akses halaman ujian."                               "Sistem cek status pendaftaran dan deteksi belum lolos verifikasi admin."                      "Halaman ujian tampil tombol 'Belum Tersedia' nonaktif beserta keterangan status."                        "(Sistem)" "Sesuai"
FillRow $t 4  "3."  "Ujian Belum Dimulai"     "Status CBT aktif namun tanggal hari ini sebelum tanggal mulai."                       "Sistem bandingkan tanggal sekarang dengan cbt_tgl_mulai dari tabel pengaturans."              "Tombol ujian tampilkan: ''''Belum Dimulai'''' dan info tanggal mulai ujian."                              "(Sistem)" "Sesuai"
FillRow $t 5  "4."  "Periode Ujian Berakhir"  "Tanggal hari ini sudah melewati batas cbt_tgl_selesai."                               "Sistem deteksi tanggal sekarang melewati cbt_tgl_selesai."                                   "Tombol ujian menampilkan '''' Periode Berakhir'''' berwarna merah, tidak dapat diklik."                   "(Sistem)" "Sesuai"
FillRow $t 6  "5."  "Mulai Ujian"             "Siswa mengeklik 'Mulai Ujian Sekarang' dan mengkonfirmasi."                           "Sistem POST ke /siswa/ujian/mulai, catat waktu_mulai, bagi soal secara acak."                "Halaman CBT tampil: soal pertama, navigator nomor soal, dan countdown timer."                            "(Sistem)" "Sesuai"
FillRow $t 7  "6."  "Timer Countdown"         "Siswa mengerjakan soal dan timer berjalan."                                           "Sistem hitung mundur durasi ujian tiap detik via JavaScript di sisi klien."                   "Countdown tampil di pojok kanan; warna berubah merah saat sisa waktu kurang dari 5 menit."               "(Sistem)" "Sesuai"
FillRow $t 8  "7."  "Simpan Jawaban"          "Siswa memilih salah satu opsi jawaban (A/B/C/D/E)."                                   "Sistem kirim request AJAX untuk simpan pilihan ke tabel jawabans."                           "Nomor soal di navigator berubah hijau sebagai penanda soal sudah dijawab."                               "(Sistem)" "Sesuai"
FillRow $t 9  "8."  "Navigasi Soal"           "Siswa mengeklik nomor soal di navigator atau tombol Sebelumnya/Berikutnya."           "Sistem muat soal yang dipilih tanpa reload halaman penuh."                                   "Soal berganti sesuai nomor dipilih; jawaban sebelumnya tetap tersimpan."                                 "(Sistem)" "Sesuai"
FillRow $t 10 "9."  "Submit Manual"           "Siswa mengeklik 'Selesaikan Ujian' sebelum waktu habis."                              "Sistem POST ke /siswa/ujian/submit, hitung skor, simpan ke tabel hasil_ujians."              "Halaman beralih ke konfirmasi selesai; status siswa berubah menjadi 'sudah_ujian'."                      "(Sistem)" "Sesuai"
FillRow $t 11 "10." "Submit Otomatis"         "Waktu ujian habis sebelum siswa menekan submit."                                      "JavaScript otomatis submit form saat timer mencapai nol."                                    "Ujian tersubmit otomatis dan siswa melihat halaman konfirmasi selesai."                                  "(Sistem)" "Sesuai"
FillRow $t 12 "11." "Sudah Dikerjakan"        "Siswa yang sudah ujian mencoba akses halaman ujian kembali."                          "Sistem deteksi hasilUjian sudah ada di database untuk siswa ini."                           "Tombol berubah jadi 'Ujian Selesai' (nonaktif) dan nilai CBT tampil di dashboard."                       "(Sistem)" "Sesuai"
FinishTable

# ===== TABEL 4.4 =====
WriteSection "d.`tPengujian Fitur Dashboard Admin"
WriteSubtitle "Tabel 4. 4 Pengujian Fitur Dashboard Admin"
$t = MakeTable 7
FillRow $t 2 "1." "Statistik Pendaftar"      "Admin membuka /admin/dashboard."                                                      "Sistem query tabel pendaftarans dan hitung jumlah berdasarkan status."                       "Tampil 5 kartu: Total Pendaftar, Menunggu, Lolos Admin, Sudah Ujian, dan Hasil Final."                  "(Sistem)" "Sesuai"
FillRow $t 3 "2." "Kuota per Jurusan"        "Admin melihat panel 'Kuota per Jurusan' di dashboard."                                "Sistem hitung siswa diterima per jurusan vs total kuota."                                    "Setiap jurusan tampil progress bar: hijau (sisa banyak), kuning (mendekati), merah (penuh)."            "(Sistem)" "Sesuai"
FillRow $t 4 "3." "Pendaftar Terbaru"        "Admin melihat tabel 5 pendaftar paling baru."                                         "Sistem ambil 5 data terbaru dari pendaftarans dengan relasi user dan jurusan."                "Tabel tampil: Nama, Asal Sekolah, Jurusan, Status badge berwarna, dan tombol Detail."                   "(Sistem)" "Sesuai"
FillRow $t 5 "4." "Aksi Cepat"              "Admin mengeklik salah satu shortcut di panel 'Aksi Cepat'."                           "Sistem navigasi ke halaman sesuai menu yang dipilih."                                        "Halaman tujuan terbuka: Data Pendaftaran, Modul Ujian, Proses Seleksi, Bank Soal, atau Pengaturan."     "(Sistem)" "Sesuai"
FillRow $t 6 "5." "Notifikasi Bell"          "Admin mengeklik ikon notifikasi di navbar."                                            "Sistem baca tabel notifications untuk notifikasi yang belum dilihat admin."                  "Dropdown notifikasi tampil berisi daftar notif terbaru dan badge jumlah belum dibaca."                   "(Sistem)" "Sesuai"
FillRow $t 7 "6." "Proteksi Middleware"      "Pengguna bukan admin mencoba akses langsung ke /admin/dashboard."                     "Middleware 'is.admin' memeriksa role pengguna yang sedang login."                            "Pengguna diarahkan ke halaman login atau menerima respons 403 Forbidden."                                "(Sistem)" "Sesuai"
FinishTable

# ===== TABEL 4.5 =====
WriteSection "e.`tPengujian Fitur Verifikasi Pendaftaran Admin"
WriteSubtitle "Tabel 4. 5 Pengujian Fitur Verifikasi Pendaftaran Admin"
$t = MakeTable 9
FillRow $t 2 "1." "Daftar Pendaftar"         "Admin membuka /admin/pendaftaran."                                                     "Sistem ambil data dari pendaftarans beserta relasi user, jurusan, dan berkas."                "Tabel pendaftar tampil: No. Daftar, Nama, Jurusan, Status, dan tombol aksi."                            "(Sistem)" "Sesuai"
FillRow $t 3 "2." "Filter Status"            "Admin memilih tab Baru atau Arsip untuk filter data."                                  "Sistem filter data query berdasarkan nilai tab yang dipilih."                                "Tabel hanya tampilkan data sesuai filter yang dipilih."                                                  "(Sistem)" "Sesuai"
FillRow $t 4 "3." "Detail Pendaftaran"       "Admin mengeklik tombol 'Detail' pada salah satu pendaftar."                           "Sistem navigasi ke /admin/pendaftaran/{id} dan muat semua data siswa."                       "Halaman detail: biodata lengkap, data orang tua, daftar berkas, dan riwayat status."                    "(Sistem)" "Sesuai"
FillRow $t 5 "4." "Pratinjau Berkas"         "Admin mengeklik tautan pratinjau berkas pada halaman detail."                         "Sistem panggil file dari storage dan tampilkan di modal lightbox."                           "Berkas dokumen (KK, Ijazah, dll) tampil dengan jelas untuk diverifikasi."                               "(Sistem)" "Sesuai"
FillRow $t 6 "5." "Loloskan Pendaftar"       "Admin mengeklik tombol 'Loloskan' setelah semua berkas diverifikasi."                 "Sistem perbarui status di tabel pendaftarans menjadi 'lolos_admin'."                         "Status pendaftar di tabel berubah jadi 'Lolos Admin'; siswa mendapat akses ujian CBT."                  "(Sistem)" "Sesuai"
FillRow $t 7 "6." "Setujui Berkas"           "Admin mengeklik 'Setujui' pada satu berkas tertentu."                                 "Sistem POST ke /admin/berkas/{id}/verifikasi dan perbarui status berkas menjadi diterima."    "Status berkas berubah jadi 'Diterima' (hijau) dan tanda verifikasi muncul."                             "(Sistem)" "Sesuai"
FillRow $t 8 "7." "Tolak Berkas"             "Admin mengeklik 'Tolak' pada berkas dan mengisi alasan penolakan."                    "Sistem simpan alasan di tabel berkas dan perbarui status menjadi ditolak."                    "Status berkas berubah jadi 'Ditolak' (merah) beserta catatan alasan penolakan."                         "(Sistem)" "Sesuai"
FillRow $t 9 "8." "Minta Revisi"             "Admin mengeklik 'Revisi' dan mengisi catatan revisi yang dibutuhkan."                  "Sistem perbarui status pendaftaran menjadi 'revisi' dan simpan catatan revisi."               "Di sisi siswa muncul notifikasi alert merah berisi instruksi perbaikan dokumen."                        "(Sistem)" "Sesuai"
FinishTable

# ===== TABEL 4.6 =====
WriteSection "f.`tPengujian Fitur Bank Soal dan Modul Ujian"
WriteSubtitle "Tabel 4. 6 Pengujian Fitur Bank Soal dan Modul Ujian"
$t = MakeTable 11
FillRow $t 2  "1."  "Daftar Bank Soal"        "Admin membuka /admin/bank_soal."                                                      "Sistem ambil data dari tabel soals beserta filter kategori dan sumber."                      "Tabel soal tampil: nomor, isi soal (ditruncate), kategori, sumber, status, dan tombol aksi."             "(Sistem)" "Sesuai"
FillRow $t 3  "2."  "Tambah Soal Baru"        "Admin mengisi form tambah soal (pertanyaan, 5 opsi, jawaban benar, kategori)."        "Sistem simpan data soal baru ke tabel soals."                                                "Soal baru muncul di tabel dan notifikasi sukses muncul."                                                  "(Sistem)" "Sesuai"
FillRow $t 4  "3."  "Toggle Status Soal"      "Admin mengeklik toggle status pada salah satu soal."                                  "Sistem perbarui kolom status soal di database menjadi aktif atau nonaktif."                   "Tampilan toggle berubah; soal nonaktif tidak diikutsertakan dalam ujian."                                 "(Sistem)" "Sesuai"
FillRow $t 5  "4."  "Hapus Soal"              "Admin mengeklik tombol hapus dan konfirmasi penghapusan."                             "Sistem hapus data soal dari tabel soals secara permanen."                                    "Soal hilang dari daftar dan notifikasi sukses penghapusan muncul."                                       "(Sistem)" "Sesuai"
FillRow $t 6  "5."  "Import Excel"            "Admin memilih file Excel template dan mengeklik 'Import'."                            "Sistem baca file Excel, validasi kolom, dan insert data soal ke tabel soals secara massal."   "Semua soal dari Excel masuk ke database dan tampil di tabel bank soal."                                   "(Sistem)" "Sesuai"
FillRow $t 7  "6."  "Download Template"       "Admin mengeklik 'Unduh Template Excel'."                                              "Sistem generate file Excel kosong berformat template sesuai kolom yang dibutuhkan."           "File template Excel otomatis terunduh ke perangkat admin."                                                "(Sistem)" "Sesuai"
FillRow $t 8  "7."  "Tambah Modul Ujian"      "Admin mengisi form buat ujian baru (nama, durasi, tanggal, jurusan)."                 "Sistem simpan data ujian ke tabel ujians."                                                   "Modul ujian baru muncul di daftar /admin/ujian."                                                         "(Sistem)" "Sesuai"
FillRow $t 9  "8."  "Assign Soal ke Modul"    "Admin pilih soal dari bank soal dan klik 'Tambahkan ke Modul'."                      "Sistem simpan relasi soal-ujian ke tabel pivot modul_ujian_soal."                           "Soal terpilih muncul di daftar soal dalam modul ujian yang dipilih."                                     "(Sistem)" "Sesuai"
FillRow $t 10 "9."  "Buka/Tutup Ujian"        "Admin mengeklik tombol 'Buka Ujian' atau 'Tutup Ujian'."                              "Sistem ubah flag is_tutup di tabel ujians."                                                   "Status ujian berubah; jika tutup, siswa tidak bisa akses ujian meskipun dalam periode aktif."            "(Sistem)" "Sesuai"
FillRow $t 11 "10." "Perpanjang Durasi"       "Admin mengeklik 'Perpanjang' dan mengisi menit tambahan."                             "Sistem tambahkan nilai menit perpanjangan ke field durasi ujian di database."                 "Durasi ujian diperbarui dan siswa mendapat tambahan waktu pengerjaan."                                    "(Sistem)" "Sesuai"
FinishTable

# ===== TABEL 4.7 =====
WriteSection "g.`tPengujian Fitur Seleksi dan Penempatan"
WriteSubtitle "Tabel 4. 7 Pengujian Fitur Seleksi dan Penempatan"
$t = MakeTable 11
FillRow $t 2  "1."  "Halaman Seleksi"         "Admin membuka /admin/seleksi."                                                        "Sistem muat daftar semua siswa berstatus sudah ujian beserta nilai ujian."                    "Tabel seleksi tampil: Nama, Jurusan Pilihan, Nilai Ujian, dan Status."                                   "(Sistem)" "Sesuai"
FillRow $t 3  "2."  "Jalankan Seleksi"        "Admin mengeklik tombol 'Jalankan Seleksi'."                                           "Sistem jalankan algoritma perangkingan berdasarkan skor dan kuota, tulis ke hasil_seleksis." "Data perangkingan muncul di tabel penempatan dan status siswa diperbarui."                               "(Sistem)" "Sesuai"
FillRow $t 4  "3."  "Tunda Seleksi"           "Admin mengeklik 'Tunda' pada siswa tertentu."                                         "Sistem ubah flag ditunda_seleksi di tabel pendaftarans."                                     "Siswa tidak ikut diperhitungkan pada proses seleksi otomatis berikutnya."                                "(Sistem)" "Sesuai"
FillRow $t 5  "4."  "Tandai Tidak Ikut Ujian" "Admin mengeklik 'Tandai Tidak Ujian' untuk siswa yang tidak hadir."                   "Sistem perbarui status pendaftaran menjadi 'tidak_mengikuti_ujian'."                         "Status siswa berubah dan tidak diikutsertakan dalam proses seleksi."                                     "(Sistem)" "Sesuai"
FillRow $t 6  "5."  "Halaman Penempatan"      "Admin membuka /admin/penempatan."                                                     "Sistem muat data hasil seleksi dari tabel hasil_seleksis beserta data siswa."                "Tabel penempatan: ranking siswa, jurusan, skor akhir, dan status kelulusan per jurusan."                 "(Sistem)" "Sesuai"
FillRow $t 7  "6."  "Preview Hitung"          "Admin mengeklik 'Preview Hitung' sebelum finalisasi."                                 "Sistem hitung simulasi perangkingan tanpa menyimpan ke database."                            "Tampil tabel simulasi ranking yang bisa dikonfirmasi sebelum benar-benar disimpan."                      "(Sistem)" "Sesuai"
FillRow $t 8  "7."  "Hitung & Simpan"         "Admin mengeklik 'Hitung & Simpan' setelah konfirmasi preview."                        "Sistem simpan hasil perangkingan final ke hasil_seleksis dan perbarui status pendaftaran."    "Data seleksi tersimpan; status siswa berubah menjadi 'siap_diumumkan'."                                  "(Sistem)" "Sesuai"
FillRow $t 9  "8."  "Update Manual Hasil"     "Admin mengubah status kelulusan siswa secara manual di tabel penempatan."             "Sistem POST ke /admin/penempatan/update/{id} dan perbarui data di hasil_seleksis."            "Perubahan tersimpan dan tampil di tabel tanpa perlu reload penuh."                                       "(Sistem)" "Sesuai"
FillRow $t 10 "9."  "Publikasi Hasil"         "Admin mengeklik 'Publish Hasil' untuk umumkan hasil ke publik."                       "Sistem ubah flag is_finalisasi dan is_published di tabel hasil_seleksis menjadi true."        "Hasil seleksi muncul di dashboard siswa; siswa diterima dapat unduh surat."                              "(Sistem)" "Sesuai"
FillRow $t 11 "10." "Unduh PDF Surat (Admin)" "Admin mengeklik 'Generate PDF' pada detail penempatan siswa."                         "Sistem render template surat dengan data siswa dan hasil seleksi menjadi PDF."               "File PDF surat penerimaan/penolakan siswa terunduh ke perangkat admin."                                  "(Sistem)" "Sesuai"
FinishTable

# ===== TABEL 4.8 =====
WriteSection "h.`tPengujian Fitur Pengaturan Sistem dan Kelola Jurusan"
WriteSubtitle "Tabel 4. 8 Pengujian Fitur Pengaturan Sistem dan Kelola Jurusan"
$t = MakeTable 9
FillRow $t 2 "1." "Pengaturan Umum"          "Admin mengisi nama sekolah, deskripsi, logo dan klik 'Simpan'."                       "Sistem simpan semua nilai ke tabel pengaturans dengan format key-value."                      "Pengaturan berhasil disimpan, notifikasi sukses muncul, nama sekolah berubah di seluruh halaman."        "(Sistem)" "Sesuai"
FillRow $t 3 "2." "Periode PPDB"             "Admin mengisi tanggal buka dan tutup pendaftaran, lalu klik 'Simpan'."                 "Sistem simpan periode ke tabel pengaturans; sistem baca ini untuk aktif/nonaktifkan daftar." "Tombol daftar di landing page aktif/nonaktif sesuai periode yang dikonfigurasi."                         "(Sistem)" "Sesuai"
FillRow $t 4 "3." "Bobot Nilai"              "Admin mengatur persentase bobot nilai ujian CBT dan komponen lainnya."                  "Sistem simpan bobot ke tabel pengaturans yang digunakan saat menjalankan seleksi."           "Bobot nilai tersimpan dan diaplikasikan pada perhitungan skor akhir di proses seleksi berikutnya."       "(Sistem)" "Sesuai"
FillRow $t 5 "4." "Periode CBT"              "Admin mengisi tanggal mulai/selesai ujian CBT dan mengubah status CBT."                "Sistem simpan cbt_tgl_mulai, cbt_tgl_selesai, dan cbt_status ke tabel pengaturans."          "Dashboard siswa tampilkan informasi periode ujian sesuai konfigurasi terbaru."                           "(Sistem)" "Sesuai"
FillRow $t 6 "5." "Tambah Jurusan"           "Admin mengisi nama jurusan, deskripsi, dan kuota, lalu klik 'Simpan'."                 "Sistem simpan data jurusan baru ke tabel jurusans."                                          "Jurusan baru muncul di daftar kelola, landing page, dan form pendaftaran siswa."                        "(Sistem)" "Sesuai"
FillRow $t 7 "6." "Edit Jurusan"             "Admin mengeklik 'Edit' pada jurusan tertentu dan memperbarui datanya."                 "Sistem perbarui data di tabel jurusans berdasarkan ID."                                      "Data jurusan berhasil diperbarui dan tampil di semua halaman terkait."                                   "(Sistem)" "Sesuai"
FillRow $t 8 "7." "Hapus Jurusan"            "Admin mengeklik 'Hapus' pada jurusan tertentu."                                       "Sistem hapus record dari tabel jurusans (dengan pengecekan relasi ke pendaftaran)."           "Jurusan terhapus dari daftar dan tidak lagi tersedia di form pilihan siswa."                             "(Sistem)" "Sesuai"
FillRow $t 9 "8." "Kelola Notifikasi"         "Admin membuka /admin/notifikasi dan mengeklik 'Tandai Semua Dibaca'."                  "Sistem POST ke notifikasi/read-all dan perbarui kolom read_at semua notifikasi admin."        "Badge jumlah notifikasi di navbar berubah menjadi 0 dan semua notifikasi tampil sudah dibaca."           "(Sistem)" "Sesuai"
FinishTable

# Save
$filename = "c:\laragon\www\ppdb-sekolah\Pengujian_Blackbox_PPDB_Final.docx"
$doc.SaveAs([ref]$filename)
$doc.Close()
$word.Quit()
[System.Runtime.Interopservices.Marshal]::ReleaseComObject($word) | Out-Null
Write-Host "SUKSES. File: $filename"
