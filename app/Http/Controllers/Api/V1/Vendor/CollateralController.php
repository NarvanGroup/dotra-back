<?php

namespace App\Http\Controllers\Api\V1\Vendor;

use App\Enums\CollateralType;
use App\Http\Controllers\Controller;
use App\Http\Resources\CollateralResource;
use App\Models\Application;
use App\Models\Collateral;
use App\Traits\MessageFormaterTrait;
use App\Traits\ResponderTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class CollateralController extends Controller
{
    use ResponderTrait;
    use MessageFormaterTrait;

    public function index(Request $request): JsonResponse
    {
        // Authorize that the user can view collaterals generally
        $this->authorize('viewAny', Collateral::class);

        $query = Collateral::query();

        if ($request->has('application_id')) {
            $query->where('application_id', $request->input('application_id'));
        }

        // Filter by vendor if the user is a vendor
        $user = $request->user();
        if ($user && $user instanceof \App\Models\Vendor) {
            $query->where('vendor_id', $user->id);
        }

        $collaterals = $query->latest()->paginate(15);

        return $this->paginateResponse(
            $collaterals,
            CollateralResource::collection($collaterals),
            'collaterals fetched successfully'
        );
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Collateral::class);

        $validated = $request->validate([
            'application_id' => ['required', 'exists:applications,id'],
            'vendor_id'      => ['required', 'exists:vendors,id'],
            'customer_id'    => ['required', 'exists:customers,id'],
            'type'           => ['required', Rule::enum(CollateralType::class)],
            'file'           => ['required', 'file', 'mimes:jpeg,png,pdf', 'max:10240'], // 10MB max
            'description'    => ['nullable', 'string', 'max:1000'],
        ]);

        $application = Application::findOrFail($validated['application_id']);

        // Ensure vendor owns the application and IDs match
        $user = $request->user();
        if ($user && $user instanceof \App\Models\Vendor) {
            if ($validated['vendor_id'] !== $user->id) {
                return $this->responseForbidden('Unauthorized vendor access');
            }
            if ($application->vendor_id !== $user->id) {
                return $this->responseForbidden('Unauthorized application access');
            }
        }

        // Validate that customer_id matches the application's customer
        if ($application->customer_id !== $validated['customer_id']) {
            return $this->response(
                ['customer_id' => 'Customer ID does not match the application customer.'],
                'Customer mismatch',
                422
            );
        }

        $path = $request->file('file')->store('collaterals', 'public');

        $collateral = Collateral::create([
            'application_id' => $validated['application_id'],
            'vendor_id'      => $validated['vendor_id'],
            'customer_id'    => $validated['customer_id'],
            'type'           => $validated['type'],
            'file_path'      => $path,
            'description'    => $validated['description'] ?? null,
        ]);

        return $this->responseCreated(
            CollateralResource::make($collateral),
            'collateral created successfully'
        );
    }

    public function show(Collateral $collateral): JsonResponse
    {
        $this->authorize('view', $collateral);

        return $this->responseShow(
            CollateralResource::make($collateral),
            'collateral retrieved successfully'
        );
    }

    public function destroy(Collateral $collateral): JsonResponse
    {
        $this->authorize('delete', $collateral);

        if ($collateral->file_path) {
            Storage::disk('public')->delete($collateral->file_path);
        }

        $collateral->delete();

        return $this->responseDestroyed('collateral deleted successfully');
    }
}
