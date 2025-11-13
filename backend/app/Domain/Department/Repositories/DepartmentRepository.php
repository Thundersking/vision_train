<?php

declare(strict_types=1);

namespace App\Domain\Department\Repositories;

use App\Domain\Department\Models\Department;

interface DepartmentRepository
{
    public function save(Department $department): void;

    public function update(Department $department, array $data): void;

    public function delete(int $id): void;
}