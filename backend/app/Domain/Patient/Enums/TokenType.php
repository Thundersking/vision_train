<?php

declare(strict_types=1);

namespace App\Domain\Patient\Enums;

enum TokenType: string
{
    case Download = 'download';
    case Connection = 'connection';

    public function getDisplayName(): string
    {
        return match ($this) {
            self::Download => 'Токен загрузки',
            self::Connection => 'Токен подключения',
        };
    }

    public static function options(): array
    {
        return [
            self::Download->value => self::Download->getDisplayName(),
            self::Connection->value => self::Connection->getDisplayName(),
        ];
    }
}