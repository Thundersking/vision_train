<?php

declare(strict_types=1);

namespace App\Domain\ExerciseType\Models;

use App\Domain\ExerciseTemplate\Models\ExerciseTemplate;
use App\Domain\Shared\Traits\RecordsAuditLog;
use App\Support\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExerciseType extends Model
{
    use HasFactory;
    use HasUuid;
    use RecordsAuditLog;

    protected $fillable = [
        'name',
        'slug',
        'dimension',
        'is_customizable',
        'description',
        'metrics_json',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'metrics_json' => 'array',
            'is_active' => 'boolean',
            'is_customizable' => 'boolean',
        ];
    }

    public function templates(): HasMany
    {
        return $this->hasMany(ExerciseTemplate::class, 'exercise_type_id');
    }
}
