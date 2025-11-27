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
            $table->unsignedBigInteger('exercise_type_id');
            $table->string('title');
            $table->string('short_description')->nullable();
            $table->string('difficulty')->nullable();
            $table->unsignedInteger('duration_seconds')->default(0);
            $table->text('instructions')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('organization_id')
                ->references('id')
                ->on('organizations')
                ->onDelete('cascade');

            $table->foreign('exercise_type_id')
                ->references('id')
                ->on('exercise_types')
                ->onDelete('cascade');

            $table->index(['organization_id', 'exercise_type_id']);
            $table->index(['organization_id', 'title']);
            $table->comment('Готовые сценарии упражнений, связанные с типами');
        });

        Schema::create('exercise_template_steps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('exercise_template_id');
            $table->unsignedInteger('step_order')->default(1);
            $table->string('title');
            $table->unsignedInteger('duration')->default(0);
            $table->text('description')->nullable();
            $table->text('hint')->nullable();
            $table->timestamps();

            $table->foreign('exercise_template_id')
                ->references('id')
                ->on('exercise_templates')
                ->onDelete('cascade');

            $table->index(['exercise_template_id', 'step_order']);
        });

        Schema::create('exercise_template_parameters', function (Blueprint $table) {
            $table->id();
            $table->uuid()->default(DB::raw('uuid_generate_v4()'))->unique();
            $table->unsignedBigInteger('exercise_template_id');
            $table->string('label')->nullable();
            $table->string('key')->nullable();
            $table->string('target_value')->nullable();
            $table->string('unit')->nullable();
            $table->timestamps();

            $table->foreign('exercise_template_id')
                ->references('id')
                ->on('exercise_templates')
                ->onDelete('cascade');

            $table->index(['exercise_template_id', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exercise_template_parameters');
        Schema::dropIfExists('exercise_template_steps');
        Schema::dropIfExists('exercise_templates');
    }
};
