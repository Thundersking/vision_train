<?php

declare(strict_types=1);

namespace App\Domain\User\Guards;
use App\Domain\User\Models\User;
use App\Domain\User\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

final readonly class UserGuard
{
    public function __construct(private UserRepository $repository) {}

    /**
     * Проверка на уникальность email в организации при создании пользователя.
     * @throws ValidationException
     */
    public function ensureEmailUnique(string $email): void
    {
        if ($this->repository->emailExists(mb_strtolower($email))) {
            throw ValidationException::withMessages([
                'email' => 'Email уже используется в организации',
            ]);
        }
    }

    /**
     * Проверка на уникальность email в организации при обновлении, исключая текущего пользователя.
     * @throws ValidationException
     */
    public function ensureEmailUniqueExceptUuid(string $email, string $exceptUuid): void
    {
        if ($this->repository->emailExistsExceptUserUuid($email, $exceptUuid)) {
            throw ValidationException::withMessages([
                'email' => 'Email уже используется в организации',
            ]);
        }
    }

    /**
     * Валидация учетных данных пользователя.
     * @throws ValidationException
     */
    public function validateCredentials(string $email, string $password): User
    {
        $user = $this->repository->findByEmail($email);

        if (!$user || !Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Неверный email или пароль.'],
            ]);
        }

        return $user;
    }

    /**
     * Проверка активности пользователя.
     * @throws ValidationException
     */
    public function validateUserStatus(User $user): void
    {
        if (!$user->is_active) {
            throw ValidationException::withMessages([
                'email' => ['Ваш аккаунт деактивирован.'],
            ]);
        }
    }
}
