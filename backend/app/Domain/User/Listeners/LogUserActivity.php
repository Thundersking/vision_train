<?php

declare(strict_types=1);

namespace App\Domain\User\Listeners;

use App\Domain\Shared\Enums\AuditActionType;
use App\Domain\Shared\Traits\RecordsAuditLog;
use App\Domain\User\Events\UserCreated;
use App\Domain\User\Events\UserRoleChanged;
use App\Domain\User\Events\UserUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogUserActivity implements ShouldQueue
{
    use RecordsAuditLog;

    public string $queue = 'audit';
    public int $tries = 3;
    public int $backoff = 5;
    public bool $afterCommit = true;

    public function handleCreated(UserCreated $event): void
    {
        $this->recordAudit(
            action: AuditActionType::CREATED,
            entity: $event->user,
            newData: $event->user->toArray()
        );
    }

    public function handleUpdated(UserUpdated $event): void
    {
        $this->recordAudit(
            action: AuditActionType::UPDATED,
            entity: $event->user,
            oldData: $event->oldData,
            newData: $event->user->toArray()
        );
    }

    public function handleRoleChanged(UserRoleChanged $event): void
    {
        $this->recordAudit(
            action: AuditActionType::ROLE_CHANGED,
            entity: $event->user,
            oldData: ['role' => $event->oldRole],
            newData: ['role' => $event->newRole]
        );
    }
}
