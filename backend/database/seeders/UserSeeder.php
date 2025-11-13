<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domain\RBAC\Models\Role;
use App\Domain\User\Models\User;
use App\Domain\Organization\Models\Organization;
use App\Domain\Department\Models\Department;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Получаем организации и их подразделения
        $visionClinic = DB::table('organizations')->where('domain', 'aa11bb22')->first();
        $olimpFitness = DB::table('organizations')->where('domain', 'aa22bb33')->first();
        $dinamoStadium = DB::table('organizations')->where('domain', 'aa33bb44')->first();

        $visionMainDept = DB::table('departments')
            ->where('organization_id', $visionClinic->id)
            ->where('name', 'Главный офис')
            ->first();

        $visionBranchDept = DB::table('departments')
            ->where('organization_id', $visionClinic->id)
            ->where('name', 'Филиал в Сокольниках')
            ->first();

        $olimpMainDept = DB::table('departments')
            ->where('organization_id', $olimpFitness->id)
            ->where('name', 'Основной зал')
            ->first();

        $dinamoDept = DB::table('departments')
            ->where('organization_id', $dinamoStadium->id)
            ->where('name', 'Медицинский кабинет')
            ->first();

        $users = [
            // Офтальмологическая клиника
            [
                'uuid' => Str::uuid(),
                'organization_id' => $visionClinic->id,
                'department_id' => $visionMainDept->id,
                'first_name' => 'Владимир',
                'last_name' => 'Петров',
                'middle_name' => 'Александрович',
                'phone' => '+7 (495) 123-45-67',
                'email' => 'petrov@vision-clinic.ru',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'override_utc_offset_minutes' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => Str::uuid(),
                'organization_id' => $visionClinic->id,
                'department_id' => $visionMainDept->id,
                'first_name' => 'Анна',
                'last_name' => 'Иванова',
                'middle_name' => 'Сергеевна',
                'phone' => '+7 (495) 123-45-69',
                'email' => 'ivanova@vision-clinic.ru',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'override_utc_offset_minutes' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => Str::uuid(),
                'organization_id' => $visionClinic->id,
                'department_id' => $visionBranchDept->id,
                'first_name' => 'Михаил',
                'last_name' => 'Смирнов',
                'middle_name' => 'Олегович',
                'phone' => '+7 (495) 123-45-70',
                'email' => 'smirnov@vision-clinic.ru',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'override_utc_offset_minutes' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Фитнес-центр Олимп
            [
                'uuid' => Str::uuid(),
                'organization_id' => $olimpFitness->id,
                'department_id' => $olimpMainDept->id,
                'first_name' => 'Елена',
                'last_name' => 'Сидорова',
                'middle_name' => 'Владимировна',
                'phone' => '+7 (812) 987-65-43',
                'email' => 'sidorova@olimp-fitness.ru',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'override_utc_offset_minutes' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => Str::uuid(),
                'organization_id' => $olimpFitness->id,
                'department_id' => $olimpMainDept->id,
                'first_name' => 'Игорь',
                'last_name' => 'Волков',
                'middle_name' => 'Дмитриевич',
                'phone' => '+7 (812) 987-65-45',
                'email' => 'volkov@olimp-fitness.ru',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'override_utc_offset_minutes' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Спортивный комплекс Динамо
            [
                'uuid' => Str::uuid(),
                'organization_id' => $dinamoStadium->id,
                'department_id' => $dinamoDept->id,
                'first_name' => 'Дмитрий',
                'last_name' => 'Козлов',
                'middle_name' => 'Петрович',
                'phone' => '+7 (495) 555-12-34',
                'email' => 'kozlov@dinamo-stadium.ru',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'override_utc_offset_minutes' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('users')->insert($users);

        // Назначаем роль organization_admin всем созданным пользователям
        $this->assignAdminRoleToUsers();
    }

    /**
     * Назначить роль organization_admin всем пользователям
     */
    private function assignAdminRoleToUsers(): void
    {
        $organizations = [
            DB::table('organizations')->where('domain', 'aa11bb22')->first(),
            DB::table('organizations')->where('domain', 'aa22bb33')->first(),
            DB::table('organizations')->where('domain', 'aa33bb44')->first(),
        ];

        foreach ($organizations as $organization) {
            // Находим роль organization_admin для данной организации
            $adminRole = Role::where('name', 'organization_admin')
                ->where('organization_id', $organization->id)
                ->first();

            if (!$adminRole) {
                $this->command->error("Role organization_admin not found for organization: {$organization->name}");
                continue;
            }

            // Находим всех пользователей этой организации
            $users = User::where('organization_id', $organization->id)->get();

            foreach ($users as $user) {
                $user->assignRole($adminRole);
                $this->command->info("Assigned organization_admin role to: {$user->email}");
            }
        }
    }
}
