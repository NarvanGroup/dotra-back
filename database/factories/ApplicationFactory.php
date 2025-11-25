<?php

namespace Database\Factories;

use App\Models\Application;
use App\Models\Application\Status;
use App\Models\CreditScore;
use App\Models\Customer;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Application>
 */
class ApplicationFactory extends Factory
{
    protected $model = Application::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $totalAmount = $this->faker->numberBetween(100, 50000) * 1000;
        $installments = $this->faker->numberBetween(1, 4);

        return [
            'customer_id' => Customer::factory(),
            'vendor_id' => Vendor::factory(),
            'credit_score_id' => CreditScore::factory(),
            'total_amount' => $totalAmount,
            'number_of_installments' => $installments,
            'interest_rate' => $this->faker->optional(0.7)->randomFloat(2, 5, 24),
            'suggested_total_amount' => $totalAmount + $this->faker->numberBetween(500_000, 2_500_000),
            'suggested_number_of_installments' => max(3, $installments + $this->faker->numberBetween(-1, 3)),
            'suggested_interest_rate' => $this->faker->optional(0.7)->randomFloat(2, 5, 24),
            'status' => $this->faker->randomElement(Status::cases()),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Application $application): void {
            $application->creditScore()
                ->update([
                    'customer_id' => $application->customer_id,
                    'initiator_type' => Vendor::class,
                    'initiator_id' => $application->vendor_id,
                ]);
        });
    }
}
