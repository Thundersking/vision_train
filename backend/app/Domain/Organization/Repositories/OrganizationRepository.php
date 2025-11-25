<?php

declare(strict_types=1);

namespace App\Domain\Organization\Repositories;

use App\Domain\Organization\Models\Organization;
use App\Support\Repositories\BaseRepository;

class OrganizationRepository extends BaseRepository
{
    protected static function modelClass(): string
    {
        return Organization::class;
    }

    public function findCurrent(): ?Organization
    {
//        TODO: Поправить после того как будем определять текущего тенанта
        $organizationId = 1;

//        if (!$organizationId) {
//            return null;
//        }

        return $this->newQuery()->find($organizationId);
    }
}
