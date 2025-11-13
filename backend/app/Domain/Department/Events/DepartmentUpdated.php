<?php

declare(strict_types=1);

namespace App\Domain\Department\Events;

use App\Domain\Department\Models\Department;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DepartmentUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly Department $department,
        public readonly array $originalData = []
    ) {}
}