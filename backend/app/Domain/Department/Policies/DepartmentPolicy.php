<?php

declare(strict_types=1);

namespace App\Domain\Department\Policies;

use App\Domain\Department\Models\Department;
use App\Domain\RBAC\Enums\Permission;
use App\Domain\User\Models\User;

class DepartmentPolicy
{
    /**
     * Может ли пользователь просматривать список отделений
     */
    public function viewAny(User $user): bool
    {
        return $user->can(Permission::DEPARTMENTS_VIEW->value);
    }

    /**
     * Может ли пользователь просматривать конкретное отделение
     */
    public function view(User $user, Department $department): bool
    {
        // Проверяем принадлежность к организации
        if ($user->organization_id !== $department->organization_id) {
            return false;
        }

        // Проверяем разрешение на просмотр отделений
        return $user->can(Permission::DEPARTMENTS_VIEW->value);
    }

    /**
     * Может ли пользователь создавать новые отделения
     */
    public function create(User $user): bool
    {
        return $user->can(Permission::DEPARTMENTS_CREATE->value);
    }

    /**
     * Может ли пользователь обновлять данные отделения
     */
    public function update(User $user, Department $department): bool
    {
        // Проверяем принадлежность к организации
        if ($user->organization_id !== $department->organization_id) {
            return false;
        }

        // Проверяем разрешение на обновление отделений
        return $user->can(Permission::DEPARTMENTS_UPDATE->value);
    }

    /**
     * Может ли пользователь архивировать отделение
     */
    public function delete(User $user, Department $department): bool
    {
        return $this->archive($user, $department);
    }

    /**
     * Может ли пользователь архивировать отделение
     */
    public function archive(User $user, Department $department): bool
    {
        // Проверяем принадлежность к организации
        if ($user->organization_id !== $department->organization_id) {
            return false;
        }

        // Проверяем разрешение на архивирование отделений
        return $user->can(Permission::DEPARTMENTS_ARCHIVE->value);
    }

    /**
     * Может ли пользователь восстанавливать архивированные отделения
     */
    public function restore(User $user, Department $department): bool
    {
        // Проверяем принадлежность к организации
        if ($user->organization_id !== $department->organization_id) {
            return false;
        }

        // Проверяем разрешение на восстановление отделений
        return $user->can(Permission::DEPARTMENTS_RESTORE->value);
    }
}