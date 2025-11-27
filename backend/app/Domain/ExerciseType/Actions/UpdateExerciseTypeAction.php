<?php

declare(strict_types=1);

namespace App\Domain\ExerciseType\Actions;

use App\Domain\ExerciseType\Guards\ExerciseTypeGuard;
use App\Domain\ExerciseType\Models\ExerciseType;
use App\Domain\ExerciseType\Repositories\ExerciseTypeRepository;
use App\Domain\Shared\Enums\AuditActionType;
use App\Domain\Shared\Traits\RecordsAuditLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class UpdateExerciseTypeAction
{
    use RecordsAuditLog;

    public function __construct(
        private readonly ExerciseTypeRepository $repository,
        private readonly ExerciseTypeGuard $guard
    ) {
    }

    /**
     * @throws ValidationException
     */
    public function execute(ExerciseType $exerciseType, array $data): ExerciseType
    {
        if (!empty($data['slug']) && $data['slug'] !== $exerciseType->slug) {
            $this->guard->ensureSlugUniqueExceptId($data['slug'], $exerciseType->id);
        }

        $data['metrics_json'] = $this->decodeJson($data['metrics_json'] ?? null);

        return DB::transaction(function () use ($exerciseType, $data) {
            $oldData = $exerciseType->toArray();

            $updated = $this->repository->update($exerciseType->id, $data);

            if ($updated) {
                $this->recordAudit(
                    action: AuditActionType::UPDATED,
                    entity: $exerciseType,
                    oldData: $oldData,
                    newData: $updated->toArray()
                );
            }

            return $updated ?? $exerciseType;
        });
    }

    private function decodeJson(mixed $value): ?array
    {
        if (is_array($value)) {
            return $value;
        }

        if (is_string($value) && $value !== '') {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded;
            }

            throw ValidationException::withMessages([
                'metrics_json' => 'Некорректный JSON',
            ]);
        }

        return null;
    }
}
