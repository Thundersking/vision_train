<?php

declare(strict_types=1);

namespace App\Domain\Organization\Events;

use App\Domain\Organization\Models\Organization;

class OrganizationUpdated
{
    public function __construct(
        public readonly Organization $organization,
        public readonly array $originalData
    ) {}
}
