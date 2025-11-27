<?php

declare(strict_types=1);

namespace App\Domain\ExerciseTemplate\Enums;

enum ExerciseParameterUnit: string
{
    case DEGREES = 'deg';
    case SECONDS = 'sec';
    case MILLISECONDS = 'ms';
    case HERTZ = 'hz';
    case MILLIMETERS = 'mm';
    case PERCENT = 'percent';
    case COUNT = 'count';
    case SCORE = 'score';

    public function label(): string
    {
        return match ($this) {
            self::DEGREES => 'Градусы',
            self::SECONDS => 'Секунды',
            self::MILLISECONDS => 'Миллисекунды',
            self::HERTZ => 'Герцы',
            self::MILLIMETERS => 'Миллиметры',
            self::PERCENT => 'Проценты',
            self::COUNT => 'Количество',
            self::SCORE => 'Баллы',
        };
    }
}
