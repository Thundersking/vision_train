<?php

namespace App\Domain\User\Controllers;

use App\Domain\User\Actions\CreateUserAction;
use App\Domain\User\Actions\DeleteUserAction;
use App\Domain\User\Actions\ToggleUserStatusAction;
use App\Domain\User\Actions\UpdateUserAction;
use App\Domain\User\Models\User;
use App\Domain\User\Repositories\UserRepository;
use App\Domain\User\Requests\UserCreateRequest;
use App\Domain\User\Requests\UserSearchRequest;
use App\Domain\User\Requests\UserUpdateRequest;
use App\Domain\User\Resources\UserDetailResource;
use App\Domain\User\Resources\UserListResource;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Illuminate\Validation\ValidationException;

final class UserController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private readonly UserRepository $repository
    ) {}

    /**
     * @throws AuthorizationException
     */
    public function index(UserSearchRequest $request): AnonymousResourceCollection
    {
//        $this->authorize('viewAny', User::class);

        $paginator = $this->repository->paginate($request->validated());

        return UserListResource::collection($paginator);
    }

    /**
     * @throws AuthorizationException
     */
    public function show(string $uuid): UserDetailResource
    {
        $user = $this->repository->findByUuid($uuid);

        // $this->authorize('view', $user);

        return new UserDetailResource($user);
    }

    /**
     * @throws ValidationException
     */
    public function store(UserCreateRequest $request, CreateUserAction $action): UserDetailResource
    {
//        $this->authorize('create', User::class);

        $user = $action->execute($request->validated());

        return new UserDetailResource($user);
    }

    /**
     * @throws ValidationException|AuthorizationException
     */
    public function update(string $uuid, UserUpdateRequest $request, UpdateUserAction $action): UserDetailResource
    {
        /** @var User $user */
        $user = $this->repository->findByUuid($uuid);

        if (!$user) {
            throw new ModelNotFoundException();
        }

//        $this->authorize('update', $user);
        $updatedUser = $action->execute($user, $request->validated());

        return new UserDetailResource($updatedUser);
    }

    /**
     * @throws AuthorizationException
     * @throws \Exception
     */
    public function toggleStatus(string $uuid, ToggleUserStatusAction $action): UserDetailResource
    {
        $user = $this->repository->findByUuid($uuid);

        $this->authorize('update', $user);

        $updatedUser = $action->execute($uuid);

        return new UserDetailResource($updatedUser);
    }

    /**
     * @throws AuthorizationException
     * @throws \Exception
     */
    public function destroy(string $uuid, DeleteUserAction $action): JsonResponse
    {
        /** @var User $user */
        $user = $this->repository->findByUuid($uuid);

        if (!$user) {
            throw new ModelNotFoundException();
        }

//        $this->authorize('delete', $user);

        $action->execute($user);

        return new JsonResponse('Запись успешно удалена.');
    }
}
