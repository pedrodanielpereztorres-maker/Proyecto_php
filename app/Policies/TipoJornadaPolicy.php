<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\TipoJornada;
use Illuminate\Auth\Access\HandlesAuthorization;

class TipoJornadaPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:TipoJornada');
    }

    public function view(AuthUser $authUser, TipoJornada $tipoJornada): bool
    {
        return $authUser->can('View:TipoJornada');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:TipoJornada');
    }

    public function update(AuthUser $authUser, TipoJornada $tipoJornada): bool
    {
        return $authUser->can('Update:TipoJornada');
    }

    public function delete(AuthUser $authUser, TipoJornada $tipoJornada): bool
    {
        return $authUser->can('Delete:TipoJornada');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:TipoJornada');
    }

    public function restore(AuthUser $authUser, TipoJornada $tipoJornada): bool
    {
        return $authUser->can('Restore:TipoJornada');
    }

    public function forceDelete(AuthUser $authUser, TipoJornada $tipoJornada): bool
    {
        return $authUser->can('ForceDelete:TipoJornada');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:TipoJornada');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:TipoJornada');
    }

    public function replicate(AuthUser $authUser, TipoJornada $tipoJornada): bool
    {
        return $authUser->can('Replicate:TipoJornada');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:TipoJornada');
    }

}