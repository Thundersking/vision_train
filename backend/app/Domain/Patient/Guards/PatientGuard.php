<?php

declare(strict_types=1);

namespace App\Domain\Patient\Guards;

use App\Domain\Patient\Repositories\PatientRepository;
use Illuminate\Validation\ValidationException;

final readonly class PatientGuard
{
    public function __construct(private PatientRepository $repository) {}

    /**
     * Проверяем уникальность email в рамках организации при создании пациента.
     * @throws ValidationException
     */
    public function ensureEmailUnique(?string $email): void
    {
        if (!$email) {
            return;
        }

        if ($this->repository->emailExists(mb_strtolower($email))) {
            throw ValidationException::withMessages([
                'email' => 'Email уже используется в организации',
            ]);
        }
    }

    /**
     * Проверяем уникальность email при обновлении пациента (исключая текущего по UUID).
     * @throws ValidationException
     */
    public function ensureEmailUniqueExceptUuid(?string $email, string $exceptUuid): void
    {
        if (!$email) {
            return;
        }

        if ($this->repository->emailExistsExceptUuid(mb_strtolower($email), $exceptUuid)) {
            throw ValidationException::withMessages([
                'email' => 'Email уже используется в организации',
            ]);
        }
    }
}
