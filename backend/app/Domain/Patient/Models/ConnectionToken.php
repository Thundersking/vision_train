<?php

declare(strict_types=1);

namespace App\Domain\Patient\Models;

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
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'used_at' => 'datetime',
            'is_active' => 'boolean',
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
     * Проверка, истек ли токен
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Проверка, использован ли токен
     */
    public function isUsed(): bool
    {
        return !is_null($this->used_at);
    }

    /**
     * Проверка валидности токена
     */
    public function isValid(): bool
    {
        return $this->is_active && !$this->isExpired() && !$this->isUsed();
    }
}
