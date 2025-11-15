<?php

declare(strict_types=1);

namespace App\Domain\User\Actions;

use App\Domain\Shared\Enums\AuditActionType;
use App\Domain\Shared\Traits\RecordsAuditLog;
use App\Domain\User\Models\User;
use App\Domain\User\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;

final class ToggleUserStatusAction
{
    use RecordsAuditLog;

    public function __construct(private UserRepository $repository)
    {
    }

    public function execute(string $uuid): User
    {
        $user = $this->repository->findByUuid($uuid);

        if (!$user) {
            throw new \Exception('Пользователь не найден');
        }

        return DB::transaction(function () use ($user) {
            $oldData = $user->toArray();
            $newStatus = !$user->is_active;

            $user->update(['is_active' => $newStatus]);

            $this->recordAudit(
                action: AuditActionType::UPDATED,
                entity: $user,
                oldData: ['is_active' => !$newStatus],
                newData: ['is_active' => $newStatus]
            );

            return $user->refresh();
        });
    }
}
