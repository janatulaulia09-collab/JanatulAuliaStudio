<?php
declare(strict_types=1);

// API endpoint: cegah warning PHP bercampur dengan JSON.
ob_start();
ini_set('display_errors', '0');
ini_set('log_errors', '1');
$apiResponseSent = false;

register_shutdown_function(static function () use (&$apiResponseSent): void {
    if ($apiResponseSent) {
        return;
    }
    $lastError = error_get_last();
    $fatalTypes = [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR];
    $isFatal = is_array($lastError) && in_array($lastError['type'] ?? 0, $fatalTypes, true);
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    if (!headers_sent()) {
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store, private');
        header('X-Content-Type-Options: nosniff');
    }
    http_response_code(500);
    echo json_encode([
        'ok' => false,
        'reply' => $isFatal
            ? 'Terjadi kesalahan internal pada endpoint chatbot. Periksa log Apache/PHP.'
            : 'Endpoint chatbot berhenti sebelum mengirim jawaban. Periksa koneksi database dan log Apache/PHP.',
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
});

$respond = static function (int $status, array $payload) use (&$apiResponseSent): never {
    $apiResponseSent = true;
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store, private');
    header('X-Content-Type-Options: nosniff');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
};

use App\Services\ChatbotService;

try {
    require_once __DIR__ . '/../config/bootstrap.php';
    ini_set('display_errors', '0');
    require_once __DIR__ . '/../app/Services/ChatbotService.php';

    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
        header('Allow: POST');
        $respond(405, ['ok' => false, 'reply' => 'Metode tidak diizinkan.']);
    }

    $csrf = (string)($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
    if (!verify_csrf($csrf)) {
        $respond(419, ['ok' => false, 'reply' => 'Sesi keamanan berakhir. Muat ulang halaman lalu coba kembali.']);
    }

    $rawBody = file_get_contents('php://input');
    $data = json_decode(is_string($rawBody) ? $rawBody : '', true);
    if (!is_array($data)) {
        $respond(400, ['ok' => false, 'reply' => 'Format permintaan tidak valid.']);
    }

    $question = $data['question'] ?? '';
    $history = $data['history'] ?? [];
    if (!is_string($question) || !is_array($history)) {
        $respond(422, ['ok' => false, 'reply' => 'Pertanyaan tidak valid.']);
    }

    $now = time();
    $requests = array_values(array_filter(
        $_SESSION['chat_requests'] ?? [],
        static fn ($timestamp) => is_int($timestamp) && ($now - $timestamp) < 60
    ));
    if (count($requests) >= 8) {
        header('Retry-After: 60');
        $respond(429, ['ok' => false, 'reply' => 'Kamu sudah mengirim beberapa pertanyaan. Tunggu sebentar sebelum mencoba lagi.']);
    }
    $requests[] = $now;
    $_SESSION['chat_requests'] = $requests;

    $aiConfigPath = __DIR__ . '/../config/ai.local.php';
    $aiConfig = is_file($aiConfigPath) ? require $aiConfigPath : [];
    if (!is_array($aiConfig)) {
        $aiConfig = [];
    }

    $service = new ChatbotService();
    $result = $service->answer($pdo, $aiConfig, $question, $history);
    $status = (int)($result['http_status'] ?? 503);
    $respond($status, [
        'ok' => (bool)($result['ok'] ?? false),
        'reply' => (string)($result['reply'] ?? 'Asisten belum tersedia. Silakan gunakan Project Brief.'),
    ]);
} catch (Throwable $exception) {
    $safeMessage = preg_replace('/[\r\n\t]+/', ' ', $exception->getMessage()) ?? '';
    $safeMessage = substr($safeMessage, 0, 240);
    error_log('[CHATBOT API] ' . get_class($exception) . ': ' . $safeMessage);
    $respond(500, ['ok' => false, 'reply' => 'Endpoint chatbot mengalami kesalahan internal. Periksa log PHP/Apache untuk entri [CHATBOT API].']);
}
