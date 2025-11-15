<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->uuid()->default(DB::raw('uuid_generate_v4()'))->unique();
            $table->string('name');
            $table->unsignedBigInteger('organization_id');
            $table->integer('utc_offset_minutes')->default(0)->comment('Timezone offset in minutes (e.g., Moscow +180, Vladivostok +600)');
            $table->string('address')->nullable()->comment('Адрес подразделения');
            $table->string('phone')->nullable()->comment('Телефон подразделения');
            $table->string('email')->nullable()->comment('Email подразделения');
            $table->boolean('is_active')->default(true);


            $table->timestamps();
            $table->softDeletes();

            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade')->comment('Организация отделения');

            $table->index('organization_id');
            $table->index(['organization_id', 'is_active']);

            $table->comment('Отделения/офисы организаций');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
