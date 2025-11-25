<?php

declare(strict_types=1);

namespace App\Domain\Department\Requests;

use App\Support\Requests\FormSearchRequest;

class DepartmentSearchRequest extends FormSearchRequest
{
    public function rules(): array
    {
        return [
            'is_active' => 'sometimes|boolean',
            'search' => 'sometimes|string|max:255',
        ];
    }
}
