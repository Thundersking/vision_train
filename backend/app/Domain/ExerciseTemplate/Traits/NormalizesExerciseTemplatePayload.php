<?php

declare(strict_types=1);

namespace App\Domain\ExerciseTemplate\Traits;

use Illuminate\Support\Str;

trait NormalizesExerciseTemplatePayload
{
    private function normalizeSteps(?array $steps): array
    {
        if ($steps === null) {
            return [];
        }

        $normalized = [];
        foreach ($steps as $index => $step) {
            $title = isset($step['title']) ? trim((string)$step['title']) : '';
            if ($title === '') {
                continue;
            }

            $duration = isset($step['duration']) ? (int)$step['duration'] : null;
            $order = isset($step['step_order']) ? (int) $step['step_order'] : $index + 1;

            $normalized[] = [
                'step_order' => $order,
                'title' => $title,
                'duration' => $duration,
                'description' => isset($step['description']) && $step['description'] !== null
                    ? trim((string)$step['description'])
                    : null,
                'hint' => isset($step['hint']) && $step['hint'] !== null
                    ? trim((string)$step['hint'])
                    : null,
            ];
        }

        return $normalized;
    }

    private function normalizeParameters(?array $parameters): array
    {
        if ($parameters === null) {
            return [];
        }

        $normalized = [];
        foreach ($parameters as $parameter) {
            $label = isset($parameter['label']) ? trim((string)$parameter['label']) : null;
            $key = isset($parameter['key']) ? trim((string)$parameter['key']) : '';
            $target = isset($parameter['target_value']) && $parameter['target_value'] !== null
                ? (string)$parameter['target_value']
                : null;
            $unit = isset($parameter['unit']) ? trim((string)$parameter['unit']) : null;

            if ($key === '' && $label !== null) {
                $key = Str::slug($label);
            }

            if ($key === '' && $label === null && $target === null && $unit === null) {
                continue;
            }

            $normalized[] = [
                'key' => $key !== '' ? $key : null,
                'label' => $label,
                'target_value' => $target,
                'unit' => $unit,
            ];
        }

        return $normalized;
    }

    private function normalizeExtraPayload(mixed $payload): ?array
    {
        if (!is_array($payload) || $payload === []) {
            return null;
        }

        return $payload;
    }

    private function calculateDuration(?array $steps, ?int $fallback = null): ?int
    {
        if ($steps) {
            $sum = 0;
            foreach ($steps as $step) {
                $duration = isset($step['duration']) ? (int)$step['duration'] : 0;
                $sum += max($duration, 0);
            }
            return $sum > 0 ? $sum : null;
        }

        if ($fallback !== null) {
            $fallback = (int)$fallback;
            return $fallback > 0 ? $fallback : null;
        }

        return null;
    }
}
