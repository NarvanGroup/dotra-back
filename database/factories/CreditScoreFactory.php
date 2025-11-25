<?php

namespace Database\Factories;

use App\Enums\CreditScore\CreditScoreStatus;
use App\Models\CreditScore;
use App\Models\Customer;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CreditScore>
 */
class CreditScoreFactory extends Factory
{
    protected $model = CreditScore::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'initiator_type' => Vendor::class,
            'initiator_id' => Vendor::factory(),
            'issued_on' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'status' => CreditScoreStatus::COMPLETED,
            'overall_score' => $this->faker->numberBetween(300, 850),
        ];
    }
}

