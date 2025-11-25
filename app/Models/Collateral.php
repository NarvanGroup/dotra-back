<?php

namespace App\Models;

use App\Enums\CollateralType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Collateral extends Model
{
    use HasFactory;
    use HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'application_id',
        'vendor_id',
        'customer_id',
        'type',
        'file_path',
        'description',
    ];

    protected $casts = [
        'type' => CollateralType::class,
    ];

    protected static function booted(): void
    {
        static::creating(function (self $collateral): void {
            if ($collateral->application && blank($collateral->vendor_id)) {
                $collateral->vendor_id = $collateral->application->vendor_id;
            }
            if ($collateral->application && blank($collateral->customer_id)) {
                $collateral->customer_id = $collateral->application->customer_id;
            }
        });
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
