<?php

declare(strict_types=1);

namespace App\Domain\Patient\Actions;

use App\Domain\Patient\Events\PatientRegistered;
use App\Domain\Patient\Guards\PatientGuard;
use App\Domain\Patient\Models\Patient;
use App\Domain\Patient\Repositories\PatientRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final readonly class CreatePatient
{
    public function __construct(
        private PatientRepository $repository,
        private PatientGuard $guard
    ) {}

    /**
     * @throws ValidationException
     */
    public function execute(array $data): Patient
    {
//        $this->guard->ensureHandSizeValid($data['hand_size_cm']);
//        $this->guard->ensureDoctorFromSameOrganization($data['user_id'], $data['organization_id']);
        \Log::info('create patient 1', ['data' => $data]);

//        if (!empty($data['email'])) {
//            $this->guard->ensureEmailUniqueInOrganization($data['email'], $data['organization_id']);
//        }

        return DB::transaction(function () use ($data) {
            $data['is_active'] = true;

            $patient = new Patient($data);
            $this->repository->save($patient);

            event(new PatientRegistered($patient));

            return $patient->fresh();
        });
    }
}
