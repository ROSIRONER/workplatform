# JobPlatform (PHP + MySQL, без фреймворков)

Простое веб-приложение платформы вакансий (аналог hh.ru) с двумя ролями:
- `job_seeker` (соискатель)
- `employer` (работодатель)

## Возможности
- Регистрация, вход, выход.
- RBAC через сессию: хранение `user_id` и `role`.
- Соискатель:
  - CRUD резюме (`title`, `content`, `file_path`)
  - Загрузка файла резюме (PDF/DOC/DOCX до 5MB)
  - Просмотр списка своих откликов
- Работодатель:
  - CRUD вакансий (`title`, `description`, `salary`)
  - Просмотр откликов на свои вакансии (JOIN users + vacancies + applications)
- Публичная витрина вакансий (`index.php`)
- Отклик на вакансию (`apply.php?id=...`) с выбором резюме

## Безопасность
- Все SQL-запросы выполняются через `PDO::prepare()`.
- Вывод пользовательских данных экранируется через `htmlspecialchars()`.
- Во всех POST-формах используется CSRF-токен + проверка через `hash_equals()`.

## Структура
- `config/` — подключение к БД, bootstrap, helper-функции
- `controllers/` — контроллеры логики
- `models/` — слой работы с БД
- `views/` — шаблоны интерфейса
- `public/` — стили
- `uploads/` — папка для пользовательских файлов (зарезервировано)
- `database.sql` — дамп схемы и начальных данных

## Установка (Beget / shared hosting)
1. В панели Beget создайте БД (если ещё не создана), например `q95376oc_user_db`.
2. Откройте `phpMyAdmin`, **выберите слева именно эту БД**, затем нажмите Import и загрузите `database.sql`.
   - В дампе специально нет `CREATE DATABASE` и `USE`, чтобы не ловить ошибку `#1045 Access denied` на shared-хостинге.
3. Настройте доступ к БД в `config/db.php`:
   - `host=localhost`
   - `dbname=ИМЯ_ВАШЕЙ_БД_ИЗ_BEGET` (например `q95376oc_user_db`)
   - `username=логин_БД_из_Beget` (например `q95376oc_user_db`)
   - `password=пароль_БД`
4. В панели Beget выберите версию PHP (рекомендуется 8.1+).
5. Откройте сайт в браузере.


## Если импорт прошёл "без ошибок", но таблиц нет
1. Проверьте, что в phpMyAdmin слева была выбрана **ваша БД**, а не пункт "Сервер".
2. Сразу после импорта выполните SQL:
   ```sql
   SHOW TABLES;
   ```
3. Если список пустой — повторите импорт `database.sql`, предварительно снова выбрав БД слева.
4. Убедитесь, что импортируете именно актуальный файл `database.sql` из проекта (в нём есть `CREATE TABLE IF NOT EXISTS ...`).
5. На Beget иногда создаётся несколько БД/пользователей: сверяйте имя БД в `config/db.php` и имя БД, выбранной в phpMyAdmin.


## Миграция для уже существующей БД
Если БД уже развернута, выполните в phpMyAdmin:
```sql
ALTER TABLE users MODIFY role ENUM('job_seeker','employer','admin') NOT NULL;
ALTER TABLE resumes ADD COLUMN file_path VARCHAR(255) NULL AFTER content;
ALTER TABLE applications ADD COLUMN resume_id INT NULL AFTER vacancy_id;
ALTER TABLE applications
  ADD CONSTRAINT fk_applications_resume FOREIGN KEY (resume_id) REFERENCES resumes(id) ON DELETE SET NULL;
```

## Запуск локально
Если используете встроенный сервер PHP:
```bash
php -S localhost:8000
```
Затем откройте `http://localhost:8000/index.php`.

## Тестовые пользователи
(после импорта `database.sql`, пароль у обоих `password123`)
- Работодатель: `employer@example.com`
- Соискатель: `seeker@example.com`


## Если `password123` не подходит
Скорее всего, в БД уже лежат старые пользователи/хэши из предыдущего импорта.

Вариант 1 (рекомендуется):
1. Повторно импортируйте `database.sql` (он пересоздаст таблицы и тестовых пользователей).
2. Войдите:
   - `employer@example.com` / `password123`
   - `seeker@example.com` / `password123`

Вариант 2 (без полного реимпорта):
в phpMyAdmin выполните:
```sql
UPDATE users
SET password_hash = '$2y$12$H/WhH7UWIhItd.ZJNBdy5u2zs71H0BHqTVhmXPAod83leP17Px2Ey'
WHERE email IN ('employer@example.com', 'seeker@example.com');
```


## Админка (управление удалением)
- В проекте есть роль `admin` и страница `admin.php`.
- Админ может удалять пользователей, резюме, вакансии и отклики.
- Удаление защищено CSRF-токеном и выполняется через `POST`.
- Самого себя админ удалить не может.

Тестовый админ после импорта `database.sql`:
- `admin@example.com` / `password123`

## Как пользоваться
1. Зарегистрируйтесь или войдите.
2. Если вы соискатель — создайте резюме и откликайтесь на вакансии.
3. Если вы работодатель — создайте вакансию и отслеживайте отклики в кабинете.
4. После CRUD-операций выполняются редиректы на `dashboard.php` или `index.php`.
