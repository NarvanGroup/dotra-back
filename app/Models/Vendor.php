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
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Vendor extends Model
{
    use HasFactory;
    use HasUuids;
    use HasApiTokens;
    use Notifiable;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
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
        'otp',
        'otp_expires_at',
        'password',
    ];

    protected $hidden = [
        'otp',
        'otp_expires_at',
        'password',
    ];

    protected $casts = [
        'type' => VendorType::class,
        'industry' => Industry::class,
        'otp_expires_at' => 'datetime',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class, 'vendor_id', 'id');
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
            'id', // Foreign key on customers table (primary key)
            'id', // Local key on vendors table (primary key)
            'customer_id' // Local key on applications table
        );

        // Default ordering by latest application creation date
        if ($orderBy === null) {
            $relation->select('customers.*')
                ->selectRaw('MAX(applications.created_at) as latest_application_date')
                ->groupBy('customers.id', 'applications.vendor_id')
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
            'id',
            'id'
        )->withTimestamps();
    }

    /**
     * Route notifications for the SMS channel.
     */
    public function routeNotificationForSms(): string
    {
        return $this->mobile;
    }
}
