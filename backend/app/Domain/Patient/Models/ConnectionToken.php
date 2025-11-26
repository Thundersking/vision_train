<?php

declare(strict_types=1);

namespace App\Domain\Patient\Models;

use App\Domain\Patient\Enums\ConnectionTokenStatus;
use App\Domain\User\Models\User;
use App\Support\Multitenancy\Traits\HasOrganization;
use App\Support\Traits\HasUuid;
use App\Domain\Shared\Traits\RecordsAuditLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class ConnectionToken extends Model
{
    use HasFactory;
    use HasOrganization;
    use HasUuid;
    use RecordsAuditLog;

    protected $fillable = [
        'organization_id',
        'patient_id',
        'token',
        'qr_code_path',
        'expires_at',
        'used_at',
        'status',
        'is_active',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'used_at' => 'datetime',
            'is_active' => 'boolean',
            'status' => ConnectionTokenStatus::class,
        ];
    }

    /**
     * Отношение к пациенту
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Пользователь, создавший токен
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Проверка, истек ли токен
     */
    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    /**
     * Проверка, использован ли токен
     */
    public function isUsed(): bool
    {
        return $this->used_at !== null;
    }

    /**
     * Проверка валидности токена
     */
    public function isValid(): bool
    {
        return $this->status === ConnectionTokenStatus::PENDING
            && !$this->isExpired()
            && !$this->isUsed();
    }
}
