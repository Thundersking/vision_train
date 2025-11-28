<?php

declare(strict_types=1);

namespace App\Domain\ExerciseTemplate\Enums;

enum ExerciseDimension: string
{
    case TWO_D = '2d';
    case THREE_D = '3d';

    public function label(): string
    {
        return match ($this) {
            self::TWO_D => '2D',
            self::THREE_D => '3D',
        };
    }
}
