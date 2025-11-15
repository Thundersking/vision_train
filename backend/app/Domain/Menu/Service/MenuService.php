<?php

declare(strict_types=1);

namespace App\Domain\Menu\Service;

use App\Domain\User\Models\User;

class MenuService
{
    /**
     * Получить меню для пользователя с учетом его разрешений
     */
    public function getMenuForUser(?User $user): array
    {
        // Если пользователь не авторизован, возвращаем пустое меню
        if (!$user) {
            return [];
        }

        $menuConfig = require app_path('Domain/Menu/Config/menu.php');

        $filteredMenu = [];

        foreach ($menuConfig as $key => $menuItem) {
            if ($this->userCanAccessMenuItem($user, $menuItem)) {
                $filteredItem = [
                    'key' => $key,
                    'title' => $menuItem['title'],
                    'icon' => $menuItem['icon'] ?? null,
                    'route' => $menuItem['route'],
                ];

                // Если есть дочерние элементы, фильтруем их тоже
                if (isset($menuItem['children'])) {
                    $filteredChildren = [];

                    foreach ($menuItem['children'] as $childKey => $childItem) {
                        if ($this->userCanAccessMenuItem($user, $childItem)) {
                            $filteredChildren[] = [
                                'key' => $childKey,
                                'title' => $childItem['title'],
                                'route' => $childItem['route'],
                            ];
                        }
                    }

                    // Добавляем children только если они есть
                    if (!empty($filteredChildren)) {
                        $filteredItem['children'] = $filteredChildren;
                    }
                }

                $filteredMenu[] = $filteredItem;
            }
        }

        return $filteredMenu;
    }

    /**
     * Проверить, может ли пользователь получить доступ к пункту меню
     */
    private function userCanAccessMenuItem(User $user, array $menuItem): bool
    {
        // Если разрешение не требуется (null), доступ разрешен
        if ($menuItem['permission'] === null) {
            return true;
        }

        // Проверяем разрешение пользователя
        return $user->can($menuItem['permission']);
    }
}
