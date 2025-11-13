<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domain\RBAC\Enums\Permission;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission as SpatiePermission;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command->info('Creating permissions...');

        // Create all permissions from the Permission enum
        $permissions = Permission::all();

        foreach ($permissions as $permission) {
            $created = SpatiePermission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'api',
            ]);

            if ($created->wasRecentlyCreated) {
                $this->command->info("Created permission: {$permission}");
            } else {
                $this->command->comment("Permission already exists: {$permission}");
            }
        }

        $this->command->info("✅ All permissions initialized successfully!");
        $this->command->info("Total permissions: " . count($permissions));
    }
}
