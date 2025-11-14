<?php

namespace App\Support\Requests;

use Illuminate\Validation\Validator;

abstract class FormSearchRequest extends FormRequest
{
    /**
     * Общие правила для пагинации/сортировки
     */
    protected function gridRules(): array
    {
        return [
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'sort_by' => 'sometimes|string',
            'sort_order' => 'sometimes|in:asc,desc',
        ];
    }

    /**
     * Автоматически подмешиваем 4 поля ко всем наследникам FormSearchRequest.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->addRules($this->gridRules());
    }
}
