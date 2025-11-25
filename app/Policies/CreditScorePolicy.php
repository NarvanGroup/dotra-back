<?php

namespace App\Policies;

use App\Models\CreditScore;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CreditScorePolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can view any credit scores.
     */
    public function viewAny(User $user): bool
    {
        // Admin users can view all credit scores
        return true;
    }

    /**
     * Determine if the user can view the credit score.
     */
    public function view(User $user, CreditScore $creditScore): bool
    {
        // Admin users can view any credit score
        return true;
    }

    /**
     * Determine if the user can create credit scores.
     */
    public function create(User $user): bool
    {
        // Admin users can create credit scores
        return true;
    }

    /**
     * Determine if the user can update the credit score.
     */
    public function update(User $user, CreditScore $creditScore): bool
    {
        // Admin users can update any credit score
        return true;
    }

    /**
     * Determine if the user can delete the credit score.
     */
    public function delete(User $user, CreditScore $creditScore): bool
    {
        // Admin users can delete any credit score
        return true;
    }

    /**
     * Determine if the user can restore the credit score.
     */
    public function restore(User $user, CreditScore $creditScore): bool
    {
        // Admin users can restore any credit score
        return true;
    }

    /**
     * Determine if the user can permanently delete the credit score.
     */
    public function forceDelete(User $user, CreditScore $creditScore): bool
    {
        // Admin users can force delete any credit score
        return true;
    }
}
