<?php

declare(strict_types=1);

require_once __DIR__ . '/config/bootstrap.php';
require_once __DIR__ . '/controllers/ResumeController.php';
require_once __DIR__ . '/models/Resume.php';

require_role('job_seeker');

$userId = (int) current_user_id();
$resumeModel = new Resume($pdo);
$controller = new ResumeController($pdo);
$resumeId = isset($_GET['id']) ? (int) $_GET['id'] : null;
$resume = null;

if ($resumeId !== null) {
    $resume = $resumeModel->findOwnedById($resumeId, $userId);
    if (!$resume) {
        flash('error', 'Резюме не найдено.');
        redirect('/dashboard.php');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        flash('error', 'CSRF-токен невалиден.');
        redirect('/dashboard.php');
    }

    $result = $controller->save($_POST, $userId, $resumeId);
    flash($result['success'] ? 'success' : 'error', $result['message']);
    redirect('/dashboard.php');
}

$title = $resume ? 'Редактировать резюме' : 'Создать резюме';
require __DIR__ . '/views/layout/header.php';
?>
<h1><?= e($title) ?></h1>
<form method="post" class="card p-3">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <div class="mb-3">
        <label class="form-label">Заголовок</label>
        <input class="form-control" type="text" name="title" value="<?= e($resume['title'] ?? '') ?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Содержание</label>
        <textarea class="form-control" name="content" rows="8" required><?= e($resume['content'] ?? '') ?></textarea>
    </div>
    <button class="btn btn-success" type="submit">Сохранить</button>
</form>
<?php require __DIR__ . '/views/layout/footer.php'; ?>
