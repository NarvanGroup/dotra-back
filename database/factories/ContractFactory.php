<?php

namespace Database\Factories;

use App\Models\Application;
use App\Models\Contract;
use App\Models\Contract\Template;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Contract>
 */
class ContractFactory extends Factory
{
    protected $model = Contract::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'application_id' => Application::factory(),
            'contract_template_id' => Template::factory(),
            'contract_text' => $this->faker->optional(0.6)->paragraphs(3, true),
            'signed_by_customer' => false,
        ];
    }
}
