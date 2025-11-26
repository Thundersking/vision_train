<?php

declare(strict_types=1);

namespace App\Domain\Patient\Controllers;

use App\Domain\Patient\Actions\ConnectDeviceUsingTokenAction;
use App\Domain\Patient\Repositories\ConnectionTokenRepository;
use App\Domain\Patient\Requests\ConnectDeviceRequest;
use App\Domain\Patient\Resources\PatientDeviceResource;
use App\Domain\Patient\Resources\PatientDetailResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Validation\ValidationException;

final class MobileConnectionTokenController extends Controller
{
    public function __construct(
        private readonly ConnectionTokenRepository $connectionTokens,
        private readonly ConnectDeviceUsingTokenAction $action,
    ) {
    }

    public function store(string $token, ConnectDeviceRequest $request): JsonResponse
    {
        $connectionToken = $this->connectionTokens->findValidByToken($token);

        if (!$connectionToken) {
            throw ValidationException::withMessages([
                'token' => ['Недействительный или истекший токен подключения.'],
            ]);
        }

        $assignment = $this->action->execute($connectionToken, $request->validated());

        $patient = $connectionToken->patient->loadMissing(['doctor', 'organization']);

        return response()->json([
            'message' => 'Устройство успешно подключено.',
            'patient' => (new PatientDetailResource($patient))->resolve($request),
            'device' => (new PatientDeviceResource($assignment))->toArray($request),
        ]);
    }
}
