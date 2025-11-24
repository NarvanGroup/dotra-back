<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
    ];

    protected $casts = [
        'birth_date' => 'datetime',
    ];

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class, 'customer_id', 'uuid');
    }
}
