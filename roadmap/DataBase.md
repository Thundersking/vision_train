# СХЕМА БАЗЫ ДАННЫХ
## CRM система управления тренажером зрения

Ниже дано прикладное описание каждой таблицы: назначение, ключевые поля, связи и примечания по использованию. Цель — по одному взгляду понимать, зачем нужна таблица и как она участвует в процессах (регистрация организаций, ведение пациентов, упражнения, программы реабилитации, монетизация, аудит).

**Формат:** dbdiagram.io  
**Дата создания:** 25 сентября 2024  
**СУБД:** PostgreSQL  

---

## ОРГАНИЗАЦИОННЫЕ СУЩНОСТИ

// Организации/клиники
Table organizations {
  id increments
  name string
  type string // medical_center, fitness_gym, stadium
  email string
  phone string
  address text
  subscription_plan string // basic, standard, premium
  status string // active, inactive
  created_at timestamp
  updated_at timestamp
}

Описание: Организация-арендатор (клиника, фитнес, стадион). Все данные в системе мультиарендны и привязаны к `organization`.
- Ключ: `id`
- Основные поля: `type` (тип организации), `subscription_plan`.
- Связи: 1→N с `departments`, `users`, `patients`, `devices`, `exercise_templates`, `complex_programs`, `subscriptions`, `files`.
- Использование: разграничение данных и тарификация по плану, контроль лимитов при создании пользователей/пациентов.

// Подразделения
Table departments {
  id increments
  organization_id int
  name string
  description text
  parent_id int // Родительское подразделение
  is_active boolean
  created_at timestamp
  updated_at timestamp
}

Описание: Иерархия подразделений внутри организации.
- Ключ: `id`
- Внешние ключи: `organization_id` → organizations.id, `parent_id` самоссылка для дерева подразделений.
- Использование: распределение персонала и пациентов по подразделениям, фильтрация доступа и отчетности.

// Пользователи системы
Table users {
  id increments
  organization_id int
  department_id int
  name string
  email string
  password string
  role string // super_admin, organization_admin, organization_manager, doctor
  license_number string // Номер лицензии врача
  phone string
  timezone string
  is_active boolean
  email_verified_at timestamp
  remember_token string
  created_at timestamp
  updated_at timestamp
}

Описание: Пользователи организации (админы, менеджеры, врачи).
- Ключ: `id`
- Внешние ключи: `organization_id`, `department_id`.
- Роли: `role` задает права (super_admin — глобальный, organization_admin/manager/doctor — в рамках арендатора).
- Использование: авторизация, аудит, назначение пациентов, создание шаблонов/программ.

---

## ПАЦИЕНТЫ И УСТРОЙСТВА

// Пациенты
Table patients {
  id increments
  organization_id int
  user_id int // Закрепленный врач
  name string
  email string
  phone string
  hand_size_cm decimal // Размер руки в см (обязательно)
  birth_date date
  timezone string
  notes text
  is_active boolean
  created_at timestamp
  updated_at timestamp
}

Описание: Карточка пациента, прикрепленного к организации и врачу.
- Ключ: `id`
- Внешние ключи: `organization_id`, `user_id` (закрепленный врач/куратор).
- Доменные поля: `hand_size_cm` — обязательный параметр, влияет на калибровку упражнений; `timezone` — планирование уведомлений/программ.
- Использование: источник прав доступа к персональным данным, агрегация упражнений, программ, платежей и обследований.

// Устройства
Table devices {
  id increments
  organization_id int
  name string
  device_type string // office_device, personal_device
  device_identifier string // Уникальный ID устройства
  status string // active, inactive
  last_activity_at timestamp
  created_at timestamp
  updated_at timestamp
}

Описание: Аппараты/клиенты, через которые пациент выполняет упражнения (офисные или личные).
- Ключ: `id`
- Внешний ключ: `organization_id` (устройство принадлежит организации).
- Идентификация: `device_identifier` — уникальный ID устройства (серийник/UUID).
- Использование: мониторинг наличия/активности устройств, привязка к пациентам.

// Связь пациентов с устройствами (многие-ко-многим)
Table patient_devices {
  id increments
  patient_id int
  device_id int
  is_primary boolean // Основное устройство пациента
  assigned_at timestamp
  assigned_by int // ID пользователя который назначил
  notes text
}

