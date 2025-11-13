<?php


namespace App\Domain\Shared\Models;

use App\Domain\Organization\Models\Organization;
use App\Domain\User\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'organization_id',
        'user_id',
        'action_type',
        'entity_type',
        'entity_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function actionLabel(): string
    {
        return match ($this->action_type) {
            'user.created' => 'Создан',
            'user.updated' => 'Обновлён',
            'user.role_changed' => 'Изменена роль',
            'user.deactivated' => 'Деактивирован',
            'auth.login' => 'Вход',
            'auth.logout' => 'Выход',
            'auth.failed_login' => 'Неудачная попытка входа',
            default => $this->action_type,
        };
    }

    public function entityLabel(): string
    {
        return $this->entity_type . ' #' . $this->entity_id;
    }

    public function actionBadgeVariant(): string
    {
        return match ($this->action_type) {
            'user.created', 'auth.login' => 'success',
            'user.deactivated', 'auth.failed_login' => 'error',
            'user.role_changed' => 'warning',
            default => 'info',
        };
    }
}
