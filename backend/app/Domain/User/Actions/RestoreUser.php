<?php

declare(strict_types=1);

namespace App\Domain\User\Actions;

use App\Domain\User\Events\UserRestored;
use App\Domain\User\Queries\UserQueries;
use App\Domain\User\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;

class RestoreUser
{
    public function __construct(
        private UserRepository $repository,
        private UserQueries $userQueries
    ) {}

    /**
     * Восстанавливает пользователя (активация)
     */
    public function execute(string $userUuid): void
    {
        // Получаем пользователя через UserQueries для проверки Policy
        $user = $this->userQueries->findByUuid($userUuid);
        
        if (!$user) {
            throw new \InvalidArgumentException("Пользователь с UUID {$userUuid} не найден");
        }
        
        DB::transaction(function () use ($user) {
            // Восстанавливаем пользователя
            $user->is_active = true;
            
            $this->repository->save($user);
            
            // Публикуем событие после сохранения
            event(new UserRestored($user));
        });
    }
}