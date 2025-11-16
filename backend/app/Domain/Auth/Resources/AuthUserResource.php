<?php

declare(strict_types=1);

namespace App\Domain\Auth\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthUserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'middle_name' => $this->middle_name,
            'name' => $this->name,
            'initials' => $this->initials(),
            'email' => $this->email,
            'phone' => $this->phone,
            'is_active' => $this->is_active,
            'organization_id' => $this->organization_id,
            'department_id' => $this->department_id,
            'timezone' => $this->getTimezone(),
            'timezone_display' => $this->getTimezoneDisplayName(),
            'roles' => $this->roles->pluck('name'),
            'permissions' => $this->getAllPermissions()->pluck('name'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}