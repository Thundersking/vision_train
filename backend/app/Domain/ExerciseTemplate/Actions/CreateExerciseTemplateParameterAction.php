<?php

declare(strict_types=1);

namespace App\Domain\ExerciseTemplate\Actions;

use App\Domain\ExerciseTemplate\Models\ExerciseTemplate;
use App\Domain\ExerciseTemplate\Models\ExerciseTemplateParameter;
use App\Domain\ExerciseTemplate\Repositories\ExerciseTemplateParameterRepository;
use App\Domain\Shared\Enums\AuditActionType;
use App\Domain\Shared\Traits\RecordsAuditLog;
use Illuminate\Support\Str;

final class CreateExerciseTemplateParameterAction
{
    use RecordsAuditLog;

    public function __construct(private readonly ExerciseTemplateParameterRepository $repository)
    {
    }

    public function execute(ExerciseTemplate $template, array $data): ExerciseTemplateParameter
    {
        $label = $data['label'] ?? null;

        $payload = [
            'exercise_template_id' => $template->id,
            'label' => $label,
            'key' => $label ? Str::slug($label) : null,
            'target_value' => $data['target_value'] ?? null,
            'unit' => $data['unit'] ?? null,
        ];

        /** @var ExerciseTemplateParameter $parameter */
        $parameter = $this->repository->create($payload);

        $this->recordAudit(
            action: AuditActionType::CREATED,
            entity: $parameter,
            newData: $parameter->toArray()
        );

        return $parameter;
    }
}
