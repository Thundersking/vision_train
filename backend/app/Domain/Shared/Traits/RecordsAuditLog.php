<?php

namespace App\Domain\Shared\Traits;

use App\Domain\Shared\Enums\AuditActionType;
use App\Domain\Shared\Models\AuditLog;
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

        $entityName = strtolower(class_basename($entity));
        $actionType = "{$entityName}.{$action->value}";

        if ($oldData !== null && $newData !== null) {
            $oldData = $this->excludeFields($oldData, $entity);
            $newData = $this->excludeFields($newData, $entity);

            $changedFields = array_diff_assoc($newData, $oldData);

            if (empty($changedFields)) {
                return;
            }

            $oldData = $this->prepareData(array_intersect_key($oldData, $changedFields));
            $newData = $this->prepareData($changedFields);
        } elseif ($newData !== null) {
            $newData = $this->prepareData($this->excludeFields($newData, $entity));
        }

        AuditLog::create([
            'organization_id' => $organizationId,
            'user_id' => auth()->id(),
            'action_type' => $actionType,
            'entity_type' => class_basename($entity),
            'entity_id' => $entity->id,
            'old_values' => $oldData,
            'new_values' => $newData,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);
    }

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

        return null;
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
            }
        }
        return $data;
    }
}
