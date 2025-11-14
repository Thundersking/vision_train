<?php

declare(strict_types=1);

namespace App\Domain\User\Queries;

use App\Domain\User\Models\User;
use App\Support\Queries\BaseQueries;

class UserQueries extends BaseQueries
{
    protected static function modelClass(): string
    {
        return User::class;
    }

    protected function applyFilters(array $filters): void
    {
        if (isset($filters['organization_id'])) {
            $this->query->where('organization_id', $filters['organization_id']);
        }

        if (isset($filters['department_id'])) {
            $this->query->where('department_id', $filters['department_id']);
        }

        if (isset($filters['is_active'])) {
            $this->query->where('is_active', $filters['is_active']);
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $this->query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }
    }
}
