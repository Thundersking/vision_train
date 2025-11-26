<?php

declare(strict_types=1);

namespace App\Domain\Patient\Controllers;

use App\Domain\Device\Repositories\DeviceRepository;
use App\Domain\Patient\Actions\DetachPatientDeviceAction;
use App\Domain\Patient\Actions\GenerateConnectionTokenAction;
use App\Domain\Patient\Repositories\ConnectionTokenRepository;
use App\Domain\Patient\Repositories\PatientDeviceRepository;
use App\Domain\Patient\Repositories\PatientRepository;
use App\Domain\Patient\Requests\GenerateConnectionTokenRequest;
use App\Domain\Patient\Resources\ConnectionTokenResource;
use App\Domain\Patient\Resources\PatientDeviceResource;
use App\Domain\Patient\Services\ConnectionTokenPayloadService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;

final class PatientDeviceController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private readonly PatientRepository $patients,
        private readonly PatientDeviceRepository $patientDevices,
        private readonly ConnectionTokenRepository $connectionTokens,
        private readonly DeviceRepository $devices,
        private readonly ConnectionTokenPayloadService $payloads
    ) {
    }

    public function index(Request $request, string $uuid): AnonymousResourceCollection
    {
        $patient = $this->patients->findWithRelations($uuid);

        if (!$patient) {
            throw new ModelNotFoundException();
        }

        $this->authorize('view', $patient);

        $devices = $this->patientDevices->listForPatient($patient->id);
        $activeToken = $this->connectionTokens->findActiveByPatient($patient->id);

        $tokenData = null;
        if ($activeToken) {
            $tokenData = array_merge(
                (new ConnectionTokenResource($activeToken))->toArray($request),
                ['qr_payload' => $this->payloads->build($activeToken)]
            );
        }

        return PatientDeviceResource::collection($devices)->additional([
            'active_token' => $tokenData,
        ]);
    }

    public function generateToken(
        string $uuid,
        GenerateConnectionTokenRequest $request,
        GenerateConnectionTokenAction $action
    ): ConnectionTokenResource {
        $patient = $this->patients->findWithRelations($uuid);

        if (!$patient) {
            throw new ModelNotFoundException();
        }

        $this->authorize('update', $patient);

        $expiresAt = $request->validated()['expires_at'] ?? null;
        $token = $action->execute($patient, $expiresAt);

        return (new ConnectionTokenResource($token))->additional([
            'qr_payload' => $this->payloads->build($token),
        ]);
    }

    public function destroy(
        string $uuid,
        string $deviceUuid,
        DetachPatientDeviceAction $action
    ): JsonResponse {
        $patient = $this->patients->findWithRelations($uuid);

        if (!$patient) {
            throw new ModelNotFoundException();
        }

        $this->authorize('update', $patient);

        $device = $this->devices->findByUuid($deviceUuid);
        if (!$device) {
            throw new ModelNotFoundException();
        }

        $action->execute($patient, $device);

        return response()->json([
            'message' => 'Запись успешно удалена.',
            'code' => 200,
        ]);
    }
}
