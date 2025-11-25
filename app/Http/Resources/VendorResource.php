<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VendorResource extends JsonResource
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
            'name' => $this->name,
            'mobile' => $this->mobile,
            'slug' => $this->slug,
            'type' => $this->type?->value ?? $this->type,
            'reffered_from' => $this->reffered_from,
            'national_code' => $this->national_code,
            'business_license_code' => $this->business_license_code,
            'website_url' => $this->website_url,
            'industry' => $this->industry,
            'phone_number' => $this->phone_number,
            'email' => $this->email,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
