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

    protected function applyFilters(Builder $query, array $filters): void
    {
        if (!empty($filters['search'])) {
            $search = trim((string) $filters['search']);
            $query->where(function (Builder $builder) use ($search) {
                $builder->where('name', 'ilike', "%{$search}%")
                    ->orWhere('description', 'ilike', "%{$search}%");
            });
        }

        if (!empty($filters['exercise_type'])) {
            $query->where('exercise_type', $filters['exercise_type']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', (bool) $filters['is_active']);
        }
    }
}
