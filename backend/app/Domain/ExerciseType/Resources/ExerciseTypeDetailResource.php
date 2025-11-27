<?php

declare(strict_types=1);

namespace App\Domain\ExerciseType\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class ExerciseTypeDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'slug' => $this->slug,
            'dimension' => $this->dimension,
            'is_customizable' => $this->is_customizable,
            'description' => $this->description,
            'metrics' => $this->metrics_json,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
