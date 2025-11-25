<?php

declare(strict_types=1);

namespace App\Domain\Organization\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrganizationResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'domain' => $this->domain,
            'type' => $this->type?->value,
            'type_label' => $this->type_label,
            'subscription_plan' => $this->subscription_plan,
            'email' => $this->email,
            'phone' => $this->phone,
            'inn' => $this->inn,
            'kpp' => $this->kpp,
            'ogrn' => $this->ogrn,
            'legal_address' => $this->legal_address,
            'actual_address' => $this->actual_address,
            'director_name' => $this->director_name,
            'license_number' => $this->license_number,
            'license_issued_at' => $this->license_issued_at,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
