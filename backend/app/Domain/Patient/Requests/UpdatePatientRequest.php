<?php

declare(strict_types=1);

namespace App\Domain\Patient\Requests;

use App\Support\Requests\FormRequest;

class UpdatePatientRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'first_name' => ['sometimes', 'string', 'max:255'],
            'last_name' => ['sometimes', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['sometimes', 'string', 'max:20'],
            'gender' => ['sometimes', 'string'],
            'hand_size_cm' => ['sometimes', 'numeric', 'min:5', 'max:30', 'decimal:0,1'],
            'birth_date' => ['sometimes', 'date', 'before:today'],
            'user_id' => ['sometimes', 'integer', 'exists:users,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
