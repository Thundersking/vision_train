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
        Schema::create('ball_collections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exercise_id')->constrained()->cascadeOnDelete()->comment('ID выполненного упражнения');
            $table->unsignedInteger('ball_sequence_number')->comment('Порядковый номер шарика в последовательности');
            $table->decimal('distance_coordinate', 8, 2)->nullable()->comment('Координата расстояния шарика');
            $table->decimal('horizontal_coordinate', 8, 2)->nullable()->comment('Горизонтальная координата шарика');
            $table->decimal('vertical_coordinate', 8, 2)->nullable()->comment('Вертикальная координата шарика');
            $table->unsignedInteger('ball_size')->nullable()->comment('Размер шарика');
            $table->decimal('accuracy_percent', 5, 2)->nullable()->comment('Точность попадания в шарик в процентах');
            $table->unsignedInteger('collection_time_ms')->nullable()->comment('Время сбора данных по шарикам в миллисекундах');
            $table->unsignedInteger('time_from_previous_ms')->nullable()->comment('Время с предыдущего шарика в миллисекундах');
            $table->timestamps();

            $table->index(['exercise_id', 'ball_sequence_number']);

            $table->comment('Детализация параметров и результатов для каждого шарика упражнения');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ball_collections');
    }
};
