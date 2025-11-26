<?php

declare(strict_types=1);

namespace App\Domain\Patient\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientListResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'middle_name' => $this->middle_name,
            'full_name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'gender' => $this->gender?->value,
            'gender_label' => $this->gender?->getDisplayName(),
            'hand_size_cm' => $this->hand_size_cm,
            'birth_date' => $this->birth_date,
            'is_active' => $this->is_active,
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
