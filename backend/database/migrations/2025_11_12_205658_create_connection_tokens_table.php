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
        Schema::create('connection_tokens', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->index();
            
            // Связи
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            
            // Основные поля
            $table->string('token', 100)->unique(); // уникальный токен
            $table->string('qr_code_path', 500)->nullable(); // путь к QR-коду
            $table->timestamp('expires_at')->nullable(); // когда истекает
            $table->timestamp('used_at')->nullable(); // когда использован
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            
            // Индексы
            $table->index(['organization_id', 'patient_id']);
            $table->index(['token']);
            $table->index(['expires_at']);
            $table->index(['is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('connection_tokens');
    }
};
