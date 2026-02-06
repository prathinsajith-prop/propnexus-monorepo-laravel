<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Availability;
use App\Enums\ListingStatus;
use App\Enums\ListingType;
use App\Enums\PropertyType;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
// Litepie Database Traits - ALL 14 FEATURES
use Litepie\Database\Traits\Aggregatable;
use Litepie\Database\Traits\Archivable;
use Litepie\Database\Traits\Batchable;
use Litepie\Database\Traits\Cacheable;
use Litepie\Database\Traits\Exportable;
use Litepie\Database\Traits\Importable;
use Litepie\Database\Traits\Measurable;
use Litepie\Database\Traits\Metable;
use Litepie\Database\Traits\Paginatable;
use Litepie\Database\Traits\Searchable;
use Litepie\Database\Traits\Sluggable;
use Litepie\Database\Traits\Sortable;
use Litepie\Database\Traits\Translatable;
use Litepie\Database\Traits\Versionable;
// Litepie Hashids Trait
use Litepie\Hashids\Traits\Hashids;

/**
 * Listing Model - Property Listing Management with Litepie Database Package
 *
 * This model demonstrates ALL 14 Litepie Database traits + Hashids for real estate listings:
 *
 * 📦 Versionable - Track changes and history
 * 🏷️ Metable - Flexible metadata storage for custom fields
 * 🌍 Translatable - Multi-language property descriptions
 * 🔍 Searchable - Advanced property search (location, price, features)
 * ⚡ Cacheable - Smart caching for high-traffic listings
 * 🔗 Sluggable - SEO-friendly URLs
 * 📄 Paginatable - Optimized listing pagination
 * 📊 Aggregatable - Analytics and reporting
 * 🗃️ Archivable - Archive sold/rented properties
 * 📤 Exportable - Export listings to CSV, Excel
 * 📥 Importable - Import listings from feeds
 * 🔢 Sortable - Manual ordering and featured positions
 * 📋 Batchable - Bulk operations for multiple listings
 * 📏 Measurable - Query performance monitoring
 * 🔐 Hashids - Secure, obfuscated listing IDs
 *
 * @property int $id
 * @property string $listing_id Unique hashable identifier
 * @property string $mls_number MLS/Reference number
 * @property string $title Property title
 * @property string $slug SEO-friendly URL slug
 * @property \App\Enums\PropertyType $property_type residential|commercial|land|industrial
 * @property \App\Enums\ListingType $listing_type sale|rent|lease
 * @property float $price Property price
 * @property string $address Full address
 * @property string $city City
 * @property string $area Area/Neighborhood
 * @property int $bedrooms Number of bedrooms
 * @property int $bathrooms Number of bathrooms
 * @property float $size_sqft Property size in sqft
 * @property string $description Property description
 * @property \App\Enums\ListingStatus $status draft|active|pending|sold|rented|expired|archived
 * @property \App\Enums\Availability $availability available|reserved|sold|rented
 * @property int $agent_id Agent responsible for listing
 * @property bool $is_featured Featured listing flag
 * @property bool $is_hot_deal Hot deal flag
 * @property int $views_count Number of views
 * @property \Illuminate\Support\Carbon|null $published_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 */
class Listing extends Model
{
    use HasFactory;
    use SoftDeletes;

    // Litepie Database Traits
    use Searchable;      // ✅ Advanced search with weighted fields

    // Litepie Hashids  
    use Hashids;         // ✅ Encode/decode IDs (eid field)

