<?php

declare(strict_types=1);

namespace App\Domain\Auth\Actions;

use App\Domain\Auth\Services\JWTService;
use App\Domain\Auth\Services\RefreshTokenService;
use App\Domain\Auth\Services\FingerprintService;
use App\Domain\User\Guards\UserGuard;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

final class LoginAction
{
    public function __construct(
        private JWTService $jwtService,
        private RefreshTokenService $refreshTokenService,
        private FingerprintService $fingerprintService,
        private UserGuard $userGuard
    ) {}

    /**
     * @throws ValidationException
     */
    public function execute(array $credentials, Request $request): array
    {
        // 1. Валидация credentials через UserGuard
        $user = $this->userGuard->validateCredentials($credentials['email'], $credentials['password']);

        // 2. Проверка активности пользователя через UserGuard
        $this->userGuard->validateUserStatus($user);

        // 3. Генерация fingerprint
        $fingerprint = $this->fingerprintService->generate($request);

        // 4. Генерация access token
        $tokenData = $this->jwtService->generateTokenPair($user, $fingerprint);

        // 5. Создание refresh token
        $refreshToken = $this->refreshTokenService->createRefreshToken(
            $user,
            $fingerprint,
            $request->ip(),
            $request->userAgent()
        );

        return [
            'user' => $user,
            'access_token' => $tokenData['access_token'],
            'token_type' => $tokenData['token_type'],
            'expires_in' => $tokenData['expires_in'],
            'refresh_token' => $refreshToken->original_token,
        ];
    }
}
