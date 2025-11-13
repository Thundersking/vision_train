<?php

declare(strict_types=1);

namespace App\Domain\Patient\Enums;

enum DeviceType: string
{
    case OfficeDevice = 'office_device';
    case PersonalDevice = 'personal_device';

    public function getDisplayName(): string
    {
        return match ($this) {
            self::OfficeDevice => 'Офисное устройство',
            self::PersonalDevice => 'Персональное устройство',
        };
    }

    public static function options(): array
    {
        return [
            self::OfficeDevice->value => self::OfficeDevice->getDisplayName(),
            self::PersonalDevice->value => self::PersonalDevice->getDisplayName(),
        ];
    }
}