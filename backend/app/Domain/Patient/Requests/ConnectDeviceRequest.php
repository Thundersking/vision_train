<?php

declare(strict_types=1);

namespace App\Domain\Patient\Requests;

use App\Support\Requests\FormRequest;

class ConnectDeviceRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'device_identifier' => ['required', 'string', 'max:255'],
            'device_type' => ['required', 'string', 'max:100'],
            'device_name' => ['nullable', 'string', 'max:255'],
            'model' => ['nullable', 'string', 'max:100'],
            'manufacturer' => ['nullable', 'string', 'max:100'],
            'firmware_version' => ['nullable', 'string', 'max:100'],
            'app_version' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
