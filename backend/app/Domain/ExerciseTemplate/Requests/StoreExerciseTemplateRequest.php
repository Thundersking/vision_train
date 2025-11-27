<?php

declare(strict_types=1);

namespace App\Domain\ExerciseTemplate\Requests;

use App\Domain\ExerciseTemplate\Enums\ExerciseParameterUnit;
use App\Support\Requests\FormRequest;
use Illuminate\Validation\Rule;

final class StoreExerciseTemplateRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'title' => trim((string) $this->title),
            'short_description' => $this->short_description ? trim((string) $this->short_description) : null,
            'difficulty' => $this->difficulty ? trim((string) $this->difficulty) : null,
            'instructions' => $this->instructions ? trim((string) $this->instructions) : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'exercise_type_id' => ['required', 'integer', 'exists:exercise_types,id'],
            'title' => ['required', 'string', 'max:255'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'difficulty' => ['nullable', 'string', 'max:100'],
            'duration_seconds' => ['nullable', 'integer', 'min:1'],
            'instructions' => ['nullable', 'string'],
            'parameters' => ['nullable', 'array'],
            'parameters.*.label' => ['nullable', 'string', 'max:255'],
            'parameters.*.target_value' => ['nullable', 'string', 'max:255'],
            'parameters.*.unit' => ['nullable', 'string', Rule::in($this->unitValues())],
            'steps' => ['required', 'array', 'min:1'],
            'steps.*.title' => ['required', 'string', 'max:255'],
            'steps.*.duration' => ['required', 'integer', 'min:1'],
            'steps.*.description' => ['nullable', 'string'],
            'steps.*.hint' => ['nullable', 'string'],
            'extra_payload' => ['nullable', 'array'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    private function unitValues(): array
    {
        return array_map(static fn(ExerciseParameterUnit $unit) => $unit->value, ExerciseParameterUnit::cases());
    }
}
