<?php

declare(strict_types=1);

namespace App\Domain\User\Actions;

use App\Domain\RBAC\Queries\RoleQueries;
use App\Domain\User\Events\UserCreated;
use App\Domain\User\Guards\UserGuard;
use App\Domain\User\Models\User;
use App\Domain\User\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final readonly class CreateUser
{
    public function __construct(
        private UserRepository $repository,
        private RoleQueries    $roleQueries,
        private UserGuard      $guard
    ) {}

    /**
     * @throws ValidationException
     */
    public function execute(array $data): User
    {
        $this->guard->ensureEmailUnique($data['organization_id'], $data['email']);

        return DB::transaction(function () use ($data) {
            $roleId = $data['role'];
            unset($data['role']);

            // Генерируем пароль автоматически
            $data['password'] = Hash::make(Str::random(12));

            $user = new User($data);
            $this->repository->save($user);

            $this->assignRoleToUser($user, $roleId);

            event(new UserCreated($user));

            return $user->fresh();
        });
    }

    private function assignRoleToUser(User $user, int $roleId): void
    {
        $role = $this->roleQueries->findById($roleId);

        if (!$role || $role->organization_id !== $user->organization_id) {
            throw new \InvalidArgumentException("Роль с ID '{$roleId}' не найдена в организации");
        }

        $user->assignRole($role);
    }
}
