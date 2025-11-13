<?php

declare(strict_types=1);

namespace App\Domain\RBAC\Models;

use App\Support\Multitenancy\Traits\HasOrganization;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    use HasOrganization;

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'guard_name',
        'organization_id',
        'is_system',
    ];

    protected $casts = [
        'organization_id' => 'integer',
        'is_system' => 'boolean',
    ];

    /**
     * Boot the model
     */
    protected static function boot(): void
    {
        parent::boot();

        // Ensure organization_id is set when creating roles
        static::creating(function (Role $role) {
            if (is_null($role->organization_id) && auth()->check()) {
                $role->organization_id = auth()->user()->organization_id;
            }
        });
    }

    /**
     * Роли для конкретного тенанта (скрывает super_admin)
     * Только роли принадлежащие данной организации
     */
    public function scopeForTenant($query, int $organizationId)
    {
        return $query->where('organization_id', $organizationId);
    }

    /**
     * Только системные роли тенанта (нельзя редактировать)
     */
    public function scopeSystemTenant($query, int $organizationId)
    {
        return $query->where('organization_id', $organizationId)
                    ->where('is_system', true);
    }

    /**
     * Только редактируемые роли тенанта (можно менять)
     */
    public function scopeEditableTenant($query, int $organizationId)
    {
        return $query->where('organization_id', $organizationId)
                    ->where('is_system', false);
    }

    /**
     * Глобальные системные роли (только для super_admin)
     */
    public function scopeGlobal($query)
    {
        return $query->whereNull('organization_id');
    }

    /**
     * Все роли (глобальные + тенантские) - только для super_admin
     */
    public function scopeForSuperAdmin($query)
    {
        return $query; // Без ограничений
    }

    /**
     * Check if role is global
     */
    public function isGlobal(): bool
    {
        return is_null($this->organization_id);
    }

    /**
     * Check if role belongs to specific organization
     */
    public function belongsToOrganization(int $organizationId): bool
    {
        return $this->organization_id === $organizationId;
    }
}