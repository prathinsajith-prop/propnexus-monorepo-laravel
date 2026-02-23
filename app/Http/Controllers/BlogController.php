<?php

namespace App\Http\Controllers;

use App\Actions\Blog\CreateBlogAction;
use App\Actions\Blog\DeleteBlogAction;
use App\Actions\Blog\ListBlogsAction;
use App\Actions\Blog\UpdateBlogAction;
use App\Actions\File\FileUploadAction;
use App\Layouts\BlogLayout;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Number;
use Litepie\Actions\ActionResult;

/**
 * BlogController
 *
 * Blog management controller using Litepie Actions pattern
 * All CRUD operations delegated to dedicated Action classes
 *
 * Endpoints:
 * - GET  /blogs           - Blog management page (layout)
 * - GET  /api/blogs       - List blogs (with filters, pagination)
 * - POST /api/blogs       - Create new blog
 * - GET  /api/blogs/{id}  - Get single blog
 * - PUT  /api/blogs/{id}  - Update blog
 * - DELETE /api/blogs/{id} - Delete blog
 */
class BlogController extends Controller
{
    /**
     * Display blog management page
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('blog.index');
    }

    /**
     * Get blog layout configuration
     * Returns complete layout structure for frontend rendering
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function blog()
    {
        $masterData = $this->getMasterData();
        $layout = BlogLayout::make($masterData);

        return response()->layout($layout);
    }

    /**
     * Get component section data by type and component name
     * Returns the specific section configuration for modals, drawers, etc.
     * Used by: /layouts/blogs/{type}/{component}
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

        // Build the section data based on type and component using BlogLayout
        $sectionData = BlogLayout::getComponentDefinition($type, $component, $masterData);

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
     * List blogs with filtering, sorting, and pagination
     * Uses ListBlogsAction
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function lists(Request $request)
    {
        return ListBlogsAction::make(null, $request->all())->run();
    }

    /**
     * List blogs with filtering, sorting, and pagination
     * Uses ListBlogsAction with structured filter format
     *
     * Query parameters:
     * - filter: Structured filter string (e.g., status:EQ(published);category:IN(Technology,Science))
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
        return ListBlogsAction::make(null, $request->all())->run();
    }

    /**
     * Create a new blog post
     * Uses CreateBlogAction
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $result = CreateBlogAction::make(null, $request->all())->run();
        $blog = $result->getData();

        return ActionResult::success(
            array_merge($blog->toArray(), [
                '_settings' => $blog->getSettings('create'),
                '_masterdatas' => $blog->getMasterdata(),
            ]),
            $result->getMessage()
        );
    }

    /**
     * Get a single blog post by ID
     * Uses route model binding directly
     *
     * @param  \App\Models\Blog  $blog  Blog model instance (auto-injected)
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Blog $blog, Request $request)
    {
        // Increment view count if requested
        if ($request->input('increment_views', false)) {
            $blog->increment('views_count');
            $blog->refresh();

            // Clear stats cache when views are incremented
            cache()->forget('blogs:stats:all');
        }

        $context = $request->boolean('edit') ? 'edit' : 'view';

        return ActionResult::success(array_merge($blog->toArray(), [
            '_settings' => $blog->getSettings($context),
            '_masterdatas' => $blog->getMasterdata(),
        ]));
    }

    /**
     * Update an existing blog post
     * Uses UpdateBlogAction with route model binding
     *
     * @param  \App\Models\Blog  $blog  Blog model instance (auto-injected)
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Blog $blog, Request $request)
    {
        $updateData = array_merge($request->all(), ['id' => $blog->id]);
        $result = UpdateBlogAction::make(null, $updateData)->run();
        $blog->refresh();

        return ActionResult::success(
            array_merge($blog->toArray(), [
                '_settings' => $blog->getSettings('edit'),
                '_masterdatas' => $blog->getMasterdata(),
            ]),
            $result->getMessage()
        );
    }

    /**
     * Delete a blog post
     * Uses DeleteBlogAction with route model binding
     *
     * @param  \App\Models\Blog  $blog  Blog model instance (auto-injected)
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Blog $blog, Request $request)
    {
        $deleteResult = DeleteBlogAction::make(null, [
            'id' => $blog->id,
            'soft_delete' => $request->input('soft_delete', false),
        ])->run();

        return ActionResult::success(
            array_merge($blog->toArray(), [
                '_settings' => $blog->getSettings('view'),
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
                "blogs:stats:{$statId}",
                config('performance.cache.stats_ttl', 300),
                function () use ($statId) {
                    return match ($statId) {
                        'stat-total-posts' => [
                            'value' => Number::abbreviate(Blog::count()),
                            'trend' => $this->calculateTrend(Blog::query()),
                            'trendDirection' => 'up',
                        ],
                        'stat-published' => [
                            'value' => Number::abbreviate(Blog::published()->count()),
                            'trend' => $this->calculateTrend(Blog::published()),
                            'trendDirection' => 'up',
                        ],
                        'stat-drafts' => [
                            'value' => Number::abbreviate(Blog::where('status', 'draft')->count()),
                            'trend' => $this->calculateTrend(Blog::where('status', 'draft')),
                            'trendDirection' => 'neutral',
                        ],
                        'stat-total-views' => [
                            'value' => Number::abbreviate(Blog::sum('views_count')),
                            'trend' => $this->calculateTrend(Blog::query(), 'views_count'),
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
     * Increment view count for a blog post
     * Uses route model binding directly
     *
     * @param  \App\Models\Blog  $blog  Blog model instance (auto-injected)
     * @return \Illuminate\Http\JsonResponse
     */
    public function incrementView(Blog $blog)
    {
        $blog->increment('views_count');

        // Clear stats cache
        cache()->forget('blogs:stats:all');

        return response()->json([
            'success' => true,
            'views' => $blog->views_count,
        ]);
    }

