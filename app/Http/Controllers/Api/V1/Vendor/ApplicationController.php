<?php

namespace App\Http\Controllers\Api\V1\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Vendor\StoreApplicationRequest;
use App\Http\Requests\Api\V1\Vendor\UpdateApplicationRequest;
use App\Http\Resources\ApplicationResource;
use App\Models\Application;
use App\Models\Customer;
use App\Models\Vendor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class ApplicationController extends Controller
{
    public function index(Request $request, Vendor $vendor, Customer $customer): AnonymousResourceCollection
    {
        $applications = $vendor->applications()
            ->with(['customer', 'creditScore'])
            ->latest()
            ->paginate(perPage: $request->integer('per_page', 15));

        return ApplicationResource::collection($applications);
    }

    public function store(StoreApplicationRequest $request, Vendor $vendor): JsonResponse
    {
        $application = Application::createByVendor($vendor, $request->validated());

        return ApplicationResource::make(
            $application->load(['customer', 'vendor', 'creditScore'])
        )
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function edit(Vendor $vendor, Application $application): ApplicationResource
    {
        return ApplicationResource::make($application->load(['customer', 'vendor', 'installments', 'creditScore']));
    }

    public function update(UpdateApplicationRequest $request, Vendor $vendor, Application $application): ApplicationResource
    {
        DB::transaction(function () use ($request, $application) {
            $application->update($request->validated());
        });

        return ApplicationResource::make($application->load(['customer', 'vendor', 'installments', 'creditScore']));
    }
}
