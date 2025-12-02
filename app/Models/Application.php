<?php

namespace App\Models;

use App\Models\Application\Status;
use App\Models\Contract\Template;
use App\Models\Installment\Status as InstallmentStatus;
use App\Models\Vendor\Industry;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use LogicException;
use DomainException;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

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
        'principal_amount',
        'down_payment_amount',
        'total_payable_amount',
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

            // Calculate total_payable_amount if principal_amount is set
            if ($application->principal_amount !== null) {
                $application->total_payable_amount = $application->calculateTotalPayableAmount();
            }
        });

        static::created(function (self $application): void {
            $application->contract()->create();
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

            $currentStatus = $application->getOriginal('status');
            if ($currentStatus !== null) {
                $status = Status::fromMixed($currentStatus);
                $isEditableStatus = in_array($status, [Status::CREATED_BY_VENDOR, Status::CREATED_BY_CUSTOMER], true);

                $numericFields = [
                    'number_of_installments',
                    'interest_rate',
                    'principal_amount',
                    'down_payment_amount',
                ];

                if (!$isEditableStatus && $application->isDirty($numericFields)) {
                    throw new LogicException('Numeric fields (number_of_installments, interest_rate, principal_amount, down_payment_amount) cannot be changed when status is not "created by vendor" or "created by customer".');
                }
            }

            // Recalculate total_payable_amount when numeric values change
            $numericFields = [
                'principal_amount',
                'down_payment_amount',
                'interest_rate',
            ];

            if ($application->isDirty($numericFields)) {
                $application->total_payable_amount = $application->calculateTotalPayableAmount();
            }
        });

        static::updated(function (self $application): void {
            // Check if status changed to VENDOR_CONFIRMED
            if ($application->isDirty('status')) {
                $newStatus = $application->status;
                $oldStatus = $application->getOriginal('status');


                if ($oldStatus !== Status::VENDOR_CONFIRMED->value && $newStatus === Status::VENDOR_CONFIRMED) {
                    $application->handleVendorConfirmed();
                }
            }
        });
    }

    public function initializeSuggestedTerms(): void
    {
        if (blank($this->suggested_total_amount)) {
            $baseAmount = $this->principal_amount ?? $this->total_payable_amount ?? random_int(100_000, 5_000_000);
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

    /**
     * Calculate the total payable amount based on principal amount, down payment, and interest rate.
     * Formula: (principal_amount - down_payment_amount) * (1 + interest_rate / 100)
     */
    public function calculateTotalPayableAmount(): ?int
    {
        $principalAmount = $this->principal_amount ?? 0;
        $downPaymentAmount = $this->down_payment_amount ?? 0;
        $interestRate = $this->interest_rate ?? 0;

        // If principal_amount is not set, return null
        if ($principalAmount === 0) {
            return null;
        }

        // Calculate loan amount after down payment
        $loanAmount = $principalAmount - $downPaymentAmount;

        // If loan amount is negative or zero, return null
        if ($loanAmount <= 0) {
            return null;
        }

        // Calculate total payable amount with interest
        $interestRateDecimal = (float) $interestRate / 100;
        $totalPayableAmount = (int) ($loanAmount * (1 + $interestRateDecimal));

        return $totalPayableAmount;
    }

    public function setStatusAttribute($value): void
    {
        $to = Status::fromMixed($value);
        $current = $this->getAttribute('status');

        if ($current instanceof Status && ! $current->canTransitionTo($to)) {
            throw new DomainException(
                "Invalid status transition from {$current->value} to {$to->value}"
            );
        }

        $this->attributes['status'] = $to->value;
    }

    public function transitionTo(Status $to): self
    {
        $this->status = $to;
        $this->save();

        return $this;
    }

    public function markVendorConfirmed(): self
    {
        return $this->transitionTo(Status::VENDOR_CONFIRMED);
    }

    public function markCustomerConfirmed(): self
    {
        return $this->transitionTo(Status::CUSTOMER_CONFIRMED);
    }

    public function markApproved(): self
    {
        return $this->transitionTo(Status::APPROVED);
    }

    public function markInRepayment(): self
    {
        return $this->transitionTo(Status::IN_REPAYMENT);
    }

    public function markOverdue(): self
    {
        return $this->transitionTo(Status::OVERDUE);
    }

    public function markRepaid(): self
    {
        return $this->transitionTo(Status::REPAID);
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

    public function collaterals(): HasMany
    {
        return $this->hasMany(Collateral::class, 'application_id', 'id');
    }

    public function contract(): HasOne
    {
        return $this->hasOne(Contract::class, 'application_id', 'id');
    }

    public static function createByVendor(Vendor $vendor, array $data): self
    {

        $application = $vendor->applications()->create([
            'status' => Status::CREATED_BY_VENDOR->value,
            ...$data,
        ]);

        return $application;
    }

    public function confirmByCustomer(): self
    {
        $this->transitionTo(Status::CUSTOMER_CONFIRMED);
        $this->createInstallments();

        return $this;
    }

    /**
     * Create installments based on total_payable_amount and number_of_installments.
     */
    public function createInstallments(): void
    {
        // Prevent creating installments if they already exist
        if ($this->installments()->exists()) {
            return;
        }

        // Validate required fields
        if (blank($this->total_payable_amount) || blank($this->number_of_installments)) {
            throw new LogicException('Cannot create installments: total_payable_amount and number_of_installments are required.');
        }

        // Use total_payable_amount (already includes interest)
        $totalAmountWithInterest = $this->total_payable_amount;

        // Calculate installment amount (divide evenly)
        $installmentAmount = (int) ($totalAmountWithInterest / $this->number_of_installments);
        $remainder = $totalAmountWithInterest % $this->number_of_installments;

        // Start date for installments (first installment due in 1 month from now)
        $startDate = Carbon::now()->addMonth();

        $installments = [];
        for ($i = 1; $i <= $this->number_of_installments; $i++) {
            // Add remainder to the last installment to account for rounding
            $amount = $installmentAmount;
            if ($i === $this->number_of_installments) {
                $amount += $remainder;
            }

            $installments[] = [
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'application_id' => $this->id,
                'installment_number' => $i,
                'amount' => $amount,
                'due_date' => $startDate->copy()->addMonths($i - 1),
                'status' => InstallmentStatus::PENDING->value,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        $this->installments()->insert($installments);
    }

    /**
     * Handle vendor confirmed status: generate contract and send SMS.
     */
    public function handleVendorConfirmed(): void
    {
        // Get the first contract template (or create a default one if none exists)
        $contractTemplate = Template::first();

        if (!$contractTemplate) {
            // If no template exists, we can't generate a contract
            // Log this or handle it appropriately
            Log::warning("No contract template found for application {$this->id}");
            return;
        }

        // Load necessary relationships
        $this->load(['customer', 'vendor', 'contract']);

        // Prepare attributes for contract template
        $attributes = [
            'vendor_name' => $this->vendor->name,
            'customer_name' => $this->customer->first_name . ' ' . $this->customer->last_name,
            'total_payable_amount' => number_format($this->total_payable_amount ?? $this->principal_amount ?? 0),
            'number_of_installments' => $this->number_of_installments ?? 0,
            'interest_rate' => number_format($this->interest_rate ?? 0, 2),
        ];

        // Generate contract text from template
        $contractText = $contractTemplate->fillAttributes($attributes);

        // Update contract with template and text
        $contract = $this->contract;
        if ($contract) {
            $contract->update([
                'contract_template_id' => $contractTemplate->id,
                'contract_text' => $contractText,
            ]);

            // Send SMS notification to customer
            $this->customer->notify(
                new \App\Notifications\ContractReadyNotification($contract, $this->vendor)
            );
        }
    }
}
