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

    public function save(array $data, array $file, int $userId, ?int $resumeId = null): array
    {
        $title = trim($data['title'] ?? '');
        $content = trim($data['content'] ?? '');

        if ($title === '' || $content === '') {
            return ['success' => false, 'message' => 'Заполните все поля.'];
        }

        $resumeModel = new Resume($this->pdo);
        $existing = null;

        if ($resumeId !== null) {
            $existing = $resumeModel->findOwnedById($resumeId, $userId);
            if (!$existing) {
                return ['success' => false, 'message' => 'Резюме не найдено.'];
            }
        }

        $uploadResult = $this->handleResumeFileUpload($file, $existing['file_path'] ?? null);
        if (!$uploadResult['success']) {
            return $uploadResult;
        }

        $filePath = $uploadResult['file_path'];

        if ($resumeId === null) {
            $ok = $resumeModel->create($userId, $title, $content, $filePath);
            return ['success' => $ok, 'message' => $ok ? 'Резюме создано.' : 'Не удалось создать резюме.'];
        }

        $ok = $resumeModel->update($resumeId, $userId, $title, $content, $filePath);
        return ['success' => $ok, 'message' => $ok ? 'Резюме обновлено.' : 'Не удалось обновить резюме.'];
    }

    public function delete(int $resumeId, int $userId): bool
    {
        $resumeModel = new Resume($this->pdo);
        $resume = $resumeModel->findOwnedById($resumeId, $userId);

        if ($resume && !empty($resume['file_path'])) {
            $fullPath = dirname(__DIR__) . $resume['file_path'];
            if (is_file($fullPath)) {
                unlink($fullPath);
            }
        }

        return $resumeModel->delete($resumeId, $userId);
    }

    private function handleResumeFileUpload(array $file, ?string $currentPath): array
    {
        if (!isset($file['error']) || (int) $file['error'] === UPLOAD_ERR_NO_FILE) {
            return ['success' => true, 'file_path' => $currentPath];
        }

        if ((int) $file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'Ошибка загрузки файла резюме.'];
        }

        if (($file['size'] ?? 0) > 5 * 1024 * 1024) {
            return ['success' => false, 'message' => 'Файл резюме должен быть не больше 5MB.'];
        }

        $allowedExtensions = ['pdf', 'doc', 'docx'];
        $extension = strtolower(pathinfo((string) $file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $allowedExtensions, true)) {
            return ['success' => false, 'message' => 'Разрешены только PDF/DOC/DOCX.'];
        }

        $uploadDir = dirname(__DIR__) . '/uploads/resumes';
        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0775, true) && !is_dir($uploadDir)) {
            return ['success' => false, 'message' => 'Не удалось создать папку uploads.'];
        }

        $newName = bin2hex(random_bytes(16)) . '.' . $extension;
        $target = $uploadDir . '/' . $newName;

        if (!move_uploaded_file((string) $file['tmp_name'], $target)) {
            return ['success' => false, 'message' => 'Не удалось сохранить файл резюме.'];
        }

        if ($currentPath) {
            $old = dirname(__DIR__) . $currentPath;
            if (is_file($old)) {
                unlink($old);
            }
        }

        return ['success' => true, 'file_path' => '/uploads/resumes/' . $newName];
    }
}
