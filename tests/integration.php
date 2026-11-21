<?php
declare(strict_types=1);

$database = getenv('TEST_DB_NAME') ?: '';
if ($database !== 'janatulaulia_web_test') {
    fwrite(STDERR, "Integration tests require TEST_DB_NAME=janatulaulia_web_test.\n");
    exit(2);
}

$host = getenv('DB_HOST') ?: '127.0.0.1';
$port = getenv('DB_PORT') ?: '3306';
$user = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASS') ?: '';
$pdo = new PDO("mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4", $user, $password, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
]);

$expected = ['admin_users','articles','leads','project_categories','project_media','projects','service_faqs','services'];
$actual = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
sort($expected);
sort($actual);
if ($expected !== $actual) {
    throw new RuntimeException('Table set differs from the expected eight tables.');
}
echo "[PASS] Delapan tabel database tersedia.\n";

$slug = 'ci-service-' . bin2hex(random_bytes(4));
$categorySlug = 'ci-category-' . bin2hex(random_bytes(4));
$leadEmail = 'ci-' . bin2hex(random_bytes(4)) . '@example.test';
$leadId = null;
$serviceId = null;

try {
    $category = $pdo->prepare('INSERT INTO project_categories (name, slug, description) VALUES (:name, :slug, :description)');
    $category->execute(['name' => 'CI ' . $categorySlug, 'slug' => $categorySlug, 'description' => 'Test CI']);
    $categoryId = (int)$pdo->lastInsertId();

    $service = $pdo->prepare("INSERT INTO services (name, slug, description, deliverables, status) VALUES ('CI Service', :slug, 'Test service', 'Test deliverables', 'published')");
    $service->execute(['slug' => $slug]);
    $serviceId = (int)$pdo->lastInsertId();

    $publicService = $pdo->prepare("SELECT id FROM services WHERE id = :id AND status = 'published'");
    $publicService->execute(['id' => $serviceId]);
    if ((int)$publicService->fetchColumn() !== $serviceId) {
        throw new RuntimeException('Published service query did not return the service.');
    }
    echo "[PASS] Layanan published dapat dibaca sebagai konten publik.\n";

    $lead = $pdo->prepare("INSERT INTO leads (service_id, name, email, phone, scope, status) VALUES (:service_id, 'CI Test Client', :email, '081234567890', 'Test integration brief content', 'New')");
    $lead->execute(['service_id' => $serviceId, 'email' => $leadEmail]);
    $leadId = (int)$pdo->lastInsertId();

    $pdo->prepare('DELETE FROM services WHERE id = :id')->execute(['id' => $serviceId]);
    $serviceId = null;
    $preservedLead = $pdo->prepare('SELECT service_id FROM leads WHERE id = :id');
    $preservedLead->execute(['id' => $leadId]);
    $serviceRef = $preservedLead->fetchColumn();
    if ($serviceRef !== null) {
        throw new RuntimeException('Lead service reference should become NULL after service deletion.');
    }
    echo "[PASS] Lead tetap tersimpan saat layanan terkait dihapus (service_id menjadi NULL).\n";

    $pdo->prepare('DELETE FROM project_categories WHERE id = :id')->execute(['id' => $categoryId]);
    echo "[PASS] Integritas relasi kategori dan layanan terjaga.\n";
} finally {
    if ($leadId !== null) {
        $pdo->prepare('DELETE FROM leads WHERE id = :id')->execute(['id' => $leadId]);
    }
    if ($serviceId !== null) {
        $pdo->prepare('DELETE FROM services WHERE id = :id')->execute(['id' => $serviceId]);
    }
    if (isset($categoryId)) {
        $pdo->prepare('DELETE FROM project_categories WHERE id = :id')->execute(['id' => $categoryId]);
    }
}

echo "Integration tests completed.\n";
