# Walkthrough PPDB Multi-Page Website & Section Unification

Kami telah berhasil memisahkan landing page PPDB satu-halaman (*one-page/scrolling page*) sebelumnya menjadi website multi-halaman (*multi-page website*) yang terstruktur, rapi, dan dinamis, serta melakukan unifikasi section sesuai permintaan Anda.

## Perubahan yang Dilakukan

### 1. Merestrukturisasi Rute Aplikasi
Kami menambahkan rute-rute publik baru pada file [web.php](file:///c:/laragon/www/ppdb-sekolah/routes/web.php) untuk mengarahkan pengguna ke halaman yang sesuai secara dinamis:
- `/` -> Beranda (Halaman Utama)
- `/periode` -> Periode PPDB & Jadwal Kegiatan
- `/persyaratan` -> Berkas Persyaratan & Ketentuan
- `/jurusan` -> Pilihan Program Keahlian & Sisa Kuota (Dinamis dari Database)
- `/biaya-pembayaran` -> Biaya Masuk & Metode Pembayaran
- `/alur-ppdb` -> Alur Pendaftaran & Layanan Bantuan
- `/faq` -> Halaman FAQ (Tanya Jawab Accordion)
- `/kontak` -> Halaman Kontak & Peta Lokasi Sekolah

### 2. Memisahkan Halaman-Halaman Baru
Setiap menu navigasi kini membuka halaman terpisah yang diletakkan di bawah direktori `resources/views/public/`:
- **Beranda**: Menampilkan Hero PPDB, Status pendaftaran, Ringkasan kuota jurusan, Alur ringkas, dan bagian Keunggulan.
- **Periode**: Menampilkan status buka/tutup dan garis waktu (timeline) detail tahapan pendaftaran.
- **Persyaratan**: Menampilkan berkas wajib scan, kualifikasi umum, dan instruksi bantuan jika dokumen calon siswa belum lengkap.
- **Jurusan & Kuota**: Menampilkan data real-time sisa kuota, jumlah pendaftar, dan status "Kuota Penuh" terintegrasi langsung dengan database sekolah.
- **Biaya & Pembayaran**: Menampilkan tabel rincian biaya pendaftaran & daftar ulang, rekening transfer resmi sekolah, dan pembayaran tunai.
- **Alur PPDB**: Menampilkan penjelasan langkah demi langkah proses PPDB disertai keterangan layanan bantuan teknis.
- **FAQ**: Kumpulan tanya jawab dengan desain interaktif (buka-tutup accordion) untuk 6 pertanyaan wajib.
- **Kontak**: Menampilkan nomor Whatsapp panitia, telepon, email, peta lokasi (Google Maps Embed), dan kebijakan bantuan fisik sekolah.

### 3. Penggabungan Section Keunggulan & CTA
- Kami memulihkan section **"Kenapa Memilih SMK Mitra Bintaro?"** ke bentuk aslinya (3 card keunggulan sederhana: Pendaftaran Cepat, Sistem Transparan, Ujian CBT).
- Kami menyatukan blok CTA **"Siap Bergabung Bersama SMK"** ke bagian bawah section keunggulan tersebut agar halaman utama tampak ringkas dan tidak memuat terlalu banyak section vertikal yang berulang.
- Kami juga membersihkan konten-konten lama/duplikat agar halaman utama benar-benar bersih dan hanya berfokus sebagai *homepage* ringkas.

### 4. Navigasi & Footer (Layout)
Kami mengupdate [app.blade.php](file:///c:/laragon/www/ppdb-sekolah/resources/views/layouts/app.blade.php):
- Mengganti navigasi dari anchor scroll (`#faq`, `#persyaratan`) menjadi link rute resmi Laravel.
- Menambahkan deteksi URL aktif otomatis (*active route state*) agar menu menu menyala biru sesuai halaman yang sedang dibuka.
- Memperbarui seluruh link pada footer agar mengarah ke halaman yang tepat.
