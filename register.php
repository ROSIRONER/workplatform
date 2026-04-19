<?php

declare(strict_types=1);

require_once __DIR__ . '/config/bootstrap.php';
require_once __DIR__ . '/controllers/AuthController.php';

if (current_user_id() !== null) {
    redirect('/dashboard.php');
}

$title = 'Регистрация';
$controller = new AuthController($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        flash('error', 'CSRF-токен невалиден.');
        redirect('/register.php');
    }

    $result = $controller->register($_POST);
    if ($result['success']) {
        flash('success', $result['message']);
        redirect('/login.php');
    }

    flash('error', $result['message']);
    redirect('/register.php');
}

require __DIR__ . '/views/layout/header.php';
?>
<h1>Регистрация</h1>
<form method="post" class="card p-3">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <div class="mb-3">
        <label class="form-label">Email</label>
        <input class="form-control" type="email" name="email" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Пароль</label>
        <input class="form-control" type="password" name="password" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Роль</label>
        <select class="form-select" name="role" required>
            <option value="job_seeker">Соискатель</option>
            <option value="employer">Работодатель</option>
        </select>
    </div>
    <button class="btn btn-primary" type="submit">Зарегистрироваться</button>
</form>
<?php require __DIR__ . '/views/layout/footer.php'; ?>
