<?php

declare(strict_types=1);

namespace App\Domain\Device\Repositories;

use App\Domain\Device\Models\Device;
use App\Support\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

final class DeviceRepository extends BaseRepository
{
    protected static function modelClass(): string
    {
        return Device::class;
    }

    protected function applyFilters(Builder $query, array $filters): void
    {
        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['serial_number'])) {
            $query->where('serial_number', $filters['serial_number']);
        }

        if (array_key_exists('is_active', $filters)) {
            $query->where('is_active', (bool) $filters['is_active']);
        }
    }

    public function findByIdentifier(string $identifier): ?Device
    {
        return $this->newQuery()
            ->where('serial_number', $identifier)
            ->first();
    }
}
