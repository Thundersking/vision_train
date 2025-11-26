<?php

declare(strict_types=1);

namespace App\Domain\Patient\Requests;

use App\Domain\Patient\Enums\PatientExaminationType;
use App\Support\Requests\FormRequest;
use Illuminate\Validation\Rule;

class StorePatientExaminationRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'title' => trim((string) $this->title),
            'description' => $this->description ? trim((string) $this->description) : null,
            'results' => $this->results ? trim((string) $this->results) : null,
            'recommendations' => $this->recommendations ? trim((string) $this->recommendations) : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'type' => ['required', Rule::in(array_map(fn(PatientExaminationType $type) => $type->value, PatientExaminationType::cases()))],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'examination_date' => ['required', 'date'],
            'results' => ['required', 'string', 'min:50'],
            'recommendations' => ['nullable', 'string', 'max:2000'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
