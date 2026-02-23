<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BixoProductPropertiesSeeder extends Seeder
{
    public function run()
    {
        // Insert the static example
        DB::table('bixo_product_properties')->insert([
            'organization_id' => 2,
            'branch_id' => 102,
            'department_id' => 206,
            'division_id' => 507,
            'region_id' => 504,
            'location_id' => 1128,
            'sublocation_id' => 7275,
            'building_id' => 4147,
            'category_type' => 'Residential',
            'category' => 'Bulk Units',
            'property_for' => 'Sales',
            'property_type' => 'Live',
            'status' => 'Published',
            'title' => 'testing',
            'ref' => 'MCS-11501',
            'unit' => '5678',
            'floor' => 'Mid floor',
            'beds' => '3',
            'baths' => 3,
            'parking' => 1,
            'bua' => 55.00,
            'price' => 55000,
            'description' => '<p>abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyza bcdefghijklmnopqrstuvwxyz&nbsp;</p>',
            'furnishing' => 'Unfurnished',
            'views' => '["Back to Back"]',
            'plot' => '55',
            'frequency' => 'yearly',
            'created_by' => 1,
            'company_listing' => 1,
            'photos' => '[]',
            'documents' => '[]',
            'public_documents' => '[]',
            'upload_folder' => '2025/09/17/101428412',
            'feature_tags' => '[ "Company Inventory", "Hot", "Pf Premium", "Pf Featured", "Pf Verified", "Byt Hot", "Byt True Check", "Byt Signature", "Dbz Premium", "Dbz Featured", "Dbz Verified", "Checked" ]',
            'watermark' => 1,
            'lead_auto_assign' => 1,
            'created_at' => Carbon::parse('2025-09-17 02:05:46'),
            'updated_at' => Carbon::parse('2025-09-17 02:14:28'),
            'temp_sync_at' => Carbon::parse('2025-06-11 10:10:10'),
        ]);

        // Generate additional fake records using the factory
        \App\Models\BixoProductProperties::factory()->count(10)->create();
    }
}
