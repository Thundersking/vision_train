<?php

namespace Database\Seeders;

use App\Domain\User\Models\User;
use Illuminate\Database\Seeder;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            OrganizationSeeder::class,
            DepartmentSeeder::class,
            PermissionSeeder::class, // Сначала разрешения
            RoleSeeder::class,       // Потом роли с назначением разрешений
            UserSeeder::class,       // Затем пользователи с назначением ролей
            ExerciseTemplateSeeder::class,
        ]);
    }
}
