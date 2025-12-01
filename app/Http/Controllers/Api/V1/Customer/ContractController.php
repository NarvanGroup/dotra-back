<?php

namespace App\Http\Controllers\Api\V1\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Customer\UpdateContractRequest;
use App\Http\Resources\ContractResource;
use App\Models\Contract;
use Illuminate\Http\JsonResponse;
use LogicException;
use Symfony\Component\HttpFoundation\Response;

class ContractController extends Controller
{
    public function show(Contract $contract): ContractResource
    {
        return ContractResource::make($contract->load('application'));
    }

    public function update(UpdateContractRequest $request, Contract $contract): ContractResource|JsonResponse
    {
        try {
            // If already signed, return without processing
            if ($contract->signed_by_customer) {
                return ContractResource::make($contract->load('application'));
            }

            // Sign the contract (this will also update application status)
            $contract->signByCustomer();
            
        } catch (LogicException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return ContractResource::make($contract->load('application'));
    }
}
