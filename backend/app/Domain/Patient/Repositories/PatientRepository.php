<?php

declare(strict_types=1);

namespace App\Domain\Patient\Repositories;

use App\Domain\Patient\Models\Patient;
use App\Support\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Str;

class PatientRepository extends BaseRepository
{
    protected static function modelClass(): string
    {
        return Patient::class;
    }

    protected function newQuery(): Builder
    {
        return parent::newQuery()->with(['doctor']);
    }

    protected function applyFilters(Builder $query, array $filters): void
    {
        if (array_key_exists('is_active', $filters)) {
            $query->where('is_active', (bool) $filters['is_active']);
        }

        if (!empty($filters['doctor_id'])) {
            $query->where('user_id', (int) $filters['doctor_id']);
        }

        if (!empty($filters['search'])) {
            $search = trim((string) $filters['search']);
            $query->where(function (Builder $builder) use ($search) {
                $builder->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('middle_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }
    }

    public function emailExists(string $email): bool
    {
        return $this->newQuery()
            ->whereRaw('LOWER(email) = ?', [Str::lower($email)])
            ->exists();
    }

    public function emailExistsExceptUuid(string $email, string $exceptUuid): bool
    {
        return $this->newQuery()
            ->whereRaw('LOWER(email) = ?', [Str::lower($email)])
            ->where('uuid', '!=', $exceptUuid)
            ->exists();
    }

    public function findWithRelations(string $uuid, array $relations = []): ?Patient
    {
        /** @var Builder|Relation $query */
        $query = $this->newQuery();

        if (!empty($relations)) {
            $query->with($relations);
        }

        /** @var Model|null $model */
        $model = $query->where('uuid', $uuid)->first();

        return $model instanceof Patient ? $model : null;
    }
}
