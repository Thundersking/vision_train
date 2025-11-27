<?php

declare(strict_types=1);

namespace App\Domain\ExerciseTemplate\Actions;

use App\Domain\ExerciseTemplate\Models\ExerciseTemplate;
use App\Domain\ExerciseTemplate\Repositories\ExerciseTemplateRepository;
use App\Domain\ExerciseTemplate\Traits\NormalizesExerciseTemplatePayload;
use App\Domain\Shared\Enums\AuditActionType;
use App\Domain\Shared\Traits\RecordsAuditLog;
use Illuminate\Support\Facades\DB;

final class UpdateExerciseTemplateAction
{
    use RecordsAuditLog;
    use NormalizesExerciseTemplatePayload;

    public function __construct(
        private readonly ExerciseTemplateRepository          $repository,
    )
    {
    }

    public function execute(ExerciseTemplate $template, array $data): ExerciseTemplate
    {
        $oldData = $template->toArray();

        $updated = DB::transaction(function () use ($template, $data) {
            return $this->repository->update($template->id, $data);
        });

        $this->recordAudit(
            action: AuditActionType::UPDATED,
            entity: $updated,
            oldData: $oldData,
            newData: $updated->toArray()
        );

        return $updated;
    }
}
