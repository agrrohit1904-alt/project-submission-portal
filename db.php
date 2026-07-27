<?php
$host    = getenv('DB_HOST') ?: '127.0.0.1';
$port    = getenv('DB_PORT') ?: '3306';
$db      = getenv('DB_NAME') ?: 'skit_portal';
$user    = getenv('DB_USER') ?: 'root';
$pass    = getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : '';
$charset = 'utf8mb4';

// Check if a full DATABASE_URL or JAWSDB_URL is provided (common in cloud hosts)
$db_url = getenv('DATABASE_URL') ?: getenv('JAWSDB_URL') ?: getenv('CLEARDB_DATABASE_URL');
if ($db_url) {
    $url = parse_url($db_url);
    $host = $url['host'] ?? $host;
    $port = $url['port'] ?? $port;
    $user = $url['user'] ?? $user;
    $pass = $url['pass'] ?? $pass;
    $db   = ltrim($url['path'] ?? '', '/') ?: $db;
}

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
if (defined('PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT')) {
    $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = false;
}

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Database connection failed. Check your database settings and environment variables. Error: " . $e->getMessage());
}
?>
