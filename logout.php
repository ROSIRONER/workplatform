<?php

declare(strict_types=1);

require_once __DIR__ . '/config/bootstrap.php';
require_once __DIR__ . '/controllers/AuthController.php';

$controller = new AuthController($pdo);
$controller->logout();
flash('success', 'Вы вышли из системы.');
redirect('/index.php');
