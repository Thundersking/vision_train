<?php

declare(strict_types=1);

namespace App\Domain\Auth\Services;

use App\Domain\User\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class JWTService
{
    public function generateTokenPair(User $user, string $fingerprint): array
    {
        $customClaims = ['fingerprint' => $fingerprint];
        $accessToken = JWTAuth::fromUser($user, $customClaims);
        
        return [
            'access_token' => $accessToken,
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl') * 60, // в секундах
        ];
    }

    public function validateAccessToken(string $token): ?User
    {
        try {
            // Проверяем blacklist
            if ($this->isBlacklisted($token)) {
                return null;
            }

            JWTAuth::setToken($token);
            return JWTAuth::authenticate();
        } catch (JWTException $e) {
            return null;
        }
    }

    public function extractUserFromToken(string $token): ?User
    {
        try {
            return JWTAuth::setToken($token)->authenticate();
        } catch (JWTException $e) {
            return null;
        }
    }

    public function blacklistToken(string $token): void
    {
        try {
            $payload = JWTAuth::setToken($token)->getPayload();
            $jti = $payload->get('jti');
            $exp = $payload->get('exp');
            
            // Добавляем в blacklist с TTL до истечения токена
            $ttlSeconds = $exp - time();
            if ($ttlSeconds > 0) {
                Cache::put("jwt:blacklist:{$jti}", $exp, $ttlSeconds);
            }
        } catch (JWTException $e) {
            // Игнорируем ошибки при добавлении в blacklist
        }
    }

    public function isBlacklisted(string $token): bool
    {
        try {
            $payload = JWTAuth::setToken($token)->getPayload();
            $jti = $payload->get('jti');
            
            return Cache::has("jwt:blacklist:{$jti}");
        } catch (JWTException $e) {
            return true; // Считаем недействительный токен заблокированным
        }
    }

    public function getTokenFingerprint(string $token): ?string
    {
        try {
            $payload = JWTAuth::setToken($token)->getPayload();
            return $payload->get('fingerprint');
        } catch (JWTException $e) {
            return null;
        }
    }

    public function refreshToken(string $token): array
    {
        try {
            $newToken = JWTAuth::refresh($token);
            
            return [
                'access_token' => $newToken,
                'token_type' => 'bearer',
                'expires_in' => config('jwt.ttl') * 60,
            ];
        } catch (JWTException $e) {
            throw new \Exception('Unable to refresh token: ' . $e->getMessage());
        }
    }
}