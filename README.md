# JanatulAulia Studio — Proyek Akhir Rekayasa Perangkat Lunak

Website studio kreatif digital yang menampilkan informasi layanan dan portofolio serta menerima Project Brief calon klien. Dashboard admin digunakan untuk mengelola layanan, portofolio, artikel, dan permintaan proyek.

## Teknologi

- PHP Native dengan PDO
- MySQL / MariaDB
- HTML, Tailwind CSS CDN, dan JavaScript
- Gemini API
- Unit Test PHP dan GitHub Actions CI
- Apache melalui XAMPP

## Link Proyek dan Dokumentasi

### Repository GitHub

Source code lengkap aplikasi:

- **GitHub Repository:** https://github.com/janatulaulia09-collab/JanatulAuliaStudio.git

### Desain UI/UX Figma

Dokumentasi desain dan prototype Figma:

- [Dokumentasi Spesifikasi Figma](docs/FIGMA_PROTOTYPE_SPEC.md)
- [Prototype Utama](https://www.figma.com/proto/4d8ybjHjJVnq3dXAIbBhvi/Untitled?node-id=9-2&t=CSSSNe71BRj1PioT-1&scaling=min-zoom&content-scaling=fixed&page-id=0%3A1&starting-point-node-id=9%3A2)
- [Prototype Kedua](https://www.figma.com/proto/4d8ybjHjJVnq3dXAIbBhvi/Untitled?node-id=7-8&t=rkto6FxshtVyxEo6-1)
- [Prototype Ketiga](https://www.figma.com/proto/4d8ybjHjJVnq3dXAIbBhvi/Untitled?node-id=8-42&t=rkto6FxshtVyxEo6-1)

### Video Presentasi Proyek

Video presentasi dan demonstrasi aplikasi:

1. **Video Progress 1 — Analisis Kebutuhan (3–5 menit)**  
   https://youtu.be/qLnBfVGJsJ0?si=u5ROlLVxf0wrx9XH

2. **Video Progress 2 — Perancangan Sistem (5–7 menit)**  
   https://youtu.be/5Mg57A2pn1U?si=Whl-49ZcvH13-RKJ
3. **Video Final — Demo Aplikasi (8–12 menit)**  
   https://youtu.be/nMg0eS-GmCs?si=V48neUCy2dd57dj3

## Database

Nama database yang digunakan adalah **`janatulaulia_web`**.

File SQL tersedia di:

`database/database_janatulaulia_web.sql`

### Tabel Aplikasi

| Tabel | Kegunaan |
|---|---|
| `admin_users` | Akun admin dan hash password |
| `project_categories` | Kategori portofolio |
| `projects` | Karya/portofolio |
| `project_media` | Media tambahan portofolio |
| `services` | Katalog layanan |
| `service_faqs` | FAQ layanan |
| `leads` | Project Brief calon klien |
| `articles` | Artikel/insight |

## Instalasi XAMPP

1. Salin folder `JanatulAuliaStudio` ke `C:\xampp\htdocs\`.
2. Jalankan Apache dan MySQL melalui XAMPP Control Panel.
3. Pastikan database `janatulaulia_web` sudah dibuat dan diimpor. Jika belum, import file `database/database_janatulaulia_web.sql` melalui phpMyAdmin.
4. Pastikan `config/env.php` tersedia. Jika belum ada, salin `config/env.example.php` menjadi `config/env.php`.
5. Default lokal XAMPP adalah host `127.0.0.1`, user `root`, dan password kosong. Sesuaikan jika konfigurasi komputer berbeda.
6. Buka `http://localhost/JanatulAuliaStudio/` untuk website publik.
7. Buka `http://localhost/JanatulAuliaStudio/auth/login.php` untuk login admin.

## Membuat Akun Admin Pertama

Aplikasi membaca akun dari tabel `admin_users` dengan kolom `name`, `email`, `password_hash`, dan `role`.

Password harus dibuat menggunakan fungsi `password_hash()` PHP, bukan teks biasa.

Buka:

`http://localhost/JanatulAuliaStudio/tools/buat_admin_pertama.php`

Isi nama, email, dan password kuat minimal 12 karakter. Helper dibatasi untuk localhost dan hanya dapat membuat akun pertama.

Setelah akun berhasil dibuat, hapus file `tools/buat_admin_pertama.php` dari folder proyek.

## Mengelola Konten

- Layanan baru akan berstatus `draft` secara default. Ubah menjadi `published` setelah deskripsi dan deliverables diperiksa.
- Portofolio membutuhkan judul, tahun, ringkasan, deskripsi, dan gambar sampul.
- Pilih layanan terkait agar karya tampil di halaman layanan tersebut.
- Sembilan studi konsep internal (bukan proyek klien) tersedia sebagai contoh visual. Setelah mengimpor database utama, jalankan `database/portfolio_studi_konsep.sql` melalui phpMyAdmin untuk memasukkannya.
- Hanya layanan dan portofolio berstatus `published` yang ditampilkan di halaman publik.
- Project Brief baru masuk ke tabel `leads` dengan status `New`.
- Admin dapat mengubah status menjadi `Contacted`, `Qualified`, `Proposal`, `Won`, atau `Lost`.

## Pengaturan Lingkungan

Edit `config/env.php` untuk kredensial database lokal.

File tersebut harus dikecualikan melalui `.gitignore`. Jangan commit konfigurasi yang berisi kredensial ke repository publik.

`APP_URL` pada konfigurasi digunakan sebagai informasi instalasi. Path aplikasi dideteksi otomatis dari URL folder project.

### Mengaktifkan Asisten JanatulAulia (Gemini API)

Panduan tersedia di:

- `docs/SETUP_GEMINI_API.md`
- `docs/AI_CHATBOT.md`

Langkah konfigurasi:

1. Buat API Key melalui [Google AI Studio](https://aistudio.google.com/api-keys).
2. Salin `config/ai.example.php` menjadi `config/ai.local.php`.
3. Isi `api_key` pada file lokal.
4. Jangan masukkan API Key ke browser, HTML, JavaScript, atau repository GitHub.
5. Pastikan koneksi internet aktif dan ekstensi PHP cURL aktif (`extension=curl` di `php.ini` XAMPP).
6. Restart Apache setelah perubahan konfigurasi.

Model awal: `gemini-3.5-flash-lite`.

Perhatikan kuota, ketentuan penggunaan, dan harga terbaru Google sebelum mengaktifkan billing atau menggunakan layanan berbayar.

Walaupun website berjalan di localhost, permintaan AI tetap memerlukan internet dan dikirim ke Gemini API.

Backend hanya mengirim konteks publik dari layanan, FAQ, portofolio, dan artikel yang berstatus `published`. Backend tidak membaca tabel `leads` atau `admin_users`.

Hindari memasukkan informasi pribadi atau sensitif ke dalam chat.

## Fitur Aplikasi

- Landing page publik dengan katalog layanan dari database.
- Tampilan mengikuti arah visual prototype Figma.
- Navigasi publik responsif untuk desktop dan mobile.
- Halaman portofolio dengan filter kategori.
- Detail portofolio beserta galeri media.
- Artikel/Insight publik dan halaman detail.
- CRUD artikel pada dashboard admin.
- Project Brief bertahap dengan tiga langkah: kontak, proyek, dan detail kebutuhan.
- Validasi server-side, CSRF token, honeypot, dan pembatasan submit berulang sederhana.
- Progress form, review ringkasan, tombol kembali, dan pesan berhasil/gagal.
- Login/logout admin menggunakan password hash dan session regeneration.
- Dashboard admin dengan ringkasan layanan, portofolio, dan Project Brief.
- CRUD layanan.
- CRUD portofolio dan kategori.
- Pengelolaan status Project Brief.
- Asisten chatbot AI menggunakan Gemini API.

## Batasan Versi Ini

- Upload media belum tersedia melalui formulir.
- Gambar portofolio dan artikel menggunakan path lokal atau URL.
- Galeri portofolio mengambil data dari tabel `project_media` dan dapat dikelola melalui database.

## Pengujian Otomatis

Jalankan unit test tanpa koneksi database:

```bash
php tests/run.php
```

Integration test membutuhkan database pengujian terpisah bernama `janatulaulia_web_test`.

Script `tests/prepare_database.php` menyiapkan schema tersebut, sedangkan `tests/integration.php` menguji delapan tabel, publikasi layanan, dan relasi lead.

Jangan arahkan integration test ke database produksi atau database kerja utama.

Contoh variabel untuk PowerShell:

```powershell
$env:DB_HOST = "127.0.0.1"
$env:DB_PORT = "3306"
$env:DB_USER = "root"
$env:DB_PASS = "password_test"
$env:TEST_DB_NAME = "janatulaulia_web_test"

php tests/prepare_database.php
php tests/integration.php
```

### GitHub Actions dan CI/CD

GitHub Actions dirancang untuk menjalankan:

- Pemeriksaan sintaks PHP.
- Unit test.
- Integration test.
- Pengujian pada PHP 8.2 dan 8.3 dengan MySQL 8.0.

Status workflow baru dapat dibuktikan setelah source code di-push ke repository GitHub dan workflow berhasil dijalankan.

## Pemeriksaan Awal

1. Buka website publik dan pastikan tidak muncul error PHP atau SQL.
2. Login menggunakan akun admin.
3. Tambahkan satu layanan sebagai Draft dan pastikan belum tampil di website.
4. Ubah layanan menjadi Published dan pastikan tampil di bagian Layanan.
5. Tambahkan satu kategori dan satu karya portofolio.
6. Kirim Project Brief percobaan dan pastikan data masuk ke tabel `leads`.
7. Ubah status brief melalui dashboard.
8. Logout dan pastikan halaman admin meminta autentikasi.
9. Uji chatbot ketika API Key belum disiapkan atau provider gagal.
10. Setelah API Key valid, ajukan pertanyaan tentang layanan yang sudah dipublikasikan.

Gunakan data dummy ketika melakukan pengujian. Jangan gunakan data pelanggan sungguhan.
