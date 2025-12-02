<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContractResource extends JsonResource
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
            'application_id' => $this->application_id,
            'contract_template_id' => $this->contract_template_id,
            'contract_text' => $this->contract_text,
            'signed_by_customer' => $this->signed_by_customer,
            'application' => ApplicationResource::make($this->whenLoaded('application')),
            'contract_template' => $this->whenLoaded('contractTemplate'),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
