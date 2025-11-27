<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exercise_types', function (Blueprint $table) {
            $table->id();
            $table->uuid()->default(DB::raw('uuid_generate_v4()'))->unique();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('dimension')->default('2d');
            $table->boolean('is_customizable')->default(false);
            $table->text('description')->nullable();
            $table->jsonb('metrics_json')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('name');
            $table->comment('Справочник типов упражнений (глобальный)');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exercise_types');
    }
};
