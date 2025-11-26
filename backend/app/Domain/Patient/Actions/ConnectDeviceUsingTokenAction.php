<?php

declare(strict_types=1);

namespace App\Domain\Patient\Actions;

use App\Domain\Device\Repositories\DeviceRepository;
use App\Domain\Patient\Models\ConnectionToken;
use App\Domain\Patient\Repositories\ConnectionTokenRepository;
use App\Domain\Patient\Repositories\PatientDeviceRepository;
use App\Domain\Shared\Enums\AuditActionType;
use App\Domain\Shared\Traits\RecordsAuditLog;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class ConnectDeviceUsingTokenAction
{
    use RecordsAuditLog;

    public function __construct(
        private readonly ConnectionTokenRepository $connectionTokens,
        private readonly DeviceRepository $devices,
        private readonly PatientDeviceRepository $patientDevices,
    ) {
    }

    public function execute(ConnectionToken $token, array $payload)
    {
        $patient = $token->patient;

        if (!$patient) {
            throw new ModelNotFoundException('Пациент не найден для токена');
        }

        return DB::transaction(function () use ($patient, $token, $payload) {
            $device = $this->devices->findByIdentifier($payload['device_identifier']);
            $deviceExists = (bool) $device;

            $deviceAttributes = [
                'organization_id' => $patient->organization_id,
                'name' => $payload['device_name'] ?? ('Устройство ' . Str::upper(Str::random(4))),
                'type' => $payload['device_type'],
                'serial_number' => $payload['device_identifier'],
                'model' => $payload['model'] ?? null,
                'manufacturer' => $payload['manufacturer'] ?? null,
                'firmware_version' => $payload['firmware_version'] ?? $payload['app_version'] ?? null,
                'last_sync_at' => now(),
                'is_active' => true,
            ];

            if ($device) {
                $device->fill($deviceAttributes);
                $device->save();
            } else {
                $device = $this->devices->create($deviceAttributes);
            }

            $assignment = $this->patientDevices->findByPatientAndDevice($patient->id, $device->id, true);

            $assignedBy = $token->created_by ?? $patient->user_id;

            $assignmentExists = (bool) $assignment;

            if ($assignment) {
                if ($assignment->trashed()) {
                    $assignment->restore();
                }

                $assignment->fill([
                    'assigned_at' => now(),
                    'assigned_by' => $assignedBy,
                    'notes' => $payload['notes'] ?? $assignment->notes,
                ])->save();
            } else {
                $assignment = $this->patientDevices->create([
                    'patient_id' => $patient->id,
                    'device_id' => $device->id,
                    'is_primary' => !$this->patientDevices->patientHasDevices($patient->id),
                    'assigned_at' => now(),
                    'assigned_by' => $assignedBy,
                    'notes' => $payload['notes'] ?? null,
                ]);
            }

            $this->connectionTokens->markUsed($token);

            $this->recordAudit(
                action: $deviceExists ? AuditActionType::UPDATED : AuditActionType::CREATED,
                entity: $device,
                newData: $deviceAttributes
            );

            $this->recordAudit(
                action: $assignmentExists ? AuditActionType::UPDATED : AuditActionType::CREATED,
                entity: $assignment,
                newData: $assignment->toArray()
            );

            return $assignment->load(['device', 'patient', 'assignedBy']);
        });
    }
}
