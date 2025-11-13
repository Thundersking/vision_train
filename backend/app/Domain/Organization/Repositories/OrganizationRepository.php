<?php

declare(strict_types=1);

namespace App\Domain\Organization\Repositories;

use App\Domain\Organization\Models\Organization;

interface OrganizationRepository
{
    public function update(Organization $organization, array $data): void;
}
