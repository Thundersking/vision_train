<?php

declare(strict_types=1);

namespace App\Domain\Organization\Listeners;

use App\Domain\Organization\Events\OrganizationUpdated;
use App\Domain\Shared\Enums\AuditActionType;
use App\Domain\Shared\Traits\RecordsAuditLog;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogOrganizationActivity implements ShouldQueue
{
    use RecordsAuditLog;

    public string $queue = 'audit';
    public int $tries = 3;
    public int $backoff = 5;
    public bool $afterCommit = true;

    public function handleUpdated(OrganizationUpdated $event): void
    {
        $this->recordAudit(
            action: AuditActionType::UPDATED,
            entity: $event->organization,
            oldData: $event->originalData,
            newData: $event->organization->toArray()
        );
    }
}
