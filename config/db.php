<?php

declare(strict_types=1);

/*
 * Безопасная схема для Beget:
 * 1) Создайте файл config/db.credentials.php (НЕ загружайте в git) и верните массив:
 *    return ['host' => 'localhost', 'dbname' => '...', 'username' => '...', 'password' => '...'];
 * 2) Либо задайте переменные окружения DB_HOST, DB_NAME, DB_USER, DB_PASS.
 */

$credentialsFile = __DIR__ . '/db.credentials.php';
$credentials = [];
if (is_file($credentialsFile)) {
    $loaded = require $credentialsFile;
    if (is_array($loaded)) {
        $credentials = $loaded;
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
