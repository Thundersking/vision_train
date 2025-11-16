<?php

use App\Domain\RBAC\Enums\Permission;

return [
    'dashboard' => [
        'label' => 'Главная',
        'icon' => 'pi pi-home',
        'path' => '/dashboard',
        'permission' => null, // Доступно всем
    ],

    'users' => [
        'label' => 'Пользователи',
        'icon' => 'pi pi-users',
        'path' => '/users',
        'permission' => Permission::USERS_VIEW->value,
    ],

    'departments' => [
        'label' => 'Отделения',
        'icon' => 'pi pi-building',
        'path' => '/departments',
        'permission' => Permission::DEPARTMENTS_VIEW->value,
    ],

    'patients' => [
        'label' => 'Пациенты',
        'icon' => 'pi pi-user',
        'path' => '/patients',
        'permission' => Permission::PATIENTS_VIEW->value,
    ],

    'exercises' => [
        'label' => 'Упражнения',
        'icon' => 'pi pi-play',
        'path' => '/exercises',
        'permission' => Permission::EXERCISES_VIEW->value,
    ],

    'programs' => [
        'label' => 'Программы реабилитации',
        'icon' => 'pi pi-list',
        'path' => '/programs',
        'permission' => Permission::PROGRAMS_VIEW->value,
    ],

    'reports' => [
        'label' => 'Отчеты',
        'icon' => 'pi pi-chart-bar',
        'path' => '/reports',
        'permission' => Permission::REPORTS_VIEW->value,
    ],

    'organization' => [
        'label' => 'Организация',
        'icon' => 'pi pi-building-columns',
        'path' => '/organization',
        'permission' => Permission::ORGANIZATION_VIEW->value,
    ],

    'settings' => [
        'label' => 'Настройки',
        'icon' => 'pi pi-cog',
        'path' => '/settings',
        'permission' => Permission::SETTINGS_VIEW->value,
    ],

    // Системные разделы (только для super_admin)
    'system' => [
        'label' => 'Система',
        'icon' => 'pi pi-server',
        'path' => '/system',
        'permission' => Permission::SYSTEM_TENANTS_MANAGE->value,
        'children' => [
            'system.tenants' => [
                'label' => 'Тенанты',
                'path' => '/system/tenants',
                'permission' => Permission::SYSTEM_TENANTS_MANAGE->value,
            ],
            'system.users' => [
                'label' => 'Системные пользователи',
                'path' => '/system/users',
                'permission' => Permission::SYSTEM_USERS_MANAGE->value,
            ],
            'system.logs' => [
                'label' => 'Логи',
                'path' => '/system/logs',
                'permission' => Permission::SYSTEM_LOGS_VIEW->value,
            ],
        ]
    ],
];
