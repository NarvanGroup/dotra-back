<?php

namespace App\Http\Controllers;

use App\Http\Resources\CustomerResource;
use App\Models\Customer;

class CustomerAuthenticationController extends BaseAuthenticationController
{
    protected function getModelClass(): string
    {
        return Customer::class;
    }

    protected function getResourceClass(): string
    {
        return CustomerResource::class;
    }

    protected function getUserTypeLabel(): string
    {
        return 'customer';
    }
}
