<?php

declare(strict_types=1);

namespace App\Domain\Patient\Enums;

enum ConnectionTokenStatus: string
{
    case PENDING = 'pending';
    case USED = 'used';
    case EXPIRED = 'expired';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Ожидает подключения',
            self::USED => 'Использован',
            self::EXPIRED => 'Истек',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
