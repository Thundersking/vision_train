<?php

declare(strict_types=1);

namespace App\Domain\Patient\Repositories;

use App\Domain\Patient\Models\PatientDevice;
use App\Support\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

final class PatientDeviceRepository extends BaseRepository
{
    protected static function modelClass(): string
    {
        return PatientDevice::class;
    }

    protected function newQuery(): Builder
    {
        return parent::newQuery()->with(['patient', 'device', 'assignedBy']);
    }

    protected function applyFilters(Builder $query, array $filters): void
    {
        if (!empty($filters['patient_id'])) {
            $query->where('patient_id', (int) $filters['patient_id']);
        }

        if (!empty($filters['device_id'])) {
            $query->where('device_id', (int) $filters['device_id']);
        }
    }

    public function listForPatient(int $patientId): Collection
    {
        return $this->newQuery()
            ->where('patient_id', $patientId)
            ->orderByDesc('assigned_at')
            ->get();
    }

    public function clearPrimaryFlag(int $patientId): void
    {
        $this->newQuery()
            ->where('patient_id', $patientId)
            ->update(['is_primary' => false]);
    }

    public function findByPatientAndDevice(int $patientId, int $deviceId, bool $withTrashed = false): ?PatientDevice
    {
        $query = $this->newQuery();

        if ($withTrashed) {
            $query->withTrashed();
        }

        return $query
            ->where('patient_id', $patientId)
            ->where('device_id', $deviceId)
            ->first();
    }

    public function patientHasDevices(int $patientId): bool
    {
        return $this->newQuery()
            ->where('patient_id', $patientId)
            ->exists();
    }
}
