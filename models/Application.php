<?php

declare(strict_types=1);

class Application
{
    public function __construct(private PDO $pdo)
    {
    }

    public function apply(int $userId, int $vacancyId): bool
    {
        $stmt = $this->pdo->prepare('INSERT INTO applications (user_id, vacancy_id) VALUES (:user_id, :vacancy_id)');

        return $stmt->execute([
            'user_id' => $userId,
            'vacancy_id' => $vacancyId,
        ]);
    }

    public function exists(int $userId, int $vacancyId): bool
    {
        $stmt = $this->pdo->prepare('SELECT id FROM applications WHERE user_id = :user_id AND vacancy_id = :vacancy_id LIMIT 1');
        $stmt->execute([
            'user_id' => $userId,
            'vacancy_id' => $vacancyId,
        ]);

        return (bool) $stmt->fetch();
    }

    public function byJobSeeker(int $userId): array
    {
        $stmt = $this->pdo->prepare('SELECT a.id, v.title, v.salary, a.created_at FROM applications a JOIN vacancies v ON v.id = a.vacancy_id WHERE a.user_id = :user_id ORDER BY a.created_at DESC');
        $stmt->execute(['user_id' => $userId]);

        return $stmt->fetchAll();
    }

    public function byEmployer(int $employerId): array
    {
        $sql = 'SELECT a.id, a.created_at, u.email AS applicant_email, v.title AS vacancy_title
                FROM applications a
                JOIN users u ON u.id = a.user_id
                JOIN vacancies v ON v.id = a.vacancy_id
                WHERE v.employer_id = :employer_id
                ORDER BY a.created_at DESC';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['employer_id' => $employerId]);

        return $stmt->fetchAll();
    }
}
