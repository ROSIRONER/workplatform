<?php

declare(strict_types=1);

require_once __DIR__ . '/../models/Resume.php';
require_once __DIR__ . '/../models/Application.php';

class ResumeController
{
    public function __construct(private PDO $pdo)
    {
    }

    public function dashboard(int $userId): array
    {
        $resumeModel = new Resume($this->pdo);
        $applicationModel = new Application($this->pdo);

        return [
            'resumes' => $resumeModel->allByUser($userId),
            'applications' => $applicationModel->byJobSeeker($userId),
        ];
    }

    public function save(array $data, int $userId, ?int $resumeId = null): array
    {
        $title = trim($data['title'] ?? '');
        $content = trim($data['content'] ?? '');

        if ($title === '' || $content === '') {
            return ['success' => false, 'message' => 'Заполните все поля.'];
        }

        $resumeModel = new Resume($this->pdo);

        if ($resumeId === null) {
            $ok = $resumeModel->create($userId, $title, $content);
            return ['success' => $ok, 'message' => $ok ? 'Резюме создано.' : 'Не удалось создать резюме.'];
        }

        $ok = $resumeModel->update($resumeId, $userId, $title, $content);
        return ['success' => $ok, 'message' => $ok ? 'Резюме обновлено.' : 'Не удалось обновить резюме.'];
    }

    public function delete(int $resumeId, int $userId): bool
    {
        $resumeModel = new Resume($this->pdo);
        return $resumeModel->delete($resumeId, $userId);
    }
}
