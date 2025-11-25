<?php

declare(strict_types=1);

namespace App\Domain\Department\Repositories;

use App\Domain\Department\Models\Department;
use App\Support\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class DepartmentRepository extends BaseRepository
{
    protected static function modelClass(): string
    {
        return Department::class;
    }

    protected function applyFilters(Builder $query, array $filters): void
    {
        if (array_key_exists('is_active', $filters)) {
            $query->where('is_active', (bool) $filters['is_active']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function (Builder $builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%");
            });
        }
    }

    public function emailExists(string $email): bool
    {
        return $this->newQuery()
            ->whereRaw('LOWER(email) = ?', [mb_strtolower($email)])
            ->exists();
    }

    public function emailExistsExceptUuid(string $email, string $exceptUuid): bool
    {
        return $this->newQuery()
            ->whereRaw('LOWER(email) = ?', [mb_strtolower($email)])
            ->where('uuid', '!=', $exceptUuid)
            ->exists();
    }
}
