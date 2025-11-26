<?php

declare(strict_types=1);

namespace App\Domain\Patient\Actions;

use App\Domain\Patient\Guards\PatientGuard;
use App\Domain\Patient\Models\Patient;
use App\Domain\Patient\Repositories\PatientRepository;
use App\Domain\Shared\Enums\AuditActionType;
use App\Domain\Shared\Traits\RecordsAuditLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class CreatePatientAction
{
    use RecordsAuditLog;

    public function __construct(
        private readonly PatientRepository $repository,
        private readonly PatientGuard $guard
    ) {}

    /**
     * @throws ValidationException
     */
    public function execute(array $data): Patient
    {
        $this->guard->ensureEmailUnique($data['email'] ?? null);

        return DB::transaction(function () use ($data) {
            $patient = $this->repository->create($data);

            $this->recordAudit(
                action: AuditActionType::CREATED,
                entity: $patient,
                newData: $patient->toArray()
            );

            return $patient;
        });
    }
}