Описание: Связующая N:M таблица пациент↔устройство с признаком основного устройства.
- Ключ: `id`
- Внешние ключи: `patient_id`, `device_id`, `assigned_by` (кто назначил).
- Использование: история назначений устройств, определение текущего основного клиента для выполнения упражнений.

// Журнал медицинских обследований
Table patient_examinations {
  id increments
  patient_id int
  examination_type string // primary, intermediate, final
  examination_text text
  examination_date timestamp
  examined_by int // ID врача проводившего обследование
  created_at timestamp
  updated_at timestamp
}

Описание: Медицинские обследования пациента (первичное/промежуточное/заключительное).
- Ключ: `id`
- Внешние ключи: `patient_id`, `examined_by` (врач-провайдер услуги).
- Использование: медицинская динамика, обоснование корректировок программ, юридический след.

---

## УПРАЖНЕНИЯ И ШАБЛОНЫ

// Типы упражнений
Table exercise_types {
  id increments
  name string
  dimension string // 2d, 3d
  is_customizable boolean // Можно ли настраивать
  description text
  created_at timestamp
  updated_at timestamp
}

Описание: Каталог типов упражнений (2D/3D) как классы активности.
- Ключ: `id`
- Поля: `dimension` (2d/3d), `is_customizable` — можно ли менять настройки.
- Использование: типизация шаблонов и фактических упражнений, UI фильтрация.

// Шаблоны упражнений
Table exercise_templates {
  id increments
  organization_id int
  exercise_type_id int
  name string
  description text
  settings json // JSON с настройками для 3D упражнений
  instructions_text text
  media_links json // JSON со ссылками на видео/аудио
  is_public boolean // Доступен всем врачам организации
  created_by int
  created_at timestamp
  updated_at timestamp
}

Описание: Шаблоны упражнений, на основании которых создаются сессии.
- Ключ: `id`
- Внешние ключи: `organization_id` (владелец), `exercise_type_id`, `created_by` (врач/методист).
- Параметры: `settings` — JSON настроек (в т.ч. 3D параметры, угловые области), `media_links`, `is_public` для шаринга внутри арендатора.
- Использование: единообразие методик, быстрое назначение в программы, версионирование через создание новых шаблонов.

// Выполненные упражнения
Table exercises {
  id increments
  patient_id int
  exercise_type_id int
  exercise_template_id int
  settings_override json // Индивидуальные настройки для пациента
  duration_ms int // Длительность в миллисекундах
  fatigue_right_eye int // Усталость правого глаза (1-5)
  fatigue_left_eye int // Усталость левого глаза (1-5)
  fatigue_head int // Усталость головы (1-5)
  patient_decision string // continue, stop
  notes text
  started_at timestamp
  completed_at timestamp
  created_at timestamp
}

Описание: Фактически выполненные пациентом упражнения (сессии).
- Ключ: `id`
- Внешние ключи: `patient_id`, `exercise_type_id`, `exercise_template_id`.
- Показатели: длительность, субъективная усталость по глазам/голове, `patient_decision`.
- Индивид. параметры: `settings_override` — отклонения от шаблона для конкретной сессии.
- Использование: аналитика прогресса, триггеры уведомлений, рекомендации по корректировке программ.

// Детальные данные по шарикам
Table ball_collections {
  id increments
  exercise_id int
  ball_sequence_number int // Порядковый номер шарика в упражнении
  distance_coordinate decimal // Координата расстояния
  horizontal_coordinate decimal // Горизонтальная координата
  vertical_coordinate decimal // Вертикальная координата
  ball_size int // Размер шарика
  accuracy_percent decimal // Процент точности попадания
  collection_time_ms int // Время сборки от начала упражнения
  time_from_previous_ms int // Время между предыдущим шариком
  created_at timestamp
}

Описание: Телеметрия по «сбору шариков» в рамках сессии.
- Ключ: `id`
- Внешний ключ: `exercise_id`.
- Метрики: координаты, размер, точность, тайминги между шариками — основа детальной аналитики моторики/бинокулярности.
- Использование: построение теплокарт, расчет сложности следующего шага, выявление усталости/сбоев внимания.

---

## ПРОГРАММЫ РЕАБИЛИТАЦИИ

// Комплексные программы
Table complex_programs {
  id increments
  organization_id int
  name string
  program_type string // development, maintenance
  description text
  duration_days int // Длительность программы в днях
  exercises_per_day int // Количество упражнений в день
  rest_pattern json // Паттерн отдыха (например 5/2)
  is_active boolean
  created_by int
  created_at timestamp
  updated_at timestamp
}

