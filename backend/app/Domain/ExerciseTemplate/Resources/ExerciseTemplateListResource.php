<?php

declare(strict_types=1);

namespace App\Domain\ExerciseTemplate\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class ExerciseTemplateListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'exercise_type' => $this->exercise_type,
            'description' => $this->description,
            'difficulty' => $this->difficulty,
            'is_active' => $this->is_active,
            'duration_seconds' => $this->duration_seconds,
            'updated_at' => $this->updated_at,
            'created_at' => $this->created_at,
        ];
    }
}
