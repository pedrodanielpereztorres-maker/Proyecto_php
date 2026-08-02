<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Especialidad;
use Illuminate\Auth\Access\HandlesAuthorization;

class EspecialidadPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Especialidad');
    }

    public function view(AuthUser $authUser, Especialidad $especialidad): bool
    {
        return $authUser->can('View:Especialidad');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Especialidad');
    }

    public function update(AuthUser $authUser, Especialidad $especialidad): bool
    {
        return $authUser->can('Update:Especialidad');
    }

    public function delete(AuthUser $authUser, Especialidad $especialidad): bool
    {
        return $authUser->can('Delete:Especialidad');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Especialidad');
    }

    public function restore(AuthUser $authUser, Especialidad $especialidad): bool
    {
        return $authUser->can('Restore:Especialidad');
    }

    public function forceDelete(AuthUser $authUser, Especialidad $especialidad): bool
    {
        return $authUser->can('ForceDelete:Especialidad');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Especialidad');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Especialidad');
    }

    public function replicate(AuthUser $authUser, Especialidad $especialidad): bool
    {
        return $authUser->can('Replicate:Especialidad');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Especialidad');
    }

}