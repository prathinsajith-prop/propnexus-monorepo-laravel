<?php

namespace Database\Seeders;

use App\Models\Blog;
use Illuminate\Database\Seeder;

class BlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 50 blog posts with various states
        Blog::factory()->count(30)->published()->create();
        Blog::factory()->count(10)->draft()->create();
        Blog::factory()->count(5)->featured()->published()->create();
        Blog::factory()->count(5)->create(['status' => 'review']);
    }
}
