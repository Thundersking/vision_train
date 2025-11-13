<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories\Eloquent;

use App\Domain\Department\Models\Department;
use App\Domain\Department\Repositories\DepartmentRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class EloquentDepartmentRepository implements DepartmentRepository
{
    public function save(Department $department): void
    {
        DB::transaction(function () use ($department) {
            $department->save();
            Cache::tags(['departments', "organization:{$department->organization_id}"])->flush();
        });
    }

    public function update(Department $department, array $data): void
    {
        DB::transaction(function () use ($department, $data) {
            $department->update($data);
            Cache::tags(['departments', "organization:{$department->organization_id}"])->flush();
        });
    }

    public function delete(int $id): void
    {
        $department = Department::findOrFail($id);

        DB::transaction(function () use ($department) {
            $department->delete();
            Cache::tags(['departments', "organization:{$department->organization_id}"])->flush();
        });
    }
}
