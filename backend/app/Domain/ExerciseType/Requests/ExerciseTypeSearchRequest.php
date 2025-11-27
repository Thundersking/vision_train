<?php

declare(strict_types=1);

namespace App\Domain\ExerciseType\Requests;

use App\Domain\ExerciseType\Enums\ExerciseDimension;
use App\Support\Requests\FormSearchRequest;
use Illuminate\Validation\Rule;

final class ExerciseTypeSearchRequest extends FormSearchRequest
{
    public function rules(): array
    {
        return [
            'search' => ['sometimes', 'string', 'max:255'],
            'dimension' => ['sometimes', Rule::in(array_map(fn(ExerciseDimension $dimension) => $dimension->value, ExerciseDimension::cases()))],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
