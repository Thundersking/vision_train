<?php

declare(strict_types=1);

namespace App\Domain\Auth\Middleware;

use App\Domain\Auth\Services\JWTService;
use App\Domain\Auth\Services\FingerprintService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as ResponseInterface;

class JWTAuth
{
    public function __construct(
        private JWTService $jwtService,
        private FingerprintService $fingerprintService
    ) {}

    public function handle(Request $request, Closure $next): ResponseInterface
    {
        // 1. Извлекаем access token из header
        $token = $this->extractTokenFromHeader($request);
        
        if (!$token) {
            return $this->unauthorizedResponse('Token not provided');
        }

        // 2. Валидируем токен и получаем пользователя
        $user = $this->jwtService->validateAccessToken($token);
        
        if (!$user) {
            return $this->unauthorizedResponse('Invalid or expired token');
        }

        // 3. Проверяем fingerprint для дополнительной безопасности
        if (!$this->validateFingerprint($token, $request)) {
            return $this->unauthorizedResponse('Invalid device fingerprint');
        }

        // 4. Проверяем активность пользователя
        if (!$user->is_active) {
            return $this->unauthorizedResponse('User account is deactivated');
        }

        // 5. Устанавливаем пользователя в request
        $request->setUserResolver(fn () => $user);

        return $next($request);
    }

    private function extractTokenFromHeader(Request $request): ?string
    {
        $header = $request->header('Authorization');
        
        if (!$header || !str_starts_with($header, 'Bearer ')) {
            return null;
        }
        
        return substr($header, 7);
    }

    private function validateFingerprint(string $token, Request $request): bool
    {
        try {
            $tokenFingerprint = $this->jwtService->getTokenFingerprint($token);
            
            if (!$tokenFingerprint) {
                return false;
            }
            
            return $this->fingerprintService->validate($request, $tokenFingerprint);
        } catch (\Exception $e) {
            return false;
        }
    }

    private function unauthorizedResponse(string $message): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'message' => $message,
        ], Response::HTTP_UNAUTHORIZED);
    }
}