<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->unsignedBigInteger('organization_id');
            $table->unsignedBigInteger('user_id')->comment('Закрепленный врач');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('middle_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 20);
            $table->string('gender')->comment('Пол пациента: male, female');
            $table->decimal('hand_size_cm', 3, 1)->comment('Размер руки в см (5-30)');
            $table->date('birth_date');
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade')->comment('Организация пациента');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->comment('Закрепленный врач');

            // Индексы для производительности
            $table->index('organization_id');
            $table->index('user_id');
            $table->index(['organization_id', 'first_name', 'last_name'], 'idx_patients_name_search');
            $table->index(['email', 'organization_id'], 'idx_patients_email_organization');
            $table->index(['organization_id', 'is_active']);

            // Уникальность email в рамках организации (если указан)
            $table->unique(['email', 'organization_id'], 'uniq_patients_email_org');

            // Комментарий к таблице
            $table->comment('Пациенты медицинских организаций');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
