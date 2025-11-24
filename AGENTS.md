# Repository Guidelines

## Структура проекта и модули
`backend/` — Laravel 12 API: доменные сервисы и Actions находятся в `app/Domain`, HTTP-контроллеры в `app/Http`, маршруты разделены между `routes/web.php` и `routes/api.php`, тесты — в `tests/`. `frontend/` — Vue 3 + Vite. Общие компоненты, формы, композиционные хуки и Pinia-хранилища лежат в `src/common`. Каждое доменное направление (`src/domains/users`) содержит `models/` с дефолтными данными и схемами Vuelidate (например, `models/User.js`) и `services/` для API-клиентов (`services/UserService.js`). Роутинг и инфраструктура — в `src/core`, публичные ассеты — в `public/`, тема и Tailwind-токены — в `src/assets/styles.css`.

## CRUD-референс
Ориентир для новых сущностей — домен пользователей. На фронте смотри `src/domains/users/UserForm.vue`, `Create.vue`, `Update.vue`, модель `models/User.js` (состояние + валидаторы), сервис `services/UserService.js` и удаление через `src/common/composables/useDeleteConfirmation.js`. На бэкенде — `app/Domain/Users`, `app/Http/Controllers/Api/UserController.php` и соответствующие руты в `routes/api.php`. Повторяйте эти паттерны при добавлении CRUD-логики.

## Команды разработки, сборки и тестов
### Backend (Docker)
Работаем из `backend/`. Запускаем инфраструктуру `docker compose up -d app queue redis smtp4dev`. Первичную установку выполняем внутри контейнера: `docker compose exec app composer install`, затем `docker compose exec app npm install` (если нужны фронтовые ассеты Laravel). Тесты: `docker compose exec app php artisan test`. Форматирование: `docker compose exec app ./vendor/bin/pint`. Для очередей и логов используем `docker compose logs -f queue` и `docker compose logs -f app`.

### Frontend
В `frontend/`: `npm install` для зависимостей, `npm run dev` для локальной разработки, `npm run build` — прод-артефакты, `npm run test:unit` — Vitest.

## Стиль кода и именование
PHP следует PSR-12; Actions/Services именуйте по ответственности (`UserDestroyAction`). Vue SFC пишем в `<script setup>` с 4 пробелами, файлы — PascalCase (`UserForm.vue`), пропсы в шаблонах — `kebab-case`. Базовые абстракции начинаются с `Base`, композиции — `useX`, Pinia-хранилища — `useXStore`. Доменные модели отвечают за подготовку состояния формы и валидаторов, чтобы представления импортировали `userModel.validationRules`. Импорты сортируем: сторонние → алиасы `@/` → относительные.

## Тестирование
Laravel-фичи размещаем в `tests/Feature`, модульные тесты — `tests/Unit`; покрываем контроллеры, политики, очереди и формирование ошибок. Во фронтенде Vitest-спеки кладём в `src/__tests__` или рядом с компонентами. Названия тестов формата `it('должен ...')`. Формы проверяют и локальную валидацию, и отрисовку ошибок из `BaseForm`. Перед PR запускаем `docker compose exec app php artisan test` и `npm run test:unit`.

## Коммиты и pull request'ы
История использует короткие императивы (`fix delete entity`); сохраняем стиль, добавляя контекст (`feat users: add Create view`). Перед PR делаем ребейз на `main`, в описании перечисляем ключевые изменения, затронутые роуты/компоненты, прикладываем скриншоты или curl-примеры и отмечаем правки `.env`. Обязательно фиксируем, какие тесты и команды были выполнены — это упрощает ревью.
