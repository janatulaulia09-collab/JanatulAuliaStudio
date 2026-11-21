# Setup Gemini API di XAMPP (Windows)

## 1. Buat API key

1. Masuk ke https://aistudio.google.com/api-keys dengan akun Google.
2. Buat atau pilih project.
3. Buat API key dan salin ke tempat aman.
4. Jangan kirim API key ke chat, jangan taruh di JavaScript, dan jangan commit ke GitHub.

## 2. Simpan API key secara lokal

Dari folder project `JanatulAuliaStudio`, salin:

`config/ai.example.php` menjadi `config/ai.local.php`.

Edit `config/ai.local.php`:

```php
return [
    'api_key' => 'TEMPEL_API_KEY_DI_SINI',
    'model' => 'gemini-3.5-flash-lite',
    'timeout' => 60,
    'max_tokens' => 500,
];
```

Jangan gunakan tanda kutip ganda tambahan atau spasi di luar API key. File `ai.local.php` sudah diabaikan oleh Git.

## 3. Aktifkan cURL

1. Buka XAMPP Control Panel → Apache → Config → `PHP (php.ini)`.
2. Cari `;extension=curl` dan ubah menjadi `extension=curl` (hapus titik koma awal).
3. Simpan, restart Apache.

## 4. Uji chatbot

1. Pastikan koneksi internet aktif.
2. Buka `http://localhost/JanatulAuliaStudio/`.
3. Klik **Tanya Asisten AI**.
4. Ajukan pertanyaan tentang layanan yang telah berstatus Published.

Contoh: `Apa saja layanan yang tersedia di JanatulAulia Studio?`

Jika mendapat balasan kuota (HTTP 429), cek batas penggunaan project di Google AI Studio. Paket gratis memiliki batas, bukan akses tak terbatas. Jangan aktifkan billing atau pilih model berbayar bila belum ingin mengeluarkan biaya.
