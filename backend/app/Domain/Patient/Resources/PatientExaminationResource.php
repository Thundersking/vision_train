<?php

declare(strict_types=1);

namespace App\Domain\Patient\Resources;

use App\Domain\Patient\Enums\PatientExaminationType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientExaminationResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $typeEnum = PatientExaminationType::tryFrom($this->type);

        return [
            'uuid' => $this->uuid,
            'type' => $this->type,
            'type_label' => $typeEnum?->getDisplayName(),
            'title' => $this->title,
            'description' => $this->description,
            'examination_date' => $this->examination_date,
            'results' => $this->results,
            'recommendations' => $this->recommendations,
            'is_active' => $this->is_active,
            'patient' => $this->whenLoaded('patient', fn() => [
                'uuid' => $this->patient->uuid,
                'full_name' => $this->patient->name,
            ]),
            'doctor' => $this->whenLoaded('doctor', fn() => [
                'uuid' => $this->doctor->uuid,
                'full_name' => $this->doctor->name,
                'email' => $this->doctor->email,
            ]),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
