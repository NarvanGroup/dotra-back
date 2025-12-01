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
        return [
            'customer_id' => Customer::factory(),
            'vendor_id' => Vendor::factory(),
            'credit_score_id' => CreditScore::factory(),
            'principal_amount' => $this->faker->numberBetween(5, 100) * 1000000,
            'down_payment_amount' => $this->faker->optional(0.5)->numberBetween(0, 2) * 1000000,
            'number_of_installments' => $this->faker->numberBetween(4, 12),
            'interest_rate' => $this->faker->randomDigit(),
            'status' => Status::CREATED_BY_VENDOR,
        ];
    }
}
