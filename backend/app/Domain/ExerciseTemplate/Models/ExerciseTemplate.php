<?php

declare(strict_types=1);

namespace App\Domain\ExerciseTemplate\Models;

use App\Domain\ExerciseType\Models\ExerciseType;
use App\Domain\Organization\Models\Organization;
use App\Domain\Shared\Traits\RecordsAuditLog;
use App\Support\Multitenancy\Traits\HasOrganization;
use App\Support\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExerciseTemplate extends Model
{
    use HasFactory;
    use HasOrganization;
    use HasUuid;
    use RecordsAuditLog;

    protected $fillable = [
        'organization_id',
        'exercise_type_id',
        'title',
        'short_description',
        'difficulty',
        'duration_seconds',
        'instructions',
        'extra_payload_json',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'extra_payload_json' => 'array',
            'duration_seconds' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(ExerciseType::class, 'exercise_type_id');
    }

    public function steps()
    {
        return $this->hasMany(ExerciseTemplateStep::class, 'exercise_template_id')->orderBy('step_order');
    }

    public function parameters()
    {
        return $this->hasMany(ExerciseTemplateParameter::class, 'exercise_template_id');
    }
}
