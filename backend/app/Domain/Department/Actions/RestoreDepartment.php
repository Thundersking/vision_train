<?php

declare(strict_types=1);

namespace App\Domain\Department\Actions;

use App\Domain\Department\Events\DepartmentRestored;
use App\Domain\Department\Queries\DepartmentQueries;
use App\Domain\Department\Repositories\DepartmentRepository;
use Illuminate\Support\Facades\DB;

final readonly class RestoreDepartment
{
    public function __construct(
        private DepartmentRepository $repository,
        private DepartmentQueries $departmentQueries
    ) {}

    public function execute(string $departmentUuid): void
    {
        // Получаем отделение через DepartmentQueries
        $department = $this->departmentQueries->findByUuid($departmentUuid);

        if (!$department) {
            throw new \InvalidArgumentException("Отделение с UUID {$departmentUuid} не найдено");
        }

        if ($department->is_active) {
            throw new \InvalidArgumentException('Отделение уже активно');
        }

        DB::transaction(function () use ($department) {
            // Активируем отделение
            $department->is_active = true;

            $this->repository->save($department);

            // Публикуем событие после сохранения
            event(new DepartmentRestored($department));
        });
    }
}
