<?php

declare(strict_types=1);

namespace App\Domain\ExerciseTemplate\Models;

use App\Support\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExerciseTemplateParameter extends Model
{
    use HasFactory;
    use HasUuid;

    protected $fillable = [
        'exercise_template_id',
        'label',
        'key',
        'target_value',
        'unit',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(ExerciseTemplate::class, 'exercise_template_id');
    }
}
