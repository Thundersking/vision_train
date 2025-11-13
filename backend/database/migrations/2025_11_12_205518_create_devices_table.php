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
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->uuid();

            $table->foreignId('organization_id')->constrained()->onDelete('cascade');

            $table->string('name', 255);
            $table->string('type', 100); // тип устройства
            $table->string('serial_number', 100)->unique();
            $table->string('model', 100)->nullable();
            $table->string('manufacturer', 100)->nullable();
            $table->string('firmware_version', 50)->nullable();
            $table->timestamp('last_sync_at')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['organization_id', 'type']);
            $table->index(['serial_number']);
            $table->index(['is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
