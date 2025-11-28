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
        Schema::create('exercises', function (Blueprint $table) {
            $table->id()->comment('Уникальный ID выполнения');
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete()->comment('Пациент');
            $table->string('exercise_type')->comment('Тип: 2D/3D');
            $table->unsignedBigInteger('exercise_template_id')->nullable()->comment('ID шаблона (nullable)');

            // Индивидуальные параметры (переопределение шаблона)
            $table->unsignedInteger('ball_count')->nullable();
            $table->unsignedInteger('ball_size')->nullable();
            $table->unsignedInteger('target_accuracy_percent')->nullable();
            $table->string('vertical_area')->nullable();
            $table->string('horizontal_area')->nullable();
            $table->string('distance_area')->nullable();
            $table->string('speed')->nullable();
            $table->unsignedInteger('duration_seconds')->default(0)->comment('Фактическая длительность выполнения');

            // Результаты
            $table->unsignedTinyInteger('fatigue_right_eye')->nullable()->comment('Усталость правого глаза 1-5');
            $table->unsignedTinyInteger('fatigue_left_eye')->nullable()->comment('Усталость левого глаза 1-5');
            $table->unsignedTinyInteger('fatigue_head')->nullable()->comment('Усталость головы 1-5');
            $table->string('patient_decision')->nullable()->comment('continue/stop');
            $table->text('notes')->nullable();

            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->foreign('exercise_template_id')->references('id')->on('exercise_templates');
            $table->index(['patient_id', 'started_at']);

            $table->comment('Выполненные упражнения пациентов');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exercises');
    }
};
