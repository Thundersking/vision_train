<?php

declare(strict_types=1);

namespace App\Domain\ExerciseTemplate\Repositories;

use App\Domain\ExerciseTemplate\Models\ExerciseTemplate;
use App\Support\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class ExerciseTemplateRepository extends BaseRepository
{
    protected static function modelClass(): string
    {
        return ExerciseTemplate::class;
    }

    protected function newQuery(): Builder
    {
        return parent::newQuery()->with('type');
    }

    protected function applyFilters(Builder $query, array $filters): void
    {
        if (!empty($filters['search'])) {
            $search = trim((string) $filters['search']);
            $query->where(function (Builder $builder) use ($search) {
                $builder->where('title', 'ilike', "%{$search}%")
                    ->orWhere('short_description', 'ilike', "%{$search}%");
            });
        }

        if (!empty($filters['exercise_type_id'])) {
            $query->where('exercise_type_id', (int) $filters['exercise_type_id']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', (bool) $filters['is_active']);
        }
    }

    public function findWithRelations(string $uuid, array $relations = []): ?ExerciseTemplate
    {
        return $this->newQuery()
            ->with($relations)
            ->where('uuid', $uuid)
            ->first();
    }
}
