<?php

declare(strict_types=1);

namespace App\Domain\ExerciseTemplate\Controllers;

use App\Domain\ExerciseTemplate\Actions\CreateExerciseTemplateParameterAction;
use App\Domain\ExerciseTemplate\Actions\DeleteExerciseTemplateParameterAction;
use App\Domain\ExerciseTemplate\Actions\UpdateExerciseTemplateParameterAction;
use App\Domain\ExerciseTemplate\Models\ExerciseTemplate;
use App\Domain\ExerciseTemplate\Models\ExerciseTemplateParameter;
use App\Domain\ExerciseTemplate\Repositories\ExerciseTemplateParameterRepository;
use App\Domain\ExerciseTemplate\Repositories\ExerciseTemplateRepository;
use App\Domain\ExerciseTemplate\Requests\StoreExerciseTemplateParameterRequest;
use App\Domain\ExerciseTemplate\Requests\UpdateExerciseTemplateParameterRequest;
use App\Domain\ExerciseTemplate\Resources\ExerciseTemplateParameterResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;

final class ExerciseTemplateParameterController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private readonly ExerciseTemplateRepository $templateRepository,
        private readonly ExerciseTemplateParameterRepository $parameterRepository,
    ) {
    }

    public function index(string $templateUuid): AnonymousResourceCollection
    {
        $template = $this->templateRepository->findByUuid($templateUuid);

        if (!$template) {
            throw new ModelNotFoundException();
        }

        $parameters = $this->parameterRepository->listByTemplateId((int)$template->id);

        return ExerciseTemplateParameterResource::collection($parameters);
    }

    public function store(
        string $templateUuid,
        StoreExerciseTemplateParameterRequest $request,
        CreateExerciseTemplateParameterAction $action
    ): ExerciseTemplateParameterResource {
        /** @var ExerciseTemplate $template */
        $template = $this->templateRepository->findByUuid($templateUuid);

        if (!$template) {
            throw new ModelNotFoundException();
        }

        $parameter = $action->execute($template, $request->validated());

        return new ExerciseTemplateParameterResource($parameter);
    }

    public function update(
        string $parameterUuid,
        UpdateExerciseTemplateParameterRequest $request,
        UpdateExerciseTemplateParameterAction $action
    ): ExerciseTemplateParameterResource {
        $parameter = $this->parameterRepository->findByUuid($parameterUuid);

        if (!$parameter) {
            throw new ModelNotFoundException();
        }

        $updated = $action->execute($parameter, $request->validated());

        return new ExerciseTemplateParameterResource($updated);
    }

    public function destroy(
        string $parameterUuid,
        DeleteExerciseTemplateParameterAction $action
    ): JsonResponse {
        $parameter = $this->parameterRepository->findByUuid($parameterUuid);

        if (!$parameter) {
            throw new ModelNotFoundException();
        }

        $action->execute($parameter);

        return response()->json([
            'message' => 'Запись успешно удалена',
            'code' => 200,
        ]);
    }
}
