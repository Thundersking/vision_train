<?php

declare(strict_types=1);

namespace App\Domain\ExerciseType\Requests;

use App\Domain\ExerciseType\Enums\ExerciseDimension;
use App\Support\Requests\FormRequest;
use Illuminate\Validation\Rule;

final class UpdateExerciseTypeRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => $this->has('name') ? trim((string) $this->name) : null,
            'slug' => $this->has('slug') && $this->slug !== null
                ? str($this->slug)->slug()->toString()
                : null,
            'description' => $this->has('description') && $this->description !== null
                ? trim((string) $this->description)
                : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'slug' => ['sometimes', 'string', 'max:255'],
            'dimension' => ['sometimes', Rule::in(array_map(fn(ExerciseDimension $dimension) => $dimension->value, ExerciseDimension::cases()))],
            'is_customizable' => ['sometimes', 'boolean'],
            'description' => ['nullable', 'string', 'max:2000'],
            'metrics_json' => ['nullable', 'json'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
