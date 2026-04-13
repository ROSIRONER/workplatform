-- Beget/shared hosting friendly dump:
-- 1) Сначала выберите нужную БД в phpMyAdmin.
-- 2) Затем импортируйте этот файл без CREATE DATABASE / USE.

DROP TABLE IF EXISTS applications;
DROP TABLE IF EXISTS resumes;
DROP TABLE IF EXISTS vacancies;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('job_seeker', 'employer') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE resumes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_resumes_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE vacancies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employer_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    salary VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_vacancies_employer FOREIGN KEY (employer_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    vacancy_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_application (user_id, vacancy_id),
    CONSTRAINT fk_applications_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_applications_vacancy FOREIGN KEY (vacancy_id) REFERENCES vacancies(id) ON DELETE CASCADE
);

-- Тестовые пользователи, пароль у обоих: password123
INSERT INTO users (email, password_hash, role) VALUES
('employer@example.com', '$2y$10$0f6T7kt4xFU9fS9F7s4IwOnEq0j2nFoRSEazYVJfKm/6QHCIr0/4W', 'employer'),
('seeker@example.com', '$2y$10$0f6T7kt4xFU9fS9F7s4IwOnEq0j2nFoRSEazYVJfKm/6QHCIr0/4W', 'job_seeker');

INSERT INTO vacancies (employer_id, title, description, salary) VALUES
(1, 'PHP Developer', 'Разработка веб-приложений на PHP + MySQL.', '150000 RUB');
