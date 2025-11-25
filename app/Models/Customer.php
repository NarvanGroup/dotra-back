<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Customer extends Model
{
    use HasFactory;
    use HasUuids;

    protected $primaryKey = 'uuid';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'uuid',
        'national_code',
        'mobile',
        'first_name',
        'last_name',
        'birth_date',
        'email',
        'address',
        'creator_type',
        'creator_id',
    ];

    protected $casts = [
        'birth_date' => 'datetime',
    ];

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class, 'customer_id', 'uuid');
    }

    public function creditScores(): HasMany
    {
        return $this->hasMany(CreditScore::class, 'customer_id', 'uuid');
    }

    public function initiatedCreditScores(): MorphMany
    {
        return $this->morphMany(CreditScore::class, 'initiator');
    }

    public function creator(): MorphTo
    {
        return $this->morphTo();
    }

    public function vendors(): BelongsToMany
    {
        return $this->belongsToMany(
            Vendor::class,
            'customer_vendor',
            'customer_id',
            'vendor_id',
            'uuid',
            'uuid'
        )->withTimestamps();;
    }

    /**
     * Find an existing customer by national code or create a new one for the vendor.
     * 
     * @param Vendor $vendor
     * @param array $payload
     * @return array{0: Customer, 1: bool} Returns the customer and whether it was newly created
     */
    public static function findOrCreateForVendor(Vendor $vendor, array $payload): array
    {
        $customer = self::where('national_code', $payload['national_code'])->first();

        if ($customer) {
            $customer->vendors()->syncWithoutDetaching([$vendor->uuid]);
            
            return [$customer, false];
        }

        // @todo fetch customer data from external API instead of user input
        $customer = $vendor->createdCustomers()->create($payload);
        $customer->vendors()->syncWithoutDetaching([$vendor->uuid]);

        return [$customer, true];
    }
}
