<?php

namespace App\Policies;

use App\Models\Collateral;
use App\Models\User;
use App\Models\Vendor;

class CollateralPolicy
{
    /**
     * Perform pre-authorization checks.
     */
    public function before($user, $ability): ?bool
    {
        if ($user instanceof User) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny($user): bool
    {
        if ($user instanceof Vendor) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view($user, Collateral $collateral): bool
    {
        if ($user instanceof Vendor) {
            return $collateral->vendor_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create($user): bool
    {
        if ($user instanceof Vendor) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update($user, Collateral $collateral): bool
    {
        if ($user instanceof Vendor) {
            return $collateral->vendor_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete($user, Collateral $collateral): bool
    {
        if ($user instanceof Vendor) {
            return $collateral->vendor_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore($user, Collateral $collateral): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete($user, Collateral $collateral): bool
    {
        return false;
    }
}
