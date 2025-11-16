<?php

declare(strict_types=1);

namespace App\Domain\Auth\Actions;

use App\Domain\Auth\Services\JWTService;
use App\Domain\Auth\Services\RefreshTokenService;
use App\Domain\Auth\Services\FingerprintService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

final class RefreshTokenAction
{
    public function __construct(
        private JWTService $jwtService,
        private RefreshTokenService $refreshTokenService,
        private FingerprintService $fingerprintService
    ) {}

    public function execute(string $refreshToken, Request $request): array
    {
        // 1. Генерация текущего fingerprint
        $fingerprint = $this->fingerprintService->generate($request);

        // 2. Валидация refresh token
        $tokenModel = $this->refreshTokenService->validateRefreshToken($refreshToken, $fingerprint);
        
        if (!$tokenModel) {
            throw ValidationException::withMessages([
                'refresh_token' => ['Invalid or expired refresh token.'],
            ]);
        }

        // 3. Получаем пользователя
        $user = $tokenModel->user;

        // 4. Проверяем активность пользователя
        if (!$user->is_active) {
            // Удаляем все токены неактивного пользователя
            $this->refreshTokenService->revokeAllUserTokens($user->id);
            
            throw ValidationException::withMessages([
                'user' => ['User account is deactivated.'],
            ]);
        }

        // 5. Генерация новой пары токенов
        $newTokenData = $this->jwtService->generateTokenPair($user, $fingerprint);

        // 6. Создание нового refresh token
        $newRefreshToken = $this->refreshTokenService->createRefreshToken(
            $user,
            $fingerprint,
            $request->ip(),
            $request->userAgent()
        );

        // 7. Инвалидация старого refresh token
        $this->refreshTokenService->revokeRefreshToken($refreshToken);

        return [
            'user' => $user,
            'access_token' => $newTokenData['access_token'],
            'token_type' => $newTokenData['token_type'],
            'expires_in' => $newTokenData['expires_in'],
            'refresh_token' => $newRefreshToken->original_token,
        ];
    }
}