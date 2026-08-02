<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\TipoEspacio;
use Illuminate\Auth\Access\HandlesAuthorization;

class TipoEspacioPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:TipoEspacio');
    }

    public function view(AuthUser $authUser, TipoEspacio $tipoEspacio): bool
    {
        return $authUser->can('View:TipoEspacio');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:TipoEspacio');
    }

    public function update(AuthUser $authUser, TipoEspacio $tipoEspacio): bool
    {
        return $authUser->can('Update:TipoEspacio');
    }

    public function delete(AuthUser $authUser, TipoEspacio $tipoEspacio): bool
    {
        return $authUser->can('Delete:TipoEspacio');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:TipoEspacio');
    }

    public function restore(AuthUser $authUser, TipoEspacio $tipoEspacio): bool
    {
        return $authUser->can('Restore:TipoEspacio');
    }

    public function forceDelete(AuthUser $authUser, TipoEspacio $tipoEspacio): bool
    {
        return $authUser->can('ForceDelete:TipoEspacio');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:TipoEspacio');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:TipoEspacio');
    }

    public function replicate(AuthUser $authUser, TipoEspacio $tipoEspacio): bool
    {
        return $authUser->can('Replicate:TipoEspacio');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:TipoEspacio');
    }

}