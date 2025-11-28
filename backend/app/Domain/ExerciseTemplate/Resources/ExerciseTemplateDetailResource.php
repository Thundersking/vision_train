<?php

declare(strict_types=1);

namespace App\Domain\ExerciseTemplate\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class ExerciseTemplateDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'title' => $this->title,
            'short_description' => $this->short_description,
            'difficulty' => $this->difficulty,
            'is_active' => $this->is_active,
            'duration_seconds' => $this->duration_seconds,
            'instructions' => $this->instructions,
            // Настройки 3D упражнения (nullable, только для 3D типов)
            'ball_count' => $this->ball_count,
            'ball_size' => $this->ball_size,
            'target_accuracy_percent' => $this->target_accuracy_percent,
            'vertical_area' => $this->vertical_area,
            'horizontal_area' => $this->horizontal_area,
            'distance_area' => $this->distance_area,
            'speed' => $this->speed,
            'type' => $this->whenLoaded('type', function () {
                return [
                    'id' => $this->type->id,
                    'uuid' => $this->type->uuid,
                    'name' => $this->type->name,
                    'dimension' => $this->type->dimension,
                ];
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
