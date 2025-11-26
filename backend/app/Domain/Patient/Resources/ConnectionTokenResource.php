<?php

declare(strict_types=1);

namespace App\Domain\Patient\Resources;

use App\Domain\Patient\Enums\ConnectionTokenStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConnectionTokenResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'token' => $this->token,
            'status' => $this->status instanceof ConnectionTokenStatus
                ? $this->status->value
                : $this->status,
            'status_label' => $this->status instanceof ConnectionTokenStatus
                ? $this->status->label()
                : null,
            'expires_at' => $this->expires_at,
            'used_at' => $this->used_at,
            'created_at' => $this->created_at,
            'creator' => $this->whenLoaded('creator', fn() => [
                'id' => $this->creator->id,
                'uuid' => $this->creator->uuid,
                'full_name' => $this->creator->name,
                'email' => $this->creator->email,
            ]),
        ];
    }
}
