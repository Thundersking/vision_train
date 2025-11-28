<?php

declare(strict_types=1);

namespace App\Domain\Exercise\Actions;

use App\Domain\Exercise\Models\Exercise;
use App\Domain\Exercise\Repositories\ExerciseRepository;
use App\Domain\Shared\Enums\AuditActionType;
use App\Domain\Shared\Traits\RecordsAuditLog;
use Illuminate\Support\Facades\DB;

final class CreateExerciseAction
{
    use RecordsAuditLog;

    public function __construct(
        private readonly ExerciseRepository $repository
    ) {
    }

    public function execute(array $data): Exercise
    {
        return DB::transaction(function () use ($data) {
            /** @var Exercise $exercise */
            $exercise = $this->repository->create($data);

            $this->recordAudit(
                action: AuditActionType::CREATED,
                entity: $exercise,
                newData: $exercise->toArray()
            );

            return $exercise;
        });
    }
}