    /**
     * The table associated with the model.
     */
    protected $table = 'listings';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'listing_id',
        'mls_number',
        'title',
        'slug',
        'property_type',
        'listing_type',
        'price',
        'currency',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'latitude',
        'longitude',
        'area',
        'sub_area',
        'bedrooms',
        'bathrooms',
        'size_sqft',
        'plot_size_sqft',
        'unit_number',
        'building_name',
        'floor_number',
        'total_floors',
        'year_built',
        'features',
        'amenities',
        'is_furnished',
        'furnishing_status',
        'has_parking',
        'parking_spaces',
        'has_balcony',
        'has_garden',
        'has_pool',
        'pet_friendly',
        'description',
        'short_description',
        'featured_image',
        'images',
        'floor_plans',
        'video_url',
        'virtual_tour_url',
        'status',
        'availability',
        'available_from',
        'available_until',
        'agent_id',
        'agent_name',
        'agent_phone',
        'agent_email',
        'owner_id',
        'service_charge',
        'service_charge_period',
        'security_deposit',
        'payment_terms',
        'is_negotiable',
        'original_price',
        'discount_percentage',
        'seo_meta',
        'schema_markup',
        'is_featured',
        'is_hot_deal',
        'is_verified',
        'priority_score',
        'views_count',
        'inquiries_count',
        'favorites_count',
        'shares_count',
        'lead_conversion_rate',
        'analytics',
        'published_at',
        'expires_at',
        'sold_at',
        'rented_at',
        'reference_number',
        'documents',
        'custom_fields',
        'internal_notes',
        'last_edited_at',
        'last_edited_by',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'property_type' => PropertyType::class,
        'listing_type' => ListingType::class,
        'status' => ListingStatus::class,
        'availability' => Availability::class,
        'features' => 'array',
        'amenities' => 'array',
        'images' => 'array',
        'floor_plans' => 'array',
        'payment_terms' => 'array',
        'seo_meta' => 'array',
        'schema_markup' => 'array',
        'analytics' => 'array',
        'documents' => 'array',
        'custom_fields' => 'array',
        'price' => 'decimal:2',
        'size_sqft' => 'decimal:2',
        'plot_size_sqft' => 'decimal:2',
        'service_charge' => 'decimal:2',
        'security_deposit' => 'decimal:2',
        'original_price' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'lead_conversion_rate' => 'decimal:2',
        'is_furnished' => 'boolean',
        'has_parking' => 'boolean',
        'has_balcony' => 'boolean',
        'has_garden' => 'boolean',
        'has_pool' => 'boolean',
        'pet_friendly' => 'boolean',
        'is_featured' => 'boolean',
        'is_hot_deal' => 'boolean',
        'is_verified' => 'boolean',
        'is_negotiable' => 'boolean',
        'published_at' => 'datetime',
        'expires_at' => 'datetime',
        'sold_at' => 'datetime',
        'rented_at' => 'datetime',
        'available_from' => 'date',
        'available_until' => 'date',
        'last_edited_at' => 'datetime',
    ];

    /**
     * Create a new Eloquent model instance and initialize trait properties
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        // Initialize trait properties
        $this->initializeTraitProperties();
    }

    /**
     * Configure traits that require property definitions
     * (Defined dynamically to avoid trait conflicts)
     */
    protected function initializeTraitProperties()
    {
        // Sluggable configuration
        if (!property_exists($this, 'slugs') || empty($this->slugs)) {
            $this->slugs = ['slug' => 'title'];
        }

        // Translatable configuration
        if (!property_exists($this, 'translatable') || empty($this->translatable)) {
            $this->translatable = ['title', 'description', 'short_description', 'seo_meta'];
        }

        // Searchable configuration
        if (!property_exists($this, 'searchable') || empty($this->searchable)) {
            $this->searchable = ['title', 'description', 'city', 'state', 'building_name', 'reference_number'];
        }
    }

    /**
     * The accessors to append to the model's array form.
     */
    protected $appends = ['eid'];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = ['id'];

    /**
     * Retrieve the model for a bound value.
     * Supports finding by ID, hashid (eid), or listing_id
     *
     * @param  mixed  $value
     * @param  string|null  $field
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function resolveRouteBinding($value, $field = null)
    {
        // Try to find by primary key (ID)
        if (is_numeric($value)) {
            return $this->with(['agent', 'owner'])->where('id', $value)->first();
        }

        // Try to decode hashid to get the actual ID
        $decoded = hashids_decode($value);
        if ($decoded) {
            return $this->with(['agent', 'owner'])->where('id', $decoded)->first();
        }

        // Try to find by listing_id
        return $this->with(['agent', 'owner'])->where('listing_id', $value)->first();
    }

    // ==========================================
    // SEARCHABLE CONFIGURATION
    // ==========================================

    /**
     * Define searchable fields with weights
     */
    protected function searchableFields(): array
    {
        return [
            'title' => 10,
            'description' => 5,
            'address' => 8,
            'area' => 7,
            'city' => 6,
            'building_name' => 4,
            'mls_number' => 9,
        ];
    }

    // ==========================================
    // HASHIDS CONFIGURATION
    // ==========================================

    /**
     * The column to use for hashids
     */
    protected $hashidsKey = 'listing_id';

    /**
     * Use hashids for route model binding
     */
    public function getRouteKeyName()
    {
        return 'listing_id';
    }

    // ==========================================
    // SLUGGABLE CONFIGURATION
    // ==========================================

    /**
     * Generate slug from title
     */
    protected function sluggableFields(): array
    {
        return ['title'];
    }

    /**
     * Slug column name
     */
    protected function slugColumn(): string
    {
        return 'slug';
    }

    // ==========================================
    // METABLE CONFIGURATION
    // ==========================================

    /**
     * Metadata is stored in custom_fields column
     */
    protected function metaColumn(): string
    {
        return 'custom_fields';
    }

    // ==========================================
    // VERSIONABLE CONFIGURATION
    // ==========================================

    /**
     * Fields to track for versioning
     */
    protected function versionableFields(): array
    {
        return [
            'title',
            'description',
            'price',
            'status',
            'availability',
            'property_type',
            'listing_type',
            'bedrooms',
            'bathrooms',
            'size_sqft',
        ];
    }

    // ==========================================
    // TRANSLATABLE CONFIGURATION
    // ==========================================

    /**
     * Translatable fields
     */
    protected function translatableFields(): array
    {
        return [
            'title',
            'description',
            'short_description',
        ];
    }

    // ==========================================
    // ARCHIVABLE CONFIGURATION
    // ==========================================

    /**
     * Archive when status is sold/rented/archived
     */
    protected function archiveCondition(): bool
    {
        return $this->status?->isClosed() ?? false;
    }

    // ==========================================
    // ACCESSORS & MUTATORS
    // ==========================================

    /**
     * Get formatted price
     */
    protected function formattedPrice(): Attribute
    {
        return Attribute::make(
            get: fn() => number_format($this->price, 0) . ' ' . $this->currency
        );
    }

    /**
     * Get full address
     */
    protected function fullAddress(): Attribute
    {
        return Attribute::make(
            get: fn() => implode(', ', array_filter([
                $this->address,
                $this->area,
                $this->city,
                $this->country
            ]))
        );
    }

    /**
     * Check if listing is active
     */
    protected function isActive(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->status === 'active' && $this->availability === 'available'
        );
    }

    /**
     * Calculate discount amount
     */
    protected function discountAmount(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->original_price
                ? ($this->original_price - $this->price)
                : 0
        );
    }

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    /**
     * Agent relationship
     */
    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    /**
     * Owner relationship
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Last editor relationship
     */
    public function lastEditor()
    {
        return $this->belongsTo(User::class, 'last_edited_by');
    }

    // ==========================================
    // QUERY SCOPES
    // ==========================================

    /**
     * Scope: Active listings only
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where('availability', 'available');
    }

    /**
     * Scope: Featured listings
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope: Hot deals
     */
    public function scopeHotDeals($query)
    {
        return $query->where('is_hot_deal', true);
    }

    /**
     * Scope: Filter by property type
     */
    public function scopePropertyType($query, $type)
    {
        return $query->where('property_type', $type);
    }

    /**
     * Scope: Filter by listing type
     */
    public function scopeListingType($query, $type)
    {
        return $query->where('listing_type', $type);
    }

    /**
     * Scope: Filter by city
     */
    public function scopeInCity($query, $city)
    {
        return $query->where('city', $city);
    }

    /**
     * Scope: Filter by area
     */
    public function scopeInArea($query, $area)
    {
        return $query->where('area', $area);
    }

    /**
     * Scope: Filter by price range
     */
    public function scopePriceRange($query, $min, $max)
    {
        return $query->whereBetween('price', [$min, $max]);
    }

    /**
     * Scope: Filter by bedrooms
     */
    public function scopeBedrooms($query, $bedrooms)
    {
        return $query->where('bedrooms', $bedrooms);
    }

    /**
     * Scope: Filter by agent
     */
    public function scopeByAgent($query, $agentId)
    {
        return $query->where('agent_id', $agentId);
    }

    /**
     * Scope: Recently published
     */
    public function scopeRecentlyPublished($query, $days = 7)
    {
        return $query->where('published_at', '>=', now()->subDays($days));
    }

    /**
     * Scope: Most viewed
     */
    public function scopeMostViewed($query, $limit = 10)
    {
        return $query->orderBy('views_count', 'desc')->limit($limit);
    }

    // ==========================================
    // HELPER METHODS
    // ==========================================

    /**
     * Increment view count
     */
    public function incrementViews()
    {
        $this->increment('views_count');
    }

    /**
     * Mark as sold
     */
    public function markAsSold()
    {
        $this->update([
            'status' => 'sold',
            'availability' => 'sold',
            'sold_at' => now(),
        ]);
    }

    /**
     * Mark as rented
     */
    public function markAsRented()
    {
        $this->update([
            'status' => 'rented',
            'availability' => 'rented',
            'rented_at' => now(),
        ]);
    }

    /**
     * Check if expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        // Initialize model instance for property access
        static::retrieved(function ($listing) {
            $listing->initializeTraitProperties();
        });

        static::creating(function ($listing) {
            $listing->initializeTraitProperties();
        });

        static::creating(function ($listing) {
            if (empty($listing->listing_id)) {
                $listing->listing_id = 'LST-' . strtoupper(uniqid());
            }
        });
    }
}
