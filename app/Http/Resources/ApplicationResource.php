<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'customer_id' => $this->customer_id,
            'vendor_id' => $this->vendor_id,
            'credit_score_id' => $this->credit_score_id,
            'principal_amount' => $this->principal_amount,
            'down_payment_amount' => $this->down_payment_amount,
            'total_payable_amount' => $this->total_payable_amount,
            'number_of_installments' => $this->number_of_installments,
            'interest_rate' => $this->interest_rate,
            'suggested_total_amount' => $this->suggested_total_amount,
            'suggested_number_of_installments' => $this->suggested_number_of_installments,
            'suggested_interest_rate' => $this->suggested_interest_rate,
            'status' => $this->status?->value ?? $this->status,
            'vendor' => VendorResource::make($this->whenLoaded('vendor')),
            'customer' => CustomerResource::make($this->whenLoaded('customer')),
            'installments' => InstallmentResource::collection($this->whenLoaded('installments')),
            'credit_score' => CreditScoreResource::make($this->whenLoaded('creditScore')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
