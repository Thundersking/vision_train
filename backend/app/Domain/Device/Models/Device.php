<?php

declare(strict_types=1);

namespace App\Domain\Device\Models;

use App\Domain\Patient\Models\Patient;
use App\Domain\Patient\Models\PatientDevice;
use App\Support\Multitenancy\Traits\HasOrganization;
use App\Support\Traits\HasUuid;
use App\Domain\Shared\Traits\RecordsAuditLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory;
    use HasOrganization;
    use HasUuid;
    use RecordsAuditLog;
    use SoftDeletes;

    protected $fillable = [
        'organization_id',
        'name',
        'type',
        'serial_number',
        'model',
        'manufacturer',
        'firmware_version',
        'last_sync_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'last_sync_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Пациенты, использующие это устройство
     */
    public function patients(): BelongsToMany
    {
        return $this->belongsToMany(Patient::class, 'patient_devices')
            ->withPivot('is_primary', 'assigned_at', 'assigned_by', 'notes')
            ->withTimestamps();
    }

    /**
     * Pivot-записи, связывающие устройство с пациентами
     */
    public function patientDevices(): HasMany
    {
        return $this->hasMany(PatientDevice::class);
    }
}
