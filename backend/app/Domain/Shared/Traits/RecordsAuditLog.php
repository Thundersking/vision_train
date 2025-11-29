<?php

namespace App\Domain\Shared\Traits;

use App\Domain\AuditLog\Models\AuditLog;
use App\Domain\Shared\Enums\AuditActionType;
use Spatie\Multitenancy\Models\Tenant;

trait RecordsAuditLog
{
    private const TEXT_TRUNCATE_LENGTH = 1000;
    private const TEXT_PREVIEW_LENGTH = 500;

    private array $defaultExclude = ['updated_at', 'remember_token', 'password', 'created_at'];

    protected function recordAudit(
        AuditActionType $action,
        object $entity,
        ?array $oldData = null,
        ?array $newData = null
    ): void {
        $organizationId = $this->getOrganizationId($entity);

        if ($oldData !== null && $newData !== null) {
            $oldData = $this->excludeFields($oldData, $entity);
            $newData = $this->excludeFields($newData, $entity);

            $changedKeys = $this->detectChangedKeys($oldData, $newData);

            if (empty($changedKeys)) {
                return;
            }

            $oldData = $this->prepareData(array_intersect_key($oldData, array_flip($changedKeys)));
            $newData = $this->prepareData(array_intersect_key($newData, array_flip($changedKeys)));
        } elseif ($newData !== null) {
            $newData = $this->prepareData($this->excludeFields($newData, $entity));
        }

        AuditLog::create([
            'organization_id' => $organizationId,
            'user_id' => auth()->id(),
            'action_type' => $action->value,
            'entity_type' => class_basename($entity),
            'entity_id' => $entity->id,
            'old_values' => $oldData,
            'new_values' => $newData,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);
    }

    /**
     * TODO: доработать
     * @param $entity
     * @return int|null
     */
    protected function getOrganizationId($entity = null): ?int
    {
        // Spatie Multitenancy — стандартный способ
        $tenant = Tenant::current();
        if ($tenant) {
            return $tenant->id;
        }

        // Fallback: если в сущности есть organization_id
        if ($entity && isset($entity->organization_id)) {
            return $entity->organization_id;
        }

        // Если запись связана с пациентом, берем организацию у пациента
        if (isset($entity->patient) && isset($entity->patient->organization_id)) {
            return $entity->patient->organization_id;
        }

        // Отдельный случай: сама организация
        if ($entity instanceof \App\Domain\Organization\Models\Organization) {
            return $entity->id;
        }

        $userOrganizationId = auth()->user()?->organization_id;
        if ($userOrganizationId) {
            return (int) $userOrganizationId;
        }

        // TODO: возвращаю по умолчанию organizaion_id = 1 - системная организация
        return 1;
    }

    private function excludeFields(array $data, object $entity): array
    {
        $exclude = array_merge(
            $this->defaultExclude,
            $entity->auditExclude ?? []
        );

        return array_diff_key($data, array_flip($exclude));
    }

    private function prepareData(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_string($value) && strlen($value) > self::TEXT_TRUNCATE_LENGTH) {
                $data[$key] = substr($value, 0, self::TEXT_PREVIEW_LENGTH)
                    . '... [обрезано, ' . strlen($value) . ' символов]';
                continue;
            }

            if (is_array($value)) {
                $data[$key] = $this->prepareArrayData($value);
            }
        }
        return $data;
    }

    private function prepareArrayData(array $value): array
    {
        foreach ($value as $key => $item) {
            if (is_array($item)) {
                $value[$key] = $this->prepareArrayData($item);
                continue;
            }

            if (is_string($item) && strlen($item) > self::TEXT_TRUNCATE_LENGTH) {
                $value[$key] = substr($item, 0, self::TEXT_PREVIEW_LENGTH)
                    . '... [обрезано, ' . strlen($item) . ' символов]';
            }
        }

        return $value;
    }

    private function detectChangedKeys(array $oldData, array $newData): array
    {
        $changed = [];

        foreach ($newData as $key => $value) {
            $oldValue = $oldData[$key] ?? null;

            if (!array_key_exists($key, $oldData)) {
                $changed[] = $key;
                continue;
            }

            if ($this->valuesAreDifferent($oldValue, $value)) {
                $changed[] = $key;
            }
        }

        foreach ($oldData as $key => $value) {
            if (!array_key_exists($key, $newData)) {
                $changed[] = $key;
            }
        }

        return $changed;
    }

    private function valuesAreDifferent(mixed $oldValue, mixed $newValue): bool
    {
        if (is_array($oldValue) || is_array($newValue)) {
            return $this->normalizeValue($oldValue) !== $this->normalizeValue($newValue);
        }

        return $oldValue !== $newValue;
    }

    private function normalizeValue(mixed $value): string
    {
        if (is_array($value)) {
            return json_encode($this->sortArrayRecursive($value), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '';
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format(DATE_ATOM);
        }

        if (is_object($value)) {
            return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: spl_object_hash($value);
        }

        return var_export($value, true);
    }

    private function sortArrayRecursive(array $value): array
    {
        ksort($value);
        foreach ($value as $key => $item) {
            if (is_array($item)) {
                $value[$key] = $this->sortArrayRecursive($item);
            }
        }

        return $value;
    }
}
