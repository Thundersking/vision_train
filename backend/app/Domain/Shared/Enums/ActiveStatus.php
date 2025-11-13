<?php

declare(strict_types=1);

namespace App\Domain\Shared\Enums;

enum ActiveStatus: int
{
    case Active = 1;
    case Inactive = 0;

    public static function forDropdown(): array
    {
        return [
            self::Active->value => 'Активные',
            self::Inactive->value => 'Деактивированные',
        ];
    }
}
