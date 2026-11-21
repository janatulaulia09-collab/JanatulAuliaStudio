<?php
declare(strict_types=1);

$host = getenv('DB_HOST') ?: '127.0.0.1';
$port = getenv('DB_PORT') ?: '3306';
$user = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASS') ?: '';
$database = getenv('TEST_DB_NAME') ?: 'janatulaulia_web_test';

if ($database !== 'janatulaulia_web_test') {
    fwrite(STDERR, "Refusing to prepare a database other than janatulaulia_web_test.\n");
    exit(2);
}

$pdo = new PDO("mysql:host={$host};port={$port};charset=utf8mb4", $user, $password, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_EMULATE_PREPARES => false,
]);

$sql = file_get_contents(__DIR__ . '/../database/database_janatulaulia_web.sql');
if (!is_string($sql)) {
    throw new RuntimeException('SQL schema file could not be read.');
}
$sql = str_replace('janatulaulia_web', $database, $sql);
$sql = preg_replace('/^\s*--.*$/m', '', $sql) ?? $sql;
$statements = preg_split('/;\s*(?:\R|$)/', $sql) ?: [];

foreach ($statements as $statement) {
    $statement = trim($statement);
    if ($statement === '' || preg_match('/^SHOW\s+TABLES/i', $statement)) {
        continue;
    }
    $pdo->exec($statement);
}

echo "Test database schema imported into {$database}.\n";
