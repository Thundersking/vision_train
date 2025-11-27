<?php

declare(strict_types=1);

namespace App\Domain\ExerciseTemplate\Requests;

use App\Support\Requests\FormRequest;

final class StoreExerciseTemplateParameterRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'label' => $this->label ? trim((string)$this->label) : null,
            'target_value' => $this->target_value ? trim((string)$this->target_value) : null,
            'unit' => $this->unit ? trim((string)$this->unit) : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'label' => ['required', 'string', 'max:255'],
            'target_value' => ['nullable', 'string', 'max:255'],
            'unit' => ['nullable', 'string'],
        ];
    }
}
