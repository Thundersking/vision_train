<?php

declare(strict_types=1);

namespace App\Domain\ExerciseTemplate\Requests;

use App\Support\Requests\FormRequest;

final class StoreExerciseTemplateRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'title' => trim((string) $this->title),
            'short_description' => $this->short_description ? trim((string) $this->short_description) : null,
            'difficulty' => $this->difficulty ? trim((string) $this->difficulty) : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'exercise_type_id' => ['required', 'integer', 'exists:exercise_types,id'],
            'title' => ['required', 'string', 'max:255'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'difficulty' => ['nullable', 'string', 'max:100'],
            'payload_json' => ['required', 'json'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
