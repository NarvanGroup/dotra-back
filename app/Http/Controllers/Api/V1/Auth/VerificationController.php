<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Vendor;
use App\Services\Api\V1\ExternalApiService;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerificationController extends Controller
{
    protected ExternalApiService $apiService;

    public function __construct(ExternalApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    public function confirm(Request $request, Vendor $vendor, Customer $customer)
    {
        $belongsToVendor = $vendor->customers()
            ->whereKey($customer->getKey())
            ->exists();

        if (!$belongsToVendor) {
            abort(Response::HTTP_NOT_FOUND, 'Customer not found for this vendor.');
        }

        try {
            $token = $this->apiService->getToken();

            $trackId = $this->apiService->sendFacilitiesSms(
                $customer->mobile,
                $customer->national_code,
                $token
            );

            return response()->json([
                'track_id'      => $trackId,
                'mobile'        => $customer->mobile,
                'national_code' => $customer->national_code,
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    function process(Request $request)
    {
        $token = $this->apiService->getToken();
        $mobile = $request->input('mobile', "09190755375"); // Default for now to match original behavior if needed, or better to require it.
        $nId = $request->input('nId', "0440383943");
        $track = $request->input('track', "0440383943");
        $otp = $request->input('otp', "0440383943");
        //$facilities = $this->apiService->getFacilities($token, $track, $otp, $mobile, $nId);
        $cheque = $this->apiService->getCheque($token, $nId);

        $firstName = $cheque->json('firstName');
        $lastName = $cheque->json('lastName');
        $bouncedcheque = count($cheque->json('cheques') ?? []);

        return response()->json(compact('firstName', 'lastName', 'bouncedcheque'));

    }
}
