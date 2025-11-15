<?php

use App\Domain\RBAC\Enums\Permission;

return [
    'dashboard' => [
        'title' => 'Главная',
        'icon' => 'pi pi-home',
        'route' => '/dashboard',
        'permission' => null, // Доступно всем
    ],

    'users' => [
        'title' => 'Пользователи',
        'icon' => 'pi pi-users',
        'route' => '/users',
        'permission' => Permission::USERS_VIEW->value,
    ],

    'departments' => [
        'title' => 'Отделения',
        'icon' => 'pi pi-building',
        'route' => '/departments',
        'permission' => Permission::DEPARTMENTS_VIEW->value,
    ],

    'patients' => [
        'title' => 'Пациенты',
        'icon' => 'pi pi-user',
        'route' => '/patients',
        'permission' => Permission::PATIENTS_VIEW->value,
    ],

    'exercises' => [
        'title' => 'Упражнения',
        'icon' => 'pi pi-play',
        'route' => '/exercises',
        'permission' => Permission::EXERCISES_VIEW->value,
    ],

    'programs' => [
        'title' => 'Программы реабилитации',
        'icon' => 'pi pi-list',
        'route' => '/programs',
        'permission' => Permission::PROGRAMS_VIEW->value,
    ],

    'reports' => [
        'title' => 'Отчеты',
        'icon' => 'pi pi-chart-bar',
        'route' => '/reports',
        'permission' => Permission::REPORTS_VIEW->value,
    ],

    'organization' => [
        'title' => 'Организация',
        'icon' => 'pi pi-building-columns',
        'route' => '/organization',
        'permission' => Permission::ORGANIZATION_VIEW->value,
    ],

    'settings' => [
        'title' => 'Настройки',
        'icon' => 'pi pi-cog',
        'route' => '/settings',
        'permission' => Permission::SETTINGS_VIEW->value,
    ],

    // Системные разделы (только для super_admin)
    'system' => [
        'title' => 'Система',
        'icon' => 'pi pi-server',
        'route' => '/system',
        'permission' => Permission::SYSTEM_TENANTS_MANAGE->value,
        'children' => [
            'system.tenants' => [
                'title' => 'Тенанты',
                'route' => '/system/tenants',
                'permission' => Permission::SYSTEM_TENANTS_MANAGE->value,
            ],
            'system.users' => [
                'title' => 'Системные пользователи',
                'route' => '/system/users',
                'permission' => Permission::SYSTEM_USERS_MANAGE->value,
            ],
            'system.logs' => [
                'title' => 'Логи',
                'route' => '/system/logs',
                'permission' => Permission::SYSTEM_LOGS_VIEW->value,
            ],
        ]
    ],
];