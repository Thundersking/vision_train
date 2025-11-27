<?php

declare(strict_types=1);

namespace App\Domain\ExerciseTemplate\Requests;

use App\Domain\ExerciseTemplate\Enums\ExerciseParameterUnit;
use App\Support\Requests\FormRequest;
use Illuminate\Validation\Rule;

final class UpdateExerciseTemplateRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $payload = [];

        if ($this->has('title')) {
            $payload['title'] = $this->title !== null ? trim((string) $this->title) : null;
        }

        if ($this->has('short_description')) {
            $payload['short_description'] = $this->short_description !== null
                ? trim((string) $this->short_description)
                : null;
        }

        if ($this->has('difficulty')) {
            $payload['difficulty'] = $this->difficulty !== null ? trim((string) $this->difficulty) : null;
        }

        if ($this->has('instructions')) {
            $payload['instructions'] = $this->instructions !== null ? trim((string) $this->instructions) : null;
        }

        $this->merge($payload);
    }

    public function rules(): array
    {
        return [
            'exercise_type_id' => ['sometimes', 'integer', 'exists:exercise_types,id'],
            'title' => ['sometimes', 'string', 'max:255'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'difficulty' => ['nullable', 'string', 'max:100'],
            'duration_seconds' => ['nullable', 'integer', 'min:1'],
            'instructions' => ['nullable', 'string'],
            'parameters' => ['nullable', 'array'],
            'parameters.*.label' => ['nullable', 'string', 'max:255'],
            'parameters.*.target_value' => ['nullable', 'string', 'max:255'],
            'parameters.*.unit' => ['nullable', 'string', Rule::in($this->unitValues())],
            'steps' => ['sometimes', 'array', 'min:1'],
            'steps.*.title' => ['required_with:steps', 'string', 'max:255'],
            'steps.*.duration' => ['required_with:steps', 'integer', 'min:1'],
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
