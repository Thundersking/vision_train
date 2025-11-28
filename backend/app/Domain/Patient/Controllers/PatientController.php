<?php

declare(strict_types=1);

namespace App\Domain\Patient\Controllers;

use App\Domain\Patient\Actions\CreatePatientAction;
use App\Domain\Patient\Actions\DeletePatientAction;
use App\Domain\Patient\Actions\UpdatePatientAction;
use App\Domain\Patient\Repositories\PatientRepository;
use App\Domain\Patient\Requests\PatientSearchRequest;
use App\Domain\Patient\Requests\StorePatientRequest;
use App\Domain\Patient\Requests\UpdatePatientRequest;
use App\Domain\Patient\Resources\PatientDetailResource;
use App\Domain\Patient\Resources\PatientListResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Illuminate\Validation\ValidationException;

final class PatientController extends Controller
{
    use AuthorizesRequests;

    public function __construct(private readonly PatientRepository $repository) {}

    public function index(PatientSearchRequest $request): AnonymousResourceCollection
    {
        $paginator = $this->repository->paginate($request->validated());

        return PatientListResource::collection($paginator);
    }

    /**
     * Поиск пациентов для автокомплита (возвращает только id и full_name)
     */
    public function search(PatientSearchRequest $request): JsonResponse
    {
        $filters = $request->validated();
        
        $patients = $this->repository->get($filters)
            ->take(20)
            ->map(function ($patient) {
                return [
                    'id' => $patient->id,
                    'full_name' => $patient->name,
                ];
            });

        return response()->json([
            'data' => $patients,
        ]);
    }

    public function show(string $uuid): PatientDetailResource
    {
        $patient = $this->repository->findWithRelations($uuid, ['doctor', 'organization']);

        if (!$patient) {
            throw new ModelNotFoundException();
        }

        return new PatientDetailResource($patient);
    }

    /**
     * @throws ValidationException
     */
    public function store(StorePatientRequest $request, CreatePatientAction $action): PatientDetailResource
    {
        $patient = $action->execute($request->validated());

        $patient->load(['doctor', 'organization']);

        return new PatientDetailResource($patient);
    }

    /**
     * @throws ValidationException
     */
    public function update(string $uuid, UpdatePatientRequest $request, UpdatePatientAction $action): PatientDetailResource
    {
        $patient = $this->repository->findWithRelations($uuid, ['doctor', 'organization']);

        if (!$patient) {
            throw new ModelNotFoundException();
        }

        $updatedPatient = $action->execute($patient, $request->validated());

        $updatedPatient->load(['doctor', 'organization']);

        return new PatientDetailResource($updatedPatient);
    }

    public function destroy(string $uuid, DeletePatientAction $action): JsonResponse
    {
        $patient = $this->repository->findWithRelations($uuid);

        if (!$patient) {
            throw new ModelNotFoundException();
        }

        $action->execute($patient);

        return response()->json([
            'message' => 'Запись успешно удалена.',
            'code' => 200,
        ]);
    }
}