Описание: Методические программы (комплексы) упражнений на период.
- Ключ: `id`
- Внешние ключи: `organization_id`, `created_by` (методист/врач).
- Параметры: `program_type` (development/maintenance), длительность, плотность (`exercises_per_day`), `rest_pattern` (например, 5/2).
- Использование: типовые курсы, которые назначаются пациентам целиком.

// Упражнения в программе
Table program_exercises {
  id increments
  complex_program_id int
  exercise_template_id int
  day_number int // День программы
  exercise_order int // Порядок выполнения в дне
  settings_override json // Индивидуальные настройки для программы
  created_at timestamp
  updated_at timestamp
}

Описание: Раскладка шаблонов в составе программы по дням и порядку.
- Ключ: `id`
- Внешние ключи: `complex_program_id`, `exercise_template_id`.
- Параметры: `day_number`, `exercise_order`, `settings_override` для тонкой настройки под программу.
- Использование: генерация дневного плана для пациента.

// Назначенные программы пациентам
Table patient_programs {
  id increments
  patient_id int
  complex_program_id int
  status string // active, completed, paused, cancelled
  current_day int // Текущий день программы
  total_exercises_completed int
  progress_percent decimal // Процент выполнения
  assigned_by int
  assigned_at timestamp
  started_at timestamp
  completed_at timestamp
  created_at timestamp
  updated_at timestamp
}

Описание: Конкретное назначение программы пациенту и его прогресс.
- Ключ: `id`
- Внешние ключи: `patient_id`, `complex_program_id`, `assigned_by`.
- Статусы: `active/completed/paused/cancelled`, счетчики выполненных упражнений и процент прогресса.
- Использование: текущее состояние курса, расчет доступности упражнений, условия для биллинга.

// История изменений статусов программ
Table program_status_history {
  id increments
  patient_program_id int
  status_from string
  status_to string
  reason_type string // medical, technical, administrative, organizational
  reason_comment text
  changed_at timestamp
  changed_by int
}

Описание: История изменений статуса назначения программы.
- Ключ: `id`
- Внешний ключ: `patient_program_id`, `changed_by` (кто изменил).
- Пояснения: `reason_type` и `reason_comment` — важно для аудита и ретроспектив.
- Использование: восстановление хода лечения, аналитика отказов/пауз.

---

## СИСТЕМА ПОДКЛЮЧЕНИЙ

// Токены для QR-кодов подключения
Table connection_tokens {
  id increments
  patient_id int
  token string // UUID токен
  token_type string // download, connection
  expires_at timestamp
  is_used boolean
  used_at timestamp
  created_by int
  created_at timestamp
  updated_at timestamp
}

Описание: Одноразовые токены для подключения приложений/загрузки.
- Ключ: `id`
- Внешние ключи: `patient_id`, `created_by` (инициатор выпуска токена).
- Параметры: `token_type` (download/connection), срок действия и отметка использования.
- Использование: выдача QR-кодов пациенту, контроль безопасности при привязке устройств.

---

## МОНЕТИЗАЦИЯ

// Подписки организаций
Table subscriptions {
  id increments
  organization_id int
  plan_name string
  price_monthly decimal
  status string // active, expired, suspended
  current_users_count int
  current_patients_count int
  started_at timestamp
  expires_at timestamp
  created_at timestamp
  updated_at timestamp
}

Описание: Подписка арендатора на тарифный план.
- Ключ: `id`
- Внешний ключ: `organization_id`.
- Параметры: лимиты, цена, статус, текущие счетчики (`current_users_count/patients_count`).
- Использование: контроль превышения лимитов, блокировки/предупреждения при продлении.

// Платежи пациентов
Table patient_payments {
  id increments
  patient_id int
  patient_program_id int
  amount decimal
  currency string // RUB, USD, EUR
  payment_method string // card
  status string // pending, paid, cancelled
  payment_date date
  notes text
  created_at timestamp
  updated_at timestamp
}

Описание: Платежи, связанные с пациентом и назначенной программой.
- Ключ: `id`
- Внешние ключи: `patient_id`, `patient_program_id`.
- Параметры: сумма/валюта, метод, статус, дата.
- Использование: отчеты по оплатам, привязка финансов к прогрессу программы.

---

## СИСТЕМНЫЕ ТАБЛИЦЫ

