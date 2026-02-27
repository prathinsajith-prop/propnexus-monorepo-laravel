<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Availability;
use App\Enums\ListingStatus;
use App\Enums\ListingType;
use App\Enums\PropertyType;
use App\Models\Concerns\HandlesActivityLogging;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Litepie\Database\Traits\Searchable;
use Litepie\Hashids\Traits\Hashids;
use Litepie\Logs\Traits\LogsActivity;

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
    use HandlesActivityLogging,
        LogsActivity {
        HandlesActivityLogging::logActivity insteadof LogsActivity;
    }
    use HasFactory;
    use Hashids;
    use Searchable;
    use SoftDeletes;

    /** Log name used to group activity entries for this model. */
    protected string $logName = 'listing';

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
     *
     * @return array<string, mixed>
     */
    public function casts(): array
    {
        return [
            'property_type' => PropertyType::class,
            'listing_type' => ListingType::class,
            'status' => ListingStatus::class,
            'availability' => Availability::class,
            // Array fields handled by Attribute mutators (removed from casts)
            // 'features' => 'array',
            // 'amenities' => 'array',
            // 'images' => 'array',
            // 'floor_plans' => 'array',
            // 'payment_terms' => 'array',
            // 'seo_meta' => 'array',
            // 'schema_markup' => 'array',
            // 'analytics' => 'array',
            // 'documents' => 'array',
            // 'custom_fields' => 'array',
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
            $result = $this->with(['agent', 'owner'])->where('id', $value)->first();

            return $result;
        }

        // Try to decode as hashid/eid (eid is just encoded id)
        $decoded = hashids_decode($value);
        if ($decoded) {
            $result = $this->with(['agent', 'owner'])->where('id', $decoded)->first();

            return $result;
        }

        // Try to find by listing_id (format: LST-XXXXX)
        $result = $this->with(['agent', 'owner'])->where('listing_id', $value)->first();

        return $result;
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
                $this->country,
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

    /**
     * Ensure features is always an array
     */
    protected function features(): Attribute
    {
        return Attribute::make(
            get: fn($value) => is_string($value) ? (json_decode($value, true) ?? []) : ($value ?? []),
            set: function ($value) {
                if (is_array($value)) {
                    return json_encode($value);
                }
                if (empty($value)) {
                    return '[]';
                }
                if (is_string($value)) {
                    json_decode($value, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        return $value;
                    }

                    return json_encode([$value]);
                }

                return '[]';
            }
        );
    }

    /**
     * Ensure amenities is always an array
     */
    protected function amenities(): Attribute
    {
        return Attribute::make(
            get: fn($value) => is_string($value) ? (json_decode($value, true) ?? []) : ($value ?? []),
            set: function ($value) {
                if (is_array($value)) {
                    return json_encode($value);
                }
                if (empty($value)) {
                    return '[]';
                }
                if (is_string($value)) {
                    json_decode($value, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        return $value;
                    }

                    return json_encode([$value]);
                }

                return '[]';
            }
        );
    }

    /**
     * Ensure images is always an array
     */
    protected function images(): Attribute
    {
        return Attribute::make(
            get: fn($value) => is_string($value) ? (json_decode($value, true) ?? []) : ($value ?? []),
            set: function ($value) {
                if (is_array($value)) {
                    return json_encode($value);
                }
                if (empty($value)) {
                    return '[]';
                }
                if (is_string($value)) {
                    json_decode($value, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        return $value;
                    }

                    return json_encode([$value]);
                }

                return '[]';
            }
        );
    }

    /**
     * Ensure documents is always an array
     */
    protected function documents(): Attribute
    {
        return Attribute::make(
            get: fn($value) => is_string($value) ? (json_decode($value, true) ?? []) : ($value ?? []),
            set: function ($value) {
                if (is_array($value)) {
                    return json_encode($value);
                }
                if (empty($value)) {
                    return '[]';
                }
                if (is_string($value)) {
                    json_decode($value, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        return $value;
                    }

                    return json_encode([$value]);
                }

                return '[]';
            }
        );
    }

    /**
     * Ensure payment_terms is always an array
     */
    protected function paymentTerms(): Attribute
    {
        return Attribute::make(
            get: fn($value) => is_string($value) ? (json_decode($value, true) ?? []) : ($value ?? []),
            set: function ($value) {
                if (is_array($value)) {
                    return json_encode($value);
                }
                if (empty($value)) {
                    return '[]';
                }
                if (is_string($value)) {
                    json_decode($value, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        return $value;
                    }

                    return json_encode([$value]);
                }

                return '[]';
            }
        );
    }

    /**
     * Ensure floor_plans is always an array
     */
    protected function floorPlans(): Attribute
    {
        return Attribute::make(
            get: fn($value) => is_string($value) ? (json_decode($value, true) ?? []) : ($value ?? []),
            set: function ($value) {
                if (is_array($value)) {
                    return json_encode($value);
                }
                if (empty($value)) {
                    return '[]';
                }
                if (is_string($value)) {
                    json_decode($value, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        return $value;
                    }

                    return json_encode([$value]);
                }

                return '[]';
            }
        );
    }

    /**
     * Ensure seo_meta is always an array
     */
    protected function seoMeta(): Attribute
    {
        return Attribute::make(
            get: fn($value) => is_string($value) ? (json_decode($value, true) ?? []) : ($value ?? []),
            set: function ($value) {
                if (is_array($value)) {
                    return json_encode($value);
                }
                if (empty($value)) {
                    return '[]';
                }
                if (is_string($value)) {
                    json_decode($value, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        return $value;
                    }

                    return json_encode([$value]);
                }

                return '[]';
            }
        );
    }

    /**
     * Ensure schema_markup is always an array
     */
    protected function schemaMarkup(): Attribute
    {
        return Attribute::make(
            get: fn($value) => is_string($value) ? (json_decode($value, true) ?? []) : ($value ?? []),
            set: function ($value) {
                if (is_array($value)) {
                    return json_encode($value);
                }
                if (empty($value)) {
                    return '[]';
                }
                if (is_string($value)) {
                    json_decode($value, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        return $value;
                    }

                    return json_encode([$value]);
                }

                return '[]';
            }
        );
    }

    /**
     * Ensure analytics is always an array
     */
    protected function analytics(): Attribute
    {
        return Attribute::make(
            get: fn($value) => is_string($value) ? (json_decode($value, true) ?? []) : ($value ?? []),
            set: function ($value) {
                if (is_array($value)) {
                    return json_encode($value);
                }
                if (empty($value)) {
                    return '[]';
                }
                if (is_string($value)) {
                    json_decode($value, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        return $value;
                    }

                    return json_encode([$value]);
                }

                return '[]';
            }
        );
    }

    /**
     * Ensure custom_fields is always an array
     */
    protected function customFields(): Attribute
    {
        return Attribute::make(
            get: fn($value) => is_string($value) ? (json_decode($value, true) ?? []) : ($value ?? []),
            set: function ($value) {
                if (is_array($value)) {
                    return json_encode($value);
                }
                if (empty($value)) {
                    return '[]';
                }
                if (is_string($value)) {
                    json_decode($value, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        return $value;
                    }

                    return json_encode([$value]);
                }

                return '[]';
            }
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

        static::creating(function ($listing) {
            if (empty($listing->listing_id)) {
                $listing->listing_id = 'LST-' . strtoupper(uniqid());
            }
        });
    }

    // ─── Settings & Masterdata ────────────────────────────────────────────────

    /**
     * Resolve whether the currently authenticated user is an admin.
     * Extend this check when a role/permission package is introduced.
     */
    private function isAdminUser(): bool
    {
        $user = auth()->user();
        if (! $user) {
            return false;
        }

        return method_exists($user, 'hasRole') && $user->hasRole(['admin', 'superuser']);
    }

    /**
     * Compute UI settings (groups + fields visibility/edit flags) for this listing.
     * Adapts based on record status, listing type, and user role.
     *
     * @param  string  $context  'view' | 'edit' | 'create'
     */
    public function getSettings(string $context = 'view'): array
    {
        $isAdmin = $this->isAdminUser();
        $statusVal = $this->status instanceof \App\Enums\ListingStatus
            ? $this->status->value
            : ($this->status ?? null);
        $typeVal = $this->listing_type instanceof \App\Enums\ListingType
            ? $this->listing_type->value
            : ($this->listing_type ?? null);
        $isClosed = in_array($statusVal, ['sold', 'rented', 'archived', 'expired']);

        $settings = \App\Support\Settings\ListingSettings::defaults();

        // ── Create: hide system-managed / stat fields ──────────────────────────
        if ($context === 'create' || ! $this->exists) {
            foreach (['views_count', 'published_at', 'analytics'] as $field) {
                $settings['fields'][$field]['show'] = false;
            }
        }

        // ── Edit: slug changes are admin-only ─────────────────────────────────
        if ($context === 'edit') {
            $settings['fields']['slug']['edit'] = $isAdmin;
        }

        // ── Status: closed listings lock most editable capabilities ───────────
        if ($isClosed) {
            $settings['groups']['form.listing-form.specifications-info']['edit'] = false;
            $settings['fields']['price']['edit'] = $isAdmin;
            $settings['fields']['status']['edit'] = $isAdmin;
            $settings['fields']['availability']['edit'] = false;
            $settings['fields']['available_from']['edit'] = false;
        }

        // ── Listing type: hide fields irrelevant to sale vs rent ───────────────
        if ($typeVal === 'sale') {
            $settings['fields']['payment_terms']['show'] = false;
        } elseif ($typeVal === 'rent') {
            $settings['fields']['commission']['show'] = false;
        }

        // ── Role: non-admins cannot see financial or sensitive agent data ──────
        if (! $isAdmin) {
            $settings['groups']['form.listing-form.financial-info'] = ['show' => false, 'edit' => false];
            $settings['groups']['form.view-listing-form.agent'] = ['show' => false, 'edit' => false];
            foreach (
                [
                    'commission',
                    'deposit_amount',
                    'service_charge',
                    'payment_terms',
                    'agent_email',
                    'agent_phone',
                    'notes',
                    'analytics',
                    'schema_markup',
                ] as $field
            ) {
                $settings['fields'][$field]['show'] = false;
            }
        }

        return $settings;
    }

    /**
     * Return dropdown options and reference data for the frontend.
     * Consumed as `_masterdatas` in API responses.
     */
    public function getMasterdata(): array
    {
        return [
            'options' => [
                'property_type' => array_map(
                    fn($e) => ['value' => $e->value, 'label' => $e->label()],
                    \App\Enums\PropertyType::cases()
                ),
                'listing_type' => array_map(
                    fn($e) => ['value' => $e->value, 'label' => $e->label()],
                    \App\Enums\ListingType::cases()
                ),
                'status' => array_map(
                    fn($e) => ['value' => $e->value, 'label' => $e->label()],
                    \App\Enums\ListingStatus::cases()
                ),
                'availability' => array_map(
                    fn($e) => ['value' => $e->value, 'label' => $e->label()],
                    \App\Enums\Availability::cases()
                ),
            ],
        ];
    }
}
