<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domain\Organization\Enums\OrganizationType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrganizationSeeder extends Seeder
{
    public function run(): void
    {
        $organizations = [
            [
                'uuid' => Str::uuid(),
                'name' => 'Офтальмологическая клиника "Четкое зрение"',
                'domain' => 'aa11bb22',
                'type' => OrganizationType::MEDICAL_CENTER->value,
                'is_active' => true,
                'subscription_plan' => 'premium',
                'email' => 'info@vision-clinic.ru',
                'phone' => '+7 (495) 123-45-67',
                'inn' => '7712345678',
                'kpp' => '771201001',
                'ogrn' => '1027700123456',
                'legal_address' => '119991, г. Москва, ул. Ленинский проспект, д. 15',
                'actual_address' => '119991, г. Москва, ул. Ленинский проспект, д. 15',
                'director_name' => 'Петров Владимир Александрович',
                'license_number' => 'ЛО-77-01-012345',
                'license_issued_at' => '2020-05-15',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => Str::uuid(),
                'name' => 'Фитнес-центр "Олимп"',
                'domain' => 'aa22bb33',
                'type' => OrganizationType::FITNESS_GYM->value,
                'is_active' => true,
                'subscription_plan' => 'standard',
                'email' => 'contact@olimp-fitness.ru',
                'phone' => '+7 (812) 987-65-43',
                'inn' => '7812345678',
                'kpp' => '781201001',
                'ogrn' => '1027800654321',
                'legal_address' => '190000, г. Санкт-Петербург, Невский проспект, д. 25',
                'actual_address' => '190000, г. Санкт-Петербург, Невский проспект, д. 25',
                'director_name' => 'Сидорова Елена Владимировна',
                'license_number' => null,
                'license_issued_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => Str::uuid(),
                'name' => 'Спортивный комплекс "Динамо"',
                'domain' => 'aa33bb44',
                'type' => OrganizationType::STADIUM->value,
                'is_active' => true,
                'subscription_plan' => 'basic',
                'email' => 'admin@dinamo-stadium.ru',
                'phone' => '+7 (495) 555-12-34',
                'inn' => '7712987654',
                'kpp' => '771201002',
                'ogrn' => '1027700987654',
                'legal_address' => '125040, г. Москва, Ленинградский проспект, д. 36',
                'actual_address' => '125040, г. Москва, Ленинградский проспект, д. 36',
                'director_name' => 'Козлов Дмитрий Петрович',
                'license_number' => null,
                'license_issued_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('organizations')->insert($organizations);
    }
}
