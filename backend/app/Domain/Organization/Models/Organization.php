<?php

declare(strict_types=1);

namespace App\Domain\Organization\Models;

use App\Domain\AuditLog\Models\AuditLog;
use App\Domain\Department\Models\Department;
use App\Domain\Device\Models\Device;
use App\Domain\Organization\Enums\OrganizationType;
use App\Domain\Patient\Models\Patient;
use App\Domain\User\Models\User;
use App\Support\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Multitenancy\Models\Tenant;

class Organization extends Tenant
{
    use HasFactory;
    use HasUuid;
    use SoftDeletes;

    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'uuid',
        'name',
        'domain',
        'type',
        'is_active',
        'subscription_plan',
        'email',
        'phone',
        'inn',
        'kpp',
        'ogrn',
        'legal_address',
        'actual_address',
        'director_name',
        'license_number',
        'license_issued_at',
    ];

    /**
     * Get casts for attributes
     */
    protected function casts(): array
    {
        return [
            'license_issued_at' => 'date',
            'is_active' => 'boolean',
            'type' => OrganizationType::class,
        ];
    }

    /**
     * Отделения организации
     */
    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

    /**
     * Пользователи организации
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Пациенты организации
     */
    public function patients(): HasMany
    {
        return $this->hasMany(Patient::class);
    }

    /**
     * Устройства организации
     */
    public function devices(): HasMany
    {
        return $this->hasMany(Device::class);
    }

    /**
     * Аудит-логи организации
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    /**
     * Scope для активных организаций
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Человекочитаемое название типа
     */
    public function getTypeLabelAttribute(): string
    {
        return $this->type?->label() ?? '';
    }
}