    /**
     * Get master data endpoint
     * Returns all master data for forms and filters
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function masterData()
    {
        $masterData = $this->getMasterData();

        return response()->json([
            'success' => true,
            'data' => $masterData,
        ]);
    }

    /**
     * Get master data for dropdowns and forms
     * Cached for better performance as this data changes infrequently
     */
    private function getMasterData(): array
    {
        return cache()->remember(
            'blogs:master-data',
            config('performance.cache.master_data_ttl', 1800),
            function () {
                return [
                    'statuses' => [
                        ['value' => 'draft', 'label' => 'Draft'],
                        ['value' => 'review', 'label' => 'In Review'],
                        ['value' => 'published', 'label' => 'Published'],
                        ['value' => 'archived', 'label' => 'Archived'],
                        ['value' => 'trash', 'label' => 'Trash'],
                    ],
                    'visibilities' => [
                        ['value' => 'public', 'label' => 'Public'],
                        ['value' => 'private', 'label' => 'Private'],
                        ['value' => 'password', 'label' => 'Password Protected'],
                    ],
                    'categories' => [
                        ['value' => 'Technology', 'label' => 'Technology'],
                        ['value' => 'Business', 'label' => 'Business'],
                        ['value' => 'Lifestyle', 'label' => 'Lifestyle'],
                        ['value' => 'Education', 'label' => 'Education'],
                        ['value' => 'Health', 'label' => 'Health'],
                        ['value' => 'Travel', 'label' => 'Travel'],
                        ['value' => 'Food', 'label' => 'Food'],
                        ['value' => 'Science', 'label' => 'Science'],
                        ['value' => 'Entertainment', 'label' => 'Entertainment'],
                        ['value' => 'Sports', 'label' => 'Sports'],
                    ],
                    'tags' => [
                        ['value' => 'Tutorial', 'label' => 'Tutorial'],
                        ['value' => 'Guide', 'label' => 'Guide'],
                        ['value' => 'News', 'label' => 'News'],
                        ['value' => 'Opinion', 'label' => 'Opinion'],
                        ['value' => 'Review', 'label' => 'Review'],
                        ['value' => 'Comparison', 'label' => 'Comparison'],
                        ['value' => 'Tips', 'label' => 'Tips'],
                        ['value' => 'Howto', 'label' => 'How-to'],
                    ],
                    'languages' => [
                        ['value' => 'en', 'label' => 'English'],
                        ['value' => 'es', 'label' => 'Spanish'],
                        ['value' => 'fr', 'label' => 'French'],
                        ['value' => 'de', 'label' => 'German'],
                        ['value' => 'it', 'label' => 'Italian'],
                        ['value' => 'pt', 'label' => 'Portuguese'],
                        ['value' => 'ja', 'label' => 'Japanese'],
                        ['value' => 'zh', 'label' => 'Chinese'],
                    ],
                    'authors' => [
                        ['value' => '1', 'label' => 'John Doe'],
                        ['value' => '2', 'label' => 'Jane Smith'],
                        ['value' => '3', 'label' => 'Bob Johnson'],
                        ['value' => '4', 'label' => 'Alice Williams'],
                    ],
                    'keywords' => [
                        ['value' => 'SEO', 'label' => 'SEO'],
                        ['value' => 'Marketing', 'label' => 'Marketing'],
                        ['value' => 'Content', 'label' => 'Content'],
                        ['value' => 'Strategy', 'label' => 'Strategy'],
                        ['value' => 'Digital', 'label' => 'Digital'],
                        ['value' => 'Analytics', 'label' => 'Analytics'],
                        ['value' => 'Growth', 'label' => 'Growth'],
                        ['value' => 'Optimization', 'label' => 'Optimization'],
                        ['value' => 'Best Practices', 'label' => 'Best Practices'],
                        ['value' => 'Web Development', 'label' => 'Web Development'],
                    ],
                ];
            }
        );
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
     * Upload a video file
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadVideo(Request $request)
    {
        return $this->handleUpload($request, 'video');
    }

    /**
     * Upload a document file
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadDocument(Request $request)
    {
        return $this->handleUpload($request, 'document');
    }

    /**
     * Upload an audio file
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadAudio(Request $request)
    {
        return $this->handleUpload($request, 'audio');
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
            'folder' => $request->input('folder'),
            'generate_thumbnail' => $request->input('generate_thumbnail', false),
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
     */
    private function getMaxFileSize(string $type): int
    {
        return match ($type) {
            'image' => 10240, // 10MB
            'video' => 102400, // 100MB
            'document' => 20480, // 20MB
            'audio' => 51200, // 50MB
            'attachment' => 51200, // 50MB
            default => 10240,
        };
    }
}
