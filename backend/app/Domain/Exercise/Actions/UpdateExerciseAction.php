<?php

declare(strict_types=1);

namespace App\Domain\Exercise\Actions;

use App\Domain\Exercise\Models\Exercise;
use App\Domain\Exercise\Repositories\ExerciseRepository;
use App\Domain\Shared\Enums\AuditActionType;
use App\Domain\Shared\Traits\RecordsAuditLog;
use Illuminate\Support\Facades\DB;

final class UpdateExerciseAction
{
    use RecordsAuditLog;

    public function __construct(
        private readonly ExerciseRepository $repository
    ) {
    }

    public function execute(Exercise $exercise, array $data): Exercise
    {
        return DB::transaction(function () use ($exercise, $data) {
            $oldData = $exercise->toArray();

            /** @var Exercise $updated */
            $updated = $this->repository->update($exercise->id, $data);

            $this->recordAudit(
                action: AuditActionType::UPDATED,
                entity: $updated,
                oldData: $oldData,
                newData: $updated->toArray()
            );

            return $updated;
        });
    }
}

