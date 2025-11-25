<?php

namespace App\Models;

use App\Models\Application\Status;
use App\Models\Vendor\Industry;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use LogicException;

/**
 * @property float|null $suggested_interest_rate
 */
class Application extends Model
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'customer_id',
        'vendor_id',
        'credit_score_id',
        'total_amount',
        'number_of_installments',
        'interest_rate',
        'suggested_total_amount',
        'suggested_number_of_installments',
        'suggested_interest_rate',
        'status',
    ];

    protected $casts = [
        'industry' => Industry::class,
        'interest_rate' => 'decimal:2',
        'suggested_interest_rate' => 'decimal:2',
        'status' => Status::class,
    ];

    public function getRouteKeyName(): string
    {
        return 'id';
    }

    protected static function booted(): void
    {
        static::creating(function (self $application): void {
            $application->initializeSuggestedTerms();
            
            if (blank($application->status)) {
                $application->status = Status::TERMS_SUGGESTED;
            }
        });

        static::updating(function (self $application): void {
            if ($application->isDirty('credit_score_id')) {
                throw new LogicException('Credit score of an application cannot be updated.');
            }

            if (
                $application->isDirty([
                    'suggested_total_amount',
                    'suggested_number_of_installments',
                    'suggested_interest_rate',
                ])
            ) {
                throw new LogicException('Suggested terms are immutable after creation.');
            }
        });
    }

    public function initializeSuggestedTerms(): void
    {
        if (blank($this->suggested_total_amount)) {
            $baseAmount = $this->total_amount ?? random_int(100_000, 5_000_000);
            $this->suggested_total_amount = max(
                1,
                $baseAmount + random_int(-2_500_000, 2_500_000)
            );
        }

        if (blank($this->suggested_number_of_installments)) {
            $baseInstallments = $this->number_of_installments ?? random_int(1, 24);
            $this->suggested_number_of_installments = max(
                1,
                $baseInstallments + random_int(-1, 3)
            );
        }

        if (blank($this->suggested_interest_rate)) {
            /** @phpstan-ignore-next-line */
            $this->suggested_interest_rate = sprintf('%.2f', random_int(500, 2400) / 100);
        }
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'vendor_id', 'id');
    }

    public function installments(): HasMany
    {
        return $this->hasMany(Installment::class, 'application_id', 'id');
    }

    public function creditScore(): BelongsTo
    {
        return $this->belongsTo(CreditScore::class, 'credit_score_id', 'id');
    }
}
