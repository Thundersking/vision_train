<?php

declare(strict_types=1);

namespace App\Domain\Exercise\Requests;

use App\Support\Requests\FormRequest;

final class StoreExerciseRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'patient_id' => ['required', 'integer', 'exists:patients,id'],
            'exercise_template_id' => ['nullable', 'integer', 'exists:exercise_templates,id'],
            'exercise_type' => ['required', 'string', 'in:2d,3d'],
            
            // Настройки 3D упражнения (nullable, только для 3D)
            'ball_count' => ['nullable', 'integer', 'between:1,50'],
            'ball_size' => ['nullable', 'integer', 'between:1,10'],
            'target_accuracy_percent' => ['nullable', 'integer', 'between:1,100'],
            'vertical_area' => ['nullable', 'string', 'in:full,top,bottom'],
            'horizontal_area' => ['nullable', 'string', 'in:full,left,right'],
            'distance_area' => ['nullable', 'string', 'in:full,near,medium,far'],
            'speed' => ['nullable', 'string', 'in:slow,medium,fast'],
            
            'duration_seconds' => ['nullable', 'integer', 'min:0'],
            
            // Результаты
            'fatigue_right_eye' => ['nullable', 'integer', 'between:1,5'],
            'fatigue_left_eye' => ['nullable', 'integer', 'between:1,5'],
            'fatigue_head' => ['nullable', 'integer', 'between:1,5'],
            'patient_decision' => ['nullable', 'string', 'in:continue,stop'],
            'notes' => ['nullable', 'string'],
            
            'started_at' => ['nullable', 'date'],
            'completed_at' => ['nullable', 'date'],
        ];
    }
}

