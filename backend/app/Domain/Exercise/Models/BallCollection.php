<?php

declare(strict_types=1);

namespace App\Domain\Exercise\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BallCollection extends Model
{
    use HasFactory;

    protected $fillable = [
        'exercise_id',
        'ball_sequence_number',
        'distance_coordinate',
        'horizontal_coordinate',
        'vertical_coordinate',
        'ball_size',
        'accuracy_percent',
        'collection_time_ms',
        'time_from_previous_ms',
    ];

    protected function casts(): array
    {
        return [
            'ball_sequence_number' => 'integer',
            'distance_coordinate' => 'decimal:2',
            'horizontal_coordinate' => 'decimal:2',
            'vertical_coordinate' => 'decimal:2',
            'ball_size' => 'integer',
            'accuracy_percent' => 'decimal:2',
            'collection_time_ms' => 'integer',
            'time_from_previous_ms' => 'integer',
        ];
    }

    public function exercise(): BelongsTo
    {
        return $this->belongsTo(Exercise::class);
    }
}

