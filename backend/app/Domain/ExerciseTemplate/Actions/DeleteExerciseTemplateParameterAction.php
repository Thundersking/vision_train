<?php

declare(strict_types=1);

namespace App\Domain\ExerciseTemplate\Actions;

use App\Domain\ExerciseTemplate\Models\ExerciseTemplateParameter;
use App\Domain\ExerciseTemplate\Repositories\ExerciseTemplateParameterRepository;
use App\Domain\Shared\Enums\AuditActionType;
use App\Domain\Shared\Traits\RecordsAuditLog;

final class DeleteExerciseTemplateParameterAction
{
    use RecordsAuditLog;

    public function __construct(private readonly ExerciseTemplateParameterRepository $repository)
    {
    }

    public function execute(ExerciseTemplateParameter $parameter): void
    {
        $this->repository->delete($parameter->id);

        $this->recordAudit(
            action: AuditActionType::DELETED,
            entity: $parameter,
        );
    }
}
