<?php

declare(strict_types=1);

namespace App\Domain\Patient\Actions;

use App\Domain\Patient\Events\PatientUpdated;
use App\Domain\Patient\Guards\PatientGuard;
use App\Domain\Patient\Models\Patient;
use App\Domain\Patient\Queries\PatientQueries;
use App\Domain\Patient\Repositories\PatientRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final readonly class UpdatePatient
{
    public function __construct(
        private PatientRepository $repository,
        private PatientQueries $patientQueries,
        private PatientGuard $guard
    ) {}

    /**
     * @throws ValidationException
     */
    public function execute(string $patientUuid, array $data): Patient
    {
        $patient = $this->patientQueries->findByUuid($patientUuid);

        if (!$patient) {
            throw new \InvalidArgumentException("Пациент с UUID {$patientUuid} не найден");
        }

        if (isset($data['hand_size_cm'])) {
            $this->guard->ensureHandSizeValid($data['hand_size_cm']);
        }

        if (isset($data['user_id'])) {
            $this->guard->ensureDoctorFromSameOrganization($data['user_id'], $patient->organization_id);
        }

        if (!empty($data['email']) && mb_strtolower($data['email']) !== mb_strtolower($patient->email)) {
            $this->guard->ensureEmailUniqueInOrganizationExceptPatient(
                $data['email'],
                $patient->organization_id,
                $patient->uuid
            );
        }

        return DB::transaction(function () use ($patient, $data) {
            $oldData = $patient->toArray();

            $patient->fill($data);
            $this->repository->save($patient);

            event(new PatientUpdated($patient, $oldData));

            return $patient->fresh();
        });
    }
}
