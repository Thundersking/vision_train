<?php

declare(strict_types=1);

namespace App\Domain\Patient\Requests;

use App\Domain\Patient\Enums\Gender;
use App\Support\Requests\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePatientRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'first_name' => $this->first_name ? trim((string) $this->first_name) : null,
            'last_name' => $this->last_name ? trim((string) $this->last_name) : null,
            'middle_name' => $this->middle_name ? trim((string) $this->middle_name) : null,
            'email' => $this->email ? strtolower(trim((string) $this->email)) : null,
            'phone' => $this->phone ? trim((string) $this->phone) : null,
            'notes' => $this->notes ? trim((string) $this->notes) : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'first_name' => ['sometimes', 'string', 'max:255'],
            'last_name' => ['sometimes', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['sometimes', 'string', 'max:20'],
            'gender' => ['sometimes', Rule::in(array_map(fn(Gender $gender) => $gender->value, Gender::cases()))],
            'hand_size_cm' => ['sometimes', 'numeric', 'min:5', 'max:30', 'decimal:0,1'],
            'birth_date' => ['sometimes', 'date', 'before:today'],
            'user_id' => ['sometimes', 'integer', 'exists:users,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
