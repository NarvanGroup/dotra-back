<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Vendor;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $vendor = Vendor::first();

        $payload = $vendor ? [
            'creator_type' => $vendor->getMorphClass(),
            'creator_id' => $vendor->getKey(),
        ] : [];

        Customer::factory(10)->create($payload);
       
    }
}
