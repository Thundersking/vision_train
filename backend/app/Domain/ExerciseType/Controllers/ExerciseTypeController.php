<?php

declare(strict_types=1);

namespace App\Domain\ExerciseType\Controllers;

use App\Domain\ExerciseType\Actions\CreateExerciseTypeAction;
use App\Domain\ExerciseType\Actions\DeleteExerciseTypeAction;
use App\Domain\ExerciseType\Actions\UpdateExerciseTypeAction;
use App\Domain\ExerciseType\Repositories\ExerciseTypeRepository;
use App\Domain\ExerciseType\Requests\ExerciseTypeSearchRequest;
use App\Domain\ExerciseType\Requests\StoreExerciseTypeRequest;
use App\Domain\ExerciseType\Requests\UpdateExerciseTypeRequest;
use App\Domain\ExerciseType\Resources\ExerciseTypeDetailResource;
use App\Domain\ExerciseType\Resources\ExerciseTypeListResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Illuminate\Validation\ValidationException;

final class ExerciseTypeController extends Controller
{
    use AuthorizesRequests;

    public function __construct(private readonly ExerciseTypeRepository $repository)
    {
    }

    public function index(ExerciseTypeSearchRequest $request): AnonymousResourceCollection
    {
        $paginator = $this->repository->paginate($request->validated());

        return ExerciseTypeListResource::collection($paginator);
    }

    public function show(string $uuid): ExerciseTypeDetailResource
    {
        $exerciseType = $this->repository->findByUuid($uuid);

        if (!$exerciseType) {
            throw new ModelNotFoundException();
        }

        return new ExerciseTypeDetailResource($exerciseType);
    }

    /**
     * @throws ValidationException
     */
    public function store(StoreExerciseTypeRequest $request, CreateExerciseTypeAction $action): ExerciseTypeDetailResource
    {
        $exerciseType = $action->execute($request->validated());

        return new ExerciseTypeDetailResource($exerciseType);
    }

    /**
     * @throws ValidationException
     */
    public function update(string $uuid, UpdateExerciseTypeRequest $request, UpdateExerciseTypeAction $action): ExerciseTypeDetailResource
    {
        $exerciseType = $this->repository->findByUuid($uuid);

        if (!$exerciseType) {
            throw new ModelNotFoundException();
        }

        $updated = $action->execute($exerciseType, $request->validated());

        return new ExerciseTypeDetailResource($updated);
    }

    /**
     * @throws ValidationException
     */
    public function destroy(string $uuid, DeleteExerciseTypeAction $action): JsonResponse
    {
        $exerciseType = $this->repository->findByUuid($uuid);

        if (!$exerciseType) {
            throw new ModelNotFoundException();
        }

        $action->execute($exerciseType);

        return response()->json([
            'message' => 'Запись успешно удалена.',
            'code' => 200,
        ]);
    }

    public function allList(): JsonResponse
    {
        $items = $this->repository->allList('name', function ($item) {
            return [
                'id' => $item->id,
                'uuid' => $item->uuid,
                'name' => $item->name,
                'dimension' => $item->dimension,
            ];
        });

        return response()->json([
            'data' => $items,
        ]);
    }
}
