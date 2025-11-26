<?php

declare(strict_types=1);

namespace App\Domain\Organization\Controllers;

use App\Domain\Organization\Actions\UpdateOrganizationAction;
use App\Domain\Organization\Repositories\OrganizationRepository;
use App\Domain\Organization\Requests\UpdateOrganizationRequest;
use App\Domain\Organization\Resources\OrganizationResource;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller;

final class OrganizationController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private readonly OrganizationRepository $repository,
    ) {}

    /**
     * @throws AuthorizationException
     */
    public function show(): OrganizationResource
    {
//        TODO: поправить обязательно - брать текущую организацию - сейчас заглушка
        $organization = $this->repository->findCurrent();

        if (!$organization) {
            throw new ModelNotFoundException();
        }

//        $this->authorize('view', $organization);

        return new OrganizationResource($organization);
    }

    /**
     * @throws AuthorizationException
     */
    public function update(UpdateOrganizationRequest $request, UpdateOrganizationAction $updateAction): OrganizationResource
    {
        $organization = $this->repository->findCurrent();

        if (!$organization) {
            throw new ModelNotFoundException();
        }

//        $this->authorize('update', $organization);

        $updatedOrganization = $updateAction->execute($organization, $request->validated());

        return new OrganizationResource($updatedOrganization);
    }
}
