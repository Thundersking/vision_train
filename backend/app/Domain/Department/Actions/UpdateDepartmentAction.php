<?php

declare(strict_types=1);

namespace App\Domain\Department\Actions;

use App\Domain\Department\Guards\DepartmentGuard;
use App\Domain\Department\Repositories\DepartmentRepository;
use App\Domain\Shared\Enums\AuditActionType;
use App\Domain\Shared\Traits\RecordsAuditLog;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class UpdateDepartmentAction
{
    use RecordsAuditLog;

    public function __construct(
        private readonly DepartmentRepository $repository,
        private readonly DepartmentGuard $guard
    ) {}

    /**
     * @throws ValidationException
     */
    public function execute(string $uuid, array $data)
    {
        $department = $this->repository->findByUuid($uuid);

        if (!$department) {
            throw new ModelNotFoundException();
        }

        if (!empty($data['email']) && $data['email'] !== $department->email) {
            $this->guard->ensureEmailUniqueExceptUuid($data['email'], $department->uuid);
        }

        return DB::transaction(function () use ($department, $data) {
            $oldData = $department->toArray();

            $newData = $this->repository->update($department->id, $data);

            $this->recordAudit(
                action: AuditActionType::UPDATED,
                entity: $department,
                oldData: $oldData,
                newData: $newData
            );

            return $newData;
        });
    }
}
