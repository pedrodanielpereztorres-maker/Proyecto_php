<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Carrera;
use Illuminate\Auth\Access\HandlesAuthorization;

class CarreraPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Carrera');
    }

    public function view(AuthUser $authUser, Carrera $carrera): bool
    {
        return $authUser->can('View:Carrera');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Carrera');
    }

    public function update(AuthUser $authUser, Carrera $carrera): bool
    {
        return $authUser->can('Update:Carrera');
    }

    public function delete(AuthUser $authUser, Carrera $carrera): bool
    {
        return $authUser->can('Delete:Carrera');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Carrera');
    }

    public function restore(AuthUser $authUser, Carrera $carrera): bool
    {
        return $authUser->can('Restore:Carrera');
    }

    public function forceDelete(AuthUser $authUser, Carrera $carrera): bool
    {
        return $authUser->can('ForceDelete:Carrera');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Carrera');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Carrera');
    }

    public function replicate(AuthUser $authUser, Carrera $carrera): bool
    {
        return $authUser->can('Replicate:Carrera');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Carrera');
    }

}