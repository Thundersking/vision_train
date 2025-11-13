<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        // Получаем ID организаций
        $visionClinic = DB::table('organizations')->where('domain', 'aa11bb22')->first();
        $olimpFitness = DB::table('organizations')->where('domain', 'aa22bb33')->first();
        $dinamoStadium = DB::table('organizations')->where('domain', 'aa33bb44')->first();

        $departments = [
            // Офтальмологическая клиника
            [
                'uuid' => Str::uuid(),
                'name' => 'Главный офис',
                'organization_id' => $visionClinic->id,
                'utc_offset_minutes' => 180, // Москва UTC+3
                'address' => 'г. Москва, ул. Ленинский проспект, д. 15, каб. 101',
                'phone' => '+7 (495) 123-45-67',
                'email' => 'main@vision-clinic.ru',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => Str::uuid(),
                'name' => 'Филиал в Сокольниках',
                'organization_id' => $visionClinic->id,
                'utc_offset_minutes' => 180, // Москва UTC+3
                'address' => 'г. Москва, Сокольническая площадь, д. 4а',
                'phone' => '+7 (495) 123-45-68',
                'email' => 'sokolniki@vision-clinic.ru',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Фитнес-центр Олимп
            [
                'uuid' => Str::uuid(),
                'name' => 'Основной зал',
                'organization_id' => $olimpFitness->id,
                'utc_offset_minutes' => 180, // СПб UTC+3
                'address' => 'г. Санкт-Петербург, Невский проспект, д. 25, 2-й этаж',
                'phone' => '+7 (812) 987-65-43',
                'email' => 'gym@olimp-fitness.ru',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => Str::uuid(),
                'name' => 'Реабилитационный центр',
                'organization_id' => $olimpFitness->id,
                'utc_offset_minutes' => 180, // СПб UTC+3
                'address' => 'г. Санкт-Петербург, Невский проспект, д. 25, 3-й этаж',
                'phone' => '+7 (812) 987-65-44',
                'email' => 'rehab@olimp-fitness.ru',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Спортивный комплекс Динамо
            [
                'uuid' => Str::uuid(),
                'name' => 'Медицинский кабинет',
                'organization_id' => $dinamoStadium->id,
                'utc_offset_minutes' => 180, // Москва UTC+3
                'address' => 'г. Москва, Ленинградский проспект, д. 36, корп. А',
                'phone' => '+7 (495) 555-12-34',
                'email' => 'medical@dinamo-stadium.ru',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('departments')->insert($departments);
    }
}
