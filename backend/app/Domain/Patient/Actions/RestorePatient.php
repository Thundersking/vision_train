<?php

declare(strict_types=1);

namespace App\Domain\Patient\Actions;

use App\Domain\Patient\Events\PatientRestored;
use App\Domain\Patient\Queries\PatientQueries;
use App\Domain\Patient\Repositories\PatientRepository;
use Illuminate\Support\Facades\DB;

final readonly class RestorePatient
{
    public function __construct(
        private PatientRepository $repository,
        private PatientQueries $patientQueries
    ) {}

    /**
     * Восстанавливает пациента (активация)
     */
    public function execute(string $patientUuid): void
    {
        $patient = $this->patientQueries->findByUuid($patientUuid);

        if (!$patient) {
            throw new \InvalidArgumentException("Пациент с UUID {$patientUuid} не найден");
        }

        if ($patient->is_active) {
            throw new \InvalidArgumentException('Пациент уже активен');
        }

        DB::transaction(function () use ($patient) {
            // Активируем пациента
            $patient->is_active = true;

            $this->repository->save($patient);

            // Публикуем событие после сохранения
            event(new PatientRestored($patient));
        });
    }
}
