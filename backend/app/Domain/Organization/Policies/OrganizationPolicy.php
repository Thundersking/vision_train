<?php

declare(strict_types=1);

namespace App\Domain\Organization\Policies;

use App\Domain\Organization\Models\Organization;
use App\Domain\RBAC\Enums\Permission;
use App\Domain\User\Models\User;

class OrganizationPolicy
{
    /**
     * Может ли пользователь просматривать данные организации
     */
    public function view(User $user, Organization $organization): bool
    {
        if ($user->organization_id !== $organization->id) {
            return false;
        }

        return $user->can(Permission::ORGANIZATION_VIEW->value);
    }

    /**
     * Может ли пользователь обновлять данные организации
     */
    public function update(User $user, Organization $organization): bool
    {
        if ($user->organization_id !== $organization->id) {
            return false;
        }

        return $user->can(Permission::ORGANIZATION_UPDATE->value);
    }
}
