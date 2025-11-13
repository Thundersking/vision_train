<?php

declare(strict_types=1);

namespace App\Domain\Organization\Policies;

use App\Domain\Organization\Models\Organization;
use App\Domain\RBAC\Enums\Permission;
use App\Domain\User\Models\User;

class OrganizationPolicy
{
    /**
     * Может ли пользователь просматривать свою организацию
     */
    public function view(User $user, Organization $organization): bool
    {
        // Проверяем принадлежность к организации
        if ($user->organization_id !== $organization->id) {
            return false;
        }

        // Проверяем разрешение на просмотр организации
        return $user->can(Permission::ORGANIZATION_VIEW->value);
    }

    /**
     * Может ли пользователь редактировать свою организацию
     */
    public function update(User $user, Organization $organization): bool
    {
        // Проверяем принадлежность к организации
        if ($user->organization_id !== $organization->id) {
            return false;
        }

        // Проверяем разрешение на редактирование организации
        return $user->can(Permission::ORGANIZATION_UPDATE->value);
    }
}
