<?php

namespace Database\Factories;

use App\Models\Listing;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Listing>
 */
class ListingFactory extends Factory
{
    protected $model = Listing::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $propertyTypes = ['residential', 'commercial', 'land', 'industrial'];
        $listingTypes = ['sale', 'rent', 'lease'];
        $cities = ['Dubai', 'Abu Dhabi', 'Sharjah', 'Ajman', 'Ras Al Khaimah'];
        $dubaiAreas = ['Dubai Marina', 'Downtown Dubai', 'Palm Jumeirah', 'Business Bay', 'JBR', 'Arabian Ranches', 'Dubai Hills', 'City Walk', 'Al Barsha', 'Jumeirah'];
        $statuses = ['draft', 'active', 'pending', 'sold', 'rented'];
        $availabilities = ['available', 'reserved', 'sold', 'rented'];

        $propertyType = fake()->randomElement($propertyTypes);
        $listingType = fake()->randomElement($listingTypes);
        $city = fake()->randomElement($cities);
        $area = fake()->randomElement($dubaiAreas);
        $bedrooms = $propertyType === 'residential' ? rand(0, 7) : 0;
        $bathrooms = $propertyType === 'residential' ? rand(1, 5) : 0;
        $status = fake()->randomElement($statuses);
        $availability = $status === 'sold' ? 'sold' : ($status === 'rented' ? 'rented' : fake()->randomElement($availabilities));

        $title = $this->generateTitle($propertyType, $listingType, $bedrooms, $area);
        $slug = \Illuminate\Support\Str::slug($title) . '-' . fake()->unique()->bothify('###??');
        $price = $this->generatePrice($propertyType, $listingType, $bedrooms);

