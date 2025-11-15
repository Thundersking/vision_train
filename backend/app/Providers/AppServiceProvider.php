<?php

namespace App\Providers;

use App\Domain\Department\Models\Department;
use App\Domain\Department\Policies\DepartmentPolicy;
use App\Domain\Organization\Models\Organization;
use App\Domain\Organization\Policies\OrganizationPolicy;
use App\Domain\Patient\Models\Patient;
use App\Domain\Patient\Policies\PatientPolicy;
use App\Domain\User\Models\User;
use App\Domain\User\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Department::class, DepartmentPolicy::class);
        Gate::policy(Organization::class, OrganizationPolicy::class);
        Gate::policy(Patient::class, PatientPolicy::class);
    }
}
