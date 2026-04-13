<?php

declare(strict_types=1);

class Resume
{
    public function __construct(private PDO $pdo)
    {
    }

    public function allByUser(int $userId): array
    {
        $stmt = $this->pdo->prepare('SELECT id, user_id, title, content, created_at FROM resumes WHERE user_id = :user_id ORDER BY created_at DESC');
        $stmt->execute(['user_id' => $userId]);

        return $stmt->fetchAll();
    }

    public function create(int $userId, string $title, string $content): bool
    {
        $stmt = $this->pdo->prepare('INSERT INTO resumes (user_id, title, content) VALUES (:user_id, :title, :content)');

        return $stmt->execute([
            'user_id' => $userId,
            'title' => $title,
            'content' => $content,
        ]);
    }

    public function findOwnedById(int $id, int $userId): ?array
    {
        $stmt = $this->pdo->prepare('SELECT id, user_id, title, content FROM resumes WHERE id = :id AND user_id = :user_id LIMIT 1');
        $stmt->execute(['id' => $id, 'user_id' => $userId]);
        $resume = $stmt->fetch();

        return $resume ?: null;
    }

    public function update(int $id, int $userId, string $title, string $content): bool
    {
        $stmt = $this->pdo->prepare('UPDATE resumes SET title = :title, content = :content WHERE id = :id AND user_id = :user_id');

        return $stmt->execute([
            'id' => $id,
            'user_id' => $userId,
            'title' => $title,
            'content' => $content,
        ]);
    }

    public function delete(int $id, int $userId): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM resumes WHERE id = :id AND user_id = :user_id');

        return $stmt->execute([
            'id' => $id,
            'user_id' => $userId,
        ]);
    }
}
