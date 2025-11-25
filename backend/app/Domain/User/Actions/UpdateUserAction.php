<?php

declare(strict_types=1);

namespace App\Domain\User\Actions;

use App\Domain\Shared\Enums\AuditActionType;
use App\Domain\Shared\Traits\RecordsAuditLog;
use App\Domain\User\Guards\UserGuard;
use App\Domain\User\Models\User;
use App\Domain\User\Repositories\UserRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

final class UpdateUserAction
{
    use RecordsAuditLog;

    public function __construct(private readonly UserRepository $repository, private readonly UserGuard $guard)
    {
    }

    /**
     * @throws ValidationException
     * @throws \Exception
     */
    public function execute(string $uuid, array $data): User
    {
        $user = $this->repository->findByUuid($uuid);

        if (!$user) {
            throw new ModelNotFoundException();
        }

        // Проверяем уникальность email если он изменяется
        if (isset($data['email']) && $data['email'] !== $user->email) {
            $this->guard->ensureEmailUniqueExceptUuid($data['email'], $user->uuid);
        }

        return DB::transaction(function () use ($user, $data) {
            $oldData = $user->toArray();

            if (!empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }

            $newData = $this->repository->update($user->id, $data);

            if (!empty($newData['roles']) && is_array($newData['roles'])) {
                $user->syncRoles($newData['roles']);
            }

            $this->recordAudit(
                action: AuditActionType::UPDATED,
                entity: $user,
                oldData: array_diff_key($oldData, ['password' => null]),
                newData: array_diff_key((array)$newData, ['password' => null])
            );

            return $newData;
        });
    }
}
