<?php
$role = current_user_role();
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? 'Job Platform') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/public/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="/index.php">JobPlatform</a>
        <div class="collapse navbar-collapse show">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="/index.php">Главная</a></li>
                <?php if (current_user_id() === null): ?>
                    <li class="nav-item"><a class="nav-link" href="/login.php">Вход</a></li>
                    <li class="nav-item"><a class="nav-link" href="/register.php">Регистрация</a></li>
                <?php elseif ($role === 'job_seeker'): ?>
                    <li class="nav-item"><a class="nav-link" href="/dashboard.php">Профиль</a></li>
                    <li class="nav-item"><a class="nav-link" href="/logout.php">Выход</a></li>
                <?php elseif ($role === 'employer'): ?>
                    <li class="nav-item"><a class="nav-link" href="/dashboard.php">Кабинет</a></li>
                    <li class="nav-item"><a class="nav-link" href="/logout.php">Выход</a></li>
                <?php elseif ($role === 'admin'): ?>
                    <li class="nav-item"><a class="nav-link" href="/admin.php">Админка</a></li>
                    <li class="nav-item"><a class="nav-link" href="/logout.php">Выход</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<main class="container">
    <?php if ($success = flash('success')): ?>
        <div class="alert alert-success"><?= e($success) ?></div>
    <?php endif; ?>
    <?php if ($error = flash('error')): ?>
        <div class="alert alert-danger"><?= e($error) ?></div>
    <?php endif; ?>
