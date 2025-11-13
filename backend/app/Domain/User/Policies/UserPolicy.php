<?php

declare(strict_types=1);

namespace App\Domain\User\Policies;

use App\Domain\RBAC\Enums\Permission;
use App\Domain\User\Models\User;

class UserPolicy
{
    /**
     * Может ли пользователь просматривать список пользователей
     */
    public function viewAny(User $user): bool
    {
        return $user->can(Permission::USERS_VIEW->value);
    }

    /**
     * Может ли пользователь просматривать конкретного пользователя
     */
    public function view(User $user, User $model): bool
    {
        // Пользователь может видеть себя
        if ($user->id === $model->id) {
            return true;
        }

        // Проверяем принадлежность к организации
        if ($user->organization_id !== $model->organization_id) {
            return false;
        }

        // Проверяем разрешение на просмотр пользователей
        return $user->can(Permission::USERS_VIEW->value);
    }

    /**
     * Может ли пользователь создавать новых пользователей
     */
    public function create(User $user): bool
    {
        return $user->can(Permission::USERS_CREATE->value);
    }

    /**
     * Может ли пользователь обновлять данные пользователя
     */
    public function update(User $user, User $model): bool
    {
        // Пользователь может обновлять свои данные
        if ($user->id === $model->id) {
            return true;
        }

        // Проверяем принадлежность к организации
        if ($user->organization_id !== $model->organization_id) {
            return false;
        }

        // Проверяем разрешение на обновление пользователей
        return $user->can(Permission::USERS_UPDATE->value);
    }

    /**
     * Может ли пользователь архивировать пользователя
     */
    public function delete(User $user, User $model): bool
    {
        return $this->archive($user, $model);
    }

    /**
     * Может ли пользователь архивировать пользователя
     */
    public function archive(User $user, User $model): bool
    {
        // Нельзя архивировать самого себя
        if ($user->id === $model->id) {
            return false;
        }

        // Проверяем принадлежность к организации
        if ($user->organization_id !== $model->organization_id) {
            return false;
        }

        // Проверяем разрешение на архивирование пользователей
        return $user->can(Permission::USERS_ARCHIVE->value);
    }

    /**
     * Может ли пользователь восстанавливать архивированных пользователей
     */
    public function restore(User $user, User $model): bool
    {
        // Проверяем принадлежность к организации
        if ($user->organization_id !== $model->organization_id) {
            return false;
        }

        // Проверяем разрешение на восстановление пользователей
        return $user->can(Permission::USERS_RESTORE->value);
    }
}
