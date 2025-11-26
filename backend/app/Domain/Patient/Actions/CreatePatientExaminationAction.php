<?php

declare(strict_types=1);

namespace App\Domain\Patient\Actions;

use App\Domain\Patient\Models\Patient;
use App\Domain\Patient\Models\PatientExamination;
use App\Domain\Patient\Repositories\PatientExaminationRepository;
use App\Domain\Shared\Enums\AuditActionType;
use App\Domain\Shared\Traits\RecordsAuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

final class CreatePatientExaminationAction
{
    use RecordsAuditLog;

    public function __construct(private readonly PatientExaminationRepository $repository) {}

    public function execute(Patient $patient, array $data): PatientExamination
    {
        return DB::transaction(function () use ($patient, $data) {
            $userId = Auth::id();

            if (!$userId) {
                $userId = $patient->user_id;
            }

            $payload = array_merge($data, [
                'patient_id' => $patient->id,
                'user_id' => $userId,
            ]);

            $examination = $this->repository->create($payload);

            $this->recordAudit(
                action: AuditActionType::CREATED,
                entity: $examination,
                newData: $payload
            );

            return $examination;
        });
    }
}
