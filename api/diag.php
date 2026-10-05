<?php

// ponytail: standalone PDO diagnostics. Delete after deployment stabilizes.
// No Laravel boot, no session/cache middleware — safe even when DB tables are missing.

$token = $_GET['token'] ?? '';
$expected = getenv('APP_KEY');
if (!hash_equals((string) $expected, (string) $token)) {
    http_response_code(403);
    exit('forbidden');
}

header('Content-Type: application/plain; charset=utf-8');

$host = getenv('DB_HOST');
$port = getenv('DB_PORT') ?: '3306';
$db = getenv('DB_DATABASE');
$user = getenv('DB_USERNAME');
$pass = getenv('DB_PASSWORD');

echo "PHP " . PHP_VERSION . "\n";
echo "APP_KEY set: " . ($expected ? 'yes' : 'no') . "\n";
echo "DB_HOST: {$host}\nDB_DATABASE: {$db}\n\n";

$dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";
try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 15,
        PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
    ]);
    echo "DB CONNECT OK\n\n";

    $rows = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
    echo "TABLES (" . count($rows) . "):\n";
    foreach ($rows as $t) {
        echo "  - {$t}\n";
    }
    echo "\n";

    foreach (['sessions', 'cache', 'users'] as $need) {
        echo "table `{$need}`: " . (in_array($need, $rows, true) ? 'EXISTS' : 'MISSING') . "\n";
    }
} catch (Throwable $e) {
    echo "DB ERROR: " . $e::class . ' ' . $e->getMessage() . "\n";
}
