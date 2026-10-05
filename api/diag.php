<?php

// ponytail: raw PDO probe — no Laravel boot. Delete after deployment stabilizes.

header('Content-Type: text/plain; charset=utf-8');

$expected = getenv('APP_KEY');
if (!hash_equals((string) $expected, (string) ($_GET['token'] ?? ''))) {
    http_response_code(403);
    exit('forbidden');
}

echo 'PHP '.PHP_VERSION."\n";
echo 'APP_KEY: '.($expected ? 'set' : 'MISSING')."\n";
echo 'DB_HOST: '.getenv('DB_HOST')."\n";
echo 'DB_DATABASE: '.getenv('DB_DATABASE')."\n\n";

$host = getenv('DB_HOST');
$port = getenv('DB_PORT') ?: '3306';
$dsn = "mysql:host={$host};port={$port};dbname=".getenv('DB_DATABASE').";charset=utf8mb4";

try {
    $pdo = new PDO($dsn, getenv('DB_USERNAME'), getenv('DB_PASSWORD'), [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 15,
    ]);
    echo "DB CONNECT: OK\n\n";

    $rows = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
    echo 'TABLES: '.count($rows)."\n";

    foreach ($rows as $t) {
        echo "  - {$t}\n";
    }

    echo "\nREQUIRED:\n";
    foreach (['users', 'sessions', 'cache', 'items'] as $n) {
        echo "  {$n}: ".(in_array($n, $rows, true) ? 'EXISTS' : 'MISSING')."\n";
    }
} catch (Throwable $e) {
    echo 'DB ERROR: '.$e::class.' '.$e->getMessage()."\n";
}
