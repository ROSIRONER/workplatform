<?php

declare(strict_types=1);

require_once __DIR__ . '/config/bootstrap.php';
require_once __DIR__ . '/controllers/ResumeController.php';
require_once __DIR__ . '/controllers/VacancyController.php';

require_auth();
$userId = current_user_id();
$role = current_user_role();
$title = 'Личный кабинет';

require __DIR__ . '/views/layout/header.php';

if ($role === 'job_seeker') {
    $controller = new ResumeController($pdo);
    $data = $controller->dashboard($userId);
    $resumes = $data['resumes'];
    $applications = $data['applications'];
    require __DIR__ . '/views/job_seeker/dashboard.php';
} elseif ($role === 'employer') {
    $controller = new VacancyController($pdo);
    $data = $controller->dashboard($userId);
    $vacancies = $data['vacancies'];
    $applications = $data['applications'];
    require __DIR__ . '/views/employer/dashboard.php';
} elseif ($role === 'admin') {
    redirect('/admin.php');
} else {
    echo '<p>Роль не определена.</p>';
}

require __DIR__ . '/views/layout/footer.php';
