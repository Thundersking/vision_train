<?php

declare(strict_types=1);

namespace App\Domain\ExerciseTemplate\Requests;

use App\Support\Requests\FormRequest;

final class UpdateExerciseTemplateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'exercise_type_id' => ['sometimes', 'integer', 'exists:exercise_types,id'],
            'title' => ['sometimes', 'string', 'max:255'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'difficulty' => ['nullable', 'string', 'max:100'],
            'duration_seconds' => ['nullable', 'integer', 'min:1'],
            'instructions' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],

            'ball_count' => ['sometimes', 'required', 'integer', 'between:1,50'],
            'ball_size' => ['sometimes', 'required', 'integer', 'between:1,10'],
            'target_accuracy_percent' => ['sometimes', 'required', 'integer', 'between:1,100'],
            'vertical_area' => ['sometimes', 'required', 'string', 'max:255'],
            'horizontal_area' => ['sometimes', 'required', 'string', 'max:255'],
            'distance_area' => ['sometimes', 'required', 'string', 'max:255'],
            'speed' => ['sometimes', 'required', 'string', 'max:255'],
        ];
    }
}