// Аудит действий
Table audit_logs {
  id increments
  user_id int
  patient_id int // Если действие связано с пациентом
  action_type string // patient.created, exercise.updated и т.д.
  table_name string
  record_id int
  old_values json // Старые значения полей
  new_values json // Новые значения полей
  ip_address string
  user_agent text
  created_at timestamp
}

Описание: Аудит изменений и действий пользователей.
- Ключ: `id`
- Внешние ключи: `user_id`, опционально `patient_id`.
- Данные: прежние/новые значения, контекст запроса (IP, UA).
- Использование: соответствие требованиям безопасности, разбор инцидентов, откат.

// Уведомления
Table notifications {
  id increments
  user_id int
  type string // exercise_completed, high_fatigue_detected и т.д.
  title string
  message text
  is_read boolean
  read_at timestamp
  created_at timestamp
}

Описание: Центр уведомлений для пользователей.
- Ключ: `id`
- Внешний ключ: `user_id`.
- Типы: события по упражнениям, высокие показатели усталости и др.
- Использование: информирование врачей/админов, триггеры follow-up действий.

// Файловое хранилище
Table files {
  id increments
  organization_id int
  filename string // Системное имя файла
  original_filename string // Оригинальное имя
  file_path string
  file_size int // Размер в байтах
  mime_type string
  uploaded_by int
  created_at timestamp
}

Описание: Управление загружаемыми файлами в рамках арендатора.
- Ключ: `id`
- Внешние ключи: `organization_id`, `uploaded_by`.
- Параметры: системное/оригинальное имя, путь, размер, MIME.
- Использование: хранение медиа-инструкций, отчетов обследований, вложений в карточках.

---

## СВЯЗИ МЕЖДУ ТАБЛИЦАМИ

// Организационная структура
Ref: "organizations"."id" < "departments"."organization_id"
Ref: "departments"."id" < "departments"."parent_id"
Ref: "organizations"."id" < "users"."organization_id"
Ref: "departments"."id" < "users"."department_id"

// Пациенты
Ref: "organizations"."id" < "patients"."organization_id"
Ref: "users"."id" < "patients"."user_id"
Ref: "patients"."id" < "patient_examinations"."patient_id"
Ref: "users"."id" < "patient_examinations"."examined_by"

// Устройства и подключения
Ref: "organizations"."id" < "devices"."organization_id"
Ref: "patients"."id" < "patient_devices"."patient_id"
Ref: "devices"."id" < "patient_devices"."device_id"
Ref: "users"."id" < "patient_devices"."assigned_by"
Ref: "patients"."id" < "connection_tokens"."patient_id"
Ref: "users"."id" < "connection_tokens"."created_by"

// Упражнения
Ref: "exercise_types"."id" < "exercise_templates"."exercise_type_id"
Ref: "organizations"."id" < "exercise_templates"."organization_id"
Ref: "users"."id" < "exercise_templates"."created_by"
Ref: "patients"."id" < "exercises"."patient_id"
Ref: "exercise_types"."id" < "exercises"."exercise_type_id"
Ref: "exercise_templates"."id" < "exercises"."exercise_template_id"
Ref: "exercises"."id" < "ball_collections"."exercise_id"

// Программы реабилитации
Ref: "organizations"."id" < "complex_programs"."organization_id"
Ref: "users"."id" < "complex_programs"."created_by"
Ref: "complex_programs"."id" < "program_exercises"."complex_program_id"
Ref: "exercise_templates"."id" < "program_exercises"."exercise_template_id"
Ref: "patients"."id" < "patient_programs"."patient_id"
Ref: "complex_programs"."id" < "patient_programs"."complex_program_id"
Ref: "users"."id" < "patient_programs"."assigned_by"
Ref: "patient_programs"."id" < "program_status_history"."patient_program_id"
Ref: "users"."id" < "program_status_history"."changed_by"

// Монетизация
Ref: "organizations"."id" < "subscriptions"."organization_id"
Ref: "patients"."id" < "patient_payments"."patient_id"
Ref: "patient_programs"."id" < "patient_payments"."patient_program_id"

// Системные таблицы
Ref: "users"."id" < "audit_logs"."user_id"
Ref: "patients"."id" < "audit_logs"."patient_id"
Ref: "users"."id" < "notifications"."user_id"
Ref: "organizations"."id" < "files"."organization_id"
Ref: "users"."id" < "files"."uploaded_by"
