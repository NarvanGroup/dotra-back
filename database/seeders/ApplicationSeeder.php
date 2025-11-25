<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\Customer;
use App\Models\Vendor;
use Illuminate\Database\Seeder;

class ApplicationSeeder extends Seeder
{
    public function run(): void
    {
        $vendors = Vendor::all();
        if ($vendors->isEmpty()) {
            $vendors = Vendor::factory(3)->create();
        }

        $customers = Customer::all();
        if ($customers->isEmpty()) {
            $customers = Customer::factory(10)->create();
        }

        foreach ($vendors as $vendor) {
            Application::factory()
                ->count(3)
                ->recycle($customers)
                ->for($vendor, 'vendor')
                ->create();
        }
    }
}
