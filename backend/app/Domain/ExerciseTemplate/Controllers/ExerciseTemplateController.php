<?php

declare(strict_types=1);

namespace App\Domain\ExerciseTemplate\Controllers;

use App\Domain\ExerciseTemplate\Actions\CreateExerciseTemplateAction;
use App\Domain\ExerciseTemplate\Actions\DeleteExerciseTemplateAction;
use App\Domain\ExerciseTemplate\Actions\UpdateExerciseTemplateAction;
use App\Domain\ExerciseTemplate\Repositories\ExerciseTemplateRepository;
use App\Domain\ExerciseTemplate\Requests\ExerciseTemplateSearchRequest;
use App\Domain\ExerciseTemplate\Requests\StoreExerciseTemplateRequest;
use App\Domain\ExerciseTemplate\Requests\UpdateExerciseTemplateRequest;
use App\Domain\ExerciseTemplate\Resources\ExerciseTemplateDetailResource;
use App\Domain\ExerciseTemplate\Resources\ExerciseTemplateListResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Illuminate\Validation\ValidationException;

final class ExerciseTemplateController extends Controller
{
    use AuthorizesRequests;

    public function __construct(private readonly ExerciseTemplateRepository $repository)
    {
    }

    public function index(ExerciseTemplateSearchRequest $request): AnonymousResourceCollection
    {
        $paginator = $this->repository->paginate($request->validated());

        return ExerciseTemplateListResource::collection($paginator);
    }

    public function show(string $uuid): ExerciseTemplateDetailResource
    {
        $template = $this->repository->findWithRelations($uuid, ['type', 'steps', 'parameters']);

        if (!$template) {
            throw new ModelNotFoundException();
        }

        return new ExerciseTemplateDetailResource($template);
    }

    /**
     * @throws ValidationException
     */
    public function store(StoreExerciseTemplateRequest $request, CreateExerciseTemplateAction $action): ExerciseTemplateDetailResource
    {
        $template = $action->execute($request->validated());

        return new ExerciseTemplateDetailResource($template);
    }

    /**
     * @throws ValidationException
     */
    public function update(string $uuid, UpdateExerciseTemplateRequest $request, UpdateExerciseTemplateAction $action): ExerciseTemplateDetailResource
    {
        $template = $this->repository->findWithRelations($uuid, ['type', 'steps', 'parameters']);

        if (!$template) {
            throw new ModelNotFoundException();
        }

        $updated = $action->execute($template, $request->validated());

        return new ExerciseTemplateDetailResource($updated);
    }

    public function destroy(string $uuid, DeleteExerciseTemplateAction $action): JsonResponse
    {
        $template = $this->repository->findByUuid($uuid);

        if (!$template) {
            throw new ModelNotFoundException();
        }

        $action->execute($template);

        return response()->json([
            'message' => 'Запись успешно удалена.',
            'code' => 200,
        ]);
    }
}
