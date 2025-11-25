<?php

declare(strict_types=1);

namespace App\Domain\User\Actions;

use App\Domain\Shared\Enums\AuditActionType;
use App\Domain\Shared\Traits\RecordsAuditLog;
use App\Domain\User\Models\User;
use App\Domain\User\Repositories\UserRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

final class DeleteUserAction
{
    use RecordsAuditLog;

    public function __construct(private readonly UserRepository $repository)
    {
    }

    /**
     * @throws \Exception
     */
    public function execute(User $user): void
    {
        DB::transaction(function () use ($user) {
            $deleted = $this->repository->delete($user->id);

            if (!$deleted) {
                throw new ModelNotFoundException();
            }

            $this->recordAudit(
                action: AuditActionType::DELETED,
                entity: $user,
            );
        });
    }
}
