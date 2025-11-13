<?php

declare(strict_types=1);

namespace App\Domain\Organization\Actions;

use App\Domain\Organization\Events\OrganizationUpdated;
use App\Domain\Organization\Models\Organization;
use App\Domain\Organization\Queries\OrganizationQueries;
use App\Domain\Organization\Repositories\OrganizationRepository;

final readonly class UpdateOrganization
{
    public function __construct(
        private readonly OrganizationQueries $queries,
        private readonly OrganizationRepository $repository
    ) {}

    public function execute(array $data): Organization
    {
        $organization = $this->queries->getCurrentOrganizationWithFullData();

        if (! $organization) {
            throw new \Exception('Организация не найдена');
        }

        $originalData = $organization->toArray();

        $this->repository->update($organization, $data);

        event(new OrganizationUpdated($organization->fresh(), $originalData));

        return $organization->fresh();
    }
}
