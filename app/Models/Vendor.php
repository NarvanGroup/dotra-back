<?php

namespace App\Models;

use App\Enums\VendorType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

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
    ];

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class, 'vendor_id', 'uuid');
    }
}
