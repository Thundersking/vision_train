<?php

declare(strict_types=1);

namespace App\Domain\ExerciseTemplate\Requests;

use App\Support\Requests\FormSearchRequest;

final class ExerciseTemplateSearchRequest extends FormSearchRequest
{
    public function rules(): array
    {
        return [
            'search' => ['sometimes', 'string', 'max:255'],
            'exercise_type' => ['sometimes', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
