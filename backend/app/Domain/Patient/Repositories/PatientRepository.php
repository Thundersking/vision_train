<?php

declare(strict_types=1);

namespace App\Domain\Patient\Repositories;

use App\Domain\Patient\Models\Patient;

interface PatientRepository
{
    public function save(Patient $patient): void;

    public function delete(int $id): void;
}