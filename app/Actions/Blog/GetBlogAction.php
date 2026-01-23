<?php

namespace App\Actions\Blog;

use App\Models\Blog;
use Litepie\Actions\BaseAction;
use Litepie\Actions\ActionResult;

/**
 * GetBlogAction
 * 
 * Retrieves a single blog post by ID, blog_id, or slug
 * Optionally increments view count
 * 
 * @package App\Actions\Blog
 */
class GetBlogAction extends BaseAction
{
    protected function rules(): array
    {
        return [
            'id' => 'required',
            'increment_views' => 'sometimes|boolean',
        ];
    }

    public function handle(): ActionResult
    {
        $id = $this->data['id'];

        // Search by id, blog_id, or slug
        $blog = Blog::where('id', $id)
            ->orWhere('blog_id', $id)
            ->orWhere('slug', $id)
            ->first();

        if (!$blog) {
            return ActionResult::failure('Blog post not found', [], 404);
        }

        // Increment view count if requested
        if ($this->data['increment_views'] ?? false) {
            $blog->incrementViews();
        }

        return ActionResult::success($blog->toArray());
    }
}
