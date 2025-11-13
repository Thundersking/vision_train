<?php

namespace App\Support\Multitenancy\Traits;

use App\Support\Multitenancy\Scopes\OrganizationScope;
use Spatie\Multitenancy\Models\Tenant;

trait HasOrganization
{
    protected static function bootHasOrganization(): void
    {
        static::addGlobalScope(new OrganizationScope());

        static::creating(function ($model) {
            if (($tenant = Tenant::current()) && empty($model->organization_id)) {
                $model->organization_id = $tenant->id;
            }
        });
    }
}
