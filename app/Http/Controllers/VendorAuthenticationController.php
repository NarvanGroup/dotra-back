<?php

namespace App\Http\Controllers;

use App\Http\Resources\VendorResource;
use App\Models\Vendor;

class VendorAuthenticationController extends BaseAuthenticationController
{
    protected function getModelClass(): string
    {
        return Vendor::class;
    }

    protected function getResourceClass(): string
    {
        return VendorResource::class;
    }

    protected function getUserTypeLabel(): string
    {
        return 'vendor';
    }
}
