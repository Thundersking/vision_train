<?php

declare(strict_types=1);

namespace App\Domain\Patient\Requests;

use App\Support\Requests\FormSearchRequest;

class PatientSearchRequest extends FormSearchRequest
{
    public function rules(): array
    {
        return [
            'doctor_id' => 'sometimes|integer|exists:users,id',
            'is_active' => 'sometimes|boolean',
            'gender' => 'sometimes|string|in:male,female',
            'search' => 'sometimes|string|max:255',
        ];
    }
}
