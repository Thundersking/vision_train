<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exercise_templates', function (Blueprint $table) {
            $table->id();
            $table->uuid()->default(DB::raw('uuid_generate_v4()'))->unique();
            $table->unsignedBigInteger('organization_id');
            $table->string('exercise_type')->comment('Тип упражнения');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('difficulty')->nullable()->comment('Уровень сложности упражнения');

            $table->unsignedInteger('ball_count')->nullable()->comment('Количество шариков');
            $table->unsignedInteger('ball_size')->nullable()->comment('Размер шариков');
            $table->unsignedInteger('target_accuracy_percent')->nullable()->comment('Целевая точность в процентах');
            $table->string('vertical_area')->nullable()->comment('Вертикальная зона');
            $table->string('horizontal_area')->nullable()->comment('Горизонтальная зона');
            $table->string('distance_area')->nullable()->comment('Зона по глубине');
            $table->string('speed')->nullable()->comment('Скорость выполнения упражнения');
            $table->string('media_link')->nullable()->comment('Ссылка на видео или медиафайл с инструкцией');

            $table->unsignedInteger('duration_seconds')->default(60)->comment('Продолжительность упражнения в секундах');
            $table->text('instructions')->nullable()->comment('Инструкции по выполнению упражнения');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');

            $table->index(['organization_id']);
            $table->index(['organization_id', 'name']);

            $table->comment('Шаблоны упражнений');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exercise_templates');
    }
};
