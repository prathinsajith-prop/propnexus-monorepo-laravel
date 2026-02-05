<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Layouts\ListingLayout;
use App\Actions\Listing\ListListingsAction;
use App\Actions\Listing\CreateListingAction;
use App\Actions\Listing\GetListingAction;
use App\Actions\Listing\UpdateListingAction;
use App\Actions\Listing\DeleteListingAction;
use App\Actions\File\FileUploadAction;
use App\Forms\Listing\ListingForm;
use App\Models\Listing;
use App\Models\User;
use App\Support\Settings\ListingSettings;
use App\Enums\PropertyType;
use App\Enums\ListingType;
use App\Enums\ListingStatus;
use App\Enums\Availability;
use Illuminate\Support\Facades\Storage;

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
 * 
 * @package App\Http\Controllers
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
     * @param Request $request
     * @param string|null $type
     * @param string|null $component
     * @return \Illuminate\Http\JsonResponse
     */
    public function getComponentSection(Request $request, $type = null, $component = null)
    {
        // Support both route parameters and query parameters
        $type = $type ?? $request->input('type');
        $component = $component ?? $request->input('component');

        if (!$type || !$component) {
            return response()->json([
                'error' => 'Missing required parameters: type and component',
            ], 400);
        }

        // Get master data for options
        $masterData = $this->getMasterData();

        // Build the section data based on type and component using ListingLayout
        $sectionData = ListingLayout::getComponentDefinition($type, $component, $masterData);

        if (!$sectionData) {
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
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function list(Request $request)
    {
        $action = new ListListingsAction(new Listing(), $request->all());
        $result = $action->execute();

        if ($result->isSuccess()) {
            return response()->json([
                'success' => true,
                'data' => $result->getData()['data'],
                'pagination' => $result->getData()['pagination'],
                'meta' => $result->getData()['meta'],
            ]);
        }

        return response()->json([
            'success' => false,
            'error' => $result->message,
        ], 400);
    }

    /**
     * Create a new listing
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        $action = new CreateListingAction(new Listing(), $request->all());
        $result = $action->execute();

        if ($result->isSuccess()) {
            return response()->json([
                'success' => true,
                'data' => $result->getData()['data'],
                'message' => $result->getData()['message'] ?? 'Listing created successfully',
            ], 201);
        }

        return response()->json([
            'success' => false,
            'error' => $result->getMessage(),
            'errors' => $result->getData()['errors'] ?? [],
        ], 422);
    }

    /**
     * Get a single listing
     * Uses route model binding
     *
     * @param \App\Models\Listing $listing Listing model instance (auto-injected)
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Listing $listing)
    {
        $action = new GetListingAction(new Listing(), ['id' => $listing->id, 'increment_views' => true]);
        $result = $action->execute();

        if ($result->isSuccess()) {
            $data = $result->getData()['data'] ?: [];
            $data['_settings'] = ListingSettings::forView();

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        }

        return response()->json([
            'success' => false,
            'error' => $result->getMessage(),
        ], 404);
    }

    /**
     * Update an existing listing
     * Uses route model binding
     *
     * @param \App\Models\Listing $listing Listing model instance (auto-injected)
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Listing $listing, Request $request)
    {
        $data = array_merge($request->all(), ['id' => $listing->id]);
        $action = new UpdateListingAction(new Listing(), $data);
        $result = $action->execute();

        if ($result->isSuccess()) {
            return response()->json([
                'success' => true,
                'data' => $result->getData()['data'],
                'message' => $result->getData()['message'] ?? 'Listing updated successfully',
            ]);
        }

        return response()->json([
            'success' => false,
            'error' => $result->getMessage(),
            'errors' => $result->getData()['errors'] ?? [],
        ], 422);
    }

    /**
     * Delete a listing
     * Uses route model binding
     *
     * @param \App\Models\Listing $listing Listing model instance (auto-injected)
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Listing $listing)
    {
        $action = new DeleteListingAction(new Listing(), ['id' => $listing->id]);
        $result = $action->execute();

        if ($result->isSuccess()) {
            return response()->json([
                'success' => true,
                'message' => $result->getData()['message'] ?? 'Listing deleted successfully',
            ]);
        }

        return response()->json([
            'success' => false,
            'error' => $result->getMessage(),
        ], 400);
    }

    /**
     * Get statistics for a specific stat card
     * Cached for better performance
     *
     * @param string $statId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStats($statId)
    {
        $stats = cache()->remember(
            "listings:stats:{$statId}",
            config('performance.cache.stats_ttl', 300),
            function () use ($statId) {
                return match ($statId) {
                    'stat-total-listings' => [
                        'value' => Listing::count(),
                        'trend' => '+' . rand(5, 15) . '%',
                        'trendDirection' => 'up',
                    ],
                    'stat-active' => [
                        'value' => Listing::where('status', 'active')->count(),
                        'trend' => '+' . rand(3, 10) . '%',
                        'trendDirection' => 'up',
                    ],
                    'stat-sold' => [
                        'value' => Listing::whereIn('status', ['sold', 'rented'])->count(),
                        'trend' => '+' . rand(2, 8) . '%',
                        'trendDirection' => 'up',
                    ],
                    'stat-total-views' => [
                        'value' => Listing::sum('views_count'),
                        'trend' => '+' . rand(10, 25) . '%',
                        'trendDirection' => 'up',
                    ],
                    default => [
                        'value' => 0,
                        'trend' => '0%',
                        'trendDirection' => 'neutral',
                    ],
                };
            }
        );

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
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
     *
     * @return array
     */
    private function getMasterData(): array
    {
        return cache()->remember(
            'listings:master-data',
            config('performance.cache.master_data_ttl', 1800),
            function () {
                return [
                    'property_types' => collect(PropertyType::cases())->map(fn($case) => [
                        'value' => $case->value,
                        'label' => $case->label(),
                        'icon' => $case->icon(),
                        'color' => $case->color(),
                    ])->keyBy('value')->toArray(),
                    'listing_types' => collect(ListingType::cases())->map(fn($case) => [
                        'value' => $case->value,
                        'label' => $case->label(),
                        'icon' => $case->icon(),
                        'color' => $case->color(),
                    ])->keyBy('value')->toArray(),
                    'statuses' => collect(ListingStatus::cases())->map(fn($case) => [
                        'value' => $case->value,
                        'label' => $case->label(),
                        'icon' => $case->icon(),
                        'color' => $case->color(),
                    ])->keyBy('value')->toArray(),
                    'availabilities' => collect(Availability::cases())->map(fn($case) => [
                        'value' => $case->value,
                        'label' => $case->label(),
                        'icon' => $case->icon(),
                        'color' => $case->color(),
                    ])->keyBy('value')->toArray(),

                    'currencies' => [
                        'AED' => 'AED',
                        'USD' => 'USD',
                        'EUR' => 'EUR',
                        'GBP' => 'GBP',
                    ],
                    'furnishing_statuses' => [
                        'unfurnished' => 'Unfurnished',
                        'semi-furnished' => 'Semi-Furnished',
                        'fully-furnished' => 'Fully-Furnished',
                    ],
                    'cities' => [
                        'Dubai' => 'Dubai',
                        'Abu Dhabi' => 'Abu Dhabi',
                        'Sharjah' => 'Sharjah',
                        'Ajman' => 'Ajman',
                        'Ras Al Khaimah' => 'Ras Al Khaimah',
                        'Fujairah' => 'Fujairah',
                        'Umm Al Quwain' => 'Umm Al Quwain',
                    ],
                    'areas' => [
                        'Dubai Marina' => 'Dubai Marina',
                        'Downtown Dubai' => 'Downtown Dubai',
                        'Palm Jumeirah' => 'Palm Jumeirah',
                        'Business Bay' => 'Business Bay',
                        'JBR' => 'JBR',
                        'Arabian Ranches' => 'Arabian Ranches',
                        'Dubai Hills' => 'Dubai Hills',
                        'City Walk' => 'City Walk',
                        'Al Barsha' => 'Al Barsha',
                        'Jumeirah' => 'Jumeirah',
                    ],
                    'agents' => User::select('id', 'name')
                        ->get()
                        ->mapWithKeys(fn($user) => [$user->id => $user->name])
                        ->toArray(),
                ];
            }
        );
    }

    /**
     * Upload an image file
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadImage(Request $request)
    {
        return $this->handleUpload($request, 'image');
    }

    /**
     * Upload a document file (floor plans, brochures)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadDocument(Request $request)
    {
        return $this->handleUpload($request, 'document');
    }

    /**
     * Upload a video file
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadVideo(Request $request)
    {
        return $this->handleUpload($request, 'video');
    }

    /**
     * Upload an attachment file
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadAttachment(Request $request)
    {
        return $this->handleUpload($request, 'attachment');
    }

    /**
     * Generic file upload
     *
     * @param Request $request
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
     * @param Request $request
     * @param string $type File type (image, video, document, attachment)
     * @return \Illuminate\Http\JsonResponse
     */
    private function handleUpload(Request $request, string $type)
    {
        $request->validate([
            'file' => 'required|file|max:' . $this->getMaxFileSize($type),
        ]);

        $result = FileUploadAction::make(null, [
            'file' => $request->file('file'),
            'type' => $type,
            'disk' => $request->input('disk', 'public'),
            'folder' => $request->input('folder', 'listings'),
            'generate_thumbnail' => $request->input('generate_thumbnail', $type === 'image'),
            'resize' => $request->input('resize', []),
            'quality' => $request->input('quality', 85),
        ])->run();

        if (!$result->isSuccess()) {
            return response()->json([
                'success' => false,
                'message' => $result->getMessage(),
                'errors' => $result->getErrors(),
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => $result->getMessage(),
            'data' => $result->getData(),
        ], 201);
    }

    /**
     * Delete a file
     *
     * @param string $path File path to delete
     * @param Request $request
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
                'message' => 'Failed to delete file: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get maximum file size for upload type (in KB)
     *
     * @param string $type File type
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
