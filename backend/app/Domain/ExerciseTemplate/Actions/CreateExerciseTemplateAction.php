<?php

declare(strict_types=1);

namespace App\Domain\ExerciseTemplate\Actions;

use App\Domain\ExerciseTemplate\Models\ExerciseTemplate;
use App\Domain\ExerciseTemplate\Repositories\ExerciseTemplateParameterRepository;
use App\Domain\ExerciseTemplate\Repositories\ExerciseTemplateRepository;
use App\Domain\ExerciseTemplate\Repositories\ExerciseTemplateStepRepository;
use App\Domain\ExerciseTemplate\Traits\NormalizesExerciseTemplatePayload;
use App\Domain\Shared\Enums\AuditActionType;
use App\Domain\Shared\Traits\RecordsAuditLog;
use Illuminate\Support\Facades\DB;

final class CreateExerciseTemplateAction
{
    use RecordsAuditLog;
    use NormalizesExerciseTemplatePayload;

    public function __construct(
        private readonly ExerciseTemplateRepository $repository,
        private readonly ExerciseTemplateStepRepository $stepRepository,
        private readonly ExerciseTemplateParameterRepository $parameterRepository,
    ) {
    }

    public function execute(array $data): ExerciseTemplate
    {
        $payload = $this->preparePayload($data);

        $template = DB::transaction(function () use ($payload) {
            /** @var ExerciseTemplate $template */
            $template = $this->repository->create($payload['template']);

            $this->stepRepository->sync($template->id, $payload['steps']);
            $this->parameterRepository->sync($template->id, $payload['parameters']);

            return $this->repository->findWithRelations($template->uuid, ['type', 'steps', 'parameters']) ?? $template;
        });

        $this->recordAudit(
            action: AuditActionType::CREATED,
            entity: $template,
            newData: $template->toArray()
        );

        return $template;
    }

    private function preparePayload(array $data): array
    {
        $steps = $this->normalizeSteps($data['steps'] ?? []);
        $parameters = $this->normalizeParameters($data['parameters'] ?? []);
        $duration = $this->calculateDuration($steps, isset($data['duration_seconds']) ? (int)$data['duration_seconds'] : null) ?? 0;

        return [
            'template' => [
                'exercise_type_id' => $data['exercise_type_id'],
                'title' => $data['title'],
                'short_description' => $data['short_description'] ?? null,
                'difficulty' => $data['difficulty'] ?? null,
                'duration_seconds' => $duration,
                'instructions' => $data['instructions'] ?? null,
                'extra_payload_json' => $this->normalizeExtraPayload($data['extra_payload'] ?? null),
                'is_active' => array_key_exists('is_active', $data) ? (bool)$data['is_active'] : true,
            ],
            'steps' => $steps,
            'parameters' => $parameters,
        ];
    }
}
