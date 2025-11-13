<?php

namespace App\Support\Multitenancy\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Spatie\Multitenancy\Models\Tenant;

class OrganizationScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if ($tenant = Tenant::current()) {
            $builder->where($model->getTable().'.organization_id', $tenant->id);
        }
    }
}
