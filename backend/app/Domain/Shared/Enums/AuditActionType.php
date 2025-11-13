<?php

namespace App\Domain\Shared\Enums;

enum AuditActionType: string
{
    case CREATED = 'created';
    case UPDATED = 'updated';
    case DELETED = 'deleted';
    case ARCHIVED = 'archived';
    case ROLE_CHANGED = 'role_changed';
    case DEACTIVATED = 'deactivated';
    case LOGIN = 'login';
    case LOGOUT = 'logout';
    case FAILED_LOGIN = 'failed_login';

    public function label(): string
    {
        return match ($this) {
            self::CREATED => 'Создан',
            self::UPDATED => 'Обновлён',
            self::DELETED => 'Удалён',
            self::ARCHIVED => 'Архивирован',
            self::ROLE_CHANGED => 'Изменена роль',
            self::DEACTIVATED => 'Деактивирован',
            self::LOGIN => 'Вход',
            self::LOGOUT => 'Выход',
            self::FAILED_LOGIN => 'Неудачная попытка входа',
        };
    }

    public function badgeVariant(): string
    {
        return match ($this) {
            self::CREATED, self::LOGIN => 'success',
            self::DELETED, self::DEACTIVATED, self::FAILED_LOGIN => 'error',
            self::UPDATED, self::ROLE_CHANGED => 'warning',
            default => 'info',
        };
    }
}
