<?php

declare(strict_types=1);

namespace App\Domain\ExerciseTemplate\Actions;

use App\Domain\ExerciseTemplate\Models\ExerciseTemplateParameter;
use App\Domain\ExerciseTemplate\Repositories\ExerciseTemplateParameterRepository;
use App\Domain\Shared\Enums\AuditActionType;
use App\Domain\Shared\Traits\RecordsAuditLog;
use Illuminate\Support\Str;

final class UpdateExerciseTemplateParameterAction
{
    use RecordsAuditLog;

    public function __construct(private readonly ExerciseTemplateParameterRepository $repository)
    {
    }

    public function execute(ExerciseTemplateParameter $parameter, array $data): ExerciseTemplateParameter
    {
        $label = $data['label'];

        $payload = [
            'label' => $label,
            'key' => Str::slug($label),
            'target_value' => $data['target_value'] ?? null,
            'unit' => $data['unit'] ?? null,
        ];

        $oldData = $parameter->toArray();

        /** @var ExerciseTemplateParameter $updated */
        $updated = $this->repository->update($parameter->id, $payload);

        $this->recordAudit(
            action: AuditActionType::UPDATED,
            entity: $updated,
            oldData: $oldData,
            newData: $updated->toArray()
        );

        return $updated;
    }
}
