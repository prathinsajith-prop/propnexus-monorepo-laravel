<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BixoProductDescriptionSeeder extends Seeder
{
    public function run()
    {
        DB::table('bixo_product_description')->insert([
            [
                'type' => 'Type 1',
                'category' => 'Category 1',
                'language' => 'en',
                'portal_id' => 1,
                'description' => 'Sample description 1',
                'user_id' => 1,
                'user_type' => 'admin',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null,
            ],
            [
                'type' => 'Type 2',
                'category' => 'Category 2',
                'language' => 'ar',
                'portal_id' => 2,
                'description' => 'Sample description 2',
                'user_id' => 2,
                'user_type' => 'user',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null,
            ]
        ]);
    }
}
