<?php

declare(strict_types=1);

namespace App\Domain\Patient\Actions;

use App\Domain\Device\Models\Device;
use App\Domain\Patient\Models\Patient;
use App\Domain\Patient\Repositories\PatientDeviceRepository;
use App\Domain\Shared\Enums\AuditActionType;
use App\Domain\Shared\Traits\RecordsAuditLog;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

final class DetachPatientDeviceAction
{
    use RecordsAuditLog;

    public function __construct(private readonly PatientDeviceRepository $repository)
    {
    }

    public function execute(Patient $patient, Device $device): void
    {
        $assignment = $this->repository->findByPatientAndDevice($patient->id, $device->id);

        if (!$assignment) {
            throw new ModelNotFoundException();
        }

        DB::transaction(function () use ($assignment, $device) {
            $oldData = $assignment->toArray();

            if ($assignment->is_primary) {
                $assignment->is_primary = false;
                $assignment->save();
            }

            $assignment->delete();

            $device->update([
                'is_active' => false,
                'last_sync_at' => now(),
            ]);

            $this->recordAudit(
                action: AuditActionType::DELETED,
                entity: $assignment,
                oldData: $oldData
            );
        });
    }
}
