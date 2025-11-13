<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories\Eloquent;

use App\Domain\Patient\Models\Patient;
use App\Domain\Patient\Repositories\PatientRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class EloquentPatientRepository implements PatientRepository
{
    public function save(Patient $patient): void
    {
        DB::transaction(function () use ($patient) {
            $patient->save();
            Cache::tags(['patients', "organization:{$patient->organization_id}"])->flush();
        });
    }

    public function delete(int $id): void
    {
        $patient = Patient::findOrFail($id);

        DB::transaction(function () use ($patient) {
            $patient->delete();
            Cache::tags(['patients', "organization:{$patient->organization_id}"])->flush();
        });
    }
}
