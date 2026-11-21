# Catatan Rilis — Tahap 3: Fondasi AI, Testing, dan CI/CD

## Perubahan utama

- Menambahkan widget Asisten JanatulAulia pada halaman Home dan Portofolio.
- Menambahkan endpoint `api/chat.php` dengan validasi POST, CSRF, pembatasan request per sesi, dan fallback saat Gemini API tidak tersedia.
- Menambahkan `ChatbotService` yang hanya mengambil layanan, FAQ, portofolio, dan artikel berstatus `published`.
- Mengintegrasikan Gemini API melalui HTTPS `generateContent` dengan API key pada header server-side.
- Menggunakan model `gemini-3.5-flash-lite`; harga input/output teks tercantum gratis pada free tier, tunduk pada kuota dan ketentuan Google.
- Mencegah pengiriman pertanyaan/riwayat yang mengandung alamat email atau nomor ponsel.
- Menambahkan konfigurasi lokal `config/ai.local.php` melalui template `config/ai.example.php`; file lokal diabaikan oleh Git.
- Menambahkan 11 unit test helper chatbot dan integration test untuk schema database.
- Menambahkan GitHub Actions workflow untuk pemeriksaan sintaks PHP, unit test, dan MySQL integration test.

## Pengaturan singkat

Lihat `docs/SETUP_GEMINI_API.md`. API key dibuat melalui Google AI Studio, disimpan di `config/ai.local.php`, dan tidak boleh diletakkan pada kode browser atau repository publik.

## Pemeriksaan otomatis paket

- Pemeriksaan sintaks PHP dan unit test dijalankan saat paket disiapkan.
- Koneksi MySQL langsung, keberhasilan API key/model pada akun Google pengguna, dan integrasi end-to-end harus diuji di XAMPP pengguna.
- GitHub Actions baru dapat diverifikasi setelah repository di-push ke GitHub.
