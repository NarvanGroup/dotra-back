<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vendor;
use Illuminate\Auth\Access\HandlesAuthorization;

class VendorPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can view any vendors.
     */
    public function viewAny(User $user): bool
    {
        // Admin users can view all vendors
        return true;
    }

    /**
     * Determine if the user can view the vendor.
     */
    public function view(User $user, Vendor $vendor): bool
    {
        // Admin users can view any vendor
        return true;
    }

    /**
     * Determine if the user can create vendors.
     */
    public function create(User $user): bool
    {
        // Admin users can create vendors
        return true;
    }

    /**
     * Determine if the user can update the vendor.
     */
    public function update(User $user, Vendor $vendor): bool
    {
        // Admin users can update any vendor
        return true;
    }

    /**
     * Determine if the user can delete the vendor.
     */
    public function delete(User $user, Vendor $vendor): bool
    {
        // Admin users can delete any vendor
        return true;
    }

    /**
     * Determine if the user can restore the vendor.
     */
    public function restore(User $user, Vendor $vendor): bool
    {
        // Admin users can restore any vendor
        return true;
    }

    /**
     * Determine if the user can permanently delete the vendor.
     */
    public function forceDelete(User $user, Vendor $vendor): bool
    {
        // Admin users can force delete any vendor
        return true;
    }
}

