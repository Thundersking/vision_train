<?php

declare(strict_types=1);

namespace App\Domain\Patient\Requests;

use App\Support\Requests\FormRequest;

class StorePatientRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'gender' => ['required', 'string'],
            'hand_size_cm' => ['required', 'numeric', 'min:5', 'max:30', 'decimal:0,1'],
            'birth_date' => ['required', 'date', 'before:today'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
