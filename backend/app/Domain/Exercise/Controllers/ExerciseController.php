<?php

declare(strict_types=1);

namespace App\Domain\Exercise\Controllers;

use App\Domain\Exercise\Actions\CreateExerciseAction;
use App\Domain\Exercise\Actions\DeleteExerciseAction;
use App\Domain\Exercise\Actions\UpdateExerciseAction;
use App\Domain\Exercise\Models\Exercise;
use App\Domain\Exercise\Repositories\ExerciseRepository;
use App\Domain\Exercise\Requests\ExerciseSearchRequest;
use App\Domain\Exercise\Requests\StoreExerciseRequest;
use App\Domain\Exercise\Requests\UpdateExerciseRequest;
use App\Domain\Exercise\Resources\ExerciseDetailResource;
use App\Domain\Exercise\Resources\ExerciseListResource;
use App\Domain\Patient\Repositories\PatientRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Illuminate\Validation\ValidationException;

final class ExerciseController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private readonly ExerciseRepository $repository,
        private readonly PatientRepository $patientRepository
    ) {
    }

    public function index(ExerciseSearchRequest $request): AnonymousResourceCollection
    {
        $paginator = $this->repository->paginate($request->validated());

        return ExerciseListResource::collection($paginator);
    }

    public function show(string $uuid): ExerciseDetailResource
    {
        $exercise = $this->repository->findWithRelations($uuid, ['patient', 'template', 'ballCollections']);

        if (!$exercise) {
            throw new ModelNotFoundException();
        }

        return new ExerciseDetailResource($exercise);
    }

    /**
     * @throws ValidationException
     */
    public function store(StoreExerciseRequest $request, CreateExerciseAction $action): ExerciseDetailResource
    {
        $exercise = $action->execute($request->validated());

        return new ExerciseDetailResource($exercise->load(['patient', 'template']));
    }

    /**
     * @throws ValidationException
     */
    public function update(string $uuid, UpdateExerciseRequest $request, UpdateExerciseAction $action): ExerciseDetailResource
    {
        $exercise = $this->repository->findByUuid($uuid);

        if (!$exercise) {
            throw new ModelNotFoundException();
        }

        $updated = $action->execute($exercise, $request->validated());

        return new ExerciseDetailResource($updated->load(['patient', 'template']));
    }

    public function destroy(string $uuid, DeleteExerciseAction $action): JsonResponse
    {
        $exercise = $this->repository->findByUuid($uuid);

        if (!$exercise) {
            throw new ModelNotFoundException();
        }

        $action->execute($exercise);

        return response()->json([
            'message' => 'Запись успешно удалена',
            'code' => 200,
        ]);
    }

    public function indexByPatient(string $patientUuid, ExerciseSearchRequest $request): AnonymousResourceCollection
    {
        $patient = $this->patientRepository->findByUuid($patientUuid);

        if (!$patient) {
            throw new ModelNotFoundException();
        }

        $filters = $request->validated();
        $filters['patient_id'] = $patient->id;

        $paginator = $this->repository->paginate($filters);

        return ExerciseListResource::collection($paginator);
    }
}

