<?php

declare(strict_types=1);

namespace App\Domain\Patient\Services;

use App\Domain\Patient\Models\ConnectionToken;

final class ConnectionTokenPayloadService
{
    public function build(ConnectionToken $token): array
    {
        return [
            'token' => $token->token,
            'endpoint' => $this->buildEndpoint($token->token),
            'expires_at' => $token->expires_at,
        ];
    }

    private function buildEndpoint(string $token): string
    {
        return route('mobile.connection-tokens.activate', ['token' => $token]);
    }
}
