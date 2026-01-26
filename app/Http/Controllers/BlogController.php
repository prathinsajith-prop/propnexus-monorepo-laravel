<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Layouts\BlogLayout;
use App\Actions\Blog\ListBlogsAction;
use App\Actions\Blog\CreateBlogAction;
use App\Actions\Blog\GetBlogAction;
use App\Actions\Blog\UpdateBlogAction;
use App\Actions\Blog\DeleteBlogAction;
use App\Actions\File\FileUploadAction;
use App\Models\Blog;
use Illuminate\Support\Facades\Storage;

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
 * 
 * @package App\Http\Controllers
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

        // Build the section data based on type and component using BlogLayout
        $sectionData = BlogLayout::getComponentDefinition($type, $component, $masterData);

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
     * List blogs with filtering, sorting, and pagination
     * Uses ListBlogsAction
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function lists(Request $request)
    {
        $result = ListBlogsAction::make(null, $request->all())->run();

        if (!$result->isSuccess()) {
            return response()->json([
                'success' => false,
                'message' => $result->getMessage(),
                'errors' => $result->getErrors(),
            ], $result->getData()['code'] ?? 400);
        }

        return response()->json([
            'success' => true,
            'data' => $result->getData()['data'],
            'meta' => $result->getData()['meta'],
        ]);
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
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function list(Request $request)
    {
        $result = ListBlogsAction::make(null, $request->all())->run();
        if (!$result->isSuccess()) {
            return response()->json([
                'success' => false,
                'message' => $result->getMessage(),
                'errors' => $result->getErrors(),
            ], $result->getData()['code'] ?? 400);
        }

        return response()->json([
            'success' => true,
            'data' => $result->getData()['data'] ?? [],
            'meta' => $result->getData()['meta'] ?? [],
        ]);
    }

    /**
     * Create a new blog post
     * Uses CreateBlogAction
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $result = CreateBlogAction::make(null, $request->all())->run();

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
     * Get a single blog post by ID
     * Uses GetBlogAction
     *
     * @param string $id Blog ID, blog_id, or slug
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id, Request $request)
    {
        $result = GetBlogAction::make(null, [
            'id' => $id,
            'increment_views' => $request->input('increment_views', false),
        ])->run();

        if (!$result->isSuccess()) {
            return response()->json([
                'success' => false,
                'message' => $result->getMessage(),
                'errors' => $result->getErrors(),
            ], $result->getData()['code'] ?? 404);
        }

        return response()->json([
            'success' => true,
            'data' => $result->getData(),
        ]);
    }

    /**
     * Update an existing blog post
     * Uses UpdateBlogAction
     *
     * @param string $id Blog ID, blog_id, or slug
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id, Request $request)
    {
        $data = array_merge($request->all(), ['id' => $id]);
        $result = UpdateBlogAction::make(null, $data)->run();

        if (!$result->isSuccess()) {
            return response()->json([
                'success' => false,
                'message' => $result->getMessage(),
                'errors' => $result->getErrors(),
            ], $result->getData()['code'] ?? 400);
        }

        return response()->json([
            'success' => true,
            'message' => $result->getMessage(),
            'data' => $result->getData(),
        ]);
    }

    /**
     * Delete a blog post
     * Uses DeleteBlogAction
     *
     * @param string $id Blog ID, blog_id, or slug
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id, Request $request)
    {
        $result = DeleteBlogAction::make(null, [
            'id' => $id,
            'soft_delete' => $request->input('soft_delete', false),
        ])->run();

        if (!$result->isSuccess()) {
            return response()->json([
                'success' => false,
                'message' => $result->getMessage(),
                'errors' => $result->getErrors(),
            ], $result->getData()['code'] ?? 404);
        }

        return response()->json([
            'success' => true,
            'message' => $result->getMessage(),
        ]);
    }

    /**
     * Get statistics for blog posts
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function stats()
    {
        $stats = [
            'total' => \App\Models\Blog::count(),
            'published' => \App\Models\Blog::where('status', 'published')->count(),
            'drafts' => \App\Models\Blog::where('status', 'draft')->count(),
            'in_review' => \App\Models\Blog::where('status', 'review')->count(),
            'archived' => \App\Models\Blog::where('status', 'archived')->count(),
            'total_views' => \App\Models\Blog::sum('views_count'),
            'total_likes' => \App\Models\Blog::sum('likes_count'),
            'total_comments' => \App\Models\Blog::sum('comments_count'),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * Increment view count for a blog post
     *
     * @param string $id Blog ID, blog_id, or slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function incrementView($id)
    {
        $result = GetBlogAction::make(null, [
            'id' => $id,
            'increment_views' => true,
        ])->run();

        if (!$result->isSuccess()) {
            return response()->json([
                'success' => false,
                'message' => $result->getMessage(),
            ], 404);
        }

        return response()->json([
            'success' => true,
            'views' => $result->getData()['views_count'] ?? 0,
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
     * Get master data for dropdowns and filters
     *
     * @return array
     */
    private function getMasterData(): array
    {
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
        ];
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
     * Upload a document file
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadDocument(Request $request)
    {
        return $this->handleUpload($request, 'document');
    }

    /**
     * Upload an audio file
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadAudio(Request $request)
    {
        return $this->handleUpload($request, 'audio');
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
     * @param string $type
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
            'folder' => $request->input('folder'),
            'generate_thumbnail' => $request->input('generate_thumbnail', false),
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
     * @param string $path
     * @param Request $request
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
                'message' => 'Failed to delete file: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get maximum file size for upload type (in KB)
     *
     * @param string $type
     * @return int
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
