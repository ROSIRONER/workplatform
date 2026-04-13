<?php

declare(strict_types=1);

require_once __DIR__ . '/config/bootstrap.php';
require_once __DIR__ . '/controllers/ApplicationController.php';
require_once __DIR__ . '/models/Vacancy.php';

require_role('job_seeker');

$vacancyId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$vacancyModel = new Vacancy($pdo);
$vacancy = $vacancyModel->findById($vacancyId);

if (!$vacancy) {
    flash('error', 'Вакансия не найдена.');
    redirect('/index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        flash('error', 'CSRF-токен невалиден.');
        redirect('/index.php');
    }

    $controller = new ApplicationController($pdo);
    $result = $controller->apply((int) current_user_id(), $vacancyId);
    flash($result['success'] ? 'success' : 'error', $result['message']);
    redirect('/index.php');
}

$title = 'Отклик на вакансию';
require __DIR__ . '/views/layout/header.php';
?>
<h1>Отклик на вакансию</h1>
<div class="card p-3 mb-3">
    <h5><?= e($vacancy['title']) ?></h5>
    <p><?= nl2br(e($vacancy['description'])) ?></p>
    <p><strong>Зарплата:</strong> <?= e($vacancy['salary']) ?></p>
</div>
<form method="post">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <button class="btn btn-primary" type="submit">Подтвердить отклик</button>
    <a class="btn btn-outline-secondary" href="/index.php">Отмена</a>
</form>
<?php require __DIR__ . '/views/layout/footer.php'; ?>
