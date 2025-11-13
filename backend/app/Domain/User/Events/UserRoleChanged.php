<?php

namespace App\Domain\User\Events;

use App\Domain\User\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserRoleChanged
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public User   $user,
        public string $oldRole,
        public string $newRole
    )
    {
    }
}
