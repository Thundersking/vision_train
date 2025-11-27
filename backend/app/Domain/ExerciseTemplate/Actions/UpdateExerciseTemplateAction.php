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

final class UpdateExerciseTemplateAction
{
    use RecordsAuditLog;
    use NormalizesExerciseTemplatePayload;

    public function __construct(
        private readonly ExerciseTemplateRepository $repository,
        private readonly ExerciseTemplateStepRepository $stepRepository,
        private readonly ExerciseTemplateParameterRepository $parameterRepository,
    ) {
    }

    public function execute(ExerciseTemplate $template, array $data): ExerciseTemplate
    {
        $template->loadMissing(['steps', 'parameters']);
        $oldData = $template->toArray();

        $payload = $this->preparePayload($data, $template);

        $updated = DB::transaction(function () use ($template, $payload) {
            if (!empty($payload['template'])) {
                $this->repository->update($template->id, $payload['template']);
            }

            $this->stepRepository->sync($template->id, $payload['steps']);
            $this->parameterRepository->sync($template->id, $payload['parameters']);

            return $this->repository->findWithRelations($template->uuid, ['type', 'steps', 'parameters']) ?? $template->refresh();
        });

        $this->recordAudit(
            action: AuditActionType::UPDATED,
            entity: $updated,
            oldData: $oldData,
            newData: $updated->toArray()
        );

        return $updated;
    }

    private function preparePayload(array $data, ExerciseTemplate $template): array
    {
        $payload = [];

        foreach ([
            'exercise_type_id',
            'title',
            'short_description',
            'difficulty',
            'instructions',
        ] as $field) {
            if (array_key_exists($field, $data)) {
                $payload[$field] = $data[$field];
            }
        }

        if (array_key_exists('is_active', $data)) {
            $payload['is_active'] = (bool)$data['is_active'];
        }

        $steps = $this->normalizeSteps($data['steps'] ?? $template->steps->toArray());
        $parameters = $this->normalizeParameters($data['parameters'] ?? $template->parameters->toArray());

        $payload['duration_seconds'] = $this->calculateDuration(
            $steps,
            $data['duration_seconds'] ?? $template->duration_seconds
        ) ?? 0;

        if (array_key_exists('extra_payload', $data)) {
            $payload['extra_payload_json'] = $this->normalizeExtraPayload($data['extra_payload']);
        }

        return [
            'template' => $payload,
            'steps' => $steps,
            'parameters' => $parameters,
        ];
    }
}
