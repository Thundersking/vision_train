<?php

declare(strict_types=1);

namespace App\Domain\Patient\Models;

use App\Domain\Device\Models\Device;
use App\Domain\Shared\Traits\RecordsAuditLog;
use App\Domain\User\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PatientDevice extends Model
{
    use HasFactory;
    use RecordsAuditLog;
    use SoftDeletes;

    protected $table = 'patient_devices';

    protected $fillable = [
        'patient_id',
        'device_id',
        'is_primary',
        'assigned_at',
        'assigned_by',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
            'assigned_at' => 'datetime',
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
