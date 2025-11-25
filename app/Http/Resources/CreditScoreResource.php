<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CreditScoreResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'customer_id' => $this->customer_id,
            'issued_on' => $this->issued_on?->toISOString(),
            'status' => $this->status?->value ?? $this->status,
            'overall_score' => $this->overall_score,
            'customer' => CustomerResource::make($this->whenLoaded('customer')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}

