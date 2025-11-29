<?php

declare(strict_types=1);

namespace App\Domain\ExerciseTemplate\Requests;

use App\Support\Requests\FormRequest;

final class StoreExerciseTemplateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'exercise_type' => ['required', 'string'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
            'difficulty' => ['nullable', 'string', 'max:100'],
            'duration_seconds' => ['nullable', 'integer', 'min:1'],
            'instructions' => ['nullable', 'string'],

            // Настройки 3D упражнения (nullable, только для 3D типов)
            'ball_count' => ['nullable', 'integer', 'between:1,50'],
            'ball_size' => ['nullable', 'integer', 'between:1,10'],
            'target_accuracy_percent' => ['nullable', 'integer', 'between:1,100'],
            'vertical_area' => ['nullable', 'string', 'in:full,top,bottom'],
            'horizontal_area' => ['nullable', 'string', 'in:full,left,right'],
            'distance_area' => ['nullable', 'string', 'in:full,near,medium,far'],
            'speed' => ['nullable', 'string', 'in:slow,medium,fast'],

            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
