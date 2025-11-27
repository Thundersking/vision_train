<?php

declare(strict_types=1);

namespace App\Domain\ExerciseTemplate\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExerciseTemplateStep extends Model
{
    use HasFactory;

    protected $fillable = [
        'exercise_template_id',
        'step_order',
        'title',
        'duration',
        'description',
        'hint',
    ];

    protected function casts(): array
    {
        return [
            'step_order' => 'integer',
            'duration' => 'integer',
        ];
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(ExerciseTemplate::class, 'exercise_template_id');
    }
}
