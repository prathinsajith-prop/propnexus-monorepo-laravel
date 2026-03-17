<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ProductCategoryType;
use App\Enums\ProductFrequency;
use App\Enums\ProductFurnishing;
use App\Enums\ProductPropertyFor;
use App\Enums\ProductPropertyStatus;
use App\Enums\ProductPropertyType;
use App\Models\Concerns\HandlesActivityLogging;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Litepie\Database\Traits\Searchable;
use Litepie\Hashids\Traits\Hashids;
use Litepie\Logs\Traits\LogsActivity;

/**
 * BixoProductProperties Model - Product Property Management
 *
 * Enhanced product properties model with full Litepie features
 *
 * @property int $id
 * @property int $organization_id
 * @property int $branch_id
 * @property int $department_id
 * @property string $ref Reference number
 * @property string $title Property title
 * @property string $category_type Commercial|Residential
 * @property string $property_for Rental|Sales
 * @property string $property_type Live|Pocket|Developer|Verified Pocket
 * @property string $status Property status
 * @property float $price Property price
 * @property int $beds Number of bedrooms
 * @property int $baths Number of bathrooms
 * @property float $bua Built-up area
 * @property string $description Property description
 */
class BixoProductProperties extends Model
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
    protected string $logName = 'product-property';

    /**
     * The table associated with the model.
     */
    protected $table = 'bixo_product_properties';

    /**
     * The attributes that are mass assignable.
     */
    protected $guarded = [];

    /**
     * Fields allowed for filterQueryString() scope.
     */
    protected function getFilterableFields(): array
    {
        return [
            'status',
            'category_type',
            'property_for',
            'property_type',
            'furnishing',
            'construction_status',
            'frequency',
            'beds',
            'baths',
            'parking',
            'price',
            'service_charge',
            'original_price',
            'bua',
            'ref',
            'ref_pf',
            'ref_byt',
            'title',
            'unit',
            'floor',
            'tower_name',
            'location_id',
            'sublocation_id',
            'building_id',
            'tower_id',
            'country_id',
            'region_id',
            'user_id',
            'assign_to',
            'marketed_by',
            'created_by',
            'contact_id',
            'developer_id',
            'exclusive',
            'premium',
            'price_on_request',
            'company_listing',
            'rented',
            'published_at',
            'unpublished_at',
            'available_from',
            'created_at',
            'updated_at',
            'archived_at',
        ];
    }

    /**
     * The attributes that should be cast.
     *
     * @return array<string, mixed>
     */
    public function casts(): array
    {
        return [
            // ── Enum casts ──────────────────────────────────────────
            'status' => ProductPropertyStatus::class,
            'category_type' => ProductCategoryType::class,
            'property_for' => ProductPropertyFor::class,
            'property_type' => ProductPropertyType::class,
            'furnishing' => ProductFurnishing::class,
            'frequency' => ProductFrequency::class,
            // ── Booleans ─────────────────────────────────────────────
            'exclusive' => 'boolean',
            'price_on_request' => 'boolean',
            'rented' => 'boolean',
            'company_listing' => 'boolean',
            'watermark' => 'boolean',
            'lead_auto_assign' => 'boolean',
            'pf_subpermit' => 'boolean',
            'price' => 'decimal:2',
            'service_charge' => 'decimal:2',
            'rented_price' => 'decimal:2',
            'original_price' => 'decimal:2',
            'bua' => 'decimal:2',
            'published_at' => 'datetime',
            'unpublished_at' => 'datetime',
            'available_from' => 'date',
            'rent_start' => 'date',
            'rent_end' => 'date',
            'completion_on' => 'date',
            'form_a_expiry' => 'date',
            'activated_at' => 'datetime',
            'trakheesi_expiry' => 'datetime',
            'dld_checked_at' => 'datetime',
            'payment_date' => 'datetime',
            'expires_at' => 'datetime',
            'handover_date' => 'datetime',
            'contract_end' => 'datetime',
            'floor_plans' => 'array',
        ];
    }

    /**
     * The accessors to append to the model's array form.
     */
    protected $appends = ['eid', 'created_by_formatted', 'created_by_company', 'created_by_avatar', 'primary_photo_url', 'photo_urls', 'photo_media_items', 'created_at_formatted', 'updated_at_formatted'];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = ['id'];

    /**
     * Retrieve the model for a bound value.
     * Supports finding by ID, hashid (eid), or ref
     *
     * @param  mixed  $value
     * @param  string|null  $field
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function resolveRouteBinding($value, $field = null)
    {
        // Try to find by primary key (ID)
        if (is_numeric($value)) {
            return $this->where('id', $value)->first();
        }

        // Try to decode as hashid/eid
        $decoded = hashids_decode($value);
        if ($decoded) {
            return $this->where('id', $decoded)->first();
        }

        // Try to find by ref (e.g., MCS-11501)
        return $this->where('ref', $value)->first();
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
            'ref' => 9,
            'unit' => 8,
            'building_id' => 6,
            'location_id' => 7,
        ];
    }

    // ==========================================
    // HASHIDS CONFIGURATION
    // ==========================================

    /**
     * The column to use for hashids
     */
    protected $hashidsKey = 'ref';

    // ==========================================
    // QUERY SCOPES
    // ==========================================

    /**
     * Scope for active properties
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'Published');
    }

    /**
     * Scope for available properties
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'Published')
            ->where('rented', 0);
    }

    /**
     * Scope for properties for sale
     */
    public function scopeForSale($query)
    {
        return $query->where('property_for', 'Sales');
    }

    /**
     * Scope for properties for rent
     */
    public function scopeForRent($query)
    {
        return $query->where('property_for', 'Rental');
    }

    /**
     * Scope for residential properties
     */
    public function scopeResidential($query)
    {
        return $query->where('category_type', 'Residential');
    }

    /**
     * Scope for commercial properties
     */
    public function scopeCommercial($query)
    {
        return $query->where('category_type', 'Commercial');
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
            get: fn() => number_format($this->price, 0) . ' AED'
        );
    }

    /**
     * Get formatted name of the user who created the property.
     */
    protected function createdByFormatted(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->creator?->name ?? 'System'
        );
    }

    /**
     * Get avatar URL for the creator/assigned user.
     */
    protected function createdByAvatar(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->creator?->profile_image_url ?? '/storage/avatars/temp-profile-image.png'
        );
    }

    /**
     * Get creator company/agency with a stable default fallback.
     */
    protected function createdByCompany(): Attribute
    {
        return Attribute::make(
            get: fn() => is_string($this->marketed_by) && trim($this->marketed_by) !== ''
                ? $this->marketed_by
                : 'Metropolitan Capital Real Estate'
        );
    }

    /**
     * Resolve first available property photo URL for summary media blocks.
     */
    protected function primaryPhotoUrl(): Attribute
    {
        return Attribute::make(
            get: function (): ?string {
                $photos = $this->photos;

                if (! is_array($photos) || empty($photos)) {
                    return null;
                }

                $firstPhoto = $photos[0] ?? null;

                if (is_array($firstPhoto)) {
                    $url = $firstPhoto['url'] ?? $firstPhoto['path'] ?? null;

                    return is_string($url) ? $url : null;
                }

                return is_string($firstPhoto) ? $firstPhoto : null;
            }
        );
    }

    /**
     * Resolve all property photo URLs for gallery rendering.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<array<int, string>, never>
     */
    protected function photoUrls(): Attribute
    {
        return Attribute::make(
            get: function (): array {
                $photos = $this->photos;

                if (! is_array($photos) || $photos === []) {
                    return [];
                }

                return collect($photos)
                    ->map(function ($photo): ?string {
                        if (is_array($photo)) {
                            $url = $photo['url'] ?? $photo['path'] ?? null;

                            return is_string($url) && $url !== '' ? $url : null;
                        }

                        return is_string($photo) && $photo !== '' ? $photo : null;
                    })
                    ->filter(fn(?string $url): bool => $url !== null)
                    ->values()
                    ->all();
            }
        );
    }

    /**
     * Resolve property photo items in MediaComponent gallery shape.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<array<int, array{src:string, alt:string|null}>, never>
     */
    protected function photoMediaItems(): Attribute
    {
        return Attribute::make(
            get: function (): array {
                return collect($this->photo_urls)
                    ->map(fn(string $url): array => [
                        'src' => $url,
                        'alt' => $this->title,
                    ])
                    ->values()
                    ->all();
            }
        );
    }

    /**
     * Formatted created_at timestamp.
     */
    protected function createdAtFormatted(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->created_at instanceof Carbon ? $this->created_at->format('Y-m-d H:i:s') : null
        );
    }

    /**
     * Formatted updated_at timestamp.
     */
    protected function updatedAtFormatted(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->updated_at instanceof Carbon ? $this->updated_at->format('Y-m-d H:i:s') : null
        );
    }

    /**
     * Check if property is active
     */
    protected function isActive(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->status === 'Published'
        );
    }

    /**
     * Ensure photos is always an array
     */
    protected function photos(): Attribute
    {
        return Attribute::make(
            get: fn($value) => is_string($value) ? (json_decode($value, true) ?? []) : ($value ?? []),
            set: function ($value) {
                if (is_array($value)) {
                    return json_encode($value);
                }
                if (empty($value)) {
                    return json_encode([]);
                }
                if (is_string($value)) {
                    $decoded = json_decode($value, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        return $value;
                    }

                    return json_encode([$value]);
                }

                return json_encode([]);
            }
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
                    return json_encode([]);
                }
                if (is_string($value)) {
                    $decoded = json_decode($value, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        return $value;
                    }

                    return json_encode([$value]);
                }

                return json_encode([]);
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
                    return json_encode([]);
                }
                if (is_string($value)) {
                    $decoded = json_decode($value, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        return $value;
                    }

                    return json_encode([$value]);
                }

                return json_encode([]);
            }
        );
    }

    /**
     * Ensure portals is always an array
     */
    protected function portals(): Attribute
    {
        return Attribute::make(
            get: fn($value) => is_string($value) ? (json_decode($value, true) ?? []) : ($value ?? []),
            set: function ($value) {
                if (is_array($value)) {
                    return json_encode($value);
                }
                if (empty($value)) {
                    return json_encode([]);
                }
                if (is_string($value)) {
                    $decoded = json_decode($value, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        return $value;
                    }

                    return json_encode([$value]);
                }

                return json_encode([]);
            }
        );
    }

    /**
     * Ensure feature_tags is always an array
     */
    protected function featureTags(): Attribute
    {
        return Attribute::make(
            get: fn($value) => is_string($value) ? (json_decode($value, true) ?? []) : ($value ?? []),
            set: function ($value) {
                if (is_array($value)) {
                    return json_encode($value);
                }
                if (empty($value)) {
                    return json_encode([]);
                }
                if (is_string($value)) {
                    $decoded = json_decode($value, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        return $value;
                    }

                    return json_encode([$value]);
                }

                return json_encode([]);
            }
        );
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
     * Compute UI settings (groups + fields visibility/edit flags) for this property.
     * Adapts based on record status, property_for type, and user role.
     *
     * @param  string  $context  'view' | 'edit' | 'create'
     */
    public function getSettings(string $context = 'view'): array
    {
        $S = \App\Support\Settings\ProductPropertySettings::class;
        $isAdmin = $this->isAdminUser();
        $statusVal = $this->status instanceof \App\Enums\ProductPropertyStatus
            ? $this->status->value : ($this->status ?? null);
        $propFor = $this->property_for instanceof \App\Enums\ProductPropertyFor
            ? $this->property_for->value : ($this->property_for ?? null);
        $isClosed = in_array($statusVal, ['Archived', 'Completed', 'Junk', 'Unpublished']);
        $isPending = in_array($statusVal, [
            'Pending',
            'Waiting Publish',
            'Waiting Teamleader',
            'Waiting Team Leader',
            'Pending Verification',
        ]);
        $isRejected = $statusVal === 'Rejected';

        $formId = match ($context) {
            'create' => $S::FORM_CREATE,
            'edit' => $S::FORM_EDIT,
            default => $S::FORM_VIEW,
        };

        $settings = $S::defaults();

        // ── Create: reg-info (Trakheesi/DLD) is filled in the edit phase ───────
        if ($context === 'create' || ! $this->exists) {
            $createForms = [$S::FORM_CREATE, $S::FORM_CREATE_MODAL];
            $settings['groups'] = array_merge(
                $settings['groups'],
                $S::buildGroups($createForms, ['reg-info'], false, false)
            );
            foreach (['trakheesi', 'trakheesi_expiry', 'permit_number', 'dld_status', 'dld_details', 'published_at'] as $field) {
                $settings['fields'][$field]['show'] = false;
            }
        }

        // ── Status: closed → specs/pricing/assignment become read-only ─────────
        if ($isClosed) {
            $settings['groups'] = array_merge(
                $settings['groups'],
                $S::buildGroups([$formId], ['pricing-info', 'assign-info', 'specs-info'], true, false)
            );
            $settings['fields']['price']['edit'] = $isAdmin;
            $settings['fields']['status']['edit'] = $isAdmin;
        }

        // ── Status: pending → lock assignment while awaiting approval ──────────
        if ($isPending) {
            $settings['groups'] = array_merge(
                $settings['groups'],
                $S::buildGroups([$formId], ['assign-info'], true, false)
            );
        }

        // ── Status: rejected → re-open everything for re-submission ──────────
        if ($isRejected) {
            $settings['groups'] = array_merge(
                $settings['groups'],
                $S::buildGroups([$formId], $S::COMMON_GROUPS, true, true)
            );
        }

        // ── Property for: hide fields irrelevant to Rental vs Sales ───────────
        if ($propFor === 'Rental') {
            foreach (['payment_plan', 'handover_date'] as $field) {
                $settings['fields'][$field]['show'] = false;
            }
        } elseif ($propFor === 'Sales') {
            foreach (['cheques', 'rented', 'rented_price', 'rent_start', 'rent_end'] as $field) {
                $settings['fields'][$field]['show'] = false;
            }
        }

        // ── Role: non-admins cannot see pricing, reg, or assignment ───────────
        if (! $isAdmin) {
            $settings['groups'] = array_merge(
                $settings['groups'],
                $S::buildGroups([$formId], ['pricing-info', 'reg-info', 'assign-info'], false, false)
            );
            foreach (['commission', 'service_charge', 'portal_settings', 'notes', 'dld_details', 'dld_status'] as $field) {
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
                'status' => array_map(
                    fn($e) => ['value' => $e->value, 'label' => $e->label()],
                    \App\Enums\ProductPropertyStatus::cases()
                ),
                'category_type' => array_map(
                    fn($e) => ['value' => $e->value, 'label' => $e->label()],
                    \App\Enums\ProductCategoryType::cases()
                ),
                'property_for' => array_map(
                    fn($e) => ['value' => $e->value, 'label' => $e->label()],
                    \App\Enums\ProductPropertyFor::cases()
                ),
                'furnishing' => array_map(
                    fn($e) => ['value' => $e->value, 'label' => $e->label()],
                    \App\Enums\ProductFurnishing::cases()
                ),
                'frequency' => array_map(
                    fn($e) => ['value' => $e->value, 'label' => $e->label()],
                    \App\Enums\ProductFrequency::cases()
                ),
            ],
        ];
    }

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    /**
     * Get the user who created the property.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
