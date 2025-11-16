<?php

declare(strict_types=1);

namespace App\Domain\Auth\Actions;

use App\Domain\Auth\Services\JWTService;
use App\Domain\Auth\Services\RefreshTokenService;

final class LogoutAction
{
    public function __construct(
        private JWTService $jwtService,
        private RefreshTokenService $refreshTokenService
    ) {}

    public function execute(string $accessToken, ?string $refreshToken = null): void
    {
        // 1. Добавляем access token в blacklist
        $this->jwtService->blacklistToken($accessToken);

        // 2. Удаляем refresh token если предоставлен
        if ($refreshToken) {
            $this->refreshTokenService->revokeRefreshToken($refreshToken);
        }
    }
}