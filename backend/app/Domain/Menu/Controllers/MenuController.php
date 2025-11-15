<?php

declare(strict_types=1);

namespace App\Domain\Menu\Controllers;

use App\Domain\Menu\Service\MenuService;
use App\Domain\User\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

final class MenuController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private readonly MenuService $menuService
    ) {}

    /**
     * Получить меню для текущего пользователя
     */
    public function index(Request $request): JsonResponse
    {
//        $user = $request->user();
//        TODO: поправить после того как авторизацию сделаю
        $user = User::find(2);

        $menu = $this->menuService->getMenuForUser($user);

        return new JsonResponse(['data' => $menu]);
    }
}
