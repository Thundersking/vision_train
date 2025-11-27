<?php

declare(strict_types=1);

namespace App\Domain\ExerciseTemplate\Actions;

use App\Domain\ExerciseTemplate\Models\ExerciseTemplate;
use App\Domain\ExerciseTemplate\Repositories\ExerciseTemplateRepository;
use App\Domain\Shared\Enums\AuditActionType;
use App\Domain\Shared\Traits\RecordsAuditLog;
use Illuminate\Support\Facades\DB;

final class CreateExerciseTemplateAction
{
    use RecordsAuditLog;

    public function __construct(private readonly ExerciseTemplateRepository $repository)
    {
    }

    public function execute(array $data): ExerciseTemplate
    {
        $template = DB::transaction(function () use ($data) {
            return $this->repository->create($data);
        });

        $this->recordAudit(
            action: AuditActionType::CREATED,
            entity: $template,
            newData: $template->toArray()
        );

        return $template;
    }
}
