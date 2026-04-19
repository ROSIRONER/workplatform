<?php

declare(strict_types=1);

require_once __DIR__ . '/../models/Vacancy.php';
require_once __DIR__ . '/../models/Application.php';

class VacancyController
{
    public function __construct(private PDO $pdo)
    {
    }

    public function publicList(): array
    {
        $vacancyModel = new Vacancy($this->pdo);
        return $vacancyModel->allPublic();
    }

    public function dashboard(int $employerId): array
    {
        $vacancyModel = new Vacancy($this->pdo);
        $applicationModel = new Application($this->pdo);

        return [
            'vacancies' => $vacancyModel->allByEmployer($employerId),
            'applications' => $applicationModel->byEmployer($employerId),
        ];
    }

    public function save(array $data, int $employerId, ?int $vacancyId = null): array
    {
        $title = trim($data['title'] ?? '');
        $description = trim($data['description'] ?? '');
        $salary = trim($data['salary'] ?? '');

        if ($title === '' || $description === '' || $salary === '') {
            return ['success' => false, 'message' => 'Заполните все поля.'];
        }

        $vacancyModel = new Vacancy($this->pdo);

        if ($vacancyId === null) {
            $ok = $vacancyModel->create($employerId, $title, $description, $salary);
            return ['success' => $ok, 'message' => $ok ? 'Вакансия создана.' : 'Не удалось создать вакансию.'];
        }

        $ok = $vacancyModel->update($vacancyId, $employerId, $title, $description, $salary);
        return ['success' => $ok, 'message' => $ok ? 'Вакансия обновлена.' : 'Не удалось обновить вакансию.'];
    }

    public function delete(int $vacancyId, int $employerId): bool
    {
        $vacancyModel = new Vacancy($this->pdo);
        return $vacancyModel->delete($vacancyId, $employerId);
    }
}
