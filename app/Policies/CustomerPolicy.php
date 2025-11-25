<?php

namespace App\Policies;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CustomerPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can view any customers.
     */
    public function viewAny(User $user): bool
    {
        // Admin users can view all customers
        return true;
    }

    /**
     * Determine if the user can view the customer.
     */
    public function view(User $user, Customer $customer): bool
    {
        // Admin users can view any customer
        return true;
    }

    /**
     * Determine if the user can create customers.
     */
    public function create(User $user): bool
    {
        // Admin users can create customers
        return true;
    }

    /**
     * Determine if the user can update the customer.
     */
    public function update(User $user, Customer $customer): bool
    {
        // Admin users can update any customer
        return true;
    }

    /**
     * Determine if the user can delete the customer.
     */
    public function delete(User $user, Customer $customer): bool
    {
        // Admin users can delete any customer
        return true;
    }

    /**
     * Determine if the user can restore the customer.
     */
    public function restore(User $user, Customer $customer): bool
    {
        // Admin users can restore any customer
        return true;
    }

    /**
     * Determine if the user can permanently delete the customer.
     */
    public function forceDelete(User $user, Customer $customer): bool
    {
        // Admin users can force delete any customer
        return true;
    }
}
