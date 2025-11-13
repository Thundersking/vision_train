<?php

declare(strict_types=1);

namespace App\Domain\RBAC\Queries;

use App\Domain\RBAC\Models\Role;
use Illuminate\Support\Facades\Cache;

class RoleQueries
{
    public function findByNameForOrganization(string $name, int $organizationId): ?Role
    {
        return Cache::tags(['roles', "organization:{$organizationId}"])
            ->remember("role.{$name}.organization.{$organizationId}", 3600, function () use ($name, $organizationId) {
                return Role::where('name', $name)
                    ->where('organization_id', $organizationId)
                    ->first();
            });
    }

    public function findById(int $id): ?Role
    {
        return Cache::tags(['roles'])
            ->remember("role.{$id}", 3600, function () use ($id) {
                return Role::find($id);
            });
    }

    /**
     * Получить все роли для текущей организации
     */
    public function forCurrentOrganization(): \Illuminate\Support\Collection
    {
        $organizationId = session('current_organization_id');
        
        return Cache::tags(['roles', "organization:{$organizationId}"])
            ->remember("roles.organization.{$organizationId}", 3600, function () use ($organizationId) {
                return Role::where('organization_id', $organizationId)
                    ->orderBy('name')
                    ->get();
            });
    }

    /**
     * Получить глобальные роли
     */
    public function global(): \Illuminate\Support\Collection
    {
        return Cache::tags(['roles'])
            ->remember('roles.global', 7200, function () {
                return Role::whereNull('organization_id')
                    ->orderBy('name')
                    ->get();
            });
    }

    /**
     * Получить роли для dropdown текущей организации
     */
    public function forDropdown(): array
    {
        return $this->forCurrentOrganization()
            ->pluck('display_name', 'id')
            ->toArray();
    }
}