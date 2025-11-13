<?php

declare(strict_types=1);

namespace App\Domain\Patient\Queries;

use App\Domain\Patient\Models\Patient;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

class PatientQueries
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
        return ['patients', "organization:{$organizationId}"];
    }

    /**
     * Пагинация пациентов для таблиц
     */
    public function paginatedForCurrentOrganization(
        array $filters = [],
        int   $perPage = 15
    ): LengthAwarePaginator
    {
        try {
            return Patient::query()
                ->when($filters['search'] ?? null, function ($q, $search) {
                    $q->where(function ($qq) use ($search) {
                        $qq->where('first_name', 'ILIKE', "%{$search}%")
                            ->orWhere('last_name', 'ILIKE', "%{$search}%")
                            ->orWhere('middle_name', 'ILIKE', "%{$search}%")
                            ->orWhere('email', 'ILIKE', "%{$search}%")
                            ->orWhere('phone', 'ILIKE', "%{$search}%");
                    });
                })
                ->when(isset($filters['is_active']), fn($q) => $q->where('is_active', (bool)$filters['is_active']))
                ->when($filters['user_id'] ?? null, fn($q, $doctorId) => $q->where('user_id', $doctorId))
                ->when($filters['gender'] ?? null, fn($q, $gender) => $q->where('gender', $gender))
                ->with(['doctor:id,uuid,first_name,last_name', 'organization:id,name'])
                ->orderBy($filters['sort_field'] ?? 'created_at', $filters['sort_direction'] ?? 'desc')
                ->paginate($perPage);

        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Найти пациента по UUID
     */
    public function findByUuid(string $uuid): ?Patient
    {
        $organizationId = session('current_organization_id');
        $key = $this->cacheKey('patients.uuid', $organizationId, ['uuid' => $uuid]);

        return Cache::tags($this->tags($organizationId))->remember(
            $key,
            self::TTL['detail'],
            fn() => Patient::query()
                ->where('uuid', $uuid)
                ->firstOrFail()
        );
    }

    /**
     * Детальная карточка пациента со всеми данными
     */
    public function findWithFullData(string $uuid): ?Patient
    {
        $organizationId = session('current_organization_id');
        $key = $this->cacheKey('patients.detail', $organizationId, ['uuid' => $uuid]);

        return Cache::tags($this->tags($organizationId))->remember(
            $key,
            self::TTL['detail'],
            fn() => Patient::query()
                ->with([
                    'doctor:id,uuid,first_name,last_name,email',
                    'organization:id,name',
                    'examinations.doctor:id,first_name,last_name',
                    'devices:id,name,type',
                    'connectionTokens' => function ($q) {
                        $q->where('expires_at', '>', now())
                          ->whereNull('used_at')
                          ->orderBy('created_at', 'desc');
                    }
                ])
                ->where('uuid', $uuid)
                ->first()
//                ->firstOrFail()
        );
    }

    /**
     * Выпадающий список активных пациентов
     */
    public function forDropdown(): Collection
    {
        $organizationId = session('current_organization_id');
        $key = $this->cacheKey('patients.dropdown', $organizationId);

        return Cache::tags($this->tags($organizationId))->remember(
            $key,
            self::TTL['dropdown'],
            fn() => Patient::query()
                ->where('is_active', true)
                ->select(['id', 'uuid', 'first_name', 'last_name', 'middle_name'])
                ->orderBy('first_name')
                ->orderBy('last_name')
                ->get()
        );
    }

    /**
     * Проверка уникальности email в организации
     */
    public function emailExists(int $organizationId, string $email): bool
    {
        return Patient::query()
            ->where('organization_id', $organizationId)
            ->whereRaw('LOWER(email) = ?', [mb_strtolower($email)])
            ->exists();
    }

    /**
     * Проверка уникальности email кроме указанного пациента
     */
    public function emailExistsExceptPatientUuid(int $organizationId, string $email, string $exceptPatientUuid): bool
    {
        return Patient::query()
            ->where('organization_id', $organizationId)
            ->whereRaw('LOWER(email) = ?', [mb_strtolower($email)])
            ->where('uuid', '!=', $exceptPatientUuid)
            ->exists();
    }
}
