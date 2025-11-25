<?php

declare(strict_types=1);

namespace App\Domain\Department\Guards;

use App\Domain\Department\Repositories\DepartmentRepository;
use Illuminate\Validation\ValidationException;

final readonly class DepartmentGuard
{
    public function __construct(private DepartmentRepository $repository) {}

    /**
     * Проверка на уникальность email в организации при создании отделения.
     * @throws ValidationException
     */
    public function ensureEmailUnique(string|null $email): void
    {
        if (empty($data['email'])) {
            return;
        }

        if ($this->repository->emailExists(mb_strtolower($email))) {
            throw ValidationException::withMessages([
                'email' => 'Email уже используется в организации',
            ]);
        }
    }

    /**
     * Проверка на уникальность email в организации при обновлении, исключая текущее отделение.
     * @throws ValidationException
     */
    public function ensureEmailUniqueExceptUuid(string|null $email, string $exceptUuid): void
    {
        if (empty($data['email'])) {
            return;
        }

        if ($this->repository->emailExistsExceptUuid($email, $exceptUuid)) {
            throw ValidationException::withMessages([
                'email' => 'Email уже используется в организации',
            ]);
        }
    }
}
