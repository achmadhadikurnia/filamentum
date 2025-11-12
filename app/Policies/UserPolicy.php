<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class UserPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:UserResource');
    }

    public function view(AuthUser $authUser): bool
    {
        return $authUser->can('View:UserResource');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:UserResource');
    }

    public function update(AuthUser $authUser, AuthUser $user): bool
    {
        // Only Super Admin users can edit other Super Admin users
        if ($user->hasRole('Super Admin')) {
            return $authUser->hasRole('Super Admin');
        }

        return $authUser->can('Update:UserResource');
    }

    public function delete(AuthUser $authUser, AuthUser $user): bool
    {
        // Prevent users from deleting themselves
        if ($authUser->is($user)) {
            return false;
        }

        // Only Super Admin users can delete other Super Admin users
        if ($user->hasRole('Super Admin')) {
            return $authUser->hasRole('Super Admin');
        }

        return $authUser->can('Delete:UserResource');
    }

    public function restore(AuthUser $authUser): bool
    {
        return $authUser->can('Restore:UserResource');
    }

    public function forceDelete(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDelete:UserResource');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:UserResource');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:UserResource');
    }

    public function replicate(AuthUser $authUser): bool
    {
        return $authUser->can('Replicate:UserResource');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:UserResource');
    }
}
