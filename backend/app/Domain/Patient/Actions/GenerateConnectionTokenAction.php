<?php

declare(strict_types=1);

namespace App\Domain\Patient\Actions;

use App\Domain\Patient\Enums\ConnectionTokenStatus;
use App\Domain\Patient\Models\ConnectionToken;
use App\Domain\Patient\Models\Patient;
use App\Domain\Patient\Repositories\ConnectionTokenRepository;
use App\Domain\Shared\Enums\AuditActionType;
use App\Domain\Shared\Traits\RecordsAuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

final class GenerateConnectionTokenAction
{
    use RecordsAuditLog;

    public function __construct(private readonly ConnectionTokenRepository $repository)
    {
    }

    public function execute(Patient $patient, ?string $expiresAt = null): ConnectionToken
    {
        return DB::transaction(function () use ($patient, $expiresAt) {
            $this->repository->invalidateActiveTokens($patient->id);

            $expiration = $expiresAt ? Carbon::parse($expiresAt) : now()->addDays(7);

            $payload = [
                'organization_id' => $patient->organization_id,
                'patient_id' => $patient->id,
                'token' => Str::uuid()->toString(),
                'expires_at' => $expiration,
                'status' => ConnectionTokenStatus::PENDING->value,
                'is_active' => true,
                'created_by' => Auth::id(),
            ];

            /** @var ConnectionToken $token */
            $token = $this->repository->create($payload);

            $this->recordAudit(
                action: AuditActionType::CREATED,
                entity: $token,
                newData: $payload
            );

            return $token;
        });
    }
}
