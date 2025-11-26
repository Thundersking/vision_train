<?php

declare(strict_types=1);

namespace App\Domain\Patient\Requests;

use App\Domain\Patient\Enums\PatientExaminationType;
use App\Support\Requests\FormSearchRequest;
use Illuminate\Validation\Rule;

class PatientExaminationSearchRequest extends FormSearchRequest
{
    public function rules(): array
    {
        return [
            'type' => ['sometimes', Rule::in(array_map(fn(PatientExaminationType $type) => $type->value, PatientExaminationType::cases()))],
            'is_active' => 'sometimes|boolean',
            'doctor_id' => 'sometimes|integer|exists:users,id',
        ];
    }
}
