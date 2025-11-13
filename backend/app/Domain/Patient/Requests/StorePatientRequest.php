<?php

declare(strict_types=1);

namespace App\Domain\Patient\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePatientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

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

    public function attributes(): array
    {
        return [
            'first_name' => 'имя',
            'last_name' => 'фамилия',
            'middle_name' => 'отчество',
            'email' => 'электронная почта',
            'phone' => 'телефон',
            'gender' => 'пол',
            'hand_size_cm' => 'размер руки (см)',
            'birth_date' => 'дата рождения',
            'user_id' => 'лечащий врач',
            'notes' => 'примечания',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'first_name' => trim((string) $this->first_name),
            'last_name' => trim((string) $this->last_name),
            'middle_name' => $this->middle_name ? trim((string) $this->middle_name) : null,
            'email' => $this->email ? strtolower(trim((string) $this->email)) : null,
            'phone' => trim((string) $this->phone),
            'gender' => $this->gender ? strtolower(trim((string) $this->gender)) : null,
            'notes' => $this->notes ? trim((string) $this->notes) : null,
        ]);
    }

    public function messages(): array
    {
        return [
            'hand_size_cm.min' => 'Размер руки должен быть не менее 5 см',
            'hand_size_cm.max' => 'Размер руки должен быть не более 30 см',
            'birth_date.before' => 'Дата рождения должна быть в прошлом',
            'gender.in' => 'Пол должен быть: мужской или женский',
        ];
    }
}
