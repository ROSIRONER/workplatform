<?php

declare(strict_types=1);

require_once __DIR__ . '/config/bootstrap.php';
require_once __DIR__ . '/controllers/VacancyController.php';

require_role('employer');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/dashboard.php');
}

if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
    flash('error', 'CSRF-токен невалиден.');
    redirect('/dashboard.php');
}

$vacancyId = isset($_POST['id']) ? (int) $_POST['id'] : 0;
$controller = new VacancyController($pdo);
$deleted = $controller->delete($vacancyId, (int) current_user_id());
flash($deleted ? 'success' : 'error', $deleted ? 'Вакансия удалена.' : 'Не удалось удалить вакансию.');
redirect('/dashboard.php');
