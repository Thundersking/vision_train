<?php

declare(strict_types=1);

namespace App\Domain\Auth\Services;

use Illuminate\Http\Request;

class FingerprintService
{
    public function generate(Request $request): string
    {
        $components = [
            $request->ip(),
            $request->userAgent() ?? '',
            $request->header('Accept-Language', ''),
            $request->header('Accept-Encoding', ''),
        ];

        $fingerprint = implode('|', $components);
        
        return hash('sha256', $fingerprint);
    }

    public function validate(Request $request, string $expectedFingerprint): bool
    {
        $currentFingerprint = $this->generate($request);
        
        return hash_equals($expectedFingerprint, $currentFingerprint);
    }

    public function hash(string $fingerprint): string
    {
        return hash('sha256', $fingerprint);
    }
}