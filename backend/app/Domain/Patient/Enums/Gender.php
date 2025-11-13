<?php

declare(strict_types=1);

namespace App\Domain\Patient\Enums;

enum Gender: string
{
    case Male = 'male';
    case Female = 'female';

    public function getDisplayName(): string
    {
        return match ($this) {
            self::Male => 'Мужской',
            self::Female => 'Женский',
        };
    }

    public static function forDropdown(): array
    {
        return [
            self::Male->value => self::Male->getDisplayName(),
            self::Female->value => self::Female->getDisplayName(),
        ];
    }
}
