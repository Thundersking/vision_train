<?php

declare(strict_types=1);

namespace App\Domain\Organization\Queries;

use App\Domain\Organization\Models\Organization;
use Illuminate\Support\Facades\Cache;

class OrganizationQueries
{
    /**
     * Получить текущую организацию пользователя с полной информацией
     */
    public function getCurrentOrganizationWithFullData(): ?Organization
    {
        $organizationId = session('current_organization_id');

        if (! $organizationId) {
            return null;
        }

        $cacheKey = "organization_full_data:{$organizationId}";

        return Cache::tags(['organizations', "organization:{$organizationId}"])
            ->remember($cacheKey, 7200, function () use ($organizationId) { // 2 часа
                return Organization::with([
                    'departments.users'
                ])
                    ->find($organizationId);
            });
    }

}
