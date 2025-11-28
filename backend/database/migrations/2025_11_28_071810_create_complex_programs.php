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
        Schema::create('complex_programs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('name')->comment('Название программы реабилитации');
            $table->string('program_type')->comment('Тип программы: development или maintenance');
            $table->text('description')->nullable()->comment('Подробное описание программы');
            $table->unsignedInteger('duration_days')->default(30)->comment('Общая длительность в днях');
            $table->unsignedInteger('exercises_per_day')->default(2)->comment('Количество упражнений в день');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['organization_id', 'program_type']);

            $table->comment('Комплексные программы реабилитации из шаблонов упражнений');
        });

        Schema::create('program_exercises', function (Blueprint $table) {
            $table->id();
            $table->foreignId('complex_program_id')->constrained()->cascadeOnDelete();
            $table->foreignId('exercise_template_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('day_number')->comment('Номер дня в программе');
            $table->unsignedInteger('exercise_order')->comment('Порядок упражнения в дне');
            $table->timestamps();

            $table->unique(['complex_program_id', 'day_number', 'exercise_order']);
            $table->index(['complex_program_id', 'day_number']);

            $table->comment('Связь шаблонов упражнений с днями программ реабилитации');
        });

        Schema::create('patient_programs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('complex_program_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('active')->comment('active, completed, paused, cancelled');
            $table->unsignedInteger('current_day')->default(1)->comment('Текущий день программы');
            $table->unsignedInteger('total_exercises_completed')->default(0)->comment('Выполнено упражнений всего');
            $table->decimal('progress_percent', 5, 2)->default(0)->comment('Процент выполнения программы');
            $table->foreignId('assigned_by')->constrained('users');
            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['patient_id', 'status']);

            $table->comment('Назначенные пациенту программы реабилитации');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complex_programs');
    }
};
