<?php

declare(strict_types=1);

namespace App\Domain\Patient\Actions;

use App\Domain\Patient\Guards\PatientGuard;
use App\Domain\Patient\Models\Patient;
use App\Domain\Patient\Repositories\PatientRepository;
use App\Domain\Shared\Enums\AuditActionType;
use App\Domain\Shared\Traits\RecordsAuditLog;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class UpdatePatientAction
{
    use RecordsAuditLog;

    public function __construct(
        private readonly PatientRepository $repository,
        private readonly PatientGuard $guard
    ) {}

    /**
     * @throws ValidationException
     */
    public function execute(Patient $patient, array $data): Patient
    {
        if (!empty($data['email']) && $data['email'] !== $patient->email) {
            $this->guard->ensureEmailUniqueExceptUuid($data['email'], $patient->uuid);
        }

        return DB::transaction(function () use ($patient, $data) {
            $oldData = $patient->toArray();

            $updatedPatient = $this->repository->update($patient->id, $data);

            if (!$updatedPatient) {
                throw new ModelNotFoundException();
            }

            $this->recordAudit(
                action: AuditActionType::UPDATED,
                entity: $patient,
                oldData: $oldData,
                newData: $updatedPatient->toArray()
            );

            return $updatedPatient;
        });
    }
}
