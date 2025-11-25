<?php

namespace App\Policies;

use App\Models\Installment;
use App\Models\Vendor;
use Illuminate\Auth\Access\HandlesAuthorization;

class InstallmentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the vendor can view any installments.
     */
    public function viewAny(Vendor $vendor): bool
    {
        // Vendors can view installments
        return true;
    }

    /**
     * Determine if the vendor can view the installment.
     */
    public function view(Vendor $vendor, Installment $installment): bool
    {
        // Vendor can view installment if it belongs to their application
        return $installment->application->vendor_id === $vendor->id;
    }

    /**
     * Determine if the vendor can create installments.
     */
    public function create(Vendor $vendor): bool
    {
        // Vendors can create installments for their applications
        return true;
    }

    /**
     * Determine if the vendor can update the installment.
     */
    public function update(Vendor $vendor, Installment $installment): bool
    {
        // Vendor can update installment if it belongs to their application
        return $installment->application->vendor_id === $vendor->id;
    }

    /**
     * Determine if the vendor can delete the installment.
     */
    public function delete(Vendor $vendor, Installment $installment): bool
    {
        // Vendor can delete installment if it belongs to their application
        return $installment->application->vendor_id === $vendor->id;
    }

    /**
     * Determine if the vendor can restore the installment.
     */
    public function restore(Vendor $vendor, Installment $installment): bool
    {
        // Vendor can restore installment if it belongs to their application
        return $installment->application->vendor_id === $vendor->id;
    }

    /**
     * Determine if the vendor can permanently delete the installment.
     */
    public function forceDelete(Vendor $vendor, Installment $installment): bool
    {
        // Vendor can force delete installment if it belongs to their application
        return $installment->application->vendor_id === $vendor->id;
    }
}
