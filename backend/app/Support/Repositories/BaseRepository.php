<?php

namespace App\Support\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Ramsey\Uuid\Nonstandard\Uuid;

abstract class BaseRepository
{
    abstract protected static function modelClass(): string;

    protected function newQuery(): Builder
    {
        $model = static::modelClass();
        /** @var Model $instance */
        $instance = new $model();
        return $instance->newQuery();
    }

    /**
     * Хук фильтрации — переопределяется в наследниках
     * @param Builder $query
     * @param array $filters
     * @return void
     */
    protected function applyFilters(Builder $query, array $filters): void
    {
        //
    }

    public function create(array $attributes): Model
    {
        $model = static::modelClass();
        /** @var Model $instance */
        $instance = new $model();
        return $instance->create($attributes);
    }

    public function find(int|string $id): ?Model
    {
        return $this->newQuery()->find($id);
    }

    public function findByUuid(string $uuid, string $column = 'uuid'): ?Model
    {
        if (!Uuid::isValid($uuid)) {
            throw (new ModelNotFoundException)->setModel(static::modelClass());
        }

        return $this->newQuery()->where($column, $uuid)->first();
    }

    public function update(int|string $id, array $attributes): ?Model
    {
        $record = $this->find($id);
        if (!$record) {
            return null;
        }
        $record->update($attributes);
        return $record->refresh();
    }

    public function delete(int|string $id): bool
    {
        $record = $this->find($id);
        return $record && $record->delete();
    }

    public function restore(int|string $id): bool
    {
        $model = static::modelClass();
        /** @var Model $instance */
        $instance = new $model();
        $record = $instance->newQuery()->withTrashed()->find($id);
        return $record && $record->restore();
    }

    public function paginate(array $filters = []): LengthAwarePaginator
    {
        $query = $this->newQuery();

        $perPage = (int)($filters['per_page'] ?? 15);
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sort = $filters['sort'] ?? 'desc';

        unset($filters['per_page'], $filters['sort_by'], $filters['sort']);

        $this->applyFilters($query, $filters);

        $sort = in_array(strtolower((string)$sort), ['asc', 'desc'], true) ? $sort : 'desc';
        if (!empty($sortBy)) {
            $query->orderBy($sortBy, $sort);
        }

        if ($perPage <= 0) {
            $perPage = 15;
        }

        return $query->paginate($perPage);
    }

    public function get(
        array   $filters = [],
        ?string $sortBy = null,
        ?string $sortOrder = 'asc'
    ): Collection
    {
        $query = $this->newQuery();
        $this->applyFilters($query, $filters);
        if ($sortBy) {
            $query->orderBy($sortBy, $sortOrder ?? 'asc');
        }
        return $query->get();
    }

    public function count(array $filters = []): int
    {
        $query = $this->newQuery();
        $this->applyFilters($query, $filters);
        return $query->count();
    }

    public function exists(string $column, mixed $value): bool
    {
        return $this->newQuery()->where($column, $value)->exists();
    }

    public function insert(array $rows): bool
    {
        if ($rows === []) {
            return true;
        }
        $model = static::modelClass();
        /** @var Model $instance */
        $instance = new $model();
        return (bool)$instance->newQuery()->insert($rows);
    }
}
