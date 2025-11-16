<?php

declare(strict_types=1);

namespace App\Domain\Auth\Controllers;

use App\Domain\Auth\Actions\LoginAction;
use App\Domain\Auth\Actions\LogoutAction;
use App\Domain\Auth\Actions\RefreshTokenAction;
use App\Domain\Auth\Actions\RevokeAllSessionsAction;
use App\Domain\Auth\Requests\LoginRequest;
use App\Domain\Auth\Resources\AuthUserResource;
use App\Domain\Auth\Resources\TokenResource;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

final class AuthController
{
    use AuthorizesRequests;

    public function __construct(
        private LoginAction             $loginAction,
        private LogoutAction            $logoutAction,
        private RefreshTokenAction      $refreshTokenAction,
        private RevokeAllSessionsAction $revokeAllSessionsAction
    )
    {
    }

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $result = $this->loginAction->execute(
                $request->validated(),
                $request
            );

            // Возвращаем access token в ответе + устанавливаем cookie
            return response()
                ->json(new TokenResource($result), Response::HTTP_OK)
                ->cookie('refresh_token', $result['refresh_token'], config('jwt.refresh_ttl'), '/', null, false, true, false, 'lax');

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Login failed',
                'errors' => $e->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Internal server error',
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function refresh(Request $request): JsonResponse
    {
        try {
            // Получаем refresh token из cookie или из запроса
            $refreshToken = $request->cookie('refresh_token');

            if (!$refreshToken) {
                return response()->json([
                    'message' => 'Refresh token not provided',
                ], Response::HTTP_UNAUTHORIZED);
            }

            $result = $this->refreshTokenAction->execute($refreshToken, $request);

            // Возвращаем новый access token + обновляем cookie
            return response()
                ->json(new TokenResource($result), Response::HTTP_OK)
                ->cookie('refresh_token', $result['refresh_token'], config('jwt.refresh_ttl'), '/', null, false, true, false, 'lax');

        } catch (ValidationException $e) {
            // Очищаем cookie при ошибке валидации
            $this->clearRefreshTokenCookie();

            return response()->json([
                'message' => 'Token refresh failed',
                'errors' => $e->errors(),
            ], Response::HTTP_UNAUTHORIZED);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Internal server error',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        try {
            $accessToken = $this->extractAccessToken($request);
            $refreshToken = $request->cookie('refresh_token');

            if ($accessToken) {
                $this->logoutAction->execute($accessToken, $refreshToken);
            }

            // Очищаем cookie
            $this->clearRefreshTokenCookie();

            return response()->json([
                'message' => 'Successfully logged out',
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Logout failed',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json(new AuthUserResource($request->user()), Response::HTTP_OK);
    }

    public function revokeAllSessions(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $revokedCount = $this->revokeAllSessionsAction->execute($user);

            return response()->json([
                'message' => 'All sessions revoked successfully',
                'revoked_sessions' => $revokedCount,
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to revoke sessions',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function clearRefreshTokenCookie(): void
    {
        cookie()->queue(cookie()->forget('refresh_token'));
    }

    private function extractAccessToken(Request $request): ?string
    {
        $header = $request->header('Authorization');

        if (!$header || !str_starts_with($header, 'Bearer ')) {
            return null;
        }

        return substr($header, 7);
    }
}
