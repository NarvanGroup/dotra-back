<?php

namespace App\Policies;

use App\Models\CreditScore;
use App\Models\Vendor;
use Illuminate\Auth\Access\HandlesAuthorization;

class CreditScorePolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the vendor can view any credit scores.
     */
    public function viewAny(Vendor $vendor): bool
    {
        // Vendors can view credit scores
        return true;
    }

    /**
     * Determine if the vendor can view the credit score.
     */
    public function view(Vendor $vendor, CreditScore $creditScore): bool
    {
        // Vendor can view credit score if:
        // 1. They initiated it
        // 2. They have an application with this customer
        if ($creditScore->initiator_type === Vendor::class && $creditScore->initiator_id === $vendor->id) {
            return true;
        }

        return $vendor->applications()
            ->where('customer_id', $creditScore->customer_id)
            ->exists();
    }

    /**
     * Determine if the vendor can create credit scores.
     */
    public function create(Vendor $vendor): bool
    {
        // Vendors can create credit scores for their customers
        return true;
    }

    /**
     * Determine if the vendor can update the credit score.
     */
    public function update(Vendor $vendor, CreditScore $creditScore): bool
    {
        // Only the initiator can update the credit score
        return $creditScore->initiator_type === Vendor::class 
            && $creditScore->initiator_id === $vendor->id;
    }

    /**
     * Determine if the vendor can delete the credit score.
     */
    public function delete(Vendor $vendor, CreditScore $creditScore): bool
    {
        // Vendors cannot delete credit scores
        return false;
    }

    /**
     * Determine if the vendor can restore the credit score.
     */
    public function restore(Vendor $vendor, CreditScore $creditScore): bool
    {
        return false;
    }

    /**
     * Determine if the vendor can permanently delete the credit score.
     */
    public function forceDelete(Vendor $vendor, CreditScore $creditScore): bool
    {
        return false;
    }
}
