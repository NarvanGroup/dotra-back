<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Customer>
 */
class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'national_code' => $this->faker->unique()->numerify('##########'),
            'mobile' => $this->faker->unique()->numerify('09#########'),
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'birth_date' => $this->faker->dateTimeBetween('-65 years', '-18 years'),
            'email' => $this->faker->optional()->safeEmail(),
            'address' => $this->faker->optional()->address(),
            'creator_type' => Vendor::class,
            'creator_id' => Vendor::factory(),
        ];
    }
}
