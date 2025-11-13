<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domain\RBAC\Constants\DefaultRoles;
use App\Domain\RBAC\Enums\Permission;
use App\Domain\RBAC\Models\Role;
use App\Domain\Organization\Models\Organization;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission as SpatiePermission;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command->info('Creating roles...');

        // 1. Создаем глобальные роли (для super_admin)
        $this->createGlobalRoles();

        // 2. Создаем тенантские роли для всех организаций
        $this->createTenantRoles();

        $this->command->info('🎉 All roles created successfully!');
    }

    /**
     * Создать глобальные системные роли
     */
    private function createGlobalRoles(): void
    {
        $this->command->info('📋 Creating global roles...');

        foreach (DefaultRoles::GLOBAL_ROLES as $roleName => $roleConfig) {
            $role = Role::firstOrCreate([
                'name' => $roleName,
                'organization_id' => null,
                'guard_name' => 'api',
            ], [
                'display_name' => $roleConfig['display_name'],
                'description' => $roleConfig['description'],
                'is_system' => $roleConfig['is_system'],
            ]);

            if ($role->wasRecentlyCreated) {
                $this->command->info("  ✅ Created global role: {$roleName}");
                $this->assignPermissionsToRole($role, $roleName);
            } else {
                $this->command->comment("  📋 Global role already exists: {$roleName}");
            }
        }
    }

    /**
     * Создать тенантские роли для всех организаций
     */
    private function createTenantRoles(): void
    {
        // Получаем все организации
        $organizations = Organization::all();

        if ($organizations->isEmpty()) {
            $this->command->error('⚠️ No organizations found! Please run OrganizationSeeder first.');
            return;
        }

        $this->command->info('🏢 Creating tenant roles...');

        $totalCreated = 0;
        $totalExisting = 0;

        foreach ($organizations as $organization) {
            $this->command->info("Processing organization: {$organization->name}");

            // Создаем все тенантские роли (системные + обычные)
            $allTenantRoles = DefaultRoles::getAllTenantRoles();

            foreach ($allTenantRoles as $roleName => $roleConfig) {
                $role = Role::firstOrCreate([
                    'name' => $roleName,
                    'organization_id' => $organization->id,
                    'guard_name' => 'api',
                ], [
                    'display_name' => $roleConfig['display_name'],
                    'description' => $roleConfig['description'],
                    'is_system' => $roleConfig['is_system'],
                ]);

                if ($role->wasRecentlyCreated) {
                    $systemFlag = $roleConfig['is_system'] ? '🔒' : '📝';
                    $this->command->info("  ✅ {$systemFlag} Created role: {$roleName}");
                    $totalCreated++;

                    // Назначаем разрешения
                    $this->assignPermissionsToRole($role, $roleName);
                } else {
                    $this->command->comment("  📋 Role already exists: {$roleName}");
                    $totalExisting++;
                }
            }
        }

        $this->command->info("Created: {$totalCreated} tenant roles");
        $this->command->info("Already existed: {$totalExisting} tenant roles");
    }

    /**
     * Назначить разрешения роли согласно Permission enum
     */
    private function assignPermissionsToRole(Role $role, string $roleName): void
    {
        $permissionNames = Permission::forRole($roleName);

        if (empty($permissionNames)) {
            $this->command->warn("    ⚠️ No permissions defined for role: {$roleName}");
            return;
        }

        // Получаем объекты Permission из БД
        $permissions = SpatiePermission::whereIn('name', $permissionNames)->get();

        if ($permissions->count() !== count($permissionNames)) {
            $this->command->error("    ❌ Some permissions not found for role: {$roleName}");
            $this->command->error("    Expected: " . implode(', ', $permissionNames));
            $this->command->error("    Found: " . $permissions->pluck('name')->implode(', '));
            return;
        }

        // Назначаем разрешения
        $role->syncPermissions($permissions);

        $this->command->info("    🔑 Assigned {$permissions->count()} permissions to {$roleName}");
    }
}
