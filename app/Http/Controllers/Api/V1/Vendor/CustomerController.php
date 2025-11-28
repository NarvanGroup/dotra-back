<?php

namespace App\Http\Controllers\Api\V1\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Vendor\StoreCustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use App\Models\Vendor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

class CustomerController extends Controller
{
    public function index(Request $request, Vendor $vendor): AnonymousResourceCollection
    {
        $customers = $vendor->customers()
            ->paginate($request->integer('per_page', 15));

        return CustomerResource::collection($customers);
    }

    public function store(StoreCustomerRequest $request, Vendor $vendor): JsonResponse
    {
        // @todo get customer's consent via otp before allowing vendor to access their data.
        [$customer, $wasCreated] = Customer::findOrCreateForVendor(
            $vendor,
            $request->validated()
        );

        return CustomerResource::make($customer)
            ->response()
            ->setStatusCode($wasCreated ? Response::HTTP_CREATED : Response::HTTP_OK);
    }

    public function show(Vendor $vendor, Customer $customer): CustomerResource
    {   
        return CustomerResource::make($customer);
    }


}
