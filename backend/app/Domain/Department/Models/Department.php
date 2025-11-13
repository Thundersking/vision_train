<?php

declare(strict_types=1);

namespace App\Domain\Department\Models;

use App\Domain\Organization\Models\Organization;
use App\Domain\User\Models\User;
use App\Support\Enums\Timezone;
use App\Support\Multitenancy\Traits\HasOrganization;
use App\Support\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use HasFactory;
    use HasOrganization;
    use HasUuid;
    use SoftDeletes;

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return \Database\Factories\DepartmentFactory::new();
    }

    protected $fillable = [
        'name',
        'organization_id',
        'utc_offset_minutes',
        'address',
        'phone',
        'email',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'utc_offset_minutes' => 'integer',
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
     * Пользователи отделения
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Активные пользователи отделения
     */
    public function activeUsers(): HasMany
    {
        return $this->users()->where('is_active', true);
    }

    /**
     * Получить таймзону отделения как Enum
     */
    public function getTimezoneEnum(): ?Timezone
    {
        return Timezone::fromMinutes($this->utc_offset_minutes);
    }

    /**
     * Получить таймзону отделения в формате Carbon
     */
    public function getTimezone(): string
    {
        $timezone = $this->getTimezoneEnum();

        if ($timezone) {
            return $timezone->getCarbonOffset();
        }

        // Fallback для нестандартных офсетов
        $hours = $this->utc_offset_minutes / 60;
        $sign = $hours >= 0 ? '+' : '-';
        $absHours = abs($hours);

        return sprintf('%s%02d:00', $sign, $absHours);
    }

    /**
     * Получить название таймзоны для отображения
     */
    public function getTimezoneDisplayName(): string
    {
        $timezone = $this->getTimezoneEnum();

        if ($timezone) {
            return $timezone->getDisplayName();
        }

        // Fallback для нестандартных офсетов
        $hours = $this->utc_offset_minutes / 60;

        return sprintf('UTC%+d', $hours);
    }

    /**
     * Установить таймзону из Enum
     */
    public function setTimezone(Timezone $timezone): void
    {
        $this->utc_offset_minutes = $timezone->getOffsetMinutes();
    }

    /**
     * Scope для активных отделений
     */
    public function scopeActive($query): mixed
    {
        return $query->where('is_active', true);
    }

    /**
     * Получить количество активных пользователей в отделении
     */
    public function getActiveUsersCountAttribute(): int
    {
        return $this->users()->active()->count();
    }
}
