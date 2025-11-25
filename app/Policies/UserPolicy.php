<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can view any users.
     */
    public function viewAny(User $user): bool
    {
        // Admin users can view all users
        return true;
    }

    /**
     * Determine if the user can view the user.
     */
    public function view(User $user, User $model): bool
    {
        // Admin users can view any user
        return true;
    }

    /**
     * Determine if the user can create users.
     */
    public function create(User $user): bool
    {
        // Admin users can create users
        return true;
    }

    /**
     * Determine if the user can update the user.
     */
    public function update(User $user, User $model): bool
    {
        // Admin users can update any user
        return true;
    }

    /**
     * Determine if the user can delete the user.
     */
    public function delete(User $user, User $model): bool
    {
        // Admin users can delete any user (except themselves)
        return $user->id !== $model->id;
    }

    /**
     * Determine if the user can restore the user.
     */
    public function restore(User $user, User $model): bool
    {
        // Admin users can restore any user
        return true;
    }

    /**
     * Determine if the user can permanently delete the user.
     */
    public function forceDelete(User $user, User $model): bool
    {
        // Admin users can force delete any user (except themselves)
        return $user->id !== $model->id;
    }
}

