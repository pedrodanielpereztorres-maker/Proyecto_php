<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\JornadaParametro;
use Illuminate\Auth\Access\HandlesAuthorization;

class JornadaParametroPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:JornadaParametro');
    }

    public function view(AuthUser $authUser, JornadaParametro $jornadaParametro): bool
    {
        return $authUser->can('View:JornadaParametro');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:JornadaParametro');
    }

    public function update(AuthUser $authUser, JornadaParametro $jornadaParametro): bool
    {
        return $authUser->can('Update:JornadaParametro');
    }

    public function delete(AuthUser $authUser, JornadaParametro $jornadaParametro): bool
    {
        return $authUser->can('Delete:JornadaParametro');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:JornadaParametro');
    }

    public function restore(AuthUser $authUser, JornadaParametro $jornadaParametro): bool
    {
        return $authUser->can('Restore:JornadaParametro');
    }

    public function forceDelete(AuthUser $authUser, JornadaParametro $jornadaParametro): bool
    {
        return $authUser->can('ForceDelete:JornadaParametro');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:JornadaParametro');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:JornadaParametro');
    }

    public function replicate(AuthUser $authUser, JornadaParametro $jornadaParametro): bool
    {
        return $authUser->can('Replicate:JornadaParametro');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:JornadaParametro');
    }

}