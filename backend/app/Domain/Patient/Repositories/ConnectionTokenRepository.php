<?php

declare(strict_types=1);

namespace App\Domain\Patient\Repositories;

use App\Domain\Patient\Enums\ConnectionTokenStatus;
use App\Domain\Patient\Models\ConnectionToken;
use App\Support\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

final class ConnectionTokenRepository extends BaseRepository
{
    protected static function modelClass(): string
    {
        return ConnectionToken::class;
    }

    protected function newQuery(): Builder
    {
        return parent::newQuery()->with(['patient', 'creator']);
    }

    protected function applyFilters(Builder $query, array $filters): void
    {
        if (!empty($filters['patient_id'])) {
            $query->where('patient_id', (int) $filters['patient_id']);
        }

        if (!empty($filters['status'])) {
            $query->whereIn('status', (array) $filters['status']);
        }
    }

    public function findActiveByPatient(int $patientId): ?ConnectionToken
    {
        return $this->newQuery()
            ->where('patient_id', $patientId)
            ->where('status', ConnectionTokenStatus::PENDING->value)
            ->where(function (Builder $builder) {
                $builder->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->latest('created_at')
            ->first();
    }

    public function invalidateActiveTokens(int $patientId): void
    {
        $this->newQuery()
            ->where('patient_id', $patientId)
            ->where('status', ConnectionTokenStatus::PENDING->value)
            ->update([
                'status' => ConnectionTokenStatus::EXPIRED->value,
                'is_active' => false,
            ]);
    }

    public function findValidByToken(string $token): ?ConnectionToken
    {
        $connectionToken = $this->newQuery()
            ->where('token', $token)
            ->first();

        if (!$connectionToken) {
            return null;
        }

        if ($connectionToken->status !== ConnectionTokenStatus::PENDING || $connectionToken->isExpired()) {
            return null;
        }

        return $connectionToken;
    }

    public function markUsed(ConnectionToken $token): ConnectionToken
    {
        $token->fill([
            'status' => ConnectionTokenStatus::USED,
            'used_at' => now(),
            'is_active' => false,
        ])->save();

        return $token->refresh();
    }

    public function expire(ConnectionToken $token): void
    {
        $token->fill([
            'status' => ConnectionTokenStatus::EXPIRED,
            'is_active' => false,
        ])->save();
    }
}
