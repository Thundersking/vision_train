<?php

declare(strict_types=1);

namespace App\Domain\Patient\Models;

use App\Support\Multitenancy\Traits\HasOrganization;
use App\Support\Traits\HasUuid;
use App\Domain\Shared\Traits\RecordsAuditLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class PatientExamination extends Model
{
    use HasFactory;
    use HasOrganization;
    use HasUuid;
    use RecordsAuditLog;
    use SoftDeletes;

    protected $fillable = [
        'organization_id',
        'patient_id',
        'user_id',
        'type',
        'title',
        'description',
        'examination_date',
        'results',
        'recommendations',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'examination_date' => 'datetime',
            'results' => 'json',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Отношение к пациенту
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Отношение к врачу, проводившему обследование
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(\App\Domain\User\Models\User::class, 'user_id');
    }
}
