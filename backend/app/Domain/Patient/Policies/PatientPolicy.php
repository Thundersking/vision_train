<?php

declare(strict_types=1);

namespace App\Domain\Patient\Policies;

use App\Domain\Patient\Models\Patient;
use App\Domain\RBAC\Enums\Permission;
use App\Domain\User\Models\User;

class PatientPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permission::PATIENTS_INDEX->value);
    }

    public function view(User $user, Patient $patient): bool
    {
        if ($user->organization_id !== $patient->organization_id) {
            return false;
        }

        return $user->id === $patient->user_id
            || $user->can(Permission::PATIENTS_VIEW->value);
    }

    public function create(User $user): bool
    {
        return $user->can(Permission::PATIENTS_CREATE->value);
    }

    public function update(User $user, Patient $patient): bool
    {
        if ($user->organization_id !== $patient->organization_id) {
            return false;
        }

        return $user->id === $patient->user_id
            || $user->can(Permission::PATIENTS_UPDATE->value);
    }

    public function delete(User $user, Patient $patient): bool
    {
        return $this->archive($user, $patient);
    }

    public function archive(User $user, Patient $patient): bool
    {
        if ($user->organization_id !== $patient->organization_id) {
            return false;
        }

        return $user->can(Permission::PATIENTS_ARCHIVE->value);
    }

    public function restore(User $user, Patient $patient): bool
    {
        if ($user->organization_id !== $patient->organization_id) {
            return false;
        }

        return $user->can(Permission::PATIENTS_RESTORE->value);
    }
}
