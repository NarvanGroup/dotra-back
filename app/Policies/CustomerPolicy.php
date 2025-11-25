<?php

namespace App\Policies;

use App\Models\Customer;
use App\Models\Vendor;
use Illuminate\Auth\Access\HandlesAuthorization;

class CustomerPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the vendor can view any customers.
     */
    public function viewAny(Vendor $vendor): bool
    {
        // Vendors can view their own customers
        return true;
    }

    /**
     * Determine if the vendor can view the customer.
     */
    public function view(Vendor $vendor, Customer $customer): bool
    {
        // Vendor can view customer if they have an application together
        // or if the vendor created this customer
        return $vendor->applications()
            ->where('customer_id', $customer->uuid)
            ->exists()
            || ($customer->creator_type === Vendor::class && $customer->creator_id === $vendor->id);
    }

    /**
     * Determine if the vendor can create customers.
     */
    public function create(Vendor $vendor): bool
    {
        // Vendors can create customers
        return true;
    }

    /**
     * Determine if the vendor can update the customer.
     */
    public function update(Vendor $vendor, Customer $customer): bool
    {
        // Vendor can update customer only if they created them
        return $customer->creator_type === Vendor::class 
            && $customer->creator_id === $vendor->id;
    }

    /**
     * Determine if the vendor can delete the customer.
     */
    public function delete(Vendor $vendor, Customer $customer): bool
    {
        // Vendors cannot delete customers
        return false;
    }

    /**
     * Determine if the vendor can restore the customer.
     */
    public function restore(Vendor $vendor, Customer $customer): bool
    {
        return false;
    }

    /**
     * Determine if the vendor can permanently delete the customer.
     */
    public function forceDelete(Vendor $vendor, Customer $customer): bool
    {
        return false;
    }
}
