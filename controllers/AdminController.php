<?php

declare(strict_types=1);

require_once __DIR__ . '/../models/Admin.php';

class AdminController
{
    public function __construct(private PDO $pdo)
    {
    }

    public function dashboardData(array $filters = []): array
    {
        $model = new Admin($this->pdo);

        return [
            'users' => $model->users(trim((string) ($filters['user_email'] ?? '')), trim((string) ($filters['user_role'] ?? ''))),
            'resumes' => $model->resumes(trim((string) ($filters['resume_title'] ?? '')), trim((string) ($filters['resume_owner'] ?? ''))),
            'vacancies' => $model->vacancies(trim((string) ($filters['vacancy_title'] ?? '')), trim((string) ($filters['vacancy_employer'] ?? ''))),
            'applications' => $model->applications(trim((string) ($filters['application_vacancy'] ?? '')), trim((string) ($filters['application_applicant'] ?? ''))),
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
