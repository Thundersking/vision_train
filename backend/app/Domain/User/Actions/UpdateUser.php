<?php

declare(strict_types=1);

namespace App\Domain\User\Actions;

use App\Domain\User\Events\UserRoleChanged;
use App\Domain\User\Events\UserUpdated;
use App\Domain\User\Guards\UserGuard;
use App\Domain\User\Models\User;
use App\Domain\User\Queries\UserQueries;
use App\Domain\User\Repositories\UserRepository;
use App\Domain\RBAC\Queries\RoleQueries;
use Illuminate\Support\Facades\DB;

final readonly class UpdateUser
{
    public function __construct(
        private UserRepository $repository,
        private UserQueries    $userQueries,
        private UserGuard      $guard,
        private RoleQueries    $roleQueries
    ) {}

    public function execute(string $userUuid, array $data): User
    {
        // Получаем пользователя через UserQueries
        $user = $this->userQueries->findByUuid($userUuid);

        if (!$user) {
            throw new \InvalidArgumentException("Пользователь с UUID {$userUuid} не найден");
        }

        if (isset($data['email']) && mb_strtolower($data['email']) !== mb_strtolower($user->email)) {
            $this->guard->ensureEmailUniqueExceptUuid($user->organization_id, $data['email'], $user->uuid);
        }

        return DB::transaction(function () use ($user, $data) {
            $oldData = $user->toArray();
            $oldRoleId = $user->roles->first()?->id;

            // Обрабатываем роль отдельно
            $roleId = null;
            if (isset($data['role_id'])) {
                $roleId = (int) $data['role_id'];
                unset($data['role_id']);
            }

            $user->fill($data);
            $this->repository->save($user);

            // Обновляем роль если она изменилась
            if ($roleId && $oldRoleId !== $roleId) {
                $this->assignRoleToUser($user, $roleId);
                event(new UserRoleChanged($user, (string)$oldRoleId, (string)$roleId));
            }

            event(new UserUpdated($user, $oldData));

            return $user->fresh();
        });
    }

    private function assignRoleToUser(User $user, int $roleId): void
    {
        $role = $this->roleQueries->findById($roleId);

        if (!$role || $role->organization_id !== $user->organization_id) {
            throw new \InvalidArgumentException("Роль с ID '{$roleId}' не найдена в организации");
        }

        // Удаляем старые роли и назначаем новую
        $user->syncRoles([$role]);
    }
}
