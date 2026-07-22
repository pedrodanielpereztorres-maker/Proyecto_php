<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Semestre;
use Illuminate\Auth\Access\HandlesAuthorization;

class SemestrePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Semestre');
    }

    public function view(AuthUser $authUser, Semestre $semestre): bool
    {
        return $authUser->can('View:Semestre');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Semestre');
    }

    public function update(AuthUser $authUser, Semestre $semestre): bool
    {
        return $authUser->can('Update:Semestre');
    }

    public function delete(AuthUser $authUser, Semestre $semestre): bool
    {
        return $authUser->can('Delete:Semestre');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Semestre');
    }

    public function restore(AuthUser $authUser, Semestre $semestre): bool
    {
        return $authUser->can('Restore:Semestre');
    }

    public function forceDelete(AuthUser $authUser, Semestre $semestre): bool
    {
        return $authUser->can('ForceDelete:Semestre');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Semestre');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Semestre');
    }

    public function replicate(AuthUser $authUser, Semestre $semestre): bool
    {
        return $authUser->can('Replicate:Semestre');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Semestre');
    }

}