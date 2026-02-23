<?php

namespace App\Http\Controllers;

use App\Actions\File\FileUploadAction;
use App\Actions\Listing\CreateListingAction;
use App\Actions\Listing\DeleteListingAction;
use App\Actions\Listing\ListListingsAction;
use App\Actions\Listing\UpdateListingAction;
use App\Enums\Availability;
use App\Enums\ListingStatus;
use App\Enums\ListingType;
use App\Enums\PropertyType;
use App\Layouts\ListingLayout;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Number;
use Litepie\Actions\ActionResult;

/**
 * ListingController
 *
 * Property listing management controller using Litepie Actions pattern
 * All CRUD operations delegated to dedicated Action classes
 *
 * Endpoints:
 * - GET  /listings           - Listing management page (layout)
 * - GET  /api/listing       - List listings (with filters, pagination)
 * - POST /api/listing       - Create new listing
 * - GET  /api/listing/{id}  - Get single listing
 * - PUT  /api/listing/{id}  - Update listing
 * - DELETE /api/listing/{id} - Delete listing
 * - GET  /api/listing/stats/{id} - Get statistics
 * - GET  /api/listing-master-data - Get master data for dropdowns
 * - POST /api/upload/*       - File upload endpoints
 * - DELETE /api/files/{path} - Delete uploaded files
 */
