<?php

namespace App\Domain\Shared\Presenters;

use App\Domain\Shared\Enums\AuditActionType;
use App\Domain\Shared\Models\AuditLog;

class AuditLogPresenter
{
    public function __construct(
        private AuditLog $log
    ) {}

    public function actionLabel(): string
    {
        $action = explode('.', $this->log->action_type)[1] ?? $this->log->action_type;

        $enum = AuditActionType::tryFrom($action);

        return $enum?->label() ?? $action;
    }

    public function actionBadgeVariant(): string
    {
        $action = explode('.', $this->log->action_type)[1] ?? '';

        $enum = AuditActionType::tryFrom($action);

        return $enum?->badgeVariant() ?? 'info';
    }

    public function entityLabel(): string
    {
        return $this->log->entity_type . ' #' . $this->log->entity_id;
    }
}
