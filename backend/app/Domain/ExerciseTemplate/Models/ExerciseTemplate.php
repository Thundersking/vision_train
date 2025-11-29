<?php

declare(strict_types=1);

namespace App\Domain\ExerciseTemplate\Models;

use App\Domain\Shared\Traits\RecordsAuditLog;
use App\Support\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExerciseTemplate extends Model
{
    use HasFactory;
    use HasUuid;
    use RecordsAuditLog;

    protected $fillable = [
        'exercise_type',
        'name',
        'description',
        'difficulty',
        'duration_seconds',
        'instructions',
        'ball_count',
        'ball_size',
        'target_accuracy_percent',
        'vertical_area',
        'horizontal_area',
        'distance_area',
        'speed',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'duration_seconds' => 'integer',
            'is_active' => 'boolean',
            'ball_count' => 'integer',
            'ball_size' => 'integer',
            'target_accuracy_percent' => 'integer',
        ];
    }
}
