<?php

declare(strict_types=1);

namespace App\Domain\ExerciseTemplate\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class ExerciseTemplateStepResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'title' => $this->title,
            'duration' => $this->duration,
            'description' => $this->description,
            'hint' => $this->hint,
            'order' => $this->step_order,
        ];
    }
}
