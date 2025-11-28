<?php

declare(strict_types=1);

namespace App\Domain\Exercise\Actions;

use App\Domain\Exercise\Models\Exercise;
use App\Domain\Exercise\Repositories\ExerciseRepository;
use App\Domain\Shared\Enums\AuditActionType;
use App\Domain\Shared\Traits\RecordsAuditLog;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

final class DeleteExerciseAction
{
    use RecordsAuditLog;

    public function __construct(
        private readonly ExerciseRepository $repository
    ) {
    }

    public function execute(Exercise $exercise): void
    {
        DB::transaction(function () use ($exercise) {
            $deleted = $this->repository->delete($exercise->id);

            if (!$deleted) {
                throw new ModelNotFoundException();
            }

            $this->recordAudit(
                action: AuditActionType::DELETED,
                entity: $exercise,
            );
        });
    }
}

