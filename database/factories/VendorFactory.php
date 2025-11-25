<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Vendor;
use App\Models\Vendor\Industry;
use App\Models\Vendor\VendorType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Vendor>
 */
class VendorFactory extends Factory
{
    protected $model = Vendor::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->company();

        return [
            'name' => $name,
            'mobile' => $this->faker->unique()->numerify('09#########'),
            'slug' => Str::slug($name . '-' . $this->faker->unique()->lexify('????')),
            'type' => $this->faker->randomElement(VendorType::values()),
            'reffered_from' => $this->faker->optional()->word(),
            'national_code' => $this->faker->numerify('##########'),
            'business_license_code' => $this->faker->optional()->numerify('##########'),
            'website_url' => $this->faker->optional()->url(),
            'industry' => $this->faker->randomElement(Industry::cases()),
            'phone_number' => $this->faker->optional()->phoneNumber(),
            'email' => $this->faker->optional()->companyEmail(),
        ];
    }
}
