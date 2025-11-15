<?php

declare(strict_types=1);

namespace App\Domain\User\Repositories;

use App\Domain\User\Models\User;
use App\Support\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class UserRepository extends BaseRepository
{
    protected static function modelClass(): string
    {
        return User::class;
    }

    protected function applyFilters(Builder $query, array $filters): void
    {
        if (isset($filters['organization_id'])) {
            $query->where('organization_id', (int) $filters['organization_id']);
        }

        if (isset($filters['department_id'])) {
            $query->where('department_id', (int) $filters['department_id']);
        }

        if (array_key_exists('is_active', $filters)) {
            $query->where('is_active', (bool) $filters['is_active']);
        }

        if (!empty($filters['search'])) {
            $s = $filters['search'];
            $query->where(function ($qq) use ($s) {
                $qq->where('first_name', 'like', "%{$s}%")
                    ->orWhere('last_name', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%")
                    ->orWhere('phone', 'like', "%{$s}%");
            });
        }
    }

    public function emailExists(string $email): bool
    {
        return $this->newQuery()
            ->where('email', $email)
            ->exists();
    }

    public function emailExistsExceptUserUuid(string $email, string $exceptUuid): bool
    {
        return $this->newQuery()
            ->where('email', $email)
            ->where('uuid', '!=', $exceptUuid)
            ->exists();
    }
}
