<?php

declare(strict_types=1);

namespace App\Domain\Organization\Actions;

use App\Domain\Organization\Models\Organization;
use App\Domain\Organization\Repositories\OrganizationRepository;
use App\Domain\Shared\Enums\AuditActionType;
use App\Domain\Shared\Traits\RecordsAuditLog;
use Illuminate\Support\Facades\DB;

final class UpdateOrganizationAction
{
    use RecordsAuditLog;

    public function __construct(
        private readonly OrganizationRepository $repository,
    ) {}

    public function execute(Organization $organization, array $data): Organization
    {
        return DB::transaction(function () use ($organization, $data) {
            $oldData = $organization->toArray();

            $updatedOrganization = $this->repository->update($organization->id, $data);
            $newData = $updatedOrganization?->toArray();

            $this->recordAudit(
                action: AuditActionType::UPDATED,
                entity: $organization,
                oldData: $oldData,
                newData: $newData,
            );

            return $updatedOrganization;
        });
    }
}
