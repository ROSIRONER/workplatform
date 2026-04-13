<?php

declare(strict_types=1);

class Vacancy
{
    public function __construct(private PDO $pdo)
    {
    }

    public function allPublic(): array
    {
        $stmt = $this->pdo->prepare('SELECT v.id, v.title, v.description, v.salary, u.email AS employer_email FROM vacancies v JOIN users u ON u.id = v.employer_id ORDER BY v.created_at DESC');
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function allByEmployer(int $employerId): array
    {
        $stmt = $this->pdo->prepare('SELECT id, employer_id, title, description, salary, created_at FROM vacancies WHERE employer_id = :employer_id ORDER BY created_at DESC');
        $stmt->execute(['employer_id' => $employerId]);

        return $stmt->fetchAll();
    }

    public function create(int $employerId, string $title, string $description, string $salary): bool
    {
        $stmt = $this->pdo->prepare('INSERT INTO vacancies (employer_id, title, description, salary) VALUES (:employer_id, :title, :description, :salary)');

        return $stmt->execute([
            'employer_id' => $employerId,
            'title' => $title,
            'description' => $description,
            'salary' => $salary,
        ]);
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT id, employer_id, title, description, salary FROM vacancies WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $vacancy = $stmt->fetch();

        return $vacancy ?: null;
    }

    public function findOwnedById(int $id, int $employerId): ?array
    {
        $stmt = $this->pdo->prepare('SELECT id, employer_id, title, description, salary FROM vacancies WHERE id = :id AND employer_id = :employer_id LIMIT 1');
        $stmt->execute(['id' => $id, 'employer_id' => $employerId]);
        $vacancy = $stmt->fetch();

        return $vacancy ?: null;
    }

    public function update(int $id, int $employerId, string $title, string $description, string $salary): bool
    {
        $stmt = $this->pdo->prepare('UPDATE vacancies SET title = :title, description = :description, salary = :salary WHERE id = :id AND employer_id = :employer_id');

        return $stmt->execute([
            'id' => $id,
            'employer_id' => $employerId,
            'title' => $title,
            'description' => $description,
            'salary' => $salary,
        ]);
    }

    public function delete(int $id, int $employerId): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM vacancies WHERE id = :id AND employer_id = :employer_id');

        return $stmt->execute([
            'id' => $id,
            'employer_id' => $employerId,
        ]);
    }
}
