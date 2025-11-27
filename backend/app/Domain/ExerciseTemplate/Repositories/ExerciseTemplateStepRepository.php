<?php

declare(strict_types=1);

namespace App\Domain\ExerciseTemplate\Repositories;

use App\Domain\ExerciseTemplate\Models\ExerciseTemplateStep;
use App\Support\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

final class ExerciseTemplateStepRepository extends BaseRepository
{
    protected static function modelClass(): string
    {
        return ExerciseTemplateStep::class;
    }

    public function sync(int $templateId, array $steps): void
    {
        $this->newQuery()->where('exercise_template_id', $templateId)->delete();

        if (empty($steps)) {
            return;
        }

        foreach ($steps as $step) {
            $this->create([
                'exercise_template_id' => $templateId,
                'step_order' => $step['step_order'] ?? 0,
                'title' => $step['title'],
                'duration' => $step['duration'] ?? 0,
                'description' => $step['description'] ?? null,
                'hint' => $step['hint'] ?? null,
            ]);
        }
    }
}
