<?php

declare(strict_types=1);

namespace App\Domain\Department\Guards;

use App\Domain\Department\Queries\DepartmentQueries;
use Illuminate\Validation\ValidationException;

final readonly class DepartmentGuard
{
    public function __construct(private DepartmentQueries $queries) {}

    /**
     * Проверка на уникальность email в организации при создании отделения.
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
     * Проверка на уникальность email в организации при обновлении, исключая текущее отделение.
     * @throws ValidationException
     */
    public function ensureEmailUniqueExceptDepartment(int $orgId, string $email, string $exceptUuid): void
    {
        if ($this->queries->emailExistsExceptDepartmentUuid($orgId, $email, $exceptUuid)) {
            throw ValidationException::withMessages([
                'email' => 'Email уже используется в организации',
            ]);
        }
    }
}