<?php

namespace App\Actions\Blog;

use App\Models\Blog;
use Litepie\Actions\BaseAction;
use Litepie\Actions\ActionResult;

/**
 * CreateBlogAction
 * 
 * Handles blog post creation with validation
 * Auto-generates blog_id, slug, and calculates reading time
 * 
 * @package App\Actions\Blog
 */
class CreateBlogAction extends BaseAction
{
    protected function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'slug' => 'sometimes|string|max:255',
            'excerpt' => 'nullable|string',
            'content' => 'required|string',
            'status' => 'sometimes|in:draft,review,published,archived,trash',
            'visibility' => 'sometimes|in:public,private,password',
            'password' => 'nullable|string',
            'author_id' => 'required|integer',
            'co_authors' => 'sometimes|array',
            'category' => 'nullable|string',
            'categories' => 'sometimes|array',
            'tags' => 'sometimes|array',
            'featured_image' => 'nullable|string',
            'gallery' => 'sometimes|array',
            'video_url' => 'nullable|url',
            'attachments' => 'sometimes|array',
            'language' => 'sometimes|string',
            'seo_meta' => 'sometimes|array',
            'is_featured' => 'sometimes|boolean',
            'is_sticky' => 'sometimes|boolean',
            'allow_comments' => 'sometimes|boolean',
            'published_at' => 'nullable|date',
            'scheduled_at' => 'nullable|date',
            'custom_fields' => 'sometimes|array',
        ];
    }

    public function handle(): ActionResult
    {
        try {
            $blog = Blog::create($this->data);

            return ActionResult::success($blog->toArray(), 'Blog post created successfully');
        } catch (\Exception $e) {
            return ActionResult::failure('Failed to create blog post: ' . $e->getMessage(), [], 500);
        }
    }
}
