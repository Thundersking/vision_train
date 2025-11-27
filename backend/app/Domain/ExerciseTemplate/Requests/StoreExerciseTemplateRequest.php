<?php

declare(strict_types=1);

namespace App\Domain\ExerciseTemplate\Requests;

use App\Support\Requests\FormRequest;

final class StoreExerciseTemplateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'exercise_type_id' => ['required', 'integer', 'exists:exercise_types,id'],
            'title' => ['required', 'string', 'max:255'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'difficulty' => ['nullable', 'string', 'max:100'],
            'duration_seconds' => ['nullable', 'integer', 'min:1'],
            'instructions' => ['nullable', 'string'],

            'ball_count' => ['required', 'integer', 'between:1,50'],
            'ball_size' => ['required', 'integer', 'between:1,10'],
            'target_accuracy_percent' => ['required', 'integer', 'between:1,100'],
            'vertical_area' => ['required', 'string', 'max:255'],
            'horizontal_area' => ['required', 'string', 'max:255'],
            'distance_area' => ['required', 'string', 'max:255'],
            'speed' => ['required', 'string', 'max:255'],

            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
