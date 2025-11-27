<?php

declare(strict_types=1);

namespace App\Domain\ExerciseType\Requests;

use App\Domain\ExerciseType\Enums\ExerciseDimension;
use App\Support\Requests\FormRequest;
use Illuminate\Validation\Rule;

final class StoreExerciseTypeRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim((string) $this->name),
            'slug' => $this->slug ? str($this->slug)->slug()->toString() : null,
            'description' => $this->description ? trim((string) $this->description) : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255'],
            'dimension' => ['required', Rule::in(array_map(fn(ExerciseDimension $dimension) => $dimension->value, ExerciseDimension::cases()))],
            'is_customizable' => ['sometimes', 'boolean'],
            'description' => ['nullable', 'string', 'max:2000'],
            'metrics_json' => ['nullable', 'json'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
