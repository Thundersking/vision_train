<?php

declare(strict_types=1);

namespace App\Domain\Auth\Services;

use App\Domain\Auth\Models\RefreshToken;
use App\Domain\Auth\Repositories\RefreshTokenRepository;
use App\Domain\User\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class RefreshTokenService
{
    private const MAX_TOKENS_PER_USER = 5;
    private const REDIS_PREFIX = 'jwt:refresh:';

    public function __construct(
        private RefreshTokenRepository $repository,
        private FingerprintService $fingerprintService
    ) {}

    public function createRefreshToken(User $user, string $fingerprint, string $ipAddress = null, string $userAgent = null): RefreshToken
    {
        // Проверяем лимит активных токенов
        $this->enforceTokenLimit($user->id);

        // Генерируем уникальный токен
        $token = $this->generateToken();
        $tokenHash = $this->hashToken($token);
        $fingerprintHash = $this->fingerprintService->hash($fingerprint);

        // Создаем токен в БД
        $refreshToken = $this->repository->createToken([
            'user_id' => $user->id,
            'token_hash' => $tokenHash,
            'fingerprint_hash' => $fingerprintHash,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'expires_at' => Carbon::now()->addMinutes(config('jwt.refresh_ttl')),
        ]);

        // Кешируем в Redis для быстрого доступа
        $this->cacheToken($user->id, $tokenHash, $refreshToken);

        // Возвращаем оригинальный токен (не хеш)
        $refreshToken->original_token = $token;
        
        return $refreshToken;
    }

    public function validateRefreshToken(string $token, string $fingerprint): ?RefreshToken
    {
        $tokenHash = $this->hashToken($token);
        $fingerprintHash = $this->fingerprintService->hash($fingerprint);

        // Сначала проверяем кеш
        $cached = $this->getCachedToken($tokenHash);
        if ($cached && $cached['fingerprint_hash'] === $fingerprintHash) {
            return $this->repository->findValidToken($tokenHash, $fingerprintHash);
        }

        // Проверяем в БД
        $refreshToken = $this->repository->findValidToken($tokenHash, $fingerprintHash);
        
        if ($refreshToken) {
            $refreshToken->markAsUsed();
            // Обновляем кеш
            $this->cacheToken($refreshToken->user_id, $tokenHash, $refreshToken);
        }

        return $refreshToken;
    }

    public function revokeRefreshToken(string $token): void
    {
        $tokenHash = $this->hashToken($token);
        
        // Удаляем из БД
        $this->repository->revokeToken($tokenHash);
        
        // Удаляем из кеша
        $this->removeCachedToken($tokenHash);
    }

    public function revokeAllUserTokens(int $userId): int
    {
        // Удаляем все токены пользователя из БД
        $deletedCount = $this->repository->revokeAllUserTokens($userId);
        
        // Очищаем кеш пользователя
        $this->clearUserCache($userId);
        
        return $deletedCount;
    }

    public function cleanup(): int
    {
        return $this->repository->deleteExpired();
    }

    private function generateToken(): string
    {
        return Str::random(64);
    }

    private function hashToken(string $token): string
    {
        return hash('sha256', $token);
    }

    private function enforceTokenLimit(int $userId): void
    {
        $tokenCount = $this->repository->countUserTokens($userId);
        
        if ($tokenCount >= self::MAX_TOKENS_PER_USER) {
            // Удаляем самый старый токен
            $oldestToken = $this->repository->getOldestUserToken($userId);
            if ($oldestToken) {
                $this->repository->revokeToken($oldestToken->token_hash);
                $this->removeCachedToken($oldestToken->token_hash);
            }
        }
    }

    private function cacheToken(int $userId, string $tokenHash, RefreshToken $token): void
    {
        $key = self::REDIS_PREFIX . $userId . ':' . $tokenHash;
        $data = [
            'fingerprint_hash' => $token->fingerprint_hash,
            'expires_at' => $token->expires_at->timestamp,
            'last_used_at' => $token->last_used_at?->timestamp,
        ];
        
        $ttlSeconds = $token->expires_at->diffInSeconds(Carbon::now());
        Cache::put($key, $data, $ttlSeconds);
    }

    private function getCachedToken(string $tokenHash): ?array
    {
        // Упрощенная версия без Redis - просто возвращаем null
        // В продакшене нужно будет настроить Redis правильно
        return null;
    }

    private function removeCachedToken(string $tokenHash): void
    {
        $key = self::REDIS_PREFIX . '*:' . $tokenHash;
        Cache::forget($key);
    }

    private function clearUserCache(int $userId): void
    {
        // В Laravel Cache нет wildcard delete, поэтому пропускаем
        // В продакшене с Redis это будет работать через Cache::store('redis')
    }
}