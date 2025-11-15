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

    public function __construct(private UserRepository $repository)
    {
    }

    /**
     * @throws \Exception
     */
    public function execute(string $uuid): bool
    {
        $user = $this->repository->findByUuid($uuid);

        if (!$user) {
            throw new ModelNotFoundException();
        }

        return DB::transaction(function () use ($user) {
            $userData = $user->toArray();

            $result = $user->delete();

            $this->recordAudit(
                action: AuditActionType::DELETED,
                entity: $user,
                oldData: array_diff_key($userData, ['password' => null])
            );

            return $result;
        });
    }
}
