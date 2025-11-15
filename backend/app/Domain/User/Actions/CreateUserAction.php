<?php

declare(strict_types=1);

namespace App\Domain\User\Actions;

use App\Domain\Shared\Enums\AuditActionType;
use App\Domain\Shared\Traits\RecordsAuditLog;
use App\Domain\User\Guards\UserGuard;
use App\Domain\User\Models\User;
use App\Domain\User\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

final class CreateUserAction
{
    use RecordsAuditLog;

    public function __construct(private readonly UserRepository $repository, private readonly UserGuard $guard)
    {
    }

    /**
     * @throws ValidationException
     */
    public function execute(array $data): User
    {
        $this->guard->ensureEmailUnique($data['email']);

        return DB::transaction(function () use ($data) {
            if (!empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }

            $user = $this->repository->create($data);

            if (!empty($data['roles']) && is_array($data['roles'])) {
                $user->syncRoles($data['roles']);
            }

            $this->recordAudit(
                action: AuditActionType::CREATED,
                entity: $user,
                newData: array_diff_key($data, ['password' => null])
            );

            return $user;
        });
    }
}
