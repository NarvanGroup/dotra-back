<?php

namespace App\Policies;

use App\Models\Application;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ApplicationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can view any applications.
     */
    public function viewAny(User $user): bool
    {
        // Admin users can view all applications
        return true;
    }

    /**
     * Determine if the user can view the application.
     */
    public function view(User $user, Application $application): bool
    {
        // Admin users can view any application
        return true;
    }

    /**
     * Determine if the user can create applications.
     */
    public function create(User $user): bool
    {
        // Admin users can create applications
        return true;
    }

    /**
     * Determine if the user can update the application.
     */
    public function update(User $user, Application $application): bool
    {
        // Admin users can update any application
        return true;
    }

    /**
     * Determine if the user can delete the application.
     */
    public function delete(User $user, Application $application): bool
    {
        // Admin users can delete any application
        return true;
    }

    /**
     * Determine if the user can restore the application.
     */
    public function restore(User $user, Application $application): bool
    {
        // Admin users can restore any application
        return true;
    }

    /**
     * Determine if the user can permanently delete the application.
     */
    public function forceDelete(User $user, Application $application): bool
    {
        // Admin users can force delete any application
        return true;
    }
}
