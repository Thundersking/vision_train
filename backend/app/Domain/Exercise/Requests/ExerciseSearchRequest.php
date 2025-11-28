<?php

declare(strict_types=1);

namespace App\Domain\Exercise\Requests;

use App\Support\Requests\FormSearchRequest;

final class ExerciseSearchRequest extends FormSearchRequest
{
    public function rules(): array
    {
        return [
            'patient_id' => ['sometimes', 'integer', 'exists:patients,id'],
            'exercise_type' => ['sometimes', 'string', 'in:2d,3d'],
            'exercise_template_id' => ['sometimes', 'integer', 'exists:exercise_templates,id'],
            'started_at_from' => ['sometimes', 'date'],
            'started_at_to' => ['sometimes', 'date'],
            'completed_at_from' => ['sometimes', 'date'],
            'completed_at_to' => ['sometimes', 'date'],
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ];
    }
}

