<?php

declare(strict_types=1);

namespace App\Domain\Exercise\Repositories;

use App\Domain\Exercise\Models\Exercise;
use App\Support\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ExerciseRepository extends BaseRepository
{
    protected static function modelClass(): string
    {
        return Exercise::class;
    }

    protected function newQuery(): Builder
    {
        return parent::newQuery()->with(['patient', 'template']);
    }

    protected function applyFilters(Builder $query, array $filters): void
    {
        if (!empty($filters['patient_id'])) {
            $query->where('patient_id', (int) $filters['patient_id']);
        }

        if (!empty($filters['exercise_type'])) {
            $query->where('exercise_type', $filters['exercise_type']);
        }

        if (!empty($filters['exercise_template_id'])) {
            $query->where('exercise_template_id', (int) $filters['exercise_template_id']);
        }

        if (!empty($filters['started_at_from'])) {
            $query->where('started_at', '>=', $filters['started_at_from']);
        }

        if (!empty($filters['started_at_to'])) {
            $query->where('started_at', '<=', $filters['started_at_to']);
        }

        if (!empty($filters['completed_at_from'])) {
            $query->where('completed_at', '>=', $filters['completed_at_from']);
        }

        if (!empty($filters['completed_at_to'])) {
            $query->where('completed_at', '<=', $filters['completed_at_to']);
        }
    }

    public function findWithRelations(string $uuid, array $relations = []): ?Exercise
    {
        /** @var Builder $query */
        $query = $this->newQuery();

        if (!empty($relations)) {
            $query->with($relations);
        }

        /** @var Model|null $model */
        $model = $query->where('uuid', $uuid)->first();

        return $model instanceof Exercise ? $model : null;
    }

}

