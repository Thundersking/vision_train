<?php

declare(strict_types=1);

namespace App\Domain\Patient\Policies;

use App\Domain\RBAC\Enums\Permission;
use App\Domain\Patient\Models\Patient;
use App\Domain\User\Models\User;

class PatientPolicy
{
    /**
     * Может ли пользователь просматривать список пациентов
     */
    public function viewAny(User $user): bool
    {
        return true;
//        return $user->can(Permission::PATIENTS_VIEW->value);
    }

    /**
     * Может ли пользователь просматривать конкретного пациента
     */
    public function view(User $user, Patient $patient): bool
    {
        return true;
//        // Проверяем принадлежность к организации
//        if ($user->organization_id !== $patient->organization_id) {
//            return false;
//        }
//
//        // Проверяем разрешение на просмотр пациентов
//        if (!$user->can(Permission::PATIENTS_VIEW->value)) {
//            return false;
//        }
//
//        // Если у пользователя есть разрешение PATIENTS_INDEX, может видеть всех пациентов
//        if ($user->can(Permission::PATIENTS_INDEX->value)) {
//            return true;
//        }
//
//        // Иначе может видеть только своих пациентов
//        return $patient->user_id === $user->id;
    }

    /**
     * Может ли пользователь создавать новых пациентов
     */
    public function create(User $user): bool
    {
        return true;
//        return $user->can(Permission::PATIENTS_CREATE->value);
    }

    /**
     * Может ли пользователь обновлять данные пациента
     */
    public function update(User $user, Patient $patient): bool
    {
        return true;
//        // Проверяем принадлежность к организации
//        if ($user->organization_id !== $patient->organization_id) {
//            return false;
//        }
//
//        // Проверяем разрешение на обновление пациентов
//        if (!$user->can(Permission::PATIENTS_UPDATE->value)) {
//            return false;
//        }
//
//        // Если у пользователя есть разрешение PATIENTS_UPDATE_ALL, может редактировать всех пациентов
//        if ($user->can(Permission::PATIENTS_UPDATE_ALL->value)) {
//            return true;
//        }
//
//        // Иначе может редактировать только своих пациентов
//        return $patient->user_id === $user->id;
    }

    /**
     * Может ли пользователь удалять пациента
     */
    public function delete(User $user, Patient $patient): bool
    {
        return true;
//        // Проверяем принадлежность к организации
//        if ($user->organization_id !== $patient->organization_id) {
//            return false;
//        }
//
//        // Проверяем разрешение на удаление пациентов
//        return $user->can(Permission::PATIENTS_DELETE->value);
    }

    /**
     * Может ли пользователь архивировать пациента
     */
    public function archive(User $user, Patient $patient): bool
    {
        return true;
//        // Проверяем принадлежность к организации
//        if ($user->organization_id !== $patient->organization_id) {
//            return false;
//        }
//
//        // Проверяем разрешение на архивирование пациентов
//        return $user->can(Permission::PATIENTS_ARCHIVE->value);
    }

    /**
     * Может ли пользователь восстанавливать архивированных пациентов
     */
    public function restore(User $user, Patient $patient): bool
    {
        return true;

//        // Проверяем принадлежность к организации
//        if ($user->organization_id !== $patient->organization_id) {
//            return false;
//        }
//
//        // Проверяем разрешение на восстановление пациентов
//        return $user->can(Permission::PATIENTS_RESTORE->value);
    }
}
