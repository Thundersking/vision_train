<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories\Eloquent;

use App\Domain\Organization\Models\Organization;
use App\Domain\Organization\Repositories\OrganizationRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class EloquentOrganizationRepository implements OrganizationRepository
{
    public function update(Organization $organization, array $data): void
    {
        DB::transaction(function () use ($organization, $data) {
            $organization->update($data);
            Cache::tags(['organizations', "organization:{$organization->id}"])->flush();
        });
    }
}
