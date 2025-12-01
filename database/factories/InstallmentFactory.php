<?php

namespace Database\Factories;

use App\Models\Application;
use App\Models\Installment;
use App\Models\Installment\Status;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Installment>
 */
class InstallmentFactory extends Factory
{
    protected $model = Installment::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $dueDate = $this->faker->dateTimeBetween('now', '+2 years');
        $status = $this->faker->randomElement(Status::cases());
        $paidAt = null;

        // If status is PAID, set paid_at to a date before or on due_date
        if ($status === Status::PAID) {
            $paidAt = $this->faker->dateTimeBetween('-1 month', $dueDate);
        }

        return [
            'application_id' => Application::factory(),
            'installment_number' => $this->faker->numberBetween(1, 24),
            'amount' => $this->faker->numberBetween(100_000, 5_000_000),
            'due_date' => $dueDate,
            'paid_at' => $paidAt,
            'status' => $status,
        ];
    }
}

