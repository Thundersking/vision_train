<?php

declare(strict_types=1);

namespace App\Domain\Exercise\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class ExerciseDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'exercise_type' => $this->exercise_type?->value,
            'exercise_type_label' => $this->exercise_type_label,
            
            // Настройки 3D упражнения
            'ball_count' => $this->ball_count,
            'ball_size' => $this->ball_size,
            'target_accuracy_percent' => $this->target_accuracy_percent,
            'vertical_area' => $this->vertical_area,
            'horizontal_area' => $this->horizontal_area,
            'distance_area' => $this->distance_area,
            'speed' => $this->speed,
            
            'duration_seconds' => $this->duration_seconds,
            
            // Результаты
            'fatigue_right_eye' => $this->fatigue_right_eye,
            'fatigue_left_eye' => $this->fatigue_left_eye,
            'fatigue_head' => $this->fatigue_head,
            'patient_decision' => $this->patient_decision,
            'notes' => $this->notes,
            
            'started_at' => $this->started_at,
            'completed_at' => $this->completed_at,
            
            'patient_id' => $this->patient_id,
            'patient' => $this->whenLoaded('patient', function () {
                return [
                    'uuid' => $this->patient->uuid,
                    'full_name' => $this->patient->name,
                ];
            }),
            'template' => $this->whenLoaded('template', function () {
                return [
                    'uuid' => $this->template->uuid,
                    'title' => $this->template->title,
                ];
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

