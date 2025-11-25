<?php

declare(strict_types=1);

namespace App\Domain\Organization\Requests;

use App\Domain\Organization\Enums\OrganizationType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrganizationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(array_map(fn($type) => $type->value, OrganizationType::cases()))],
            'subscription_plan' => ['nullable', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:20'],
            'inn' => ['nullable', 'string', 'max:12'],
            'kpp' => ['nullable', 'string', 'max:9'],
            'ogrn' => ['nullable', 'string', 'max:15'],
            'legal_address' => ['nullable', 'string', 'max:500'],
            'actual_address' => ['nullable', 'string', 'max:500'],
            'director_name' => ['nullable', 'string', 'max:255'],
            'license_number' => ['nullable', 'string', 'max:100'],
            'license_issued_at' => ['nullable', 'date'],
            'is_active' => ['boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'название организации',
            'type' => 'тип организации',
            'subscription_plan' => 'тариф',
            'email' => 'электронная почта',
            'phone' => 'телефон',
            'inn' => 'ИНН',
            'kpp' => 'КПП',
            'ogrn' => 'ОГРН',
            'legal_address' => 'юридический адрес',
            'actual_address' => 'фактический адрес',
            'director_name' => 'ФИО руководителя',
            'license_number' => 'номер лицензии',
            'license_issued_at' => 'дата выдачи лицензии',
            'is_active' => 'статус активности',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => $this->name ? trim((string)$this->name) : null,
            'subscription_plan' => $this->subscription_plan ? trim((string)$this->subscription_plan) : null,
            'email' => $this->email ? strtolower(trim((string)$this->email)) : null,
            'phone' => $this->phone ? trim((string)$this->phone) : null,
            'inn' => $this->inn ? trim((string)$this->inn) : null,
            'kpp' => $this->kpp ? trim((string)$this->kpp) : null,
            'ogrn' => $this->ogrn ? trim((string)$this->ogrn) : null,
            'legal_address' => $this->legal_address ? trim((string)$this->legal_address) : null,
            'actual_address' => $this->actual_address ? trim((string)$this->actual_address) : null,
            'director_name' => $this->director_name ? trim((string)$this->director_name) : null,
            'license_number' => $this->license_number ? trim((string)$this->license_number) : null,
            'license_issued_at' => $this->license_issued_at ?: null,
            'is_active' => $this->boolean('is_active', true),
        ]);
    }
}
