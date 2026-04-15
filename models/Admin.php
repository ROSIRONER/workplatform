<?php

declare(strict_types=1);

class Admin
{
    public function __construct(private PDO $pdo)
    {
    }

    public function users(): array
    {
        $stmt = $this->pdo->prepare('SELECT id, email, role, created_at FROM users ORDER BY id DESC');
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function resumes(): array
    {
        $sql = 'SELECT r.id, r.title, u.email AS owner_email, r.created_at
                FROM resumes r
                JOIN users u ON u.id = r.user_id
                ORDER BY r.id DESC';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function vacancies(): array
    {
        $sql = 'SELECT v.id, v.title, v.salary, u.email AS employer_email, v.created_at
                FROM vacancies v
                JOIN users u ON u.id = v.employer_id
                ORDER BY v.id DESC';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function applications(): array
    {
        $sql = 'SELECT a.id, a.created_at, u.email AS applicant_email, v.title AS vacancy_title
                FROM applications a
                JOIN users u ON u.id = a.user_id
                JOIN vacancies v ON v.id = a.vacancy_id
                ORDER BY a.id DESC';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function deleteUser(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM users WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }

    public function deleteResume(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM resumes WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }

    public function deleteVacancy(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM vacancies WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }

    public function deleteApplication(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM applications WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }
}
