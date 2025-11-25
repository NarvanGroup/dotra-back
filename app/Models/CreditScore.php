<?php

namespace App\Models;

use App\Enums\CreditScore\CreditScoreStatus;
use App\Support\ScoringEngine\IndividualCreditScorer;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class CreditScore extends Model
{
    use HasFactory;
    use HasUuids;

    protected $primaryKey = 'uuid';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'uuid',
        'customer_id',
        'initiator_type',
        'initiator_id',
        'issued_on',
        'status',
        'overall_score',
    ];

    protected $casts = [
        'issued_on' => 'date',
        'status' => CreditScoreStatus::class,
        'overall_score' => 'integer',
    ];

    /**
     * Credit score owner.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'uuid');
    }

    /**
     * The entity that initiated the credit scoring request.
     */
    public function initiator(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Create a credit score for a customer initiated by a vendor.
     *
     * @param Customer $customer
     * @param Vendor $vendor
     * @return static
     */
    public static function createForCustomerByVendor(Customer $customer, Vendor $vendor): self
    {
        // @todo if there is a recent credit score for this customer, return it.
        // Calculate credit score
        $scorer = new IndividualCreditScorer();
        $scoreData = $scorer->calculate($customer->national_code, $customer->mobile);

        // Create credit score with vendor as initiator
        return $customer->creditScores()->create([
            'initiator_type' => Vendor::class,
            'initiator_id' => $vendor->uuid,
            'issued_on' => now(),
            'status' => CreditScoreStatus::COMPLETED,
            'overall_score' => $scoreData['final_score'],
        ]);
    }
}

