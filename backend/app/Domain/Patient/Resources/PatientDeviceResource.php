<?php

declare(strict_types=1);

namespace App\Domain\Patient\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientDeviceResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $device = $this->whenLoaded('device');

        return [
            'id' => $this->id,
            'patient_id' => $this->patient_id,
            'device_id' => $this->device_id,
            'is_primary' => $this->is_primary,
            'assigned_at' => $this->assigned_at,
            'notes' => $this->notes,
            'device' => $device ? [
                'uuid' => $device->uuid,
                'name' => $device->name,
                'type' => $device->type,
                'serial_number' => $device->serial_number,
                'model' => $device->model,
                'manufacturer' => $device->manufacturer,
                'firmware_version' => $device->firmware_version,
                'last_sync_at' => $device->last_sync_at,
                'is_active' => $device->is_active,
            ] : null,
            'assigned_by' => $this->whenLoaded('assignedBy', fn() => [
                'id' => $this->assignedBy->id,
                'uuid' => $this->assignedBy->uuid,
                'full_name' => $this->assignedBy->name,
                'email' => $this->assignedBy->email,
            ]),
        ];
    }
}
