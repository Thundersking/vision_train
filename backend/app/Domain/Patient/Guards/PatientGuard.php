<?php

declare(strict_types=1);

namespace App\Domain\Patient\Guards;

use App\Domain\Patient\Queries\PatientQueries;
use App\Domain\User\Repositories\UserRepository;
use Illuminate\Validation\ValidationException;

final readonly class PatientGuard
{
    public function __construct(
        private PatientQueries $queries,
        private UserRepository $userRepository
    ) {}

    /**
     * Проверка корректности размера руки (5-30 см)
     */
    public function ensureHandSizeValid(float $handSize): void
    {
        if ($handSize < 5.0 || $handSize > 30.0) {
            throw ValidationException::withMessages([
                'hand_size_cm' => 'Размер руки должен быть от 5 до 30 см',
            ]);
        }
    }

    /**
     * Проверка, что врач принадлежит организации и активен
     */
    public function ensureDoctorFromSameOrganization(int $doctorId, int $organizationId): void
    {
        $doctor = $this->userRepository->findActiveInOrganization($doctorId, $organizationId);

        if (!$doctor) {
            throw ValidationException::withMessages([
                'user_id' => 'Врач не найден или не принадлежит данной организации',
            ]);
        }
    }

    /**
     * Проверка уникальности email в организации при создании пациента
     */
    public function ensureEmailUniqueInOrganization(string $email, int $organizationId): void
    {
        if ($this->queries->emailExists($organizationId, mb_strtolower($email))) {
            throw ValidationException::withMessages([
                'email' => 'Email уже используется другим пациентом в организации',
            ]);
        }
    }

    /**
     * Проверка уникальности email в организации при обновлении, исключая текущего пациента
     */
    public function ensureEmailUniqueInOrganizationExceptPatient(string $email, int $organizationId, string $exceptPatientUuid): void
    {
        if ($this->queries->emailExistsExceptPatientUuid($organizationId, $email, $exceptPatientUuid)) {
            throw ValidationException::withMessages([
                'email' => 'Email уже используется другим пациентом в организации',
            ]);
        }
    }
}
