<?php

declare(strict_types=1);

namespace App\Domain\User\Guards;
use App\Domain\User\Queries\UserQueries;
use Illuminate\Validation\ValidationException;

final readonly class UserGuard
{
    public function __construct(private UserQueries $queries) {}

    /**
     * Проверка на уникальность email в организации при создании пользователя.
     */
    public function ensureEmailUnique(int $orgId, string $email): void
    {
        if ($this->queries->emailExists($orgId, mb_strtolower($email))) {
            throw ValidationException::withMessages([
                'email' => 'Email уже используется в организации',
            ]);
        }
    }

    /**
     * Проверка на уникальность email в организации при обновлении, исключая текущего пользователя.
     * @throws ValidationException
     */
    public function ensureEmailUniqueExceptUuid(int $orgId, string $email, string $exceptUuid): void
    {
        if ($this->queries->emailExistsExceptUserUuid($orgId, $email, $exceptUuid)) {
            throw ValidationException::withMessages([
                'email' => 'Email уже используется в организации',
            ]);
        }
    }
}
