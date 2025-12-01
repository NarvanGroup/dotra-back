<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\ApplicationSeeder;
use Database\Seeders\Contract\TemplateSeeder;
use Database\Seeders\CustomerSeeder;
use Database\Seeders\VendorSeeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            VendorSeeder::class,
            TemplateSeeder::class,
            CustomerSeeder::class,
        ]);
    }
}
