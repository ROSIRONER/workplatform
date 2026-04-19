<?php

declare(strict_types=1);

class User
{
    public function __construct(private PDO $pdo)
    {
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->pdo->prepare('SELECT id, email, password_hash, role FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        return $user ?: null;
    }

    public function create(string $email, string $passwordHash, string $role): bool
    {
        $stmt = $this->pdo->prepare('INSERT INTO users (email, password_hash, role) VALUES (:email, :password_hash, :role)');

        return $stmt->execute([
            'email' => $email,
            'password_hash' => $passwordHash,
            'role' => $role,
        ]);
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT id, email, role FROM users WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch();

        return $user ?: null;
    }
}
