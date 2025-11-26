<?php

declare(strict_types=1);

namespace App\Domain\Patient\Controllers;

use App\Domain\Patient\Actions\CreatePatientExaminationAction;
use App\Domain\Patient\Repositories\PatientExaminationRepository;
use App\Domain\Patient\Repositories\PatientRepository;
use App\Domain\Patient\Requests\PatientExaminationSearchRequest;
use App\Domain\Patient\Requests\StorePatientExaminationRequest;
use App\Domain\Patient\Resources\PatientExaminationResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;

final class PatientExaminationController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private readonly PatientRepository $patientRepository,
        private readonly PatientExaminationRepository $examinationRepository
    ) {}

    public function index(string $patientUuid, PatientExaminationSearchRequest $request): AnonymousResourceCollection
    {
        $patient = $this->patientRepository->findWithRelations($patientUuid);

        if (!$patient) {
            throw new ModelNotFoundException();
        }

        $filters = $request->validated();

        if (isset($filters['doctor_id'])) {
            $filters['user_id'] = $filters['doctor_id'];
            unset($filters['doctor_id']);
        }

        $paginator = $this->examinationRepository->paginateForPatient($patient->id, $filters);

        return PatientExaminationResource::collection($paginator);
    }

    public function store(
        string $patientUuid,
        StorePatientExaminationRequest $request,
        CreatePatientExaminationAction $action
    ): PatientExaminationResource {
        $patient = $this->patientRepository->findWithRelations($patientUuid);

        if (!$patient) {
            throw new ModelNotFoundException();
        }

        $examination = $action->execute($patient, $request->validated());
        $examination->load(['patient', 'doctor']);

        return new PatientExaminationResource($examination);
    }
}
