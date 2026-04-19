<?php

declare(strict_types=1);

/*
 * Варианты хранения секретов (по приоритету):
 * 1) ../db.php (на уровень выше public_html), как просили преподаватели
 *    Файл должен возвращать массив ['host' => ..., 'dbname' => ..., 'username' => ..., 'password' => ...]
 * 2) config/db.credentials.php (локальный приватный файл)
 * 3) ENV-переменные DB_HOST, DB_NAME, DB_USER, DB_PASS
 */

$credentials = [];

$externalDbFile = dirname(__DIR__) . '/../db.php';
if (is_file($externalDbFile)) {
    $loaded = require $externalDbFile;
    if (is_array($loaded)) {
        $credentials = $loaded;
    }
}

if (!$credentials) {
    $credentialsFile = __DIR__ . '/db.credentials.php';
    if (is_file($credentialsFile)) {
        $loaded = require $credentialsFile;
        if (is_array($loaded)) {
            $credentials = $loaded;
        }
    }
}

$host = (string) ($credentials['host'] ?? getenv('DB_HOST') ?: 'localhost');
$dbname = (string) ($credentials['dbname'] ?? getenv('DB_NAME') ?: 'user_db');
$username = (string) ($credentials['username'] ?? getenv('DB_USER') ?: 'user_db');
$password = (string) ($credentials['password'] ?? getenv('DB_PASS') ?: 'CHANGE_ME');
$charset = 'utf8mb4';

$dsn = "mysql:host={$host};dbname={$dbname};charset={$charset}";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $exception) {
    exit('Database connection failed: ' . $exception->getMessage());
}
