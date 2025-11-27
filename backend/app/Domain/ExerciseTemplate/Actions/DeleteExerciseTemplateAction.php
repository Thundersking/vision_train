<?php

declare(strict_types=1);

namespace App\Domain\ExerciseTemplate\Actions;

use App\Domain\ExerciseTemplate\Models\ExerciseTemplate;
use App\Domain\ExerciseTemplate\Repositories\ExerciseTemplateRepository;
use App\Domain\Shared\Enums\AuditActionType;
use App\Domain\Shared\Traits\RecordsAuditLog;
use Illuminate\Support\Facades\DB;

final class DeleteExerciseTemplateAction
{
    use RecordsAuditLog;

    public function __construct(private readonly ExerciseTemplateRepository $repository)
    {
    }

    public function execute(ExerciseTemplate $template): void
    {
        DB::transaction(function () use ($template) {
            $oldData = $template->toArray();

            $this->repository->delete($template->id);

            $this->recordAudit(
                action: AuditActionType::DELETED,
                entity: $template,
                oldData: $oldData
            );
        });
    }
}
