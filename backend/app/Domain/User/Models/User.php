<?php

declare(strict_types=1);

namespace App\Domain\User\Models;

use Spatie\Permission\Traits\HasRoles;
use App\Domain\Department\Models\Department;
use App\Domain\Organization\Models\Organization;
use App\Support\Enums\Timezone;
use App\Support\Multitenancy\Traits\HasOrganization;
use App\Support\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory;
    use HasOrganization;
    use HasUuid;
    use Notifiable;
    use SoftDeletes;
    use HasRoles;

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return \Database\Factories\UserFactory::new();
    }

    protected string $guard_name = 'api';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'middle_name',
        'phone',
        'email',
        'password',
        'organization_id',
        'department_id',
        'override_utc_offset_minutes',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'override_utc_offset_minutes' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Отношение к организации
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Отношение к отделению
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Получить персональную таймзону пользователя как Enum
     */
    public function getPersonalTimezoneEnum(): ?Timezone
    {
        if ($this->override_utc_offset_minutes === null) {
            return null;
        }

        return Timezone::fromMinutes($this->override_utc_offset_minutes);
    }

    /**
     * Получить эффективную таймзону пользователя как Enum
     */
    public function getEffectiveTimezoneEnum(): ?Timezone
    {
        return $this->getPersonalTimezoneEnum() ?? $this->department->getTimezoneEnum();
    }

    /**
     * Получить таймзону пользователя в формате Carbon
     */
    public function getTimezone(): string
    {
        $timezone = $this->getEffectiveTimezoneEnum();

        if ($timezone) {
            return $timezone->getCarbonOffset();
        }

        // Fallback - используем таймзону отделения
        return $this->department->getTimezone();
    }

    /**
     * Получить название таймзоны для отображения
     */
    public function getTimezoneDisplayName(): string
    {
        $personalTimezone = $this->getPersonalTimezoneEnum();

        if ($personalTimezone) {
            return $personalTimezone->getDisplayName() . ' (персональная)';
        }

        return $this->department->getTimezoneDisplayName();
    }

    /**
     * Установить персональную таймзону из Enum
     */
    public function setPersonalTimezone(?Timezone $timezone): void
    {
        $this->override_utc_offset_minutes = $timezone?->getOffsetMinutes();
    }

    /**
     * Scope для активных пользователей
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope для поиска по имени или email
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%");
        });
    }

    /**
     * Scope для фильтрации по отделению
     */
    public function scopeInDepartment($query, int $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    /**
     * Get the user's full name
     */
    public function getNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function getRoleNames(): string
    {
        return $this->roles->pluck('display_name');
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }
}
