<?php

declare(strict_types=1);

namespace App\Domain\Patient\Repositories;

use App\Domain\Patient\Models\PatientExamination;
use App\Support\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class PatientExaminationRepository extends BaseRepository
{
    protected static function modelClass(): string
    {
        return PatientExamination::class;
    }

    protected function newQuery(): Builder
    {
        return parent::newQuery()->with(['patient', 'doctor']);
    }

    protected function applyFilters(Builder $query, array $filters): void
    {
        if (!empty($filters['patient_id'])) {
            $query->where('patient_id', (int) $filters['patient_id']);
        }

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['user_id'])) {
            $query->where('user_id', (int) $filters['user_id']);
        }

        if (array_key_exists('is_active', $filters)) {
            $query->where('is_active', (bool) $filters['is_active']);
        }
    }

    public function paginateForPatient(int $patientId, array $filters = []): LengthAwarePaginator
    {
        $filters['patient_id'] = $patientId;

        return $this->paginate($filters);
    }
}