        return [
            'listing_id' => 'LST-' . strtoupper(uniqid()),
            'mls_number' => 'MLS-' . fake()->unique()->numerify('#####'),
            'title' => $title,
            'slug' => $slug,
            'property_type' => $propertyType,
            'listing_type' => $listingType,
            'price' => $price,
            'currency' => 'AED',
            'address' => fake()->streetAddress(),
            'city' => $city,
            'state' => $city,
            'country' => 'UAE',
            'postal_code' => fake()->numerify('#####'),
            'latitude' => fake()->latitude(24, 26),
            'longitude' => fake()->longitude(54, 56),
            'area' => $area,
            'sub_area' => fake()->randomElement(['Marina Gate', 'Emaar Beachfront', 'The Views', 'Greens', 'Springs']),
            'bedrooms' => $bedrooms,
            'bathrooms' => $bathrooms,
            'size_sqft' => rand(500, 5000),
            'plot_size_sqft' => $propertyType === 'land' ? rand(2000, 10000) : null,
            'unit_number' => fake()->bothify('Unit ###'),
            'building_name' => fake()->randomElement(['Marina Tower', 'Sky View', 'Ocean Heights', 'Palm Residence', 'Downtown Plaza']),
            'floor_number' => rand(1, 40),
            'total_floors' => rand(20, 50),
            'year_built' => rand(2010, 2024),
            'features' => $this->generateFeatures($propertyType),
            'amenities' => $this->generateAmenities(),
            'is_furnished' => fake()->boolean(60),
            'furnishing_status' => fake()->randomElement(['unfurnished', 'semi-furnished', 'fully-furnished']),
            'has_parking' => fake()->boolean(80),
            'parking_spaces' => rand(1, 3),
            'has_balcony' => fake()->boolean(70),
            'has_garden' => fake()->boolean(30),
            'has_pool' => fake()->boolean(40),
            'pet_friendly' => fake()->boolean(30),
            'description' => $this->generateDescription($propertyType, $bedrooms, $area),
            'short_description' => fake()->sentence(15),
            'featured_image' => '/images/listings/' . fake()->slug() . '.jpg',
            'images' => [
                '/images/listings/' . fake()->slug() . '-1.jpg',
                '/images/listings/' . fake()->slug() . '-2.jpg',
                '/images/listings/' . fake()->slug() . '-3.jpg',
            ],
            'floor_plans' => [
                '/images/floor-plans/' . fake()->slug() . '.pdf',
            ],
            'video_url' => fake()->boolean(40) ? 'https://youtube.com/watch?v=' . fake()->bothify('???########') : null,
            'virtual_tour_url' => fake()->boolean(30) ? 'https://virtualtour.com/' . fake()->slug() : null,
            'status' => $status,
            'availability' => $availability,
            'available_from' => fake()->dateTimeBetween('now', '+2 months'),
            'available_until' => fake()->boolean(30) ? fake()->dateTimeBetween('+3 months', '+1 year') : null,
            'agent_id' => 1, // Default to first user
            'agent_name' => fake()->name(),
            'agent_phone' => fake()->phoneNumber(),
            'agent_email' => fake()->email(),
            'owner_id' => null,
            'service_charge' => rand(5000, 50000),
            'service_charge_period' => 'yearly',
            'security_deposit' => $price * 0.05,
            'payment_terms' => [
                'cheques' => rand(1, 4),
                'deposit' => '5%',
            ],
            'is_negotiable' => fake()->boolean(40),
            'original_price' => fake()->boolean(30) ? $price * 1.1 : null,
            'discount_percentage' => fake()->boolean(30) ? rand(5, 15) : 0,
            'seo_meta' => [
                'title' => $title,
                'description' => fake()->sentence(20),
                'keywords' => [$propertyType, $listingType, $city, $area],
            ],
            'schema_markup' => [
                '@context' => 'https://schema.org',
                '@type' => 'RealEstateListing',
                'name' => $title,
            ],
            'is_featured' => fake()->boolean(25),
            'is_hot_deal' => fake()->boolean(15),
            'is_verified' => fake()->boolean(60),
            'priority_score' => rand(30, 90),
            'views_count' => rand(50, 2000),
            'inquiries_count' => rand(0, 50),
            'favorites_count' => rand(0, 100),
            'shares_count' => rand(0, 30),
            'lead_conversion_rate' => rand(5, 30),
            'analytics' => [
                'page_views' => rand(100, 5000),
                'unique_visitors' => rand(50, 2000),
            ],
            'published_at' => $status === 'active' ? fake()->dateTimeBetween('-6 months', 'now') : null,
            'expires_at' => fake()->boolean(20) ? fake()->dateTimeBetween('+1 month', '+6 months') : null,
            'sold_at' => $status === 'sold' ? fake()->dateTimeBetween('-2 months', 'now') : null,
            'rented_at' => $status === 'rented' ? fake()->dateTimeBetween('-2 months', 'now') : null,
            'reference_number' => 'REF-' . fake()->numerify('#####'),
            'documents' => [],
            'custom_fields' => [],
            'internal_notes' => fake()->boolean(40) ? fake()->sentence(10) : null,
            'last_edited_at' => null,
            'last_edited_by' => null,
        ];
    }

    /**
     * Generate property title
     */
    private function generateTitle($propertyType, $listingType, $bedrooms, $area): string
    {
        $typeLabel = match ($propertyType) {
            'residential' => $bedrooms > 0 ? "{$bedrooms}BR Apartment" : "Studio Apartment",
            'commercial' => 'Commercial Space',
            'land' => 'Land Plot',
            'industrial' => 'Industrial Unit',
        };

        $actionLabel = match ($listingType) {
            'sale' => 'for Sale',
            'rent' => 'for Rent',
            'lease' => 'for Lease',
        };

        return "{$typeLabel} {$actionLabel} in {$area}";
    }

    /**
     * Generate realistic price based on property
     */
    private function generatePrice($propertyType, $listingType, $bedrooms): float
    {
        if ($listingType === 'sale') {
            return match ($propertyType) {
                'residential' => rand(500000, 5000000),
                'commercial' => rand(1000000, 10000000),
                'land' => rand(2000000, 20000000),
                'industrial' => rand(3000000, 15000000),
            };
        } else {
            // Rent/Lease
            return match ($propertyType) {
                'residential' => rand(50000, 300000),
                'commercial' => rand(100000, 500000),
                'land' => rand(150000, 800000),
                'industrial' => rand(200000, 1000000),
            };
        }
    }

    /**
     * Generate property features
     */
    private function generateFeatures($propertyType): array
    {
        $commonFeatures = [
            'Central AC',
            'Built-in wardrobes',
            'Private parking',
            'Security',
            '24/7 Maintenance',
        ];

        $residentialFeatures = [
            'Balcony',
            'Kitchen appliances',
            'Maid\'s room',
            'Study room',
            'Storage room',
            'Walk-in closet',
        ];

        $commercialFeatures = [
            'Reception area',
            'Conference room',
            'Pantry',
            'Server room',
            'Raised flooring',
        ];

        $features = $commonFeatures;

        if ($propertyType === 'residential') {
            $features = array_merge($features, fake()->randomElements($residentialFeatures, rand(2, 4)));
        } elseif ($propertyType === 'commercial') {
            $features = array_merge($features, fake()->randomElements($commercialFeatures, rand(2, 4)));
        }

        return $features;
    }

    /**
     * Generate amenities
     */
    private function generateAmenities(): array
    {
        $amenities = [
            'Swimming pool',
            'Gym',
            'Children\'s play area',
            'BBQ area',
            'Sauna',
            'Steam room',
            'Concierge service',
            'Retail outlets',
            'Restaurants',
            'Covered parking',
        ];

        return fake()->randomElements($amenities, rand(3, 6));
    }

    /**
     * Generate property description
     */
    private function generateDescription($propertyType, $bedrooms, $area): string
    {
        $descriptions = [
            "Stunning property in the heart of {$area}. This exceptional unit offers breathtaking views and modern amenities.",
            "Luxurious living space featuring contemporary design and premium finishes. Perfect for those seeking comfort and style.",
            "Prime location in {$area} with easy access to major landmarks, shopping, and dining. Don't miss this opportunity!",
            "Spacious and well-maintained property with high-quality fittings. Ideal for families or professionals.",
            "Modern property with elegant interiors and state-of-the-art facilities. Experience luxury living at its finest.",
        ];

        return fake()->randomElement($descriptions);
    }

    /**
     * State: Residential property
     */
    public function residential(): static
    {
        return $this->state(fn(array $attributes) => [
            'property_type' => 'residential',
        ]);
    }

    /**
     * State: Commercial property
     */
    public function commercial(): static
    {
        return $this->state(fn(array $attributes) => [
            'property_type' => 'commercial',
        ]);
    }

    /**
     * State: For sale
     */
    public function forSale(): static
    {
        return $this->state(fn(array $attributes) => [
            'listing_type' => 'sale',
        ]);
    }

    /**
     * State: For rent
     */
    public function forRent(): static
    {
        return $this->state(fn(array $attributes) => [
            'listing_type' => 'rent',
        ]);
    }

    /**
     * State: Active listing
     */
    public function active(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'active',
            'availability' => 'available',
            'published_at' => now(),
        ]);
    }

    /**
     * State: Featured listing
     */
    public function featured(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_featured' => true,
        ]);
    }
}
