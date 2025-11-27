<?php

declare(strict_types=1);

namespace App\Domain\ExerciseTemplate\Actions;

use App\Domain\ExerciseTemplate\Models\ExerciseTemplate;
use App\Domain\ExerciseTemplate\Repositories\ExerciseTemplateRepository;
use App\Domain\Shared\Enums\AuditActionType;
use App\Domain\Shared\Traits\RecordsAuditLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class UpdateExerciseTemplateAction
{
    use RecordsAuditLog;

    public function __construct(private readonly ExerciseTemplateRepository $repository)
    {
    }

    /**
     * @throws ValidationException
     */
    public function execute(ExerciseTemplate $template, array $data): ExerciseTemplate
    {
        $data['payload_json'] = $this->decodePayload($data['payload_json'] ?? null);

        return DB::transaction(function () use ($template, $data) {
            $oldData = $template->toArray();

            $updated = $this->repository->update($template->id, $data);
            $updated?->load('type');

            if ($updated) {
                $this->recordAudit(
                    action: AuditActionType::UPDATED,
                    entity: $template,
                    oldData: $oldData,
                    newData: $updated->toArray()
                );
            }

            return $updated ?? $template;
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
