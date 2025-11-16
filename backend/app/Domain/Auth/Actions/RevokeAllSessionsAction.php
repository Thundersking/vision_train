<?php

declare(strict_types=1);

namespace App\Domain\Auth\Actions;

use App\Domain\Auth\Services\RefreshTokenService;
use App\Domain\User\Models\User;

final class RevokeAllSessionsAction
{
    public function __construct(
        private RefreshTokenService $refreshTokenService
    ) {}

    public function execute(User $user): int
    {
        // Удаляем все refresh токены пользователя
        return $this->refreshTokenService->revokeAllUserTokens($user->id);
    }
}