<?php

declare(strict_types=1);

require_once __DIR__ . '/config/bootstrap.php';
require_once __DIR__ . '/controllers/ResumeController.php';

require_role('job_seeker');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/dashboard.php');
}

if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
    flash('error', 'CSRF-токен невалиден.');
    redirect('/dashboard.php');
}

$resumeId = isset($_POST['id']) ? (int) $_POST['id'] : 0;
$controller = new ResumeController($pdo);
$deleted = $controller->delete($resumeId, (int) current_user_id());
flash($deleted ? 'success' : 'error', $deleted ? 'Резюме удалено.' : 'Не удалось удалить резюме.');
redirect('/dashboard.php');
