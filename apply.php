<?php

declare(strict_types=1);

require_once __DIR__ . '/config/bootstrap.php';
require_once __DIR__ . '/controllers/ApplicationController.php';
require_once __DIR__ . '/models/Vacancy.php';
require_once __DIR__ . '/models/Resume.php';

require_role('job_seeker');

$vacancyId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$vacancyModel = new Vacancy($pdo);
$resumeModel = new Resume($pdo);
$vacancy = $vacancyModel->findById($vacancyId);
$resumes = $resumeModel->allByUser((int) current_user_id());

if (!$vacancy) {
    flash('error', 'Вакансия не найдена.');
    redirect('/index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        flash('error', 'CSRF-токен невалиден.');
        redirect('/index.php');
    }

    $resumeId = isset($_POST['resume_id']) ? (int) $_POST['resume_id'] : 0;
    $controller = new ApplicationController($pdo);
    $result = $controller->apply((int) current_user_id(), $vacancyId, $resumeId);
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
<?php if (!$resumes): ?>
    <div class="alert alert-warning">
        У вас нет резюме. Сначала создайте резюме в личном кабинете, затем отправьте отклик.
    </div>
    <a class="btn btn-primary" href="/resume_form.php">Создать резюме</a>
<?php else: ?>
<form method="post">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <div class="mb-3">
        <label class="form-label">Выберите резюме для отклика</label>
        <select class="form-select" name="resume_id" required>
            <option value="">-- Выберите --</option>
            <?php foreach ($resumes as $resume): ?>
                <option value="<?= (int) $resume['id'] ?>"><?= e($resume['title']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <button class="btn btn-primary" type="submit">Подтвердить отклик</button>
    <a class="btn btn-outline-secondary" href="/index.php">Отмена</a>
</form>
<?php endif; ?>
<?php require __DIR__ . '/views/layout/footer.php'; ?>
