<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Application extends Model
{
    use HasFactory;
    use HasUuids;

    protected $primaryKey = 'uuid';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'uuid',
        'customer_id',
        'vendor_id',
        'total_amount',
        'number_of_installments',
        'interest_rate',
        'suggested_total_amount',
        'suggested_number_of_installments',
        'suggested_interest_rate',
        'status',
    ];

    protected $casts = [
        'interest_rate' => 'decimal:2',
        'suggested_interest_rate' => 'decimal:2',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'uuid');
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'vendor_id', 'uuid');
    }

    public function installments(): HasMany
    {
        return $this->hasMany(Installment::class, 'application_id', 'uuid');
    }
}
