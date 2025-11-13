<?php

declare(strict_types=1);

namespace App\Domain\User\Queries;

use App\Domain\User\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

class UserQueries
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
        return ['users', "organization:{$organizationId}"];
    }

    /**
     * Пагинация пользователей для таблиц
     */
    public function paginatedForCurrentOrganization(
        array $filters = [],
        int   $perPage = 15
    ): LengthAwarePaginator
    {
        try {
            return User::query()
                ->when($filters['search'] ?? null, function ($q, $search) {
                    $q->where(function ($qq) use ($search) {
                        $qq->where('first_name', 'ILIKE', "%{$search}%")
                            ->orWhere('last_name', 'ILIKE', "%{$search}%")
                            ->orWhere('email', 'ILIKE', "%{$search}%")
                            ->orWhere('phone', 'ILIKE', "%{$search}%");
                    });
                })
                ->when(isset($filters['is_active']), fn($q) => $q->where('is_active', (bool)$filters['is_active']))
                ->when($filters['department_id'] ?? null, fn($q, $departmentId) => $q->where('department_id', $departmentId))
                ->when($filters['role'] ?? null, fn($q, $role) => $q->whereHas('roles', function ($qq) use ($role) {
                    $qq->where('name', $role);
                }))
                ->with(['department:id,name', 'roles:id,name'])
                ->orderBy($filters['sort_field'], $filters['sort_direction'])
                ->paginate($perPage);

        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function emailExists(int $organizationId, string $email): bool
    {
        return User::query()
            ->where('organization_id', $organizationId)
            ->whereRaw('LOWER(email) = ?', [mb_strtolower($email)])
            ->exists();
    }

    public function emailExistsExceptUserUuid(int $organizationId, string $email, string $exceptUserUuid): bool
    {
        return User::query()
            ->where('organization_id', $organizationId)
            ->whereRaw('LOWER(email) = ?', [mb_strtolower($email)])
            ->where('uuid', '!=', $exceptUserUuid)
            ->exists();
    }

    /**
     * Детальная карточка пользователя со всеми данными
     */
    public function findWithFullData(string $uuid): ?User
    {
        $organizationId = session('current_organization_id');
        $key = $this->cacheKey('users.detail', $organizationId, ['uuid' => $uuid]);

        return Cache::tags($this->tags($organizationId))->remember(
            $key,
            self::TTL['detail'],
            fn() => User::query()
                ->with([
                    'department:id,name,utc_offset_minutes',
                    'organization:id,name',
                    'roles:id,name,display_name',
                ])
                ->where('uuid', $uuid)
                ->first()
        );
    }

    /**
     * Выпадающий список активных пользователей
     */
    public function forDropdown(): array
    {
        $organizationId = session('current_organization_id');
        $key = $this->cacheKey('users.dropdown', $organizationId);

        $users = Cache::tags($this->tags($organizationId))->remember(
            $key,
            self::TTL['dropdown'],
            fn() => User::query()
                // organization_id фильтруется автоматически через OrganizationScope
                ->where('is_active', true)
                ->select(['id', 'first_name', 'last_name'])
                ->orderBy('first_name')
                ->orderBy('last_name')
                ->get()
        );

        return $users->mapWithKeys(fn($user) => [$user->id => $user->first_name . ' ' . $user->last_name])
            ->toArray();
    }

    /**
     * Статистика пользователей
     */
    public function getStatistics(): array
    {
        $organizationId = session('current_organization_id');
        $key = $this->cacheKey('users.stats', $organizationId);

        return Cache::tags($this->tags($organizationId))->remember(
            $key,
            self::TTL['stats'],
            function () {
                // organization_id фильтруется автоматически через OrganizationScope
                $base = User::query();

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
        // organization_id фильтруется автоматически через OrganizationScope
        return User::query()
            ->where(function ($q) use ($query) {
                $q->where('first_name', 'ILIKE', "%{$query}%")
                    ->orWhere('last_name', 'ILIKE', "%{$query}%")
                    ->orWhere('email', 'ILIKE', "%{$query}%");
            })
            ->where('is_active', true)
            ->select(['id', 'uuid', 'first_name', 'last_name', 'email'])
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->limit($limit)
            ->get();
    }

    /**
     * Найти пользователя по UUID для Actions (с ролями)
     */
    public function findByUuid(string $uuid): ?User
    {
        $organizationId = session('current_organization_id');
        $key = $this->cacheKey('users.uuid', $organizationId, ['uuid' => $uuid]);

        return Cache::tags($this->tags($organizationId))->remember(
            $key,
            self::TTL['detail'],
            fn() => User::query()
                ->where('uuid', $uuid)
                ->with('roles:id,name')
                ->first()
        );
    }

    /**
     * Получить текущую роль пользователя
     */
    public function getUserCurrentRole(User $user): ?string
    {
        return Cache::tags(['users', "user:{$user->id}"])
            ->remember("user.{$user->id}.current_role", self::TTL['detail'], function () use ($user) {
                return $user->roles()->first()?->name;
            });
    }

    /**
     * Найти активного пользователя в организации по ID
     */
    public function findActiveInOrganization(int $userId, int $organizationId): ?User
    {
        return User::query()
            ->where('id', $userId)
            ->where('organization_id', $organizationId)
            ->where('is_active', true)
            ->first();
    }
}
