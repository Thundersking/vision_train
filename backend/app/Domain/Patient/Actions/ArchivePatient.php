<?php

declare(strict_types=1);

namespace App\Domain\Patient\Actions;

use App\Domain\Patient\Events\PatientArchived;
use App\Domain\Patient\Queries\PatientQueries;
use App\Domain\Patient\Repositories\PatientRepository;
use Illuminate\Support\Facades\DB;

final readonly class ArchivePatient
{
    public function __construct(
        private PatientRepository $repository,
        private PatientQueries $patientQueries
    ) {}

    /**
     * Архивирует пациента (деактивация)
     */
    public function execute(string $patientUuid): void
    {
        // Получаем пациента через PatientQueries
        $patient = $this->patientQueries->findByUuid($patientUuid);

        if (!$patient) {
            throw new \InvalidArgumentException("Пациент с UUID {$patientUuid} не найден");
        }

        if (!$patient->is_active) {
            throw new \InvalidArgumentException('Пациент уже архивирован');
        }

        DB::transaction(function () use ($patient) {
            // Деактивируем пациента
            $patient->is_active = false;

            $this->repository->save($patient);

            // Публикуем событие после сохранения
            event(new PatientArchived($patient));
        });
    }
}
