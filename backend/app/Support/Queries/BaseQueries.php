<?php

namespace App\Support\Queries;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class BaseQueries
{
    protected Builder $query;

    abstract protected static function modelClass(): string;

    /**
     * Инициализация Builder
     */
    public function __construct()
    {
        $model = static::modelClass();
        $this->query = $model::query();
    }

    /**
     * Унифицированная пагинация
     */
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $this->applyFilters($filters);

        $sortBy = $filters['sort_by'] ?? $this->defaultSortBy();
        $sortOrder = $filters['sort_order'] ?? 'desc';

        return $this->query
            ->orderBy($sortBy, $sortOrder)
            ->paginate($perPage);
    }

    public function findByUuid(string $uuid): ?Model
    {
        return $this->query->where('uuid', $uuid)->first();
    }

    public function find(int $id): ?Model
    {
        return $this->query->find($id);
    }

    public function existsBy(string $column, mixed $value): bool
    {
        return $this->query->where($column, $value)->exists();
    }

    public function count(array $filters = []): int
    {
        $this->applyFilters($filters);
        return $this->query->count();
    }

    public function get(array $filters = []): Collection
    {
        $this->applyFilters($filters);
        return $this->query->get();
    }

    /**
     * Хук для фильтрации. По умолчанию — пустой.
     * Наследник реализует только если нужен грид с фильтрами.
     */
    protected function applyFilters(array $filters): void
    {
        //
    }

    /**
     * Дефолтная сортировка.
     */
    protected function defaultSortBy(): string
    {
        return 'created_at';
    }
}
