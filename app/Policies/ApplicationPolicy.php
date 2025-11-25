<?php

namespace App\Policies;

use App\Models\Application;
use App\Models\Vendor;
use Illuminate\Auth\Access\HandlesAuthorization;

class ApplicationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the vendor can view any applications.
     */
    public function viewAny(Vendor $vendor): bool
    {
        // Vendors can view their own applications
        return true;
    }

    /**
     * Determine if the vendor can view the application.
     */
    public function view(Vendor $vendor, Application $application): bool
    {
        // Vendor can only view their own applications
        return $application->vendor_id === $vendor->id;
    }

    /**
     * Determine if the vendor can create applications.
     */
    public function create(Vendor $vendor): bool
    {
        // Vendors can create applications
        return true;
    }

    /**
     * Determine if the vendor can update the application.
     */
    public function update(Vendor $vendor, Application $application): bool
    {
        // Vendor can only update their own applications
        return $application->vendor_id === $vendor->id;
    }

    /**
     * Determine if the vendor can delete the application.
     */
    public function delete(Vendor $vendor, Application $application): bool
    {
        // Vendor can only delete their own applications
        return $application->vendor_id === $vendor->id;
    }

    /**
     * Determine if the vendor can restore the application.
     */
    public function restore(Vendor $vendor, Application $application): bool
    {
        // Vendor can only restore their own applications
        return $application->vendor_id === $vendor->id;
    }

    /**
     * Determine if the vendor can permanently delete the application.
     */
    public function forceDelete(Vendor $vendor, Application $application): bool
    {
        // Vendor can only force delete their own applications
        return $application->vendor_id === $vendor->id;
    }
}
