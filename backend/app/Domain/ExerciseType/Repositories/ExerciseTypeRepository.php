<?php

declare(strict_types=1);

namespace App\Domain\ExerciseType\Repositories;

use App\Domain\ExerciseType\Models\ExerciseType;
use App\Support\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class ExerciseTypeRepository extends BaseRepository
{
    protected static function modelClass(): string
    {
        return ExerciseType::class;
    }

    protected function applyFilters(Builder $query, array $filters): void
    {
        if (!empty($filters['search'])) {
            $search = trim((string) $filters['search']);
            $query->where(function (Builder $builder) use ($search) {
                $builder->where('name', 'ilike', "%{$search}%")
                    ->orWhere('slug', 'ilike', "%{$search}%")
                    ->orWhere('description', 'ilike', "%{$search}%");
            });
        }

        if (!empty($filters['dimension'])) {
            $query->where('dimension', $filters['dimension']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', (bool) $filters['is_active']);
        }
    }

    public function slugExists(string $slug): bool
    {
        return $this->newQuery()
            ->whereRaw('LOWER(slug) = ?', [mb_strtolower($slug)])
            ->exists();
    }

    public function slugExistsExceptId(string $slug, int $exceptId): bool
    {
        return $this->newQuery()
            ->whereRaw('LOWER(slug) = ?', [mb_strtolower($slug)])
            ->where('id', '!=', $exceptId)
            ->exists();
    }
}
