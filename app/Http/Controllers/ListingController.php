<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Layouts\ListingLayout;
use App\Actions\Listing\ListListingsAction;
use App\Actions\Listing\CreateListingAction;
use App\Actions\Listing\GetListingAction;
use App\Actions\Listing\UpdateListingAction;
use App\Actions\Listing\DeleteListingAction;
use App\Forms\Listing\ListingForm;
use App\Models\Listing;
use App\Models\User;
use App\Enums\PropertyType;
use App\Enums\ListingType;
use App\Enums\ListingStatus;
use App\Enums\Availability;

/**
 * ListingController
 * 
 * Property listing management controller using Litepie Actions pattern
 * All CRUD operations delegated to dedicated Action classes
 * 
 * Endpoints:
 * - GET  /listings           - Listing management page (layout)
 * - GET  /api/listings       - List listings (with filters, pagination)
 * - POST /api/listings       - Create new listing
 * - GET  /api/listings/{id}  - Get single listing
 * - PUT  /api/listings/{id}  - Update listing
 * - DELETE /api/listings/{id} - Delete listing
 * - GET  /api/listings/stats/{id} - Get statistics
 * - GET  /api/listings-master-data - Get master data for dropdowns
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
            return response()->json([
                'success' => true,
                'data' => $result->getData()['data'],
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
}
