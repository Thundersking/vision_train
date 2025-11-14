<?php

declare(strict_types=1);

namespace App\Domain\User\Repositories;

use App\Domain\User\Models\User;

interface UserRepositoryInterface
{
    /**
     * Создать пользователя
     */
    public function create(array $data): User;

    /**
     * Обновить пользователя
     */
    public function update(int $id, array $data): User;

    /**
     * Удалить пользователя (soft delete)
     */
    public function delete(int $id): bool;

    /**
     * Восстановить удалённого пользователя
     */
    public function restore(int $id): bool;
}
