<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\PeriodoAcademico;
use Illuminate\Auth\Access\HandlesAuthorization;

class PeriodoAcademicoPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PeriodoAcademico');
    }

    public function view(AuthUser $authUser, PeriodoAcademico $periodoAcademico): bool
    {
        return $authUser->can('View:PeriodoAcademico');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PeriodoAcademico');
    }

    public function update(AuthUser $authUser, PeriodoAcademico $periodoAcademico): bool
    {
        return $authUser->can('Update:PeriodoAcademico');
    }

    public function delete(AuthUser $authUser, PeriodoAcademico $periodoAcademico): bool
    {
        return $authUser->can('Delete:PeriodoAcademico');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PeriodoAcademico');
    }

    public function restore(AuthUser $authUser, PeriodoAcademico $periodoAcademico): bool
    {
        return $authUser->can('Restore:PeriodoAcademico');
    }

    public function forceDelete(AuthUser $authUser, PeriodoAcademico $periodoAcademico): bool
    {
        return $authUser->can('ForceDelete:PeriodoAcademico');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PeriodoAcademico');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PeriodoAcademico');
    }

    public function replicate(AuthUser $authUser, PeriodoAcademico $periodoAcademico): bool
    {
        return $authUser->can('Replicate:PeriodoAcademico');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PeriodoAcademico');
    }

}