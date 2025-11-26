<?php

declare(strict_types=1);

namespace App\Domain\Patient\Requests;

use App\Support\Requests\FormRequest;

class GenerateConnectionTokenRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'expires_at' => ['nullable', 'date', 'after:now'],
        ];
    }
}
