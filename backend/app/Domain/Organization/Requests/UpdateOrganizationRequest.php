<?php

declare(strict_types=1);

namespace App\Domain\Organization\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'type' => ['required', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'название организации',
            'type' => 'тип организации',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim((string) $this->name),
        ]);
    }
}
