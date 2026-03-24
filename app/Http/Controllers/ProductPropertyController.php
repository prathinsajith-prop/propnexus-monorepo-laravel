<?php

namespace App\Http\Controllers;

use App\Actions\File\FileUploadAction;
use App\Actions\ProductProperty\ArchiveProductPropertyAction;
use App\Actions\ProductProperty\CreateProductPropertyAction;
use App\Actions\ProductProperty\CreateProductPropertyFollowUpAction;
use App\Actions\ProductProperty\CreateProductPropertyNoteAction;
use App\Actions\ProductProperty\DeleteProductPropertyAction;
use App\Actions\ProductProperty\DeleteProductPropertyFollowUpAction;
use App\Actions\ProductProperty\DeleteProductPropertyNoteAction;
use App\Actions\ProductProperty\DuplicateProductPropertyAction;
use App\Actions\ProductProperty\ListProductPropertiesAction;
use App\Actions\ProductProperty\ListProductPropertyFollowUpsAction;
use App\Actions\ProductProperty\ListProductPropertyNotesAction;
use App\Actions\ProductProperty\MarkFeaturedProductPropertyAction;
use App\Actions\ProductProperty\MarkVerifiedProductPropertyAction;
use App\Actions\ProductProperty\PreviewProductPropertyAction;
use App\Actions\ProductProperty\PublishProductPropertyAction;
use App\Actions\ProductProperty\UnpublishProductPropertyAction;
use App\Actions\ProductProperty\UpdateProductPropertyAction;
use App\Actions\ProductProperty\UpdateProductPropertyFollowUpAction;
use App\Actions\ProductProperty\UpdateProductPropertyNoteAction;
use App\Enums\ConstructionStatus;
use App\Enums\FollowUpStatus;
use App\Enums\FollowUpType;
use App\Enums\ListingSource;
use App\Enums\ProductCategoryType;
use App\Enums\ProductFrequency;
use App\Enums\ProductFurnishing;
use App\Enums\ProductPropertyFor;
use App\Enums\ProductPropertyStatus;
use App\Enums\ProductPropertyType;
use App\Layouts\ProductPropertyLayout;
use App\Models\BixoProductProperties;
use App\Models\BixoSchedulesFollowUp;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Number;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Litepie\Actions\ActionResult;
use Litepie\Logs\Models\ActivityLog;

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
     * @return View
     */
    public function index()
    {
        return view('product-property.index');
    }

    /**
     * Get product property layout configuration
     * Returns complete layout structure for frontend rendering
     *
     * @return JsonResponse
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
     */
    public function getComponentSection(Request $request, $type = null, $component = null): ActionResult
    {
        // Support both route parameters and query parameters
        $type = $type ?? $request->input('type');
        $component = $component ?? $request->input('component');

        if (! $type || ! $component) {
            return ActionResult::failure('Missing required parameters: type and component');
        }

        // Get master data for options
        $masterData = $this->getMasterData();

        // Build the section data using ProductPropertyLayout
        $sectionData = ProductPropertyLayout::getComponentDefinition($type, $component, $masterData);

        if (! $sectionData) {
            return ActionResult::failure('Component definition not found');
        }

        return ActionResult::success($sectionData);
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
     * @return JsonResponse
     */
    public function list(Request $request)
    {
        return ListProductPropertiesAction::make(null, $request->all())->run();
    }

    /**
     * Create a new property
     *
     * @return JsonResponse
     */
    public function create(Request $request)
    {
        $result = CreateProductPropertyAction::make(null, $request->all())->run();

        if ($result->isFailure()) {
            return ActionResult::failure($result->getMessage(), $result->getErrors());
        }

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
     * @return JsonResponse
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

        if ($result->isFailure()) {
            return ActionResult::failure($result->getMessage(), $result->getErrors());
        }

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
     * Get statistics for a specific property — used by the fullscreen detail view charts.
     * Returns category_type distribution across all properties for portfolio context.
     */
    public function getPropertyStats(BixoProductProperties $property): ActionResult
    {
        try {
            $stats = cache()->remember(
                'product-properties:property-stats:global',
                config('performance.cache.stats_ttl', 300),
                function () {
                    $distribution = BixoProductProperties::query()
                        ->select('category_type', DB::raw('count(*) as count'))
                        ->whereNotNull('category_type')
                        ->groupBy('category_type')
                        ->pluck('count', 'category_type')
                        ->toArray();

                    return [
                        'category_type_distribution' => [
                            $distribution['Commercial'] ?? 0,
                            $distribution['Residential'] ?? 0,
                        ],
                    ];
                }
            );

            return ActionResult::success($stats);
        } catch (\Exception $e) {
            return ActionResult::failure('Failed to fetch property statistics: ' . $e->getMessage());
        }
    }

    /**
     * Get statistics for a specific stat card
     *
     * @param  string  $statId
     */
    public function getStats($statId): ActionResult
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

            return ActionResult::success($stats);
        } catch (\Exception $e) {
            return ActionResult::failure('Failed to fetch statistics: ' . $e->getMessage());
        }
    }

    /**
     * Calculate trend percentage comparing current month vs. last month
     *
     * @param  Builder  $query
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

        return $prefix . round($change, 1) . '%';
    }

    /**
     * Get master data for dropdowns and options
     */
    public function getMasterDataApi(): ActionResult
    {
        return ActionResult::success($this->getMasterData());
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
                        'construction_statuses' => ConstructionStatus::options(),
                        'listing_sources' => ListingSource::options(),
                        'users' => User::select('id', 'name')
                            ->orderBy('name')
                            ->get()
                            ->map(fn($user) => ['value' => $user->id, 'label' => $user->name])
                            ->values()
                            ->toArray(),
                    ];
                }
            );
        } catch (\Exception $e) {
            Log::error('Failed to fetch master data: ' . $e->getMessage());

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
     * @return JsonResponse
     */
    public function uploadImage(Request $request)
    {
        return $this->handleUpload($request, 'image');
    }

    /**
     * Upload a document file
     *
     * @return JsonResponse
     */
    public function uploadDocument(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file|max:' . $this->getMaxFileSize('document'),
            ]);
        } catch (ValidationException $e) {
            return ActionResult::failure('Validation failed', $e->errors());
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
     * @return JsonResponse
     */
    public function uploadVideo(Request $request)
    {
        return $this->handleUpload($request, 'video');
    }

    /**
     * Upload an attachment file
     *
     * @return JsonResponse
     */
    public function uploadAttachment(Request $request)
    {
        return $this->handleUpload($request, 'attachment');
    }

    /**
     * Generic file upload
     *
     * @return JsonResponse
     */
    public function upload(Request $request)
    {
        $type = $request->input('type', 'attachment');

        return $this->handleUpload($request, $type);
    }

    /**
     * Handle file upload using FileUploadAction
     */
    private function handleUpload(Request $request, string $type)
    {
        try {
            $request->validate([
                'file' => 'required|file|max:' . $this->getMaxFileSize($type),
            ]);
        } catch (ValidationException $e) {
            return ActionResult::failure('Validation failed', $e->errors());
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
     */
    public function deleteFile($path, Request $request): ActionResult
    {
        $disk = $request->input('disk', 'public');

        try {
            if (Storage::disk($disk)->exists($path)) {
                Storage::disk($disk)->delete($path);

                $thumbnailPath = str_replace('/uploads/', '/uploads/thumbnails/', $path);
                if (Storage::disk($disk)->exists($thumbnailPath)) {
                    Storage::disk($disk)->delete($thumbnailPath);
                }

                return ActionResult::success(null, 'File deleted successfully');
            }

            return ActionResult::failure('File not found');
        } catch (\Exception $e) {
            return ActionResult::failure('Failed to delete file: ' . $e->getMessage());
        }
    }

    /**
     * Publish a property.
     * POST /api/product-property/{property}/publish
     */
    public function publish(BixoProductProperties $property): ActionResult
    {
        return PublishProductPropertyAction::make(null, ['id' => $property->getKey()])->run();
    }

    /**
     * Unpublish a property with a reason.
     * POST /api/product-property/{property}/unpublish
     */
    public function unpublish(BixoProductProperties $property, Request $request): ActionResult
    {
        return UnpublishProductPropertyAction::make(null, array_merge(
            $request->only(['reason', 'description']),
            ['id' => $property->getKey()]
        ))->run();
    }

    /**
     * Generate a preview URL for a property.
     * POST /api/product-property/{property}/preview
     */
    public function preview(BixoProductProperties $property, Request $request): ActionResult
    {
        return PreviewProductPropertyAction::make(null, array_merge(
            $request->only(['preview_type', 'price']),
            ['id' => $property->getKey()]
        ))->run();
    }

    /**
     * Archive a property.
     * POST /api/product-property/{property}/archive
     */
    public function archive(BixoProductProperties $property): ActionResult
    {
        return ArchiveProductPropertyAction::make(null, ['id' => $property->getKey()])->run();
    }

    /**
     * Mark a property as verified.
     * POST /api/product-property/{property}/mark-verified
     */
    public function markVerified(BixoProductProperties $property): ActionResult
    {
        return MarkVerifiedProductPropertyAction::make(null, ['id' => $property->getKey()])->run();
    }

    /**
     * Toggle the featured (premium) flag on a property.
     * POST /api/product-property/{property}/mark-featured
     */
    public function markFeatured(BixoProductProperties $property): ActionResult
    {
        return MarkFeaturedProductPropertyAction::make(null, ['id' => $property->getKey()])->run();
    }

    /**
     * Duplicate a property as a new Draft.
     * POST /api/product-property/{property}/duplicate
     */
    public function duplicate(BixoProductProperties $property, Request $request): ActionResult
    {
        return DuplicateProductPropertyAction::make(null, array_merge(
            $request->only(['ref', 'title']),
            ['id' => $property->getKey()]
        ))->run();
    }

    /**
     * Export a property's full data as JSON.
     * GET /api/product-property/{property}/export
     */
    public function export(BixoProductProperties $property): ActionResult
    {
        return ActionResult::success(
            array_merge($property->toArray(), [
                '_masterdatas' => $property->getMasterdata(),
            ]),
            'Property exported successfully'
        );
    }

    /**
     * Return download URLs for all photos.
     * GET /api/product-property/{property}/download/photos
     */
    public function downloadPhotos(BixoProductProperties $property): ActionResult
    {
        $photos = is_array($property->photos) ? $property->photos : [];

        return ActionResult::success([
            'files' => $photos,
            'count' => count($photos),
        ]);
    }

    /**
     * Return download URLs for floor plans.
     * GET /api/product-property/{property}/download/floor-plans
     */
    public function downloadFloorPlans(BixoProductProperties $property): ActionResult
    {
        $floorPlans = is_array($property->floor_plans) ? $property->floor_plans : [];

        return ActionResult::success([
            'files' => $floorPlans,
            'count' => count($floorPlans),
        ]);
    }

    /**
     * Return download URLs for documents.
     * GET /api/product-property/{property}/download/documents
     */
    public function downloadDocuments(BixoProductProperties $property): ActionResult
    {
        $documents = is_array($property->documents) ? $property->documents : [];

        return ActionResult::success([
            'files' => $documents,
            'count' => count($documents),
        ]);
    }

    /**
     * Return download URLs for all files (photos + floor plans + documents).
     * GET /api/product-property/{property}/download/all
     */
    public function downloadAll(BixoProductProperties $property): ActionResult
    {
        $photos = is_array($property->photos) ? $property->photos : [];
        $floorPlans = is_array($property->floor_plans) ? $property->floor_plans : [];
        $documents = is_array($property->documents) ? $property->documents : [];

        return ActionResult::success([
            'photos' => $photos,
            'floor_plans' => $floorPlans,
            'documents' => $documents,
            'count' => count($photos) + count($floorPlans) + count($documents),
        ]);
    }

    /**
     * Return the number of leads associated with a property.
     * Currently returns 0 — placeholder until a leads module is implemented.
     */
    public function leadsCount(BixoProductProperties $property): ActionResult
    {
        return ActionResult::success(['leads_count' => 10]);
    }

    public function activities(BixoProductProperties $property, Request $request): ActionResult
    {
        $perPage = (int) $request->get('per_page', 15);

        $paginated = $property->activities()
            ->with('causer')
            ->latest()
            ->paginate($perPage);

        $activities = collect($paginated->items())->map(fn(ActivityLog $log) => [
            'id' => $log->getKey(),
            'type' => $log->event ?? 'activity',
            'description' => $log->description,
            'subject' => [
                'eid' => $property->eid,
                'ref' => $property->ref,
                'title' => $property->title,
            ],
            'causer' => $log->causer ? [
                'id' => $log->causer->getKey(),
                'name' => $log->causer->name ?? null,
            ] : null,
            'properties' => $log->properties,
            'occurred_at' => $log->created_at?->toIso8601String(),
        ])->values()->all();

        return ActionResult::success($activities, null, [
            'total' => $paginated->total(),
            'per_page' => $paginated->perPage(),
            'current_page' => $paginated->currentPage(),
            'last_page' => $paginated->lastPage(),
            'subject' => ['eid' => $property->eid, 'ref' => $property->ref, 'title' => $property->title],
        ]);
    }

    /**
     * List all follow-ups for a property.
     * GET /api/product-property/{property}/followups
     */
    public function listFollowUps(BixoProductProperties $property, Request $request): ActionResult
    {
        $result = ListProductPropertyFollowUpsAction::make(null, array_merge(
            $request->all(),
            ['property_id' => $property->getKey()]
        ))->run();

        if ($result->isFailure()) {
            return ActionResult::failure($result->getMessage());
        }

        return ActionResult::success(
            $result->getData(),
            $result->getMessage(),
            array_merge(
                $result->getMetadata() ?? [],
                [
                    'badgeConfig' => [
                        'followup_type' => FollowUpType::badgeConfig(),
                        'status' => FollowUpStatus::badgeConfig(),
                    ],
                    'editData' => [
                        'component' => 'edit-property-followup',
                        'type' => 'modal',
                        'action' => 'edit',
                        'hasParent' => true,
                        'config' => [
                            'width' => '500px',
                            'height' => '100vh',
                            'anchor' => 'right',
                            'backdrop' => true,
                        ],
                        'params' => ['id' => ':property_id', 'followup_id' => ':eid'],
                        'url' => '/api/product-property/:id/followups/:followup_id',
                    ],
                    'deleteData' => [
                        'component' => 'delete-property-followup',
                        'type' => 'confirm',
                        'action' => 'delete',
                        'hasParent' => true,
                        'method' => 'DELETE',
                        'url' => '/api/product-property/:id/followups/:followup_id',
                        'config' => [
                            'width' => '400px',
                            'height' => 'auto',
                            'anchor' => 'center',
                            'backdrop' => true,
                        ],
                        'params' => ['id' => ':property_id', 'followup_id' => ':eid'],
                    ],
                ]
            )
        );
    }

    /**
     * Create a follow-up for a property.
     * POST /api/product-property/{property}/followups
     */
    public function createFollowUp(BixoProductProperties $property, Request $request): ActionResult
    {
        $result = CreateProductPropertyFollowUpAction::make(null, array_merge(
            $request->all(),
            ['property_id' => $property->getKey()]
        ))->run();

        if ($result->isFailure()) {
            return ActionResult::failure($result->getMessage(), $result->getErrors());
        }

        return ActionResult::success($result->getData(), $result->getMessage());
    }

    /**
     * Get a single follow-up for a property.
     * GET /api/product-property/{property}/followups/{followupId}
     */
    public function showFollowUp(BixoProductProperties $property, string $followupEid): ActionResult
    {
        $followUpId = hashids_decode($followupEid);

        if (! $followUpId) {
            return ActionResult::failure('Follow-up not found');
        }

        $followUp = BixoSchedulesFollowUp::with('createdBy')
            ->where('id', $followUpId)
            ->where('property_id', $property->getKey())
            ->first();

        if (! $followUp) {
            return ActionResult::failure('Follow-up not found');
        }

        $details = $followUp->details ? json_decode($followUp->details, true) : [];

        return ActionResult::success([
            'eid' => $followUp->eid,
            'property_id' => $property->eid,
            'followup_title' => $followUp->title,
            'followup_date' => $followUp->start_date?->toISOString(),
            'followup_date_formatted' => $followUp->start_date?->format('d M Y H:i'),
            'followup_date_day' => $followUp->start_date?->format('j'),
            'followup_date_month' => $followUp->start_date ? strtoupper($followUp->start_date->format('M')) : null,
            'followup_type' => $followUp->type,
            'followup_type_label' => FollowUpType::tryFrom($followUp->type)?->label(),
            'description' => $followUp->description,
            'send_reminder' => $details['send_reminder'] ?? false,
            'status' => $followUp->status?->value,
            'created_by' => $followUp->created_by,
            'created_by_name' => $followUp->createdBy?->name,
            'created_at' => $followUp->created_at?->toISOString(),
            'updated_at' => $followUp->updated_at?->toISOString(),
        ]);
    }

    /**
     * Update a follow-up for a property.
     * PUT /api/product-property/{property}/followups/{followupId}
     */
    public function updateFollowUp(BixoProductProperties $property, string $followupEid, Request $request): ActionResult
    {
        $result = UpdateProductPropertyFollowUpAction::make(null, array_merge(
            $request->except(['_method', '_token']),
            [
                'property_id' => $property->getKey(),
                'followup_eid' => $followupEid,
            ]
        ))->run();

        if ($result->isFailure()) {
            return ActionResult::failure($result->getMessage(), $result->getErrors());
        }

        return ActionResult::success($result->getData(), $result->getMessage());
    }

    /**
     * Delete a follow-up for a property.
     * DELETE /api/product-property/{property}/followups/{followupId}
     */
    public function deleteFollowUp(BixoProductProperties $property, string $followupEid): ActionResult
    {
        $result = DeleteProductPropertyFollowUpAction::make(null, [
            'property_id' => $property->getKey(),
            'followup_eid' => $followupEid,
        ])->run();

        if ($result->isFailure()) {
            return ActionResult::failure($result->getMessage());
        }

        return ActionResult::success(null, $result->getMessage());
    }

    /**
     * List all notes for a property.
     * GET /api/product-property/{property}/notes
     */
    public function listNotes(BixoProductProperties $property, Request $request): ActionResult
    {
        $result = ListProductPropertyNotesAction::make(null, array_merge(
            $request->all(),
            ['property_id' => $property->getKey()]
        ))->run();

        if ($result->isFailure()) {
            return ActionResult::failure($result->getMessage());
        }

        return $result;
    }

    /**
     * Create a note for a property.
     * POST /api/product-property/{property}/notes
     */
    public function createNote(BixoProductProperties $property, Request $request): ActionResult
    {
        $result = CreateProductPropertyNoteAction::make(null, array_merge(
            $request->all(),
            ['property_id' => $property->getKey()]
        ))->run();

        if ($result->isFailure()) {
            return ActionResult::failure($result->getMessage(), $result->getErrors());
        }

        return ActionResult::success($result->getData(), $result->getMessage());
    }

    /**
     * Update a note for a property.
     * PATCH /api/product-property/{property}/notes/{noteId}
     */
    public function updateNote(BixoProductProperties $property, string $noteEid, Request $request): ActionResult
    {
        $result = UpdateProductPropertyNoteAction::make(null, array_merge(
            $request->all(),
            ['property_id' => $property->getKey(), 'note_eid' => $noteEid]
        ))->run();

        if ($result->isFailure()) {
            return ActionResult::failure($result->getMessage(), $result->getErrors());
        }

        return ActionResult::success($result->getData(), $result->getMessage());
    }

    /**
     * Delete a note for a property.
     * DELETE /api/product-property/{property}/notes/{noteId}
     */
    public function deleteNote(BixoProductProperties $property, string $noteEid): ActionResult
    {
        $result = DeleteProductPropertyNoteAction::make(null, [
            'property_id' => $property->getKey(),
            'note_eid' => $noteEid,
        ])->run();

        if ($result->isFailure()) {
            return ActionResult::failure($result->getMessage());
        }

        return ActionResult::success(null, $result->getMessage());
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
