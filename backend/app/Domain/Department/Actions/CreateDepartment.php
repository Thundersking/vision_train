<?php

declare(strict_types=1);

namespace App\Domain\Department\Actions;

use App\Domain\Department\Events\DepartmentCreated;
use App\Domain\Department\Guards\DepartmentGuard;
use App\Domain\Department\Models\Department;
use App\Domain\Department\Repositories\DepartmentRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final readonly class CreateDepartment
{
    public function __construct(
        private DepartmentRepository $repository,
        private DepartmentGuard      $guard
    ) {}

    /**
     * @throws ValidationException
     */
    public function execute(array $data): Department
    {
        if (!empty($data['email'])) {
            $this->guard->ensureEmailUnique($data['organization_id'], $data['email']);
        }

        return DB::transaction(function () use ($data) {
            $department = new Department($data);
            $this->repository->save($department);

            event(new DepartmentCreated($department));

            return $department->fresh();
        });
    }
}
