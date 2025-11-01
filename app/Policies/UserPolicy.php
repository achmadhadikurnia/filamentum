<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class UserPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:User');
    }

    public function view(AuthUser $authUser): bool
    {
        return $authUser->can('View:User');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:User');
    }

    public function update(AuthUser $authUser, AuthUser $user): bool
    {
        // Prevent users from updating themselves
        if ($authUser->is($user)) {
            return false;
        }

        // Admins cannot update Super Admins or other Admins
        if ($authUser->hasRole('Admin')) {
            // Admins can only update Regular users
            return !$user->hasRole('Super Admin') && !$user->hasRole('Admin') && $authUser->can('Update:User');
        }

        // Super Admins can update anyone
        if ($authUser->hasRole('Super Admin')) {
            return $authUser->can('Update:User');
        }

        return false;
    }

    public function delete(AuthUser $authUser, AuthUser $user): bool
    {
        // Prevent users from deleting themselves
        if ($authUser->is($user)) {
            return false;
        }

        // Admins cannot delete Super Admins or other Admins
        if ($authUser->hasRole('Admin')) {
            // Admins can only delete Regular users
            return !$user->hasRole('Super Admin') && !$user->hasRole('Admin') && $authUser->can('Delete:User');
        }

        // Super Admins can delete anyone
        if ($authUser->hasRole('Super Admin')) {
            return $authUser->can('Delete:User');
        }

        return false;
    }

    public function restore(AuthUser $authUser): bool
    {
        return $authUser->can('Restore:User');
    }

    public function forceDelete(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDelete:User');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:User');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:User');
    }

    public function replicate(AuthUser $authUser): bool
    {
        return $authUser->can('Replicate:User');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:User');
    }
}
