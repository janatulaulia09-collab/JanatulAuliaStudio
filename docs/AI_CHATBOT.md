# Asisten JanatulAulia — Gemini API

## Tujuan

Asisten JanatulAulia memberi informasi awal mengenai layanan, FAQ, portofolio, dan artikel yang telah dipublikasikan. Chatbot tidak membuat keputusan bisnis, menentukan harga, menjanjikan deadline, atau menyetujui proyek.

## Arsitektur

1. Widget mengirim pertanyaan ke `api/chat.php` menggunakan `POST` JSON dan header CSRF.
2. Backend memvalidasi sesi, format pertanyaan, panjang input, informasi kontak, dan batas permintaan per sesi.
3. `ChatbotService` hanya membaca tabel layanan, FAQ, portofolio, dan artikel berstatus `published`.
4. Backend mengirim system instruction, konteks publik, pertanyaan, dan maksimal enam pesan riwayat percakapan ke Gemini API.
5. Backend memanggil `generateContent` melalui HTTPS dengan API key di header `x-goog-api-key`.
6. Jawaban teks dikirim kembali ke widget. Riwayat chat tidak disimpan permanen dalam database.

## Model dan biaya

Model awal: `gemini-3.5-flash-lite`. Harga resmi Google mencantumkan input dan output teks pada free tier sebagai gratis, tetapi berlaku kuota/rate limit dan ketersediaan berdasarkan project. Penggunaan di luar batas/level gratis atau pada fitur berbayar dapat dikenakan biaya. Versi free tier menyatakan konten dapat digunakan untuk meningkatkan produk Google. Periksa harga dan syarat terkini sebelum mengaktifkan project berbayar.

Referensi resmi:
- Harga: https://ai.google.dev/gemini-api/docs/pricing
- Memulai dan API key: https://ai.google.dev/gemini-api/docs/get-started
- AI Studio: https://aistudio.google.com/api-keys

## Data yang boleh dikirim

- Nama, deskripsi, dan deliverables layanan published.
- FAQ dari layanan published.
- Judul, kategori, tahun, ringkasan, dan deskripsi singkat portofolio published.
- Judul, ringkasan, dan isi terbatas artikel published.
- Pertanyaan umum pengunjung setelah melewati validasi.

## Data yang tidak boleh dikirim

- Isi tabel `leads` atau `admin_users`.
- Password/hash password, sesi admin, kredensial database, atau API key.
- Email dan nomor ponsel yang dimasukkan pada pertanyaan/riwayat chat (ditolak di backend).
- Data draft/unpublished atau data pribadi pelanggan.

Walaupun website hanya berjalan di localhost, permintaan AI tetap membutuhkan koneksi internet dan konten prompt yang dikirim ke Gemini diproses melalui layanan Google. Jangan mengetikkan data sensitif pada chatbot. API key hanya disimpan di backend dalam `config/ai.local.php` atau environment variable server; jangan diletakkan di HTML/JavaScript.

## Konfigurasi Windows/XAMPP

1. Pastikan komputer memiliki koneksi internet.
2. Buka https://aistudio.google.com/api-keys dan buat API key untuk project Google AI Studio.
3. Salin `config/ai.example.php` menjadi `config/ai.local.php`.
4. Isi nilai `api_key` di `config/ai.local.php`; jangan menaruh key di `partials/chat_widget.php`, HTML, JavaScript, atau repository GitHub.
5. Pastikan ekstensi PHP cURL aktif pada `php.ini` XAMPP (`extension=curl`), lalu restart Apache.
6. Pastikan nama model tetap `gemini-3.5-flash-lite`.
7. Buka website dan uji chatbot dengan pertanyaan tentang layanan/portofolio yang sudah berstatus published.

Alternatif: set environment variable `GEMINI_API_KEY` pada environment Apache/PHP, lalu restart Apache. `config/ai.local.php` sudah tercantum dalam `.gitignore`.

## Penanganan error

- API key belum diisi: chatbot memberi pesan konfigurasi belum lengkap.
- HTTP 401/403: periksa API key, project, dan izin API.
- HTTP 404: periksa dukungan model untuk project/key yang digunakan.
- HTTP 429: batas kuota/rate limit tercapai; tunggu reset atau tinjau halaman kuota AI Studio.
- Koneksi gagal: periksa internet, DNS, firewall, dan ekstensi cURL.

## Batas MVP

- Batas aplikasi saat ini: 8 pertanyaan per 60 detik per sesi. Kuota Google berlaku terpisah.
- Riwayat percakapan disimpan sementara di browser dan tidak disimpan permanen pada database.
- Tidak menggunakan Google Search grounding atau tools berbayar; hanya generasi teks standar.
- Jika Gemini tidak tersedia, widget menyediakan pesan fallback dan pengunjung masih dapat menggunakan Project Brief.
- API key tidak boleh masuk GitHub atau dibagikan melalui percakapan.
