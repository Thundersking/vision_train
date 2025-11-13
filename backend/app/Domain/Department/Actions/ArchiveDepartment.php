<?php

declare(strict_types=1);

namespace App\Domain\Department\Actions;

use App\Domain\Department\Events\DepartmentArchived;
use App\Domain\Department\Queries\DepartmentQueries;
use App\Domain\Department\Repositories\DepartmentRepository;
use Illuminate\Support\Facades\DB;

final readonly class ArchiveDepartment
{
    public function __construct(
        private DepartmentRepository $repository,
        private DepartmentQueries $departmentQueries
    ) {}

    public function execute(string $departmentUuid): void
    {
        $department = $this->departmentQueries->findByUuid($departmentUuid);

        if (!$department) {
            throw new \InvalidArgumentException("Отделение с UUID {$departmentUuid} не найдено");
        }

        if (!$department->is_active) {
            throw new \InvalidArgumentException('Отделение уже архивировано');
        }

        // Проверяем, есть ли активные пользователи в отделении
        if ($department->users()->where('is_active', true)->exists()) {
            throw new \InvalidArgumentException('Невозможно архивировать отделение с активными пользователями');
        }

        DB::transaction(function () use ($department) {
            // Деактивируем отделение
            $department->is_active = false;

            $this->repository->save($department);

            // Публикуем событие после сохранения
            event(new DepartmentArchived($department));
        });
    }
}
