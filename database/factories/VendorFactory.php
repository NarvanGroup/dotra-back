<?php

namespace Database\Factories;

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
        $type = $this->faker->randomElement(VendorType::cases());
        $isIndividual = $type === VendorType::INDIVIDUAL;

        $definition = [
            'name' => $name = $this->faker->company,
            'mobile' => $this->faker->unique()->numerify('09#########'),
            'slug' => Str::slug($name) . '-' . $this->faker->unique()->lexify('????'),
            'type' => $type->value,
            'reffered_from' => $this->faker->optional()->word(),
            'national_code' => $this->faker->unique()->numerify('##########'),
            'website_url' => $this->faker->optional()->url(),
            'industry' => $this->faker->randomElement(Industry::cases()),
            'phone_number' => $this->faker->optional()->numerify('0##########'),
            'email' => $this->faker->optional()->companyEmail(),
        ];

        // Add owner fields for individual vendors
        if ($isIndividual) {
            $definition['owner_first_name'] = $this->faker->firstName;
            $definition['owner_last_name'] = $this->faker->lastName;
            $definition['owner_birth_date'] = $this->faker->dateTimeBetween('-65 years', '-25 years');
            $definition['business_license_code'] = $this->faker->unique()->numerify('##########');
        }

        return $definition;
    }
}
