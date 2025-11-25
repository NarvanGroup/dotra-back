<?php

namespace App\Models;

use App\Models\Vendor\Industry;
use App\Models\Vendor\VendorType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Vendor extends Model
{
    use HasFactory;
    use HasUuids;

    protected $primaryKey = 'uuid';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'uuid',
        'name',
        'mobile',
        'slug',
        'type',
        'reffered_from',
        'national_code',
        'business_license_code',
        'website_url',
        'industry',
        'phone_number',
        'email',
    ];

    protected $casts = [
        'type' => VendorType::class,
        'industry' => Industry::class,
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class, 'vendor_id', 'uuid');
    }

    public function initiatedCreditScores(): MorphMany
    {
        return $this->morphMany(CreditScore::class, 'initiator');
    }

    public function createdCustomers(): MorphMany
    {
        return $this->morphMany(Customer::class, 'creator');
    }

    public function appliedCustomers(?string $orderBy = null): HasManyThrough
    {
        $relation = $this->hasManyThrough(
            Customer::class,
            Application::class,
            'vendor_id', // Foreign key on applications table
            'uuid', // Foreign key on customers table (primary key)
            'uuid', // Local key on vendors table (primary key)
            'customer_id' // Local key on applications table
        );

        // Default ordering by latest application creation date
        if ($orderBy === null) {
            $relation->select('customers.*')
                ->selectRaw('MAX(applications.created_at) as latest_application_date')
                ->groupBy('customers.uuid', 'applications.vendor_id')
                ->orderByDesc('latest_application_date');
        } else {
            $relation->orderBy($orderBy);
        }

        return $relation;
    }

    public function customers(): BelongsToMany
    {
        return $this->belongsToMany(
            Customer::class,
            'customer_vendor',
            'vendor_id',
            'customer_id',
            'uuid',
            'uuid'
        )->withTimestamps();
    }
}
