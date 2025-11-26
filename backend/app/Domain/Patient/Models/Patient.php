<?php

declare(strict_types=1);

namespace App\Domain\Patient\Models;

use App\Domain\Organization\Models\Organization;
use App\Domain\User\Models\User;
use App\Support\Multitenancy\Traits\HasOrganization;
use App\Support\Traits\HasUuid;
use App\Domain\Shared\Traits\RecordsAuditLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;
    use HasOrganization;
    use HasUuid;
    use RecordsAuditLog;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'organization_id',
        'user_id',
        'first_name',
        'last_name',
        'middle_name',
        'email',
        'phone',
        'gender',
        'hand_size_cm',
        'birth_date',
        'notes',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'hand_size_cm' => 'decimal:1',
            'is_active' => 'boolean',
            'gender' => \App\Domain\Patient\Enums\Gender::class,
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
     * Отношение к закрепленному врачу
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Обследования пациента
     */
    public function examinations(): HasMany
    {
        return $this->hasMany(PatientExamination::class);
    }

    /**
     * Устройства пациента (через pivot таблицу)
     */
    public function devices(): BelongsToMany
    {
        return $this->belongsToMany(\App\Domain\Device\Models\Device::class, 'patient_devices')
            ->withPivot('is_primary', 'assigned_at', 'assigned_by', 'notes')
            ->withTimestamps();
    }

    /**
     * Основное устройство пациента
     */
    public function primaryDevice(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->devices()->wherePivot('is_primary', true);
    }

    /**
     * Токены подключения пациента
     */
    public function connectionTokens(): HasMany
    {
        return $this->hasMany(ConnectionToken::class);
    }

    /**
     * Прямой доступ к pivot-записям устройств пациента
     */
    public function patientDevices(): HasMany
    {
        return $this->hasMany(PatientDevice::class);
    }

    /**
     * Полное имя пациента
     */
    public function getNameAttribute(): string
    {
        $name = trim("{$this->first_name} {$this->last_name}");
        if ($this->middle_name) {
            $name .= " {$this->middle_name}";
        }
        return $name;
    }

    /**
     * Возраст пациента
     */
    public function getAgeAttribute(): int
    {
        return $this->birth_date?->diffInYears(now()) ?? 0;
    }

    /**
     * Scope для активных пациентов
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope для поиска по ФИО
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('first_name', 'like', "%{$search}%")
              ->orWhere('last_name', 'like', "%{$search}%")
              ->orWhere('middle_name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%");
        });
    }

    /**
     * Scope для фильтрации по врачу
     */
    public function scopeForDoctor($query, int $doctorId)
    {
        return $query->where('user_id', $doctorId);
    }

    /**
     * Scope для фильтрации по полу
     */
    public function scopeByGender($query, string $gender)
    {
        return $query->where('gender', $gender);
    }
}
