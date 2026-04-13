<?php

declare(strict_types=1);

require_once __DIR__ . '/../models/User.php';

class AuthController
{
    public function __construct(private PDO $pdo)
    {
    }

    public function register(array $data): array
    {
        $email = trim($data['email'] ?? '');
        $password = $data['password'] ?? '';
        $role = $data['role'] ?? '';

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Введите корректный email.'];
        }

        if (strlen($password) < 6) {
            return ['success' => false, 'message' => 'Пароль должен быть не менее 6 символов.'];
        }

        if (!in_array($role, ['job_seeker', 'employer'], true)) {
            return ['success' => false, 'message' => 'Неверная роль.'];
        }

        $userModel = new User($this->pdo);
        if ($userModel->findByEmail($email)) {
            return ['success' => false, 'message' => 'Пользователь уже существует.'];
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $saved = $userModel->create($email, $passwordHash, $role);

        if (!$saved) {
            return ['success' => false, 'message' => 'Не удалось зарегистрировать пользователя.'];
        }

        return ['success' => true, 'message' => 'Регистрация успешна. Теперь войдите.'];
    }

    public function login(array $data): array
    {
        $email = trim($data['email'] ?? '');
        $password = $data['password'] ?? '';

        $userModel = new User($this->pdo);
        $user = $userModel->findByEmail($email);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            return ['success' => false, 'message' => 'Неверный email или пароль.'];
        }

        $_SESSION['user_id'] = (int) $user['id'];
        $_SESSION['role'] = $user['role'];

        return ['success' => true, 'message' => 'Вход выполнен.'];
    }

    public function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }

        session_destroy();
    }
}
