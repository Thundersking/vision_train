<?php

declare(strict_types=1);

namespace App\Domain\Department\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DepartmentDetailResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'is_active' => $this->is_active,
            'utc_offset_minutes' => $this->utc_offset_minutes,
            'timezone_display' => $this->getTimezoneDisplayName(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'organization' => $this->whenLoaded('organization', fn() => [
                'id' => $this->organization->id,
                'name' => $this->organization->name,
            ]),
        ];
    }
}
