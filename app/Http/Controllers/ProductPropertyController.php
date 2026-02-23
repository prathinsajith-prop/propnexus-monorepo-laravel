<?php

namespace App\Http\Controllers;

use App\Actions\File\FileUploadAction;
use App\Actions\ProductProperty\CreateProductPropertyAction;
use App\Actions\ProductProperty\DeleteProductPropertyAction;
use App\Actions\ProductProperty\ListProductPropertiesAction;
use App\Actions\ProductProperty\UpdateProductPropertyAction;
use App\Enums\ProductCategoryType;
use App\Enums\ProductFrequency;
use App\Enums\ProductFurnishing;
use App\Enums\ProductPropertyFor;
use App\Enums\ProductPropertyStatus;
use App\Enums\ProductPropertyType;
use App\Layouts\ProductPropertyLayout;
use App\Models\BixoProductProperties;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Number;
use Litepie\Actions\ActionResult;

/**
 * ProductPropertyController
 *
 * Product property management controller using Litepie Actions pattern
 * All CRUD operations for bixo_product_properties
 *
 * Endpoints:
 * - GET  /product-properties           - Property management page (layout)
 * - GET  /api/product-property        - List properties (with filters, pagination)
 * - POST /api/product-property        - Create new property
 * - GET  /api/product-property/{id}   - Get single property
 * - PUT  /api/product-property/{id}   - Update property
 * - DELETE /api/product-property/{id} - Delete property
 * - GET  /api/product-property/stats/{id} - Get statistics
 * - GET  /api/product-property-master-data - Get master data for dropdowns
 * - POST /api/upload/*                - File upload endpoints
 * - DELETE /api/files/{path}          - Delete uploaded files
 */
