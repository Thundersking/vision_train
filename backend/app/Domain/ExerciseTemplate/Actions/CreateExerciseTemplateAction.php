<?php

declare(strict_types=1);

namespace App\Domain\ExerciseTemplate\Actions;

use App\Domain\ExerciseTemplate\Models\ExerciseTemplate;
use App\Domain\ExerciseTemplate\Repositories\ExerciseTemplateRepository;
use App\Domain\Shared\Enums\AuditActionType;
use App\Domain\Shared\Traits\RecordsAuditLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class CreateExerciseTemplateAction
{
    use RecordsAuditLog;

    public function __construct(private readonly ExerciseTemplateRepository $repository)
    {
    }

    /**
     * @throws ValidationException
     */
    public function execute(array $data): ExerciseTemplate
    {
        $data['payload_json'] = $this->decodePayload($data['payload_json'] ?? null);

        return DB::transaction(function () use ($data) {
            /** @var ExerciseTemplate $template */
            $template = $this->repository->create($data);
            $template->load('type');

            $this->recordAudit(
                action: AuditActionType::CREATED,
                entity: $template,
                newData: $template->toArray()
            );

            return $template;
        });
    }

    private function decodePayload(mixed $value): ?array
    {
        if (is_array($value)) {
            return $value;
        }

        if (is_string($value) && $value !== '') {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded;
            }

            throw ValidationException::withMessages([
                'payload_json' => 'Некорректный JSON',
            ]);
        }

        return null;
    }
}
