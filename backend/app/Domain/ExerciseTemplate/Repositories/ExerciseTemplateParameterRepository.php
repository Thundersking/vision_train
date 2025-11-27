<?php

declare(strict_types=1);

namespace App\Domain\ExerciseTemplate\Repositories;

use App\Domain\ExerciseTemplate\Models\ExerciseTemplateParameter;
use App\Support\Repositories\BaseRepository;

final class ExerciseTemplateParameterRepository extends BaseRepository
{
    protected static function modelClass(): string
    {
        return ExerciseTemplateParameter::class;
    }

    public function sync(int $templateId, array $parameters): void
    {
        $this->newQuery()->where('exercise_template_id', $templateId)->delete();

        if (empty($parameters)) {
            return;
        }

        foreach ($parameters as $parameter) {
            $this->create([
                'exercise_template_id' => $templateId,
                'label' => $parameter['label'] ?? null,
                'key' => $parameter['key'] ?? null,
                'target_value' => $parameter['target_value'] ?? null,
                'unit' => $parameter['unit'] ?? null,
            ]);
        }
    }
}
