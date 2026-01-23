<?php

namespace App\Actions\Blog;

use App\Models\Blog;
use Litepie\Actions\BaseAction;
use Litepie\Actions\ActionResult;

/**
 * UpdateBlogAction
 * 
 * Updates an existing blog post
 * Handles slug regeneration and reading time recalculation if needed
 * Increments revision number
 * 
 * @package App\Actions\Blog
 */
class UpdateBlogAction extends BaseAction
{
    protected function rules(): array
    {
        return [
            'id' => 'required',
            'title' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|max:255',
            'excerpt' => 'nullable|string',
            'content' => 'sometimes|string',
            'status' => 'sometimes|in:draft,review,published,archived,trash',
            'visibility' => 'sometimes|in:public,private,password',
            'password' => 'nullable|string',
            'author_id' => 'sometimes|integer',
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
            'expired_at' => 'nullable|date',
            'custom_fields' => 'sometimes|array',
        ];
    }

    public function handle(): ActionResult
    {
        $id = $this->data['id'];
        unset($this->data['id']);

        // Find the blog
        $blog = Blog::where('id', $id)
            ->orWhere('blog_id', $id)
            ->orWhere('slug', $id)
            ->first();

        if (!$blog) {
            return ActionResult::failure('Blog post not found', [], 404);
        }

        try {
            $blog->update($this->data);
            $blog->refresh();

            return ActionResult::success($blog->toArray(), 'Blog post updated successfully');
        } catch (\Exception $e) {
            return ActionResult::failure('Failed to update blog post: ' . $e->getMessage(), [], 500);
        }
    }
}
