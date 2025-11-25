<?php

namespace Database\Seeders;

use App\Models\Vendor;
use Illuminate\Database\Seeder;

class VendorSeeder extends Seeder
{
    public function run(): void
    {
        Vendor::factory()->create(['name' => 'داترا', 'slug' => 'dotra']);
        Vendor::factory(5)->create();
    }
}
