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

    public function listByTemplateId(int $templateId)
    {
        return $this->newQuery()
            ->where('exercise_template_id', $templateId)
            ->orderBy('id')
            ->get();
    }
}
