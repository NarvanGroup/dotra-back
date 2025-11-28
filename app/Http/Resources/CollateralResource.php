<?php

namespace App\Http\Resources;

use App\Enums\CollateralType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class CollateralResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'application_id' => $this->application_id,
            'vendor_id'      => $this->vendor_id,
            'customer_id'    => $this->customer_id,
            'type'           => $this->type,
            'type_label'     => $this->type instanceof CollateralType ? $this->type->getLabel() : $this->type,
            'file_path'      => $this->file_path ? Storage::disk('public')->url($this->file_path) : null,
            'description'    => $this->description,
            'created_at'     => $this->created_at?->toISOString(),
            'updated_at'     => $this->updated_at?->toISOString(),
        ];
    }
}

