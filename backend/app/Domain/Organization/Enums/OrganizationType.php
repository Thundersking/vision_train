<?php

declare(strict_types=1);

namespace App\Domain\Organization\Enums;

enum OrganizationType: string
{
    case MEDICAL_CENTER = 'medical_center';
    case FITNESS_GYM = 'fitness_gym';
    case STADIUM = 'stadium';

    public function label(): string
    {
        return match ($this) {
            self::MEDICAL_CENTER => 'Медицинский центр',
            self::FITNESS_GYM => 'Фитнес-центр',
            self::STADIUM => 'Стадион',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::MEDICAL_CENTER => 'Клиники, медицинские центры, поликлиники',
            self::FITNESS_GYM => 'Фитнес-клубы, тренажерные залы, спортивные центры',
            self::STADIUM => 'Стадионы, спортивные комплексы, арены',
        };
    }

    public static function forDropdown(): array
    {
        return [
            self::MEDICAL_CENTER->value => self::MEDICAL_CENTER->label(),
            self::FITNESS_GYM->value => self::FITNESS_GYM->label(),
            self::STADIUM->value => self::STADIUM->label(),
        ];
    }
}
