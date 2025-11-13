<?php

declare(strict_types=1);

namespace App\Domain\Department\Requests;

use App\Support\Enums\Timezone;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDepartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'utc_offset_minutes' => [
                'required',
                'numeric',
                Rule::in(collect(Timezone::cases())->map(fn($tz) => $tz->getOffsetMinutes())->toArray())
            ],
            'address' => ['nullable', 'string', 'max:500'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => [
                'nullable',
                'email',
                'max:100',
            ],
            'is_active' => ['boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'название отделения',
            'utc_offset_minutes' => 'часовой пояс',
            'address' => 'адрес',
            'phone' => 'телефон',
            'email' => 'электронная почта',
            'is_active' => 'статус активности',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim((string) $this->name),
            'address' => $this->address ? trim((string) $this->address) : null,
            'phone' => $this->phone ? trim((string) $this->phone) : null,
            'email' => $this->email ? strtolower(trim((string) $this->email)) : null,
            'is_active' => $this->boolean('is_active', true),
        ]);
    }
}