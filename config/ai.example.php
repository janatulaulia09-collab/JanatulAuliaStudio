<?php
/**
 * Konfigurasi Gemini API untuk Asisten JanatulAulia.
 * Salin file ini menjadi ai.local.php, lalu isi API key dari Google AI Studio.
 * Jangan unggah ai.local.php atau API key ke GitHub.
 */
return [
    'api_key' => '', // Bisa diisi melalui environment variable GEMINI_API_KEY.
    'model' => 'gemini-3.5-flash-lite',
    'timeout' => 60,
    'max_tokens' => 500,
];
