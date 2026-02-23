<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BixoProductPortalsSeeder extends Seeder
{
    public function run()
    {
        DB::table('bixo_product_portals')->insert([
            [
                'name' => 'Portal A',
                'description' => 'Description for Portal A',
                'email' => 'portalA@example.com',
                'slug' => 'portal-a',
                'status' => 'Enabled',
                'sort_order' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Portal B',
                'description' => 'Description for Portal B',
                'email' => 'portalB@example.com',
                'slug' => 'portal-b',
                'status' => 'Enabled',
                'sort_order' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
