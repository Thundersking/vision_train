<?php

namespace App\Domain\RBAC\Enums;

enum Permission: string
{
    // === ГЛОБАЛЬНЫЕ РАЗРЕШЕНИЯ (только для super_admin) ===
    case SYSTEM_TENANTS_MANAGE = 'system.tenants.manage';
    case SYSTEM_USERS_MANAGE = 'system.users.manage';
    case SYSTEM_SETTINGS_MANAGE = 'system.settings.manage';
    case SYSTEM_LOGS_VIEW = 'system.logs.view';

    // === ТЕНАНТСКИЕ РАЗРЕШЕНИЯ ===
    // Пользователи
    case USERS_VIEW = 'users.view';
    case USERS_CREATE = 'users.create';
    case USERS_UPDATE = 'users.update';
    case USERS_DELETE = 'users.delete';
    case USERS_ARCHIVE = 'users.archive';
    case USERS_RESTORE = 'users.restore';

    case ORGANIZATION_VIEW = 'organization.view';
    case ORGANIZATION_UPDATE = 'organization.update';

    // Отделения
    case DEPARTMENTS_VIEW = 'departments.view';
    case DEPARTMENTS_CREATE = 'departments.create';
    case DEPARTMENTS_UPDATE = 'departments.update';
    case DEPARTMENTS_DELETE = 'departments.delete';
    case DEPARTMENTS_ARCHIVE = 'departments.archive';
    case DEPARTMENTS_RESTORE = 'departments.restore';

    // Пациенты
    case PATIENTS_INDEX = 'patients.index';
    case PATIENTS_VIEW = 'patients.view';
    case PATIENTS_CREATE = 'patients.create';
    case PATIENTS_UPDATE = 'patients.update';
    case PATIENTS_DELETE = 'patients.delete';
    case PATIENTS_ARCHIVE = 'patients.archive';
    case PATIENTS_RESTORE = 'patients.restore';

    // Упражнения
    case EXERCISES_VIEW = 'exercises.view';
    case EXERCISES_CREATE = 'exercises.create';
    case EXERCISES_UPDATE = 'exercises.update';
    case EXERCISES_DELETE = 'exercises.delete';

    // Программы реабилитации
    case PROGRAMS_VIEW = 'programs.view';
    case PROGRAMS_CREATE = 'programs.create';
    case PROGRAMS_UPDATE = 'programs.update';
    case PROGRAMS_DELETE = 'programs.delete';

    // Отчеты
    case REPORTS_VIEW = 'reports.view';
    case REPORTS_EXPORT = 'reports.export';

    // Настройки организации
    case SETTINGS_VIEW = 'settings.view';
    case SETTINGS_UPDATE = 'settings.update';

    // Получить все разрешения
    public static function all(): array
    {
        return array_column(self::cases(), 'value');
    }

    // Группировка по ресурсам
    public static function forResource(string $resource): array
    {
        return array_filter(
            self::all(),
            fn($p) => str_starts_with($p, $resource . '.')
        );
    }

    /**
     * Получить все глобальные разрешения (для super_admin)
     */
    public static function allGlobal(): array
    {
        return [
            self::SYSTEM_TENANTS_MANAGE->value,
            self::SYSTEM_USERS_MANAGE->value,
            self::SYSTEM_SETTINGS_MANAGE->value,
            self::SYSTEM_LOGS_VIEW->value,
        ];
    }

    /**
     * Получить все тенантские разрешения
     */
    public static function allTenant(): array
    {
        $allPermissions = self::all();
        $globalPermissions = self::allGlobal();

        return array_diff($allPermissions, $globalPermissions);
    }

    /**
     * Разрешения для конкретной роли
     */
    public static function forRole(string $role): array
    {
        return match ($role) {
            // Глобальная роль
            'super_admin' => self::all(), // Все разрешения

            // Системная тенантская роль
            'organization_admin' => self::allTenant(), // Все тенантские разрешения

            // Обычные тенантские роли
            'organization_manager' => [
                self::USERS_VIEW->value,
                self::USERS_UPDATE->value,
                self::DEPARTMENTS_VIEW->value,
                self::PATIENTS_VIEW->value,
                self::PATIENTS_CREATE->value,
                self::PATIENTS_UPDATE->value,
                self::PATIENTS_ARCHIVE->value,
                self::PATIENTS_RESTORE->value,
                self::EXERCISES_VIEW->value,
                self::PROGRAMS_VIEW->value,
                self::REPORTS_VIEW->value,
                self::SETTINGS_VIEW->value,
            ],

            'doctor' => [
                self::DEPARTMENTS_VIEW->value,
                self::PATIENTS_VIEW->value,
                self::PATIENTS_CREATE->value,
                self::PATIENTS_UPDATE->value,
                self::EXERCISES_VIEW->value,
                self::EXERCISES_CREATE->value,
                self::EXERCISES_UPDATE->value,
                self::PROGRAMS_VIEW->value,
                self::PROGRAMS_CREATE->value,
            ],

            default => [],
        };
    }
}
