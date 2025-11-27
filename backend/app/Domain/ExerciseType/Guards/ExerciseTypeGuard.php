<?php

declare(strict_types=1);

namespace App\Domain\ExerciseType\Guards;

use App\Domain\ExerciseType\Repositories\ExerciseTypeRepository;
use Illuminate\Validation\ValidationException;

final readonly class ExerciseTypeGuard
{
    public function __construct(private ExerciseTypeRepository $repository)
    {
    }

    /**
     * @throws ValidationException
     */
    public function ensureSlugUnique(?string $slug): void
    {
        if (empty($slug)) {
            return;
        }

        if ($this->repository->slugExists($slug)) {
            throw ValidationException::withMessages([
                'slug' => 'Слаг уже используется',
            ]);
        }
    }

    /**
     * @throws ValidationException
     */
    public function ensureSlugUniqueExceptId(?string $slug, int $exceptId): void
    {
        if (empty($slug)) {
            return;
        }

        if ($this->repository->slugExistsExceptId($slug, $exceptId)) {
            throw ValidationException::withMessages([
                'slug' => 'Слаг уже используется',
            ]);
        }
    }
}
