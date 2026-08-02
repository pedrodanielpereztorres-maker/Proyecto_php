<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\NivelAcademico;
use Illuminate\Auth\Access\HandlesAuthorization;

class NivelAcademicoPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:NivelAcademico');
    }

    public function view(AuthUser $authUser, NivelAcademico $nivelAcademico): bool
    {
        return $authUser->can('View:NivelAcademico');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:NivelAcademico');
    }

    public function update(AuthUser $authUser, NivelAcademico $nivelAcademico): bool
    {
        return $authUser->can('Update:NivelAcademico');
    }

    public function delete(AuthUser $authUser, NivelAcademico $nivelAcademico): bool
    {
        return $authUser->can('Delete:NivelAcademico');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:NivelAcademico');
    }

    public function restore(AuthUser $authUser, NivelAcademico $nivelAcademico): bool
    {
        return $authUser->can('Restore:NivelAcademico');
    }

    public function forceDelete(AuthUser $authUser, NivelAcademico $nivelAcademico): bool
    {
        return $authUser->can('ForceDelete:NivelAcademico');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:NivelAcademico');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:NivelAcademico');
    }

    public function replicate(AuthUser $authUser, NivelAcademico $nivelAcademico): bool
    {
        return $authUser->can('Replicate:NivelAcademico');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:NivelAcademico');
    }

}