-- JobPlatform schema dump (Beget/shared hosting friendly)
-- ВАЖНО: перед импортом обязательно выберите нужную БД слева в phpMyAdmin.
-- После импорта проверьте, что таблицы users/resumes/vacancies/applications появились.

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS applications;
DROP TABLE IF EXISTS resumes;
DROP TABLE IF EXISTS vacancies;
DROP TABLE IF EXISTS users;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('job_seeker', 'employer', 'admin') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS resumes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    file_path VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_resumes_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS vacancies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employer_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    salary VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_vacancies_employer FOREIGN KEY (employer_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    vacancy_id INT NOT NULL,
    resume_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_application (user_id, vacancy_id),
    CONSTRAINT fk_applications_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_applications_vacancy FOREIGN KEY (vacancy_id) REFERENCES vacancies(id) ON DELETE CASCADE,
    CONSTRAINT fk_applications_resume FOREIGN KEY (resume_id) REFERENCES resumes(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- Тестовые пользователи, пароль у всех: password123
INSERT INTO users (email, password_hash, role) VALUES
('employer@example.com', '$2y$12$H/WhH7UWIhItd.ZJNBdy5u2zs71H0BHqTVhmXPAod83leP17Px2Ey', 'employer'),
('seeker@example.com', '$2y$12$H/WhH7UWIhItd.ZJNBdy5u2zs71H0BHqTVhmXPAod83leP17Px2Ey', 'job_seeker'),
('admin@example.com', '$2y$12$H/WhH7UWIhItd.ZJNBdy5u2zs71H0BHqTVhmXPAod83leP17Px2Ey', 'admin');

INSERT INTO resumes (user_id, title, content, file_path) VALUES
(2, 'PHP Backend Developer', 'Опыт 3+ года, стек: PHP, MySQL, REST.', NULL);

INSERT INTO vacancies (employer_id, title, description, salary) VALUES
(1, 'PHP Developer', 'Разработка веб-приложений на PHP + MySQL.', '150000 RUB');

INSERT INTO applications (user_id, vacancy_id, resume_id) VALUES
(2, 1, 1);

-- Проверка после импорта (можно выполнить вручную в SQL-вкладке):
-- SHOW TABLES;
