<?php

declare(strict_types=1);

namespace App\Domain\Department\Queries;

use App\Domain\Department\Models\Department;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

class DepartmentQueries
{
    private const TTL = [
        'list' => 3600,
        'detail' => 7200,
        'stats' => 1800,
        'dropdown' => 14400,
    ];

    private function cacheKey(string $prefix, int $organizationId, array $params = []): string
    {
        $query = Arr::query(Arr::sortRecursive($params));
        return "{$prefix}:org{$organizationId}" . ($query ? ":{$query}" : '');
    }

    private function tags(int $organizationId): array
    {
        return ['departments', "organization:{$organizationId}"];
    }

    /**
     * Пагинация отделений для таблиц
     */
    public function paginatedForCurrentOrganization(
        array $filters = [],
        int   $perPage = 15
    ): LengthAwarePaginator
    {
        try {
            return Department::query()
                ->when($filters['search'] ?? null, function ($q, $search) {
                    $q->where(function ($qq) use ($search) {
                        $qq->where('name', 'ILIKE', "%{$search}%")
                            ->orWhere('email', 'ILIKE', "%{$search}%")
                            ->orWhere('phone', 'ILIKE', "%{$search}%")
                            ->orWhere('address', 'ILIKE', "%{$search}%");
                    });
                })
                ->when(isset($filters['is_active']), fn($q) => $q->where('is_active', (bool)$filters['is_active']))
                ->orderBy($filters['sort_field'] ?? 'name', $filters['sort_direction'] ?? 'asc')
                ->paginate($perPage);

        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Детальная карточка отделения со всеми данными
     */
    public function findWithFullData(string $uuid): ?Department
    {
        $organizationId = session('current_organization_id');
        $key = $this->cacheKey('departments.detail', $organizationId, ['uuid' => $uuid]);

        return Cache::tags($this->tags($organizationId))->remember(
            $key,
            self::TTL['detail'],
            fn() => Department::query()
                ->with([
                    'organization:id,name',
                ])
                ->where('uuid', $uuid)
                ->first()
        );
    }

    /**
     * Выпадающий список активных отделений
     */
    public function forDropdown(): Collection
    {
        $organizationId = session('current_organization_id');
        $key = $this->cacheKey('departments.dropdown', $organizationId);

        return Cache::tags($this->tags($organizationId))->remember(
            $key,
            self::TTL['dropdown'],
            fn() => Department::query()
                ->where('is_active', true)
                ->select(['id', 'uuid', 'name'])
                ->orderBy('name')
                ->get()
        );
    }

    /**
     * Статистика отделений
     */
    public function getStatistics(): array
    {
        $organizationId = session('current_organization_id');
        $key = $this->cacheKey('departments.stats', $organizationId);

        return Cache::tags($this->tags($organizationId))->remember(
            $key,
            self::TTL['stats'],
            function () {
                $base = Department::query();

                $total = (clone $base)->count();
                $active = (clone $base)->where('is_active', true)->count();
                $archived = (clone $base)->where('is_active', false)->count();
                $thisMonth = (clone $base)->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();

                return compact('total', 'active', 'archived', 'thisMonth');
            }
        );
    }

    /**
     * Подсказки для поиска
     */
    public function searchSuggest(string $query, int $limit = 10): Collection
    {
        return Department::query()
            ->where(function ($q) use ($query) {
                $q->where('name', 'ILIKE', "%{$query}%")
                    ->orWhere('email', 'ILIKE', "%{$query}%")
                    ->orWhere('phone', 'ILIKE', "%{$query}%");
            })
            ->where('is_active', true)
            ->select(['id', 'uuid', 'name', 'email', 'phone'])
            ->orderBy('name')
            ->limit($limit)
            ->get();
    }

    /**
     * Найти отделение по UUID для Actions
     */
    public function findByUuid(string $uuid): ?Department
    {
        $organizationId = session('current_organization_id');
        $key = $this->cacheKey('departments.uuid', $organizationId, ['uuid' => $uuid]);

        return Cache::tags($this->tags($organizationId))->remember(
            $key,
            self::TTL['detail'],
            fn() => Department::query()
                ->where('uuid', $uuid)
                ->first()
        );
    }

    /**
     * Проверить существование email отделения
     */
    public function emailExists(int $organizationId, string $email): bool
    {
        return Department::query()
            ->where('organization_id', $organizationId)
            ->whereRaw('LOWER(email) = ?', [mb_strtolower($email)])
            ->exists();
    }

    /**
     * Проверить существование email отделения кроме указанного UUID
     */
    public function emailExistsExceptDepartmentUuid(int $organizationId, string $email, string $exceptDepartmentUuid): bool
    {
        return Department::query()
            ->where('organization_id', $organizationId)
            ->whereRaw('LOWER(email) = ?', [mb_strtolower($email)])
            ->where('uuid', '!=', $exceptDepartmentUuid)
            ->exists();
    }
}
