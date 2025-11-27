<?php

declare(strict_types=1);

namespace App\Domain\ExerciseType\Actions;

use App\Domain\ExerciseType\Models\ExerciseType;
use App\Domain\ExerciseType\Repositories\ExerciseTypeRepository;
use App\Domain\Shared\Enums\AuditActionType;
use App\Domain\Shared\Traits\RecordsAuditLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class DeleteExerciseTypeAction
{
    use RecordsAuditLog;

    public function __construct(private readonly ExerciseTypeRepository $repository)
    {
    }

    /**
     * @throws ValidationException
     */
    public function execute(ExerciseType $exerciseType): void
    {
        if ($exerciseType->templates()->exists()) {
            throw ValidationException::withMessages([
                'exercise_type' => 'Нельзя удалить тип, пока есть связанные шаблоны',
            ]);
        }

        DB::transaction(function () use ($exerciseType) {
            $oldData = $exerciseType->toArray();

            $this->repository->delete($exerciseType->id);

//            TODO: подумать как логировать - это действия суперадмина, проблема с organization_id
//            $this->recordAudit(
//                action: AuditActionType::DELETED,
//                entity: $exerciseType,
//                oldData: $oldData
//            );
        });
    }
}
