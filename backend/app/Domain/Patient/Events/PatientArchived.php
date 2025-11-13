<?php

declare(strict_types=1);

namespace App\Domain\Patient\Events;

use App\Domain\Patient\Models\Patient;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PatientArchived
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly Patient $patient
    ) {}
}