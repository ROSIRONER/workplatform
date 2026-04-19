<section class="mb-4">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h2>Мои резюме</h2>
        <a class="btn btn-success" href="/resume_form.php">+ Создать резюме</a>
    </div>
    <table class="table table-striped">
        <thead>
        <tr><th>ID</th><th>Заголовок</th><th>Файл</th><th>Действия</th></tr>
        </thead>
        <tbody>
        <?php foreach ($resumes as $resume): ?>
            <tr>
                <td><?= (int) $resume['id'] ?></td>
                <td><?= e($resume['title']) ?></td>
                <td>
                    <?php if (!empty($resume['file_path'])): ?>
                        <a href="<?= e($resume['file_path']) ?>" target="_blank" rel="noopener">Скачать</a>
                    <?php else: ?>
                        —
                    <?php endif; ?>
                </td>
                <td class="d-flex gap-2">
                    <a class="btn btn-sm btn-outline-primary" href="/resume_form.php?id=<?= (int) $resume['id'] ?>">Редактировать</a>
                    <form method="post" action="/resume_delete.php" onsubmit="return confirm('Удалить резюме?');">
                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                        <input type="hidden" name="id" value="<?= (int) $resume['id'] ?>">
                        <button class="btn btn-sm btn-outline-danger" type="submit">Удалить</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$resumes): ?>
            <tr><td colspan="4">Резюме пока нет.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</section>

<section>
    <h2>Мои отклики</h2>
    <table class="table table-bordered">
        <thead>
        <tr><th>ID</th><th>Вакансия</th><th>Резюме</th><th>Зарплата</th><th>Дата</th></tr>
        </thead>
        <tbody>
        <?php foreach ($applications as $application): ?>
            <tr>
                <td><?= (int) $application['id'] ?></td>
                <td><?= e($application['title']) ?></td>
                <td>
                    <?= e($application['resume_title'] ?? '—') ?>
                    <?php if (!empty($application['resume_file'])): ?>
                        (<a href="<?= e($application['resume_file']) ?>" target="_blank" rel="noopener">файл</a>)
                    <?php endif; ?>
                </td>
                <td><?= e($application['salary']) ?></td>
                <td><?= e($application['created_at']) ?></td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$applications): ?>
            <tr><td colspan="5">Откликов пока нет.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</section>
