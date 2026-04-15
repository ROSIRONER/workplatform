<?php

declare(strict_types=1);

require_once __DIR__ . '/../models/Admin.php';

class AdminController
{
    public function __construct(private PDO $pdo)
    {
    }

    public function dashboardData(): array
    {
        $model = new Admin($this->pdo);

        return [
            'users' => $model->users(),
            'resumes' => $model->resumes(),
            'vacancies' => $model->vacancies(),
            'applications' => $model->applications(),
        ];
    }

    public function delete(string $entity, int $id, int $currentAdminId): array
    {
        $model = new Admin($this->pdo);

        if ($id <= 0) {
            return ['success' => false, 'message' => 'Некорректный ID.'];
        }

        $ok = false;
        $message = 'Не удалось удалить запись.';

        if ($entity === 'user') {
            if ($id === $currentAdminId) {
                return ['success' => false, 'message' => 'Нельзя удалить самого себя.'];
            }
            $ok = $model->deleteUser($id);
            $message = $ok ? 'Пользователь удалён.' : $message;
        } elseif ($entity === 'resume') {
            $ok = $model->deleteResume($id);
            $message = $ok ? 'Резюме удалено.' : $message;
        } elseif ($entity === 'vacancy') {
            $ok = $model->deleteVacancy($id);
            $message = $ok ? 'Вакансия удалена.' : $message;
        } elseif ($entity === 'application') {
            $ok = $model->deleteApplication($id);
            $message = $ok ? 'Отклик удалён.' : $message;
        }

        return ['success' => $ok, 'message' => $message];
    }
}
