<?php

declare(strict_types=1);

namespace App\Domain\User\Requests;

use App\Support\Requests\FormRequest;

class UserCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Авторизация в Action
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100'],
            'phone' => ['nullable', 'string', 'max:20'],
            'department_id' => ['required', 'integer', 'exists:departments,id'],
            'role_id' => ['required', 'integer', 'exists:roles,id'],
        ];
    }

    public function attributes(): array
    {
        return [
            'first_name' => 'имя',
            'last_name' => 'фамилия',
            'middle_name' => 'отчество',
            'email' => 'электронная почта',
            'phone' => 'телефон',
            'department_id' => 'отделение',
            'role_id' => 'роль',
        ];
    }

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
}
