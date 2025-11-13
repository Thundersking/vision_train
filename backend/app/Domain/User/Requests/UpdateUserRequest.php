<?php

declare(strict_types=1);

namespace App\Domain\User\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'email' => [
                'required',
                'email',
                'max:100'
            ],
            'phone' => ['nullable', 'string', 'max:20'],
            'department_id' => ['required', 'integer', 'exists:departments,id'],
            'role_id' => ['required', 'integer', 'exists:roles,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'Поле "Имя" обязательно для заполнения.',
            'last_name.required' => 'Поле "Фамилия" обязательно для заполнения.',
            'email.required' => 'Поле "Email" обязательно для заполнения.',
            'email.email' => 'Поле "Email" должно содержать корректный email адрес.',
            'email.unique' => 'Пользователь с таким email уже существует.',
            'department_id.required' => 'Поле "Отделение" обязательно для заполнения.',
            'department_id.exists' => 'Выбранное отделение не существует.',
            'role_id.exists' => 'Выбранная роль не найдена.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'first_name' => trim((string) $this->first_name),
            'last_name' => trim((string) $this->last_name),
            'middle_name' => $this->middle_name ? trim((string) $this->middle_name) : null,
            'email' => $this->email ? strtolower(trim((string) $this->email)) : null,
            'phone' => $this->phone ? trim((string) $this->phone) : null,
        ]);
    }
}
