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
        Schema::create('patient_devices', function (Blueprint $table) {
            $table->id();
            
            // Связи
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('device_id')->constrained()->onDelete('cascade');
            
            // Дополнительные поля pivot таблицы
            $table->boolean('is_primary')->default(false); // основное устройство
            $table->timestamp('assigned_at')->useCurrent();
            $table->foreignId('assigned_by')->constrained('users'); // кто назначил
            $table->text('notes')->nullable(); // заметки о назначении
            
            $table->timestamps();
            
            // Индексы и ограничения
            $table->unique(['patient_id', 'device_id']);
            $table->index(['patient_id', 'is_primary']);
            $table->index(['device_id']);
            
            // Только одно основное устройство на пациента
            $table->unique(['patient_id'], 'patient_primary_device')->where('is_primary', true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_devices');
    }
};
