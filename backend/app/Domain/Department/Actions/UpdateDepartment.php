<?php

declare(strict_types=1);

namespace App\Domain\Department\Actions;

use App\Domain\Department\Events\DepartmentUpdated;
use App\Domain\Department\Guards\DepartmentGuard;
use App\Domain\Department\Models\Department;
use App\Domain\Department\Repositories\DepartmentRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final readonly class UpdateDepartment
{
    public function __construct(
        private DepartmentRepository $repository,
        private DepartmentGuard      $guard
    ) {}

    /**
     * @throws ValidationException
     */
    public function execute(Department $department, array $data): Department
    {
        if (!empty($data['email']) && $data['email'] !== $department->email) {
            $this->guard->ensureEmailUniqueExceptDepartment(
                $department->organization_id,
                $data['email'],
                $department->uuid
            );
        }

        return DB::transaction(function () use ($department, $data) {
            $original = $department->getOriginal();

            $this->repository->update($department, $data);

            event(new DepartmentUpdated($department, $original));

            return $department->fresh();
        });
    }
}
