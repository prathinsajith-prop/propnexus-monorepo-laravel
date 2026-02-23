<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BixoProductPropertiesFactory extends Factory
{
    protected $model = \App\Models\BixoProductProperties::class;

    public function definition()
    {
        return [
            'organization_id' => $this->faker->numberBetween(1, 10),
            'branch_id' => $this->faker->numberBetween(100, 200),
            'department_id' => $this->faker->numberBetween(200, 300),
            'division_id' => $this->faker->numberBetween(300, 600),
            'region_id' => $this->faker->numberBetween(1, 600),
            'location_id' => $this->faker->numberBetween(1000, 2000),
            'sublocation_id' => $this->faker->numberBetween(2000, 8000),
            'building_id' => $this->faker->numberBetween(4000, 5000),
            'category_type' => $this->faker->randomElement(['Commercial', 'Residential']),
            'category' => $this->faker->word,
            'property_for' => $this->faker->randomElement(['Rental', 'Sales']),
            'property_type' => $this->faker->randomElement(['Live', 'Pocket', 'Developer', 'Verified Pocket']),
            'status' => $this->faker->randomElement(['Draft', 'Pending', 'Waiting Publish', 'Published', 'Waiting Unpublish', 'Unpublished', 'Archived', 'Approved', 'Rejected', 'Pocket Publish', 'Pending Verification', 'Verified', 'Completed', 'Public', 'Private', 'Junk', 'Waiting Teamleader', 'Waiting Team Leader']),
            'title' => $this->faker->sentence(3),
            'ref' => 'MCS-'.$this->faker->unique()->numberBetween(10000, 99999),
            'unit' => $this->faker->bothify('####'),
            'floor' => $this->faker->word,
            'beds' => $this->faker->numberBetween(1, 5),
            'baths' => $this->faker->numberBetween(1, 5),
            'parking' => $this->faker->numberBetween(0, 3),
            'bua' => $this->faker->randomFloat(2, 30, 500),
            'price' => $this->faker->numberBetween(50000, 5000000),
            'description' => $this->faker->paragraph,
            'furnishing' => $this->faker->randomElement(['Furnished', 'Unfurnished', 'Partly Furnished', 'Fitted', 'Not Fitted', 'Shell And Core']),
            'views' => json_encode([$this->faker->word]),
            'plot' => $this->faker->bothify('##'),
            'frequency' => $this->faker->randomElement(['yearly', 'monthly', 'weekly', 'daily']),
            'created_by' => 1,
            'company_listing' => $this->faker->boolean,
            'photos' => json_encode([]),
            'documents' => json_encode([]),
            'public_documents' => json_encode([]),
            'upload_folder' => $this->faker->date('Y/m/d').'/'.$this->faker->numberBetween(100000000, 999999999),
            'feature_tags' => json_encode(['Company Inventory', 'Hot', 'Pf Premium']),
            'watermark' => 1,
            'lead_auto_assign' => 1,
            'created_at' => $this->faker->dateTimeBetween('-1 years', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-1 years', 'now'),
            'temp_sync_at' => $this->faker->dateTimeBetween('-1 years', 'now'),
        ];
    }
}
