<?php

declare(strict_types=1);

require_once __DIR__ . '/config/bootstrap.php';
require_once __DIR__ . '/controllers/AdminController.php';

require_role('admin');

$controller = new AdminController($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        flash('error', 'CSRF-токен невалиден.');
        redirect('/admin.php');
    }

    $entity = $_POST['entity'] ?? '';
    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    $result = $controller->delete($entity, $id, (int) current_user_id());
    flash($result['success'] ? 'success' : 'error', $result['message']);
    redirect('/admin.php');
}

$data = $controller->dashboardData();
$users = $data['users'];
$resumes = $data['resumes'];
$vacancies = $data['vacancies'];
$applications = $data['applications'];

$title = 'Админка';
require __DIR__ . '/views/layout/header.php';
?>
<h1 class="mb-3">Админ-панель</h1>

<section class="mb-4">
    <h2>Пользователи</h2>
    <table class="table table-sm table-striped">
        <thead><tr><th>ID</th><th>Email</th><th>Роль</th><th>Дата</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?= (int) $user['id'] ?></td>
                <td><?= e($user['email']) ?></td>
                <td><?= e($user['role']) ?></td>
                <td><?= e($user['created_at']) ?></td>
                <td>
                    <?php if ((int) $user['id'] !== (int) current_user_id()): ?>
                    <form method="post" onsubmit="return confirm('Удалить пользователя?');">
                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                        <input type="hidden" name="entity" value="user">
                        <input type="hidden" name="id" value="<?= (int) $user['id'] ?>">
                        <button class="btn btn-sm btn-danger">Удалить</button>
                    </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>

<section class="mb-4">
    <h2>Резюме</h2>
    <table class="table table-sm table-striped">
        <thead><tr><th>ID</th><th>Заголовок</th><th>Владелец</th><th>Дата</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($resumes as $resume): ?>
            <tr>
                <td><?= (int) $resume['id'] ?></td>
                <td><?= e($resume['title']) ?></td>
                <td><?= e($resume['owner_email']) ?></td>
                <td><?= e($resume['created_at']) ?></td>
                <td>
                    <form method="post" onsubmit="return confirm('Удалить резюме?');">
                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                        <input type="hidden" name="entity" value="resume">
                        <input type="hidden" name="id" value="<?= (int) $resume['id'] ?>">
                        <button class="btn btn-sm btn-danger">Удалить</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>

<section class="mb-4">
    <h2>Вакансии</h2>
    <table class="table table-sm table-striped">
        <thead><tr><th>ID</th><th>Название</th><th>Работодатель</th><th>Зарплата</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($vacancies as $vacancy): ?>
            <tr>
                <td><?= (int) $vacancy['id'] ?></td>
                <td><?= e($vacancy['title']) ?></td>
                <td><?= e($vacancy['employer_email']) ?></td>
                <td><?= e($vacancy['salary']) ?></td>
                <td>
                    <form method="post" onsubmit="return confirm('Удалить вакансию?');">
                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                        <input type="hidden" name="entity" value="vacancy">
                        <input type="hidden" name="id" value="<?= (int) $vacancy['id'] ?>">
                        <button class="btn btn-sm btn-danger">Удалить</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>

<section class="mb-4">
    <h2>Отклики</h2>
    <table class="table table-sm table-striped">
        <thead><tr><th>ID</th><th>Вакансия</th><th>Соискатель</th><th>Резюме</th><th>Дата</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($applications as $application): ?>
            <tr>
                <td><?= (int) $application['id'] ?></td>
                <td><?= e($application['vacancy_title']) ?></td>
                <td><?= e($application['applicant_email']) ?></td>
                <td>
                    <?= e($application['resume_title'] ?? '—') ?>
                    <?php if (!empty($application['resume_file'])): ?>
                        (<a href="<?= e($application['resume_file']) ?>" target="_blank" rel="noopener">файл</a>)
                    <?php endif; ?>
                </td>
                <td><?= e($application['created_at']) ?></td>
                <td>
                    <form method="post" onsubmit="return confirm('Удалить отклик?');">
                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                        <input type="hidden" name="entity" value="application">
                        <input type="hidden" name="id" value="<?= (int) $application['id'] ?>">
                        <button class="btn btn-sm btn-danger">Удалить</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>

<?php require __DIR__ . '/views/layout/footer.php'; ?>
