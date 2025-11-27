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
        'payload_json',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'payload_json' => 'array',
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
}
