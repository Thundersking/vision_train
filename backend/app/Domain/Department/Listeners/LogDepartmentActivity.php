<?php

declare(strict_types=1);

namespace App\Domain\Department\Listeners;

use App\Domain\Department\Events\DepartmentArchived;
use App\Domain\Department\Events\DepartmentCreated;
use App\Domain\Department\Events\DepartmentRestored;
use App\Domain\Department\Events\DepartmentUpdated;
use App\Domain\Shared\Enums\AuditActionType;
use App\Domain\Shared\Traits\RecordsAuditLog;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogDepartmentActivity implements ShouldQueue
{
    use RecordsAuditLog;

    public string $queue = 'audit';
    public int $tries = 3;
    public int $backoff = 5;
    public bool $afterCommit = true;

    public function handleCreated(DepartmentCreated $event): void
    {
        $this->recordAudit(
            action: AuditActionType::CREATED,
            entity: $event->department,
            newData: $event->department->toArray()
        );
    }

    public function handleUpdated(DepartmentUpdated $event): void
    {
        $this->recordAudit(
            action: AuditActionType::UPDATED,
            entity: $event->department,
            oldData: $event->originalData,
            newData: $event->department->toArray()
        );
    }

    public function handleArchived(DepartmentArchived $event): void
    {
        $this->recordAudit(
            action: AuditActionType::ARCHIVED,
            entity: $event->department,
            newData: ['is_active' => false]
        );
    }

    public function handleRestored(DepartmentRestored $event): void
    {
        $this->recordAudit(
            action: AuditActionType::RESTORED,
            entity: $event->department,
            newData: ['is_active' => true]
        );
    }
}