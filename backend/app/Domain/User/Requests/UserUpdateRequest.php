<?php

declare(strict_types=1);

namespace App\Domain\User\Requests;

use App\Support\Requests\FormRequest;

class UserUpdateRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'first_name' => trim((string)$this->first_name),
            'last_name' => trim((string)$this->last_name),
            'middle_name' => $this->middle_name ? trim((string)$this->middle_name) : null,
            'email' => $this->email ? strtolower(trim((string)$this->email)) : null,
            'phone' => $this->phone ? trim((string)$this->phone) : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'first_name' => ['sometimes', 'required', 'string', 'max:100'],
            'last_name' => ['sometimes', 'required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'email' => ['sometimes', 'required', 'email', 'max:100'],
            'phone' => ['nullable', 'string', 'max:20'],
            'department_id' => ['sometimes', 'required', 'integer', 'exists:departments,id'],
            'role_id' => ['sometimes', 'required', 'integer', 'exists:roles,id'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}