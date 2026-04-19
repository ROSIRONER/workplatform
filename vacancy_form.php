<?php

declare(strict_types=1);

require_once __DIR__ . '/config/bootstrap.php';
require_once __DIR__ . '/controllers/VacancyController.php';
require_once __DIR__ . '/models/Vacancy.php';

require_role('employer');

$employerId = (int) current_user_id();
$vacancyModel = new Vacancy($pdo);
$controller = new VacancyController($pdo);
$vacancyId = isset($_GET['id']) ? (int) $_GET['id'] : null;
$vacancy = null;

if ($vacancyId !== null) {
    $vacancy = $vacancyModel->findOwnedById($vacancyId, $employerId);
    if (!$vacancy) {
        flash('error', 'Вакансия не найдена.');
        redirect('/dashboard.php');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        flash('error', 'CSRF-токен невалиден.');
        redirect('/dashboard.php');
    }

    $result = $controller->save($_POST, $employerId, $vacancyId);
    flash($result['success'] ? 'success' : 'error', $result['message']);
    redirect('/dashboard.php');
}

$title = $vacancy ? 'Редактировать вакансию' : 'Создать вакансию';
require __DIR__ . '/views/layout/header.php';
?>
<h1><?= e($title) ?></h1>
<form method="post" class="card p-3">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <div class="mb-3">
        <label class="form-label">Название</label>
        <input class="form-control" type="text" name="title" value="<?= e($vacancy['title'] ?? '') ?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Описание</label>
        <textarea class="form-control" name="description" rows="8" required><?= e($vacancy['description'] ?? '') ?></textarea>
    </div>
    <div class="mb-3">
        <label class="form-label">Зарплата</label>
        <input class="form-control" type="text" name="salary" value="<?= e($vacancy['salary'] ?? '') ?>" required>
    </div>
    <button class="btn btn-success" type="submit">Сохранить</button>
</form>
<?php require __DIR__ . '/views/layout/footer.php'; ?>
