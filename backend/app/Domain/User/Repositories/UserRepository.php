<?php

declare(strict_types=1);

namespace App\Domain\User\Repositories;

use App\Domain\User\Models\User;

interface UserRepository
{
    public function save(User $user): void;

    public function delete(int $id): void;
}