class ListingController extends Controller
{
    /**
     * Display listing management page
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('listing.index');
    }

    /**
     * Get listing layout configuration
     * Returns complete layout structure for frontend rendering
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function listing()
    {
        $masterData = $this->getMasterData();
        $layout = ListingLayout::make($masterData);

        return response()->layout($layout);
    }

    /**
     * Get component section data by type and component name
     * Returns the specific section configuration for modals, drawers, etc.
     * Used by: /layouts/listings/{type}/{component}
     *
     * @param  string|null  $type
     * @param  string|null  $component
     * @return \Illuminate\Http\JsonResponse
     */
    public function getComponentSection(Request $request, $type = null, $component = null)
    {
        // Support both route parameters and query parameters
        $type = $type ?? $request->input('type');
        $component = $component ?? $request->input('component');

        if (! $type || ! $component) {
            return response()->json([
                'error' => 'Missing required parameters: type and component',
            ], 400);
        }

        // Get master data for options
        $masterData = $this->getMasterData();

        // Build the section data based on type and component using ListingLayout
        $sectionData = ListingLayout::getComponentDefinition($type, $component, $masterData);

        if (! $sectionData) {
            return response()->json([
                'error' => 'Component definition not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $sectionData,
        ]);
    }

    /**
     * List all listings with filters and pagination
     * Uses ListListingsAction with structured filter format
     *
     * Query parameters:
     * - filter: Structured filter string (e.g., status:EQ(active);property_type:IN(apartment,villa))
     * - q or search: Search term across searchable fields
     * - sort_by or sort: Field to sort by (default: created_at)
     * - sort_dir or direction: Sort direction asc/desc (default: desc)
     * - per_page or limit: Items per page (default: 10)
     * - page: Page number (default: 1)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function list(Request $request)
    {
        return ListListingsAction::make(null, $request->all())->run();
    }

    /**
     * Create a new listing
     * Uses CreateListingAction
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        $result = CreateListingAction::make(null, $request->all())->run();
        $listing = $result->getData();

        return ActionResult::success(
            array_merge($listing->toArray(), [
                '_settings' => $listing->getSettings('create'),
                '_masterdatas' => $listing->getMasterdata(),
            ]),
            $result->getMessage()
        );
    }

    /**
     * Get a single listing
     * Uses route model binding with automatic relationship loading
     *
     * @param  \App\Models\Listing  $listing  Listing model instance (auto-injected with relationships)
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Listing $listing, Request $request)
    {
        // Increment view count if requested
        if ($request->input('increment_views', false)) {
            $listing->increment('views_count');
            $listing->refresh();

            // Clear stats cache when views are incremented
            cache()->forget('listings:stats:all');
        }

        $context = $request->boolean('edit') ? 'edit' : 'view';

        return ActionResult::success(array_merge($listing->toArray(), [
            '_settings' => $listing->getSettings($context),
            '_masterdatas' => $listing->getMasterdata(),
        ]));
    }

    /**
     * Update an existing listing
     * Uses route model binding
     *
     * @param  \App\Models\Listing  $listing  Listing model instance (auto-injected)
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Listing $listing, Request $request)
    {
        $result = UpdateListingAction::make(null, array_merge($request->all(), ['id' => $listing->id]))->run();
        $listing->refresh();

        return ActionResult::success(
            array_merge($listing->toArray(), [
                '_settings' => $listing->getSettings('edit'),
                '_masterdatas' => $listing->getMasterdata(),
            ]),
            $result->getMessage()
        );
    }

    /**
     * Delete a listing
     * Uses route model binding
     *
     * @param  \App\Models\Listing  $listing  Listing model instance (auto-injected)
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Listing $listing, Request $request)
    {
        $deleteResult = DeleteListingAction::make(null, [
            'id' => $listing->id,
            'force' => $request->boolean('force', false),
        ])->run();

        return ActionResult::success(
            array_merge($listing->toArray(), [
                '_settings' => $listing->getSettings('view'),
            ]),
            $deleteResult->getMessage()
        );
    }

    /**
     * Get statistics for a specific stat card
     * Cached for better performance with proper cache tagging
     *
     * @param  string  $statId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStats($statId)
    {
        try {
            $stats = cache()->remember(
                "listings:stats:{$statId}",
                config('performance.cache.stats_ttl', 300),
                function () use ($statId) {
                    return match ($statId) {
                        'stat-total-listings' => [
                            'value' => Number::abbreviate(Listing::count()),
                            'trend' => $this->calculateTrend(Listing::query()),
                            'trendDirection' => 'up',
                        ],
                        'stat-active' => [
                            'value' => Number::abbreviate(Listing::active()->count()),
                            'trend' => $this->calculateTrend(Listing::active()),
                            'trendDirection' => 'up',
                        ],
                        'stat-sold' => [
                            'value' => Number::abbreviate(Listing::whereIn('status', ['sold', 'rented'])->count()),
                            'trend' => $this->calculateTrend(Listing::whereIn('status', ['sold', 'rented'])),
                            'trendDirection' => 'up',
                        ],
                        'stat-total-views' => [
                            'value' => Number::abbreviate(Listing::sum('views_count')),
                            'trend' => $this->calculateTrend(Listing::query(), 'views_count'),
                            'trendDirection' => 'up',
                        ],
                        default => [
                            'value' => 0,
                            'trend' => 0,
                            'trendDirection' => 'neutral',
                        ],
                    };
                }
            );

            return response()->json([
                'success' => true,
                'data' => $stats,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch statistics',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Calculate trend percentage comparing current month vs. last month
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string|null  $sumColumn  If provided, calculates trend for sum instead of count
     * @return string Formatted trend percentage (e.g., "+10%")
     */
    private function calculateTrend($query, $sumColumn = null): string
    {
        $currentMonthStart = now()->startOfMonth();
        $lastMonthStart = now()->subMonth()->startOfMonth();
        $lastMonthEnd = now()->subMonth()->endOfMonth();

        $currentQuery = clone $query;
        $lastQuery = clone $query;

        if ($sumColumn) {
            $currentValue = $currentQuery->where('created_at', '>=', $currentMonthStart)->sum($sumColumn);
            $lastValue = $lastQuery->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->sum($sumColumn);
        } else {
            $currentValue = $currentQuery->where('created_at', '>=', $currentMonthStart)->count();
            $lastValue = $lastQuery->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->count();
        }

        if ($lastValue == 0) {
            return $currentValue > 0 ? '+100%' : '+0%';
        }

        $change = (($currentValue - $lastValue) / $lastValue) * 100;
        $prefix = $change >= 0 ? '+' : '';

        return $prefix.round($change, 1).'%';
    }

    /**
     * Get master data for dropdowns and options
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMasterDataApi()
    {
        return response()->json([
            'success' => true,
            'data' => $this->getMasterData(),
        ]);
    }

    /**
     * Get master data for forms
     * Cached for better performance as this data changes infrequently
     */
    private function getMasterData(): array
    {
        try {
            return cache()->remember(
                'listings:master-data',
                config('performance.cache.master_data_ttl', 1800),
                function () {
                    // Get full enum data with icons and colors
                    $propertyTypesFull = collect(PropertyType::cases())->map(fn ($case) => [
                        'value' => $case->value,
                        'label' => $case->label(),
                        'icon' => $case->icon(),
                        'color' => $case->color(),
                    ])->keyBy('value')->toArray();

                    $listingTypesFull = collect(ListingType::cases())->map(fn ($case) => [
                        'value' => $case->value,
                        'label' => $case->label(),
                        'icon' => $case->icon(),
                        'color' => $case->color(),
                    ])->keyBy('value')->toArray();

                    $statusesFull = collect(ListingStatus::cases())->map(fn ($case) => [
                        'value' => $case->value,
                        'label' => $case->label(),
                        'icon' => $case->icon(),
                        'color' => $case->color(),
                    ])->keyBy('value')->toArray();

                    $availabilitiesFull = collect(Availability::cases())->map(fn ($case) => [
                        'value' => $case->value,
                        'label' => $case->label(),
                        'icon' => $case->icon(),
                        'color' => $case->color(),
                    ])->keyBy('value')->toArray();

                    return [
                        // Form-friendly format (array with value/label for select options)
                        'property_types' => collect($propertyTypesFull)
                            ->map(fn ($item) => ['value' => $item['value'], 'label' => $item['label']])
                            ->values()
                            ->toArray(),
                        'listing_types' => collect($listingTypesFull)
                            ->map(fn ($item) => ['value' => $item['value'], 'label' => $item['label']])
                            ->values()
                            ->toArray(),
                        'statuses' => collect($statusesFull)
                            ->map(fn ($item) => ['value' => $item['value'], 'label' => $item['label']])
                            ->values()
                            ->toArray(),
                        'availabilities' => collect($availabilitiesFull)
                            ->map(fn ($item) => ['value' => $item['value'], 'label' => $item['label']])
                            ->values()
                            ->toArray(),

                        // Full enum data (for advanced components that need icons/colors)
                        'property_types_full' => $propertyTypesFull,
                        'listing_types_full' => $listingTypesFull,
                        'statuses_full' => $statusesFull,
                        'availabilities_full' => $availabilitiesFull,

                        'currencies' => [
                            ['value' => 'AED', 'label' => 'AED'],
                            ['value' => 'USD', 'label' => 'USD'],
                            ['value' => 'EUR', 'label' => 'EUR'],
                            ['value' => 'GBP', 'label' => 'GBP'],
                        ],
                        'furnishing_statuses' => [
                            ['value' => 'unfurnished', 'label' => 'Unfurnished'],
                            ['value' => 'semi-furnished', 'label' => 'Semi-Furnished'],
                            ['value' => 'fully-furnished', 'label' => 'Fully-Furnished'],
                        ],
                        'cities' => [
                            ['value' => 'Dubai', 'label' => 'Dubai'],
                            ['value' => 'Abu Dhabi', 'label' => 'Abu Dhabi'],
                            ['value' => 'Sharjah', 'label' => 'Sharjah'],
                            ['value' => 'Ajman', 'label' => 'Ajman'],
                            ['value' => 'Ras Al Khaimah', 'label' => 'Ras Al Khaimah'],
                            ['value' => 'Fujairah', 'label' => 'Fujairah'],
                            ['value' => 'Umm Al Quwain', 'label' => 'Umm Al Quwain'],
                        ],
                        'areas' => [
                            ['value' => 'Dubai Marina', 'label' => 'Dubai Marina'],
                            ['value' => 'Downtown Dubai', 'label' => 'Downtown Dubai'],
                            ['value' => 'Palm Jumeirah', 'label' => 'Palm Jumeirah'],
                            ['value' => 'Business Bay', 'label' => 'Business Bay'],
                            ['value' => 'JBR', 'label' => 'JBR'],
                            ['value' => 'Arabian Ranches', 'label' => 'Arabian Ranches'],
                            ['value' => 'Dubai Hills', 'label' => 'Dubai Hills'],
                            ['value' => 'City Walk', 'label' => 'City Walk'],
                            ['value' => 'Al Barsha', 'label' => 'Al Barsha'],
                            ['value' => 'Jumeirah', 'label' => 'Jumeirah'],
                        ],
                        'agents' => User::select('id', 'name')
                            ->orderBy('name')
                            ->get()
                            ->map(fn ($user) => ['value' => $user->id, 'label' => $user->name])
                            ->values()
                            ->toArray(),
                    ];
                }
            );
        } catch (\Exception $e) {
            Log::error('Failed to fetch master data: '.$e->getMessage());

            // Return minimal fallback data
            return [
                'property_types' => [],
                'listing_types' => [],
                'statuses' => [],
                'availabilities' => [],
                'currencies' => ['AED' => 'AED', 'USD' => 'USD'],
                'furnishing_statuses' => [],
                'cities' => [],
                'areas' => [],
                'agents' => [],
            ];
        }
    }

    /**
     * Upload an image file
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadImage(Request $request)
    {
        return $this->handleUpload($request, 'image');
    }

    /**
     * Upload a document file (floor plans, brochures, images)
     * Auto-detects file type: images are treated as 'image', PDFs as 'document'
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadDocument(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file|max:'.$this->getMaxFileSize('document'),
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }

        // Auto-detect file type from MIME type
        $file = $request->file('file');
        $mimeType = $file->getMimeType();

        // Treat image MIME types as 'image' type for proper thumbnail generation
        if (str_starts_with($mimeType, 'image/')) {
            $type = 'image';
        } else {
            $type = 'document';
        }

        return $this->handleUpload($request, $type);
    }

    /**
     * Upload a video file
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadVideo(Request $request)
    {
        return $this->handleUpload($request, 'video');
    }

    /**
     * Upload an attachment file
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadAttachment(Request $request)
    {
        return $this->handleUpload($request, 'attachment');
    }

    /**
     * Generic file upload
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(Request $request)
    {
        $type = $request->input('type', 'attachment');

        return $this->handleUpload($request, $type);
    }

    /**
     * Handle file upload using FileUploadAction
     *
     * @param  string  $type  File type (image, video, document, attachment)
     * @return \Illuminate\Http\JsonResponse
     */
    private function handleUpload(Request $request, string $type)
    {
        try {
            $request->validate([
                'file' => 'required|file|max:'.$this->getMaxFileSize($type),
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }

        return FileUploadAction::make(null, [
            'file' => $request->file('file'),
            'type' => $type,
            'disk' => $request->input('disk', 'public'),
            'folder' => $request->input('folder', 'listings'),
            'generate_thumbnail' => $request->input('generate_thumbnail', $type === 'image'),
            'resize' => $request->input('resize', []),
            'quality' => $request->input('quality', 85),
        ])->run();
    }

    /**
     * Delete a file
     *
     * @param  string  $path  File path to delete
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteFile($path, Request $request)
    {
        $disk = $request->input('disk', 'public');

        try {
            if (Storage::disk($disk)->exists($path)) {
                Storage::disk($disk)->delete($path);

                // Also delete thumbnail if it exists
                $thumbnailPath = str_replace('/uploads/', '/uploads/thumbnails/', $path);
                if (Storage::disk($disk)->exists($thumbnailPath)) {
                    Storage::disk($disk)->delete($thumbnailPath);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'File deleted successfully',
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'File not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete file: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get maximum file size for upload type (in KB)
     *
     * @param  string  $type  File type
     * @return int Maximum file size in KB
     */
    private function getMaxFileSize(string $type): int
    {
        return match ($type) {
            'image' => 10240, // 10MB - property photos
            'video' => 102400, // 100MB - property videos/virtual tours
            'document' => 20480, // 20MB - floor plans, brochures
            'attachment' => 51200, // 50MB - general attachments
            default => 10240,
        };
    }
}
