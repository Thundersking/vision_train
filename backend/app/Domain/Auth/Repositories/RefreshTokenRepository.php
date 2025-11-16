<?php

declare(strict_types=1);

namespace App\Domain\Auth\Repositories;

use App\Domain\Auth\Models\RefreshToken;
use App\Support\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class RefreshTokenRepository extends BaseRepository
{
    protected static function modelClass(): string
    {
        return RefreshToken::class;
    }

    protected function applyFilters(Builder $query, array $filters): void
    {
        if (isset($filters['user_id'])) {
            $query->where('user_id', (int) $filters['user_id']);
        }

        if (isset($filters['is_valid']) && $filters['is_valid']) {
            $query->valid();
        }

        if (isset($filters['is_expired']) && $filters['is_expired']) {
            $query->expired();
        }
    }

    public function findByTokenHash(string $tokenHash): ?RefreshToken
    {
        return $this->newQuery()
            ->where('token_hash', $tokenHash)
            ->first();
    }

    public function findValidToken(string $tokenHash, string $fingerprintHash): ?RefreshToken
    {
        return $this->newQuery()
            ->where('token_hash', $tokenHash)
            ->where('fingerprint_hash', $fingerprintHash)
            ->valid()
            ->first();
    }

    public function revokeToken(string $tokenHash): bool
    {
        return $this->newQuery()
            ->where('token_hash', $tokenHash)
            ->delete() > 0;
    }

    public function revokeAllUserTokens(int $userId): int
    {
        return $this->newQuery()
            ->where('user_id', $userId)
            ->delete();
    }

    public function deleteExpired(): int
    {
        return $this->newQuery()
            ->expired()
            ->delete();
    }

    public function countUserTokens(int $userId): int
    {
        return $this->newQuery()
            ->forUser($userId)
            ->valid()
            ->count();
    }

    public function getOldestUserToken(int $userId): ?RefreshToken
    {
        return $this->newQuery()
            ->forUser($userId)
            ->valid()
            ->oldest('created_at')
            ->first();
    }

    public function createToken(array $data): RefreshToken
    {
        return $this->create($data);
    }
}