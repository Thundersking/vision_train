<?php

declare(strict_types=1);

namespace App\Domain\Department\Actions;

use App\Domain\Department\Guards\DepartmentGuard;
use App\Domain\Department\Models\Department;
use App\Domain\Department\Repositories\DepartmentRepository;
use App\Domain\Shared\Enums\AuditActionType;
use App\Domain\Shared\Traits\RecordsAuditLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class CreateDepartmentAction
{
    use RecordsAuditLog;

    public function __construct(
        private readonly DepartmentRepository $repository,
        private readonly DepartmentGuard      $guard
    )
    {
    }

    /**
     * @throws ValidationException
     */
    public function execute(array $data): Department
    {
        $this->guard->ensureEmailUnique($data['email']);

        return DB::transaction(function () use ($data) {
            $department = $this->repository->create($data);

            $this->recordAudit(
                action: AuditActionType::CREATED,
                entity: $department,
                newData: $data
            );

            return $department;
        });
    }
}
