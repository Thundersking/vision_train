<?php

declare(strict_types=1);

namespace App\Domain\Organization\Models;

use App\Domain\Department\Models\Department;
use App\Domain\User\Models\User;
use App\Support\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Multitenancy\Models\Tenant;

class Organization extends Tenant
{
    use HasUuid;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'domain',
        'type',
        'is_active',
        'subscription_plan',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Отделения организации
     */
    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

    /**
     * Активные отделения организации
     */
    public function activeDepartments(): HasMany
    {
        return $this->departments()->where('is_active', true);
    }

    /**
     * Пользователи организации (через отделения)
     */
    public function users(): HasManyThrough
    {
        return $this->hasManyThrough(User::class, Department::class);
    }

    /**
     * Активные пользователи организации
     */
    public function activeUsers(): HasManyThrough
    {
        return $this->users()->where('users.is_active', true);
    }

    /**
     * Scope для активных организаций
     */
    public function scopeActive($query): mixed
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope для поиска по названию
     */
    public function scopeSearch($query, string $search): mixed
    {
        return $query->where('name', 'like', "%{$search}%");
    }

    /**
     * Scope по типу организации
     */
    public function scopeOfType($query, string $type): mixed
    {
        return $query->where('type', $type);
    }
}
