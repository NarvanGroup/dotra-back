<?php

namespace App\Policies;

use App\Models\Installment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class InstallmentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can view any installments.
     */
    public function viewAny(User $user): bool
    {
        // Admin users can view all installments
        return true;
    }

    /**
     * Determine if the user can view the installment.
     */
    public function view(User $user, Installment $installment): bool
    {
        // Admin users can view any installment
        return true;
    }

    /**
     * Determine if the user can create installments.
     */
    public function create(User $user): bool
    {
        // Admin users can create installments
        return true;
    }

    /**
     * Determine if the user can update the installment.
     */
    public function update(User $user, Installment $installment): bool
    {
        // Admin users can update any installment
        return true;
    }

    /**
     * Determine if the user can delete the installment.
     */
    public function delete(User $user, Installment $installment): bool
    {
        // Admin users can delete any installment
        return true;
    }

    /**
     * Determine if the user can restore the installment.
     */
    public function restore(User $user, Installment $installment): bool
    {
        // Admin users can restore any installment
        return true;
    }

    /**
     * Determine if the user can permanently delete the installment.
     */
    public function forceDelete(User $user, Installment $installment): bool
    {
        // Admin users can force delete any installment
        return true;
    }
}
