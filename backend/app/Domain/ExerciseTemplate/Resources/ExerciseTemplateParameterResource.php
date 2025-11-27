<?php

declare(strict_types=1);

namespace App\Domain\ExerciseTemplate\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class ExerciseTemplateParameterResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'label' => $this->label,
            'key' => $this->key,
            'target_value' => $this->target_value,
            'unit' => $this->unit,
        ];
    }
}
