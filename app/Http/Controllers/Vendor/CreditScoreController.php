<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Resources\CreditScoreResource;
use App\Models\CreditScore;
use App\Models\Customer;
use App\Models\Vendor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CreditScoreController extends Controller
{
    public function store(Request $request, Vendor $vendor, Customer $customer): JsonResponse
    {
        $creditScore = CreditScore::createForCustomerByVendor($customer, $vendor);

        return CreditScoreResource::make($creditScore)
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Vendor $vendor, Customer $customer): CreditScoreResource
    {
        $creditScore = $customer->creditScores()
            ->latest('issued_on')
            ->firstOrFail();

        return CreditScoreResource::make($creditScore);
    }
}

