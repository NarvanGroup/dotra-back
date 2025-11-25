<?php

namespace App\Policies;

use App\Models\Application;
use App\Models\Vendor;

class ApplicationPolicy
{
    /**
     * Determine if the given vendor can view the application.
     */
    public function view(Vendor $vendor, Application $application): bool
    {
        return $application->vendor_id === $vendor->uuid;
    }

    /**
     * Determine if the given vendor can update the application.
     */
    public function update(Vendor $vendor, Application $application): bool
    {
        return $application->vendor_id === $vendor->uuid;
    }

    /**
     * Determine if the given vendor can delete the application.
     */
    public function delete(Vendor $vendor, Application $application): bool
    {
        return $application->vendor_id === $vendor->uuid;
    }
}
