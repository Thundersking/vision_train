<?php

declare(strict_types=1);

namespace App\Domain\Patient\Enums;

enum PatientExaminationType: string
{
    case Primary = 'primary';
    case Intermediate = 'intermediate';
    case Final = 'final';

    public function getDisplayName(): string
    {
        return match ($this) {
            self::Primary => 'Первичное обследование',
            self::Intermediate => 'Промежуточное обследование',
            self::Final => 'Заключительное обследование',
        };
    }

    public static function options(): array
    {
        return [
            self::Primary->value => self::Primary->getDisplayName(),
            self::Intermediate->value => self::Intermediate->getDisplayName(),
            self::Final->value => self::Final->getDisplayName(),
        ];
    }
}