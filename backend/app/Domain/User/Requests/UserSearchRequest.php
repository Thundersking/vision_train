<?php

declare(strict_types=1);

namespace App\Domain\User\Requests;

use App\Support\Requests\FormSearchRequest;

class UserSearchRequest extends FormSearchRequest
{
    public function rules(): array
    {
        return [
            'organization_id' => 'sometimes|integer|exists:organizations,id',
            'department_id' => 'sometimes|integer|exists:departments,id',
            'is_active' => 'sometimes|boolean',
            'search' => 'sometimes|string|max:255',
        ];
    }
}
