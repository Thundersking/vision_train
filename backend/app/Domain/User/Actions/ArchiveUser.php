<?php

declare(strict_types=1);

namespace App\Domain\User\Actions;

use App\Domain\User\Events\UserArchived;
use App\Domain\User\Queries\UserQueries;
use App\Domain\User\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;

final readonly class ArchiveUser
{
    public function __construct(
        private UserRepository $repository,
        private UserQueries    $userQueries
    ) {}

    /**
     * Архивирует пользователя (деактивация)
     */
    public function execute(string $userUuid): void
    {
        // Получаем пользователя через UserQueries
        $user = $this->userQueries->findByUuid($userUuid);

        if (!$user) {
            throw new \InvalidArgumentException("Пользователь с UUID {$userUuid} не найден");
        }

        DB::transaction(function () use ($user) {
            // Деактивируем пользователя
            $user->is_active = false;

            $this->repository->save($user);

            // Публикуем событие после сохранения
            event(new UserArchived($user));
        });
    }
}
