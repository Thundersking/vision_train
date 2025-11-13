<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories\Eloquent;

use App\Domain\User\Models\User;
use App\Domain\User\Repositories\UserRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class EloquentUserRepository implements UserRepository
{
    public function save(User $user): void
    {
        DB::transaction(function () use ($user) {
            $user->save();
            Cache::tags(['users', "organization:{$user->organization_id}"])->flush();
        });
    }

    public function delete(int $id): void
    {
        $user = User::findOrFail($id);

        DB::transaction(function () use ($user) {
            $user->delete();
            Cache::tags(['users', "organization:{$user->organization_id}"])->flush();
        });
    }

    public function restore(string $uuid): void
    {
        $user = $this->findByUuid($uuid);
        
        if (!$user) {
            throw new \InvalidArgumentException("Пользователь с UUID {$uuid} не найден");
        }

        DB::transaction(function () use ($user) {
            $user->is_active = true;
            $this->save($user);
        });
    }

    public function findOrFail(int $id): User
    {
        return User::findOrFail($id);
    }
    
    public function findByUuid(string $uuid): ?User
    {
        return User::where('uuid', $uuid)->first();
    }

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function findByOrganization(int $organizationId): Collection
    {
        return User::where('organization_id', $organizationId)
            ->orderBy('name')
            ->get();
    }
}
