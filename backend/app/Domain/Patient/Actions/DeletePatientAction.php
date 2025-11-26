<?php

declare(strict_types=1);

namespace App\Domain\Patient\Actions;

use App\Domain\Patient\Models\Patient;
use App\Domain\Patient\Repositories\PatientRepository;
use App\Domain\Shared\Enums\AuditActionType;
use App\Domain\Shared\Traits\RecordsAuditLog;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

final class DeletePatientAction
{
    use RecordsAuditLog;

    public function __construct(private readonly PatientRepository $repository) {}

    public function execute(Patient $patient): void
    {
        DB::transaction(function () use ($patient) {
            $deleted = $this->repository->delete($patient->id);

            if (!$deleted) {
                throw new ModelNotFoundException();
            }

            $this->recordAudit(
                action: AuditActionType::DELETED,
                entity: $patient,
            );
        });
    }
}
