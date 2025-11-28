<?php

declare(strict_types=1);

namespace App\Domain\Exercise\Models;

use App\Domain\ExerciseTemplate\Enums\ExerciseDimension;
use App\Domain\ExerciseTemplate\Models\ExerciseTemplate;
use App\Domain\Patient\Models\Patient;
use App\Domain\Shared\Traits\RecordsAuditLog;
use App\Support\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exercise extends Model
{
    use HasFactory;
    use HasUuid;
    use RecordsAuditLog;

    protected $fillable = [
        'patient_id',
        'exercise_type',
        'exercise_template_id',
        'ball_count',
        'ball_size',
        'target_accuracy_percent',
        'vertical_area',
        'horizontal_area',
        'distance_area',
        'speed',
        'duration_seconds',
        'fatigue_right_eye',
        'fatigue_left_eye',
        'fatigue_head',
        'patient_decision',
        'notes',
        'started_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'exercise_type' => ExerciseDimension::class,
            'ball_count' => 'integer',
            'ball_size' => 'integer',
            'target_accuracy_percent' => 'integer',
            'duration_seconds' => 'integer',
            'fatigue_right_eye' => 'integer',
            'fatigue_left_eye' => 'integer',
            'fatigue_head' => 'integer',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(ExerciseTemplate::class, 'exercise_template_id');
    }

    public function ballCollections(): HasMany
    {
        return $this->hasMany(BallCollection::class);
    }

    public function getExerciseTypeLabelAttribute(): string
    {
        return $this->exercise_type?->label() ?? '';
    }
}

