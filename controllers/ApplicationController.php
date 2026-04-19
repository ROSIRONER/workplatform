<?php

declare(strict_types=1);

require_once __DIR__ . '/../models/Application.php';
require_once __DIR__ . '/../models/Vacancy.php';
require_once __DIR__ . '/../models/Resume.php';

class ApplicationController
{
    public function __construct(private PDO $pdo)
    {
    }

    public function apply(int $userId, int $vacancyId, int $resumeId): array
    {
        $vacancyModel = new Vacancy($this->pdo);
        $applicationModel = new Application($this->pdo);
        $resumeModel = new Resume($this->pdo);

        if (!$vacancyModel->findById($vacancyId)) {
            return ['success' => false, 'message' => 'Вакансия не найдена.'];
        }

        if (!$resumeModel->findOwnedById($resumeId, $userId)) {
            return ['success' => false, 'message' => 'Выберите корректное резюме для отклика.'];
        }

        if ($applicationModel->exists($userId, $vacancyId)) {
            return ['success' => false, 'message' => 'Вы уже откликались на эту вакансию.'];
        }

        $ok = $applicationModel->apply($userId, $vacancyId, $resumeId);

        return [
            'success' => $ok,
            'message' => $ok ? 'Отклик успешно отправлен.' : 'Не удалось отправить отклик.',
        ];
    }
}
