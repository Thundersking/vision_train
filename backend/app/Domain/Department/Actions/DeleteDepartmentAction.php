<?php

declare(strict_types=1);

namespace App\Domain\Department\Actions;

use App\Domain\Department\Models\Department;
use App\Domain\Department\Repositories\DepartmentRepository;
use App\Domain\Shared\Enums\AuditActionType;
use App\Domain\Shared\Traits\RecordsAuditLog;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Throwable;

final class DeleteDepartmentAction
{
    use RecordsAuditLog;

    public function __construct(
        private readonly DepartmentRepository $repository
    ) {}

    /**
     * @throws Throwable
     */
    public function execute(Department $department): void
    {
        DB::transaction(function () use ($department) {
            $deleted = $this->repository->delete($department->id);

            if (!$deleted) {
                throw new ModelNotFoundException();
            }

            $this->recordAudit(
                action: AuditActionType::DELETED,
                entity: $department,
            );
        });
    }
}
