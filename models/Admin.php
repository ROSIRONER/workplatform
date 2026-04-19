<?php

declare(strict_types=1);

class Admin
{
    public function __construct(private PDO $pdo)
    {
    }

    public function users(string $email = '', string $role = ''): array
    {
        $sql = 'SELECT id, email, role, created_at FROM users WHERE 1=1';
        $params = [];

        if ($email !== '') {
            $sql .= ' AND email LIKE :email';
            $params['email'] = '%' . $email . '%';
        }

        if ($role !== '') {
            $sql .= ' AND role = :role';
            $params['role'] = $role;
        }

        $sql .= ' ORDER BY id DESC';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function resumes(string $title = '', string $owner = ''): array
    {
        $sql = 'SELECT r.id, r.title, u.email AS owner_email, r.file_path, r.created_at
                FROM resumes r
                JOIN users u ON u.id = r.user_id
                WHERE 1=1';
        $params = [];

        if ($title !== '') {
            $sql .= ' AND r.title LIKE :title';
            $params['title'] = '%' . $title . '%';
        }

        if ($owner !== '') {
            $sql .= ' AND u.email LIKE :owner';
            $params['owner'] = '%' . $owner . '%';
        }

        $sql .= ' ORDER BY r.id DESC';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function vacancies(string $title = '', string $employer = ''): array
    {
        $sql = 'SELECT v.id, v.title, v.salary, u.email AS employer_email, v.created_at
                FROM vacancies v
                JOIN users u ON u.id = v.employer_id
                WHERE 1=1';
        $params = [];

        if ($title !== '') {
            $sql .= ' AND v.title LIKE :title';
            $params['title'] = '%' . $title . '%';
        }

        if ($employer !== '') {
            $sql .= ' AND u.email LIKE :employer';
            $params['employer'] = '%' . $employer . '%';
        }

        $sql .= ' ORDER BY v.id DESC';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function applications(string $vacancy = '', string $applicant = ''): array
    {
        $sql = 'SELECT a.id, a.created_at, u.email AS applicant_email, v.title AS vacancy_title,
                       r.title AS resume_title, r.file_path AS resume_file
                FROM applications a
                JOIN users u ON u.id = a.user_id
                JOIN vacancies v ON v.id = a.vacancy_id
                LEFT JOIN resumes r ON r.id = a.resume_id
                WHERE 1=1';
        $params = [];

        if ($vacancy !== '') {
            $sql .= ' AND v.title LIKE :vacancy';
            $params['vacancy'] = '%' . $vacancy . '%';
        }

        if ($applicant !== '') {
            $sql .= ' AND u.email LIKE :applicant';
            $params['applicant'] = '%' . $applicant . '%';
        }

        $sql .= ' ORDER BY a.id DESC';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

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
