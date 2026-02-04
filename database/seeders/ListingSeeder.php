<?php

namespace Database\Seeders;

use App\Models\Listing;
use Illuminate\Database\Seeder;

class ListingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 50 listings with various types and statuses
        Listing::factory()->count(20)->active()->create();
        Listing::factory()->count(10)->featured()->active()->create();
        Listing::factory()->count(5)->forSale()->residential()->create();
        Listing::factory()->count(5)->forRent()->residential()->create();
        Listing::factory()->count(5)->commercial()->create();
        Listing::factory()->count(5)->state(['status' => 'sold'])->create();
    }
}
