<?php

declare(strict_types=1);

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
        Schema::create('patient_examinations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->index();
            
            // Связи
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // врач
            
            // Основные поля
            $table->string('type', 100); // тип обследования
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->timestamp('examination_date');
            $table->json('results')->nullable(); // результаты в JSON
            $table->text('recommendations')->nullable();
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            $table->softDeletes();
            
            // Индексы
            $table->index(['organization_id', 'patient_id']);
            $table->index(['organization_id', 'user_id']);
            $table->index(['examination_date']);
            $table->index(['type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_examinations');
    }
};
