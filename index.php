<?php

declare(strict_types=1);

require_once __DIR__ . '/config/bootstrap.php';
require_once __DIR__ . '/controllers/VacancyController.php';

$controller = new VacancyController($pdo);
$vacancies = $controller->publicList();
$title = 'Публичная витрина вакансий';

require __DIR__ . '/views/layout/header.php';
?>
<h1 class="mb-3">Открытые вакансии</h1>
<div class="row g-3">
    <?php foreach ($vacancies as $vacancy): ?>
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title"><?= e($vacancy['title']) ?></h5>
                    <h6 class="card-subtitle mb-2 text-muted">Работодатель: <?= e($vacancy['employer_email']) ?></h6>
                    <p class="card-text"><?= nl2br(e($vacancy['description'])) ?></p>
                    <p><strong>Зарплата:</strong> <?= e($vacancy['salary']) ?></p>
                    <?php if (current_user_role() === 'job_seeker'): ?>
                        <a class="btn btn-primary btn-sm" href="/apply.php?id=<?= (int) $vacancy['id'] ?>">Откликнуться</a>
                    <?php elseif (current_user_id() === null): ?>
                        <a class="btn btn-outline-primary btn-sm" href="/login.php">Войдите, чтобы откликнуться</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    <?php if (!$vacancies): ?>
        <p>Пока вакансий нет.</p>
    <?php endif; ?>
</div>
<?php require __DIR__ . '/views/layout/footer.php'; ?>
