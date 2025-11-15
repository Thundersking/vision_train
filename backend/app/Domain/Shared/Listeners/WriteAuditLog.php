<?php

namespace App\Domain\Shared\Listeners;

use App\Domain\Shared\Traits\RecordsAuditLog;
use Illuminate\Contracts\Queue\ShouldQueue;

class WriteAuditLog implements ShouldQueue
{
    use RecordsAuditLog;

    /**
     * Слушает любые бизнес-события с action/targetId/targetType/meta
     * Пример: event(new UserCreated($user));
     */
    public function handle(object $event): void
    {
        // Универсальная структура события: action, targetId, targetType, meta
        $action      = $event->action ?? class_basename($event);
        $targetId    = $event->targetId ?? null;
        $targetType  = $event->targetType ?? null;
        $meta        = $event->meta ?? [];

        $this->recordAudit($action, $targetId, $targetType, $meta);
    }
}
