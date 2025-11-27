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
            $table->jsonb('payload_json');
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
    }

    public function down(): void
    {
        Schema::dropIfExists('exercise_templates');
    }
};
