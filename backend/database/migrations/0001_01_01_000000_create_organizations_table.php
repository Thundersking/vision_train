<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('CREATE EXTENSION IF NOT EXISTS "uuid-ossp";');

        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->uuid()->default(DB::raw('uuid_generate_v4()'))->unique();
            $table->string('name');
            $table->string('domain', 8)->unique();
            $table->string('type');
            $table->boolean('is_active')->default(true);
            $table->string('subscription_plan')->nullable();
            $table->string('email')->nullable()->comment('Контактный email организации');
            $table->string('phone')->nullable()->comment('Контактный телефон');
            $table->string('inn')->nullable()->comment('ИНН организации');
            $table->string('kpp')->nullable()->comment('КПП организации');
            $table->string('ogrn')->nullable()->comment('ОГРН организации');
            $table->string('legal_address')->nullable()->comment('Юридический адрес');
            $table->string('actual_address')->nullable()->comment('Фактический адрес');
            $table->string('director_name')->nullable()->comment('ФИО руководителя');
            $table->string('license_number')->nullable()->comment('Номер медицинской лицензии');
            $table->date('license_issued_at')->nullable()->comment('Дата выдачи лицензии');

            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index('is_active');
            $table->index('type');
            $table->index('domain');

            $table->comment('Организации (клиники, фитнес-центры, стадионы)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};
