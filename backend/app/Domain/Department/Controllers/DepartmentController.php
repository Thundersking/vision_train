<?php

declare(strict_types=1);

namespace App\Domain\Department\Controllers;

use App\Domain\Department\Actions\CreateDepartmentAction;
use App\Domain\Department\Actions\DeleteDepartmentAction;
use App\Domain\Department\Actions\UpdateDepartmentAction;
use App\Domain\Department\Models\Department;
use App\Domain\Department\Repositories\DepartmentRepository;
use App\Domain\Department\Requests\DepartmentSearchRequest;
use App\Domain\Department\Requests\StoreDepartmentRequest;
use App\Domain\Department\Requests\UpdateDepartmentRequest;
use App\Domain\Department\Resources\DepartmentDetailResource;
use App\Domain\Department\Resources\DepartmentListResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Illuminate\Validation\ValidationException;

final class DepartmentController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private readonly DepartmentRepository $repository
    ) {}

    public function index(DepartmentSearchRequest $request): AnonymousResourceCollection
    {
        $paginator = $this->repository->paginate($request->validated());

        return DepartmentListResource::collection($paginator);
    }

    public function show(string $uuid): DepartmentDetailResource
    {
        $department = $this->repository->findByUuid($uuid);

        return new DepartmentDetailResource($department);
    }

    /**
     * @throws ValidationException
     */
    public function store(StoreDepartmentRequest $request, CreateDepartmentAction $action): DepartmentDetailResource
    {
        $department = $action->execute($request->validated());

        return new DepartmentDetailResource($department);
    }

    /**
     * @throws ValidationException
     */
    public function update(string $uuid, UpdateDepartmentRequest $request, UpdateDepartmentAction $action): DepartmentDetailResource
    {
        $department = $action->execute($uuid, $request->validated());

        return new DepartmentDetailResource($department);
    }

    /**
     * @throws \Throwable
     */
    public function destroy(string $uuid, DeleteDepartmentAction $action): JsonResponse
    {
        /** @var Department $department */
        $department = $this->repository->findByUuid($uuid);

        if (!$department) {
            throw new ModelNotFoundException();
        }

//        $this->authorize('delete', $department);

        $action->execute($department);

        return response()->json([
            'message' => 'Запись успешно удалена.',
            'code' => 200,
        ]);
    }
}