class ProductPropertyController extends Controller
{
    /**
     * Display product property management page
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('product-property.index');
    }

    /**
     * Get product property layout configuration
     * Returns complete layout structure for frontend rendering
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function listing()
    {
        $masterData = $this->getMasterData();
        $layout = ProductPropertyLayout::make($masterData);

        return response()->layout($layout);
    }

    /**
     * Get component section data by type and component name
     * Returns the specific section configuration for modals, drawers, etc.
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

        // Build the section data using ProductPropertyLayout
        $sectionData = ProductPropertyLayout::getComponentDefinition($type, $component, $masterData);

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
     * List all properties with filters and pagination
     *
     * Query parameters:
     * - filter: Structured filter string
     * - q or search: Search term
     * - sort_by or sort: Field to sort by (default: created_at)
     * - sort_dir or direction: Sort direction asc/desc (default: desc)
     * - per_page or limit: Items per page (default: 10)
     * - page: Page number (default: 1)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function list(Request $request)
    {
        return ListProductPropertiesAction::make(null, $request->all())->run();
    }

    /**
     * Create a new property
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        $result = CreateProductPropertyAction::make(null, $request->all())->run();
        $property = $result->getData();

        return ActionResult::success(
            array_merge($property->toArray(), [
                '_settings' => $property->getSettings('create'),
                '_masterdatas' => $property->getMasterdata(),
            ]),
            $result->getMessage()
        );
    }

    /**
     * Get a single property
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(BixoProductProperties $property, Request $request)
    {
        $context = $request->boolean('edit') ? 'edit' : 'view';

        return ActionResult::success(
            array_merge($property->toArray(), [
                '_settings' => $property->getSettings($context),
                '_masterdatas' => $property->getMasterdata(),
            ])
        );
    }

    public function update(BixoProductProperties $property, Request $request)
    {
        $result = UpdateProductPropertyAction::make(null, array_merge(
            $request->except(['_method', '_token']),
            ['id' => $property->getKey()]
        ))->run();
        $property->refresh();

        return ActionResult::success(
            array_merge($property->toArray(), [
                '_settings' => $property->getSettings('edit'),
                '_masterdatas' => $property->getMasterdata(),
            ]),
            $result->getMessage()
        );
    }

    public function delete(BixoProductProperties $property, Request $request)
    {
        $deleteResult = DeleteProductPropertyAction::make(null, [
            'id' => $property->getKey(),
            'force' => $request->boolean('force', false),
        ])->run();

        return ActionResult::success(
            array_merge($property->toArray(), [
                '_settings' => $property->getSettings('view'),
            ]),
            $deleteResult->getMessage()
        );
    }

    /**
     * Get statistics for a specific stat card
     *
     * @param  string  $statId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStats($statId)
    {
        try {
            $stats = cache()->remember(
                "product-properties:stats:{$statId}",
                config('performance.cache.stats_ttl', 300),
                function () use ($statId) {
                    return match ($statId) {
                        'stat-total-properties' => [
                            'value' => Number::abbreviate(BixoProductProperties::count()),
                            'trend' => $this->calculateTrend(BixoProductProperties::query()),
                            'trendDirection' => 'up',
                        ],
                        'stat-published' => [
                            'value' => Number::abbreviate(BixoProductProperties::active()->count()),
                            'trend' => $this->calculateTrend(BixoProductProperties::active()),
                            'trendDirection' => 'up',
                        ],
                        'stat-for-sale' => [
                            'value' => Number::abbreviate(BixoProductProperties::forSale()->count()),
                            'trend' => $this->calculateTrend(BixoProductProperties::forSale()),
                            'trendDirection' => 'up',
                        ],
                        'stat-for-rent' => [
                            'value' => Number::abbreviate(BixoProductProperties::forRent()->count()),
                            'trend' => $this->calculateTrend(BixoProductProperties::forRent()),
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
     */
    private function calculateTrend($query): string
    {
        $currentMonthStart = now()->startOfMonth();
        $lastMonthStart = now()->subMonth()->startOfMonth();
        $lastMonthEnd = now()->subMonth()->endOfMonth();

        $currentQuery = clone $query;
        $lastQuery = clone $query;

        $currentValue = $currentQuery->where('created_at', '>=', $currentMonthStart)->count();
        $lastValue = $lastQuery->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->count();

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
     */
    private function getMasterData(): array
    {
        try {
            return cache()->remember(
                'product-properties:master-data',
                config('performance.cache.master_data_ttl', 1800),
                function () {
                    return [
                        'category_types' => ProductCategoryType::options(),
                        'property_for' => ProductPropertyFor::options(),
                        'property_types' => ProductPropertyType::options(),
                        'statuses' => ProductPropertyStatus::options(),
                        'furnishing_statuses' => ProductFurnishing::options(),
                        'frequencies' => ProductFrequency::options(),
                        'construction_statuses' => [
                            ['value' => 'Completed', 'label' => __('product_property.construction_completed')],
                            ['value' => 'Under Construction', 'label' => __('product_property.construction_under_construction')],
                        ],
                        'listing_sources' => [
                            ['value' => 'Direct', 'label' => __('product_property.source_direct')],
                            ['value' => 'Referral', 'label' => __('product_property.source_referral')],
                            ['value' => 'Portal', 'label' => __('product_property.source_portal')],
                            ['value' => 'Social Media', 'label' => __('product_property.source_social_media')],
                            ['value' => 'Other', 'label' => __('product_property.source_other')],
                        ],
                        'users' => User::select('id', 'name')
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

            return [
                'category_types' => [],
                'property_for' => [],
                'property_types' => [],
                'statuses' => [],
                'furnishing_statuses' => [],
                'construction_statuses' => [],
                'frequencies' => [],
                'listing_sources' => [],
                'users' => [],
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
     * Upload a document file
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

        $file = $request->file('file');
        $mimeType = $file->getMimeType();

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
            'folder' => $request->input('folder', 'product-properties'),
            'generate_thumbnail' => $request->input('generate_thumbnail', $type === 'image'),
            'resize' => $request->input('resize', []),
            'quality' => $request->input('quality', 85),
        ])->run();
    }

    /**
     * Delete a file
     *
     * @param  string  $path
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteFile($path, Request $request)
    {
        $disk = $request->input('disk', 'public');

        try {
            if (Storage::disk($disk)->exists($path)) {
                Storage::disk($disk)->delete($path);

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

    public function activities(BixoProductProperties $property)
    {
        $activities = [];
        $index = 1;

        // Created event
        $activities[] = [
            'id' => $index++,
            'type' => 'created',
            'description' => 'Property record created',
            'subject' => ['id' => $property->getKey(), 'ref' => $property->ref, 'title' => $property->title],
            'properties' => ['status' => $property->status?->value ?? $property->status],
            'occurred_at' => $property->created_at?->toIso8601String(),
        ];

        // Published event
        if ($property->published_at) {
            $activities[] = [
                'id' => $index++,
                'type' => 'published',
                'description' => 'Property published',
                'subject' => ['id' => $property->getKey(), 'ref' => $property->ref],
                'properties' => ['published_at' => $property->published_at?->toIso8601String()],
                'occurred_at' => $property->published_at?->toIso8601String(),
            ];
        }

        // Activated event
        if ($property->activated_at) {
            $activities[] = [
                'id' => $index++,
                'type' => 'activated',
                'description' => 'Property activated',
                'subject' => ['id' => $property->getKey(), 'ref' => $property->ref],
                'properties' => ['activated_at' => $property->activated_at?->toIso8601String()],
                'occurred_at' => $property->activated_at?->toIso8601String(),
            ];
        }

        // Updated event (if updated after creation)
        if ($property->updated_at && $property->updated_at->ne($property->created_at)) {
            $activities[] = [
                'id' => $index++,
                'type' => 'updated',
                'description' => 'Property record updated',
                'subject' => ['id' => $property->getKey(), 'ref' => $property->ref],
                'properties' => ['status' => $property->status?->value ?? $property->status],
                'occurred_at' => $property->updated_at?->toIso8601String(),
            ];
        }

        // Sort descending by occurred_at
        usort($activities, fn ($a, $b) => strcmp((string) ($b['occurred_at'] ?? ''), (string) ($a['occurred_at'] ?? '')));

        return response()->json([
            'success' => true,
            'data' => $activities,
            'meta' => [
                'total' => count($activities),
                'subject' => ['id' => $property->getKey(), 'ref' => $property->ref, 'title' => $property->title],
            ],
        ]);
    }

    /**
     * Get maximum file size for upload type (in KB)
     */
    private function getMaxFileSize(string $type): int
    {
        return match ($type) {
            'image' => 10240,      // 10MB
            'video' => 102400,     // 100MB
            'document' => 20480,   // 20MB
            'attachment' => 51200, // 50MB
            default => 10240,
        };
    }
}
