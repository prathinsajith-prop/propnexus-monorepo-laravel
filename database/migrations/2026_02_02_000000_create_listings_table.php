<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('listings', function (Blueprint $table) {
            $table->id();

            // Core identifiers
            $table->string('listing_id', 50)->unique()->index();
            $table->string('mls_number', 50)->unique()->nullable()->index();
            $table->string('title');
            $table->string('slug')->unique()->index();

            // Property Details
            $table->enum('property_type', ['residential', 'commercial', 'land', 'industrial'])->default('residential')->index();
            $table->enum('listing_type', ['sale', 'rent', 'lease'])->default('sale')->index();
            $table->decimal('price', 15, 2)->index();
            $table->string('currency', 10)->default('AED');

            // Location
            $table->text('address');
            $table->string('city')->index();
            $table->string('state')->nullable();
            $table->string('country')->default('UAE');
            $table->string('postal_code', 20)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('area')->nullable()->index();
            $table->string('sub_area')->nullable();

            // Property Specifications
            $table->unsignedSmallInteger('bedrooms')->default(0)->index();
            $table->unsignedSmallInteger('bathrooms')->default(0)->index();
            $table->decimal('size_sqft', 10, 2)->nullable();
            $table->decimal('plot_size_sqft', 10, 2)->nullable();
            $table->string('unit_number')->nullable();
            $table->string('building_name')->nullable();
            $table->unsignedSmallInteger('floor_number')->nullable();
            $table->unsignedSmallInteger('total_floors')->nullable();
            $table->year('year_built')->nullable();

            // Features & Amenities
            $table->json('features')->nullable();
            $table->json('amenities')->nullable();
            $table->boolean('is_furnished')->default(false);
            $table->enum('furnishing_status', ['unfurnished', 'semi-furnished', 'fully-furnished'])->nullable();
            $table->boolean('has_parking')->default(false);
            $table->unsignedTinyInteger('parking_spaces')->default(0);
            $table->boolean('has_balcony')->default(false);
            $table->boolean('has_garden')->default(false);
            $table->boolean('has_pool')->default(false);
            $table->boolean('pet_friendly')->default(false);

            // Content
            $table->text('description');
            $table->text('short_description')->nullable();

            // Media
            $table->string('featured_image')->nullable();
            $table->json('images')->nullable();
            $table->json('floor_plans')->nullable();
            $table->string('video_url')->nullable();
            $table->string('virtual_tour_url')->nullable();

            // Status & Availability
            $table->enum('status', ['draft', 'active', 'pending', 'sold', 'rented', 'expired', 'archived'])->default('draft')->index();
            $table->enum('availability', ['available', 'reserved', 'sold', 'rented'])->default('available')->index();
            $table->date('available_from')->nullable();
            $table->date('available_until')->nullable();

            // Agent/Owner Information
            $table->unsignedBigInteger('agent_id')->index();
            $table->string('agent_name')->nullable();
            $table->string('agent_phone')->nullable();
            $table->string('agent_email')->nullable();
            $table->unsignedBigInteger('owner_id')->nullable();

            // Financial Details
            $table->decimal('service_charge', 10, 2)->nullable();
            $table->string('service_charge_period')->nullable(); // yearly, monthly
            $table->decimal('security_deposit', 10, 2)->nullable();
            $table->json('payment_terms')->nullable();
            $table->boolean('is_negotiable')->default(false);
            $table->decimal('original_price', 15, 2)->nullable();
            $table->decimal('discount_percentage', 5, 2)->default(0);

            // SEO & Marketing
            $table->json('seo_meta')->nullable();
            $table->json('schema_markup')->nullable();
            $table->boolean('is_featured')->default(false)->index();
            $table->boolean('is_hot_deal')->default(false)->index();
            $table->boolean('is_verified')->default(false);
            $table->unsignedInteger('priority_score')->default(50)->index();

            // Metrics & Analytics
            $table->unsignedBigInteger('views_count')->default(0)->index();
            $table->unsignedInteger('inquiries_count')->default(0);
            $table->unsignedInteger('favorites_count')->default(0);
            $table->unsignedInteger('shares_count')->default(0);
            $table->decimal('lead_conversion_rate', 5, 2)->default(0);
            $table->json('analytics')->nullable();

            // Scheduling
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamp('expires_at')->nullable()->index();
            $table->timestamp('sold_at')->nullable();
            $table->timestamp('rented_at')->nullable();

            // Additional Info
            $table->string('reference_number')->nullable();
            $table->json('documents')->nullable();
            $table->json('custom_fields')->nullable();
            $table->text('internal_notes')->nullable();

            // Version Control
            $table->timestamp('last_edited_at')->nullable();
            $table->unsignedBigInteger('last_edited_by')->nullable();

            // Timestamps
            $table->timestamps();
            $table->softDeletes();

            // Composite Indexes for performance
            $table->index(['status', 'availability']);
            $table->index(['property_type', 'listing_type', 'status']);
            $table->index(['city', 'area', 'status']);
            $table->index(['price', 'bedrooms', 'status']);
            $table->index(['is_featured', 'status', 'published_at']);
            $table->index(['agent_id', 'status']);

            // Fulltext index for search
            if (config('database.default') !== 'sqlite') {
                $table->fullText(['title', 'description', 'address', 'area']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listings');
    }
};
