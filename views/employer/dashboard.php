<section class="mb-4">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h2>Мои вакансии</h2>
        <a class="btn btn-success" href="/vacancy_form.php">+ Создать вакансию</a>
    </div>
    <table class="table table-striped">
        <thead>
        <tr><th>ID</th><th>Название</th><th>Зарплата</th><th>Действия</th></tr>
        </thead>
        <tbody>
        <?php foreach ($vacancies as $vacancy): ?>
            <tr>
                <td><?= (int) $vacancy['id'] ?></td>
                <td><?= e($vacancy['title']) ?></td>
                <td><?= e($vacancy['salary']) ?></td>
                <td class="d-flex gap-2">
                    <a class="btn btn-sm btn-outline-primary" href="/vacancy_form.php?id=<?= (int) $vacancy['id'] ?>">Редактировать</a>
                    <form method="post" action="/vacancy_delete.php" onsubmit="return confirm('Удалить вакансию?');">
                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                        <input type="hidden" name="id" value="<?= (int) $vacancy['id'] ?>">
                        <button class="btn btn-sm btn-outline-danger" type="submit">Удалить</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$vacancies): ?>
            <tr><td colspan="4">Вакансий пока нет.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</section>

<section>
    <h2>Отклики на мои вакансии</h2>
    <table class="table table-bordered">
        <thead>
        <tr><th>ID</th><th>Вакансия</th><th>Соискатель</th><th>Резюме</th><th>Дата</th></tr>
        </thead>
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
            </tr>
        <?php endforeach; ?>
        <?php if (!$applications): ?>
            <tr><td colspan="5">Откликов пока нет.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</section>
