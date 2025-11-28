<?php

declare(strict_types=1);

namespace App\Domain\Exercise\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class ExerciseListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'exercise_type' => $this->exercise_type?->value,
            'exercise_type_label' => $this->exercise_type_label,
            'duration_seconds' => $this->duration_seconds,
            'started_at' => $this->started_at,
            'completed_at' => $this->completed_at,
            'patient' => $this->patient ? [
                'uuid' => $this->patient->uuid,
                'full_name' => $this->patient->name,
            ] : null,
            'template' => $this->template ? [
                'uuid' => $this->template->uuid,
                'title' => $this->template->title,
            ] : null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

