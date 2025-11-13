<?php

declare(strict_types=1);

namespace App\Domain\RBAC\Constants;

class DefaultRoles
{
    /**
     * Глобальные роли (только для системы, organization_id = null)
     * Тенанты НЕ ВИДЯТ эти роли
     */
    public const GLOBAL_ROLES = [
        'super_admin' => [
            'display_name' => 'Системный администратор',
            'description' => 'Доступ ко всем тенантам и системным функциям',
            'organization_id' => null,
            'is_system' => true,
            'permissions' => 'all_global'
        ]
    ];

    /**
     * Системные роли тенанта (нельзя удалять/редактировать)
     * is_system = true, organization_id = X
     */
    public const SYSTEM_TENANT_ROLES = [
        'organization_admin' => [
            'display_name' => 'Администратор организации',
            'description' => 'Полный доступ к функциям организации',
            'is_system' => true,
            'permissions' => 'all_tenant'
        ]
    ];

    /**
     * Роли по умолчанию для тенанта (можно редактировать/удалять)
     * is_system = false, organization_id = X
     */
    public const DEFAULT_TENANT_ROLES = [
        'organization_manager' => [
            'display_name' => 'Менеджер организации',
            'description' => 'Управление пациентами и просмотр отчетов',
            'is_system' => false,
            'permissions' => [
                'users.view',
                'patients.view',
                'patients.create', 
                'patients.update',
                'exercises.view',
                'programs.view',
                'reports.view'
            ]
        ],
        'doctor' => [
            'display_name' => 'Врач/Тренер',
            'description' => 'Работа с пациентами и проведение упражнений',
            'is_system' => false,
            'permissions' => [
                'patients.view',
                'patients.create',
                'patients.update', 
                'exercises.view',
                'exercises.create',
                'exercises.update',
                'programs.view',
                'programs.create'
            ]
        ]
    ];

    /**
     * Получить все роли для создания в организации
     */
    public static function getAllTenantRoles(): array
    {
        return array_merge(
            self::SYSTEM_TENANT_ROLES,
            self::DEFAULT_TENANT_ROLES
        );
    }

    /**
     * Получить только редактируемые роли для организации
     */
    public static function getEditableTenantRoles(): array
    {
        return self::DEFAULT_TENANT_ROLES;
    }

    /**
     * Проверить является ли роль системной для тенанта
     */
    public static function isSystemTenantRole(string $roleName): bool
    {
        return array_key_exists($roleName, self::SYSTEM_TENANT_ROLES);
    }

    /**
     * Проверить является ли роль глобальной системной
     */
    public static function isGlobalRole(string $roleName): bool
    {
        return array_key_exists($roleName, self::GLOBAL_ROLES);
    }
}