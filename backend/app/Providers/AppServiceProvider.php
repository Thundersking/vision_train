<?php

namespace App\Providers;

use App\Domain\Department\Models\Department;
use App\Domain\Department\Policies\DepartmentPolicy;
use App\Domain\Department\Repositories\DepartmentRepository;
use App\Domain\Organization\Models\Organization;
use App\Domain\Organization\Policies\OrganizationPolicy;
use App\Domain\Organization\Repositories\OrganizationRepository;
use App\Domain\Patient\Models\Patient;
use App\Domain\Patient\Policies\PatientPolicy;
use App\Domain\Patient\Repositories\PatientRepository;
use App\Domain\User\Models\User;
use App\Domain\User\Policies\UserPolicy;
use App\Domain\User\Repositories\UserRepository;
use App\Infrastructure\Repositories\Eloquent\EloquentDepartmentRepository;
use App\Infrastructure\Repositories\Eloquent\EloquentOrganizationRepository;
use App\Infrastructure\Repositories\Eloquent\EloquentPatientRepository;
use App\Infrastructure\Repositories\Eloquent\EloquentUserRepository;
use App\Support\Multitenancy\Services\OrganizationResolver;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(OrganizationResolver::class, function ($app) {
            return new OrganizationResolver;
        });

        $this->app->bind(UserRepository::class, EloquentUserRepository::class);
        $this->app->bind(DepartmentRepository::class, EloquentDepartmentRepository::class);
        $this->app->bind(OrganizationRepository::class, EloquentOrganizationRepository::class);
        $this->app->bind(PatientRepository::class, EloquentPatientRepository::class);
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
