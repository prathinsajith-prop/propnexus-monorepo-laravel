<?php

namespace App\Actions\Blog;

use App\Models\Blog;
use Litepie\Actions\ActionResult;
use Litepie\Actions\BaseAction;

/**
 * GetBlogAction
 *
 * Retrieves a single blog post by ID (numeric or encoded hashid)
 *
 * IMPORTANT: The 'id' parameter can be:
 * - Numeric ID: Direct blog table ID (e.g., 1, 42, 123)
 * - Encoded Hashid: Encoded blog table ID (e.g., "jR", "9x", "YEz")
 *
 * Hashids are ALWAYS decoded to the blog table's 'id' column only.
 * No fallback to blog_id or slug when using hashids.
 *
 * Optionally increments view count
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
        $numericId = $id;

        // Decode hashid to get the numeric blog table ID
        if (is_string($id) && ! is_numeric($id)) {
            try {
                $decoded = hashids_decode($id);
                if ($decoded && is_numeric($decoded)) {
                    // Hashid successfully decoded - use it as the blog.id
                    $numericId = $decoded;
                } else {
                    // Invalid hashid
                    return ActionResult::failure('Invalid blog ID format');
                }
            } catch (\Exception $e) {
                return ActionResult::failure('Invalid blog ID format');
            }
        }

        // Find by numeric ID only (from blog table's id column)
        $blog = Blog::find($numericId);

        if (! $blog) {
            return ActionResult::failure('Blog post not found');
        }

        // Increment view count if requested
        if ($this->data['increment_views'] ?? false) {
            $blog->incrementViews();
        }

        return ActionResult::success($blog->toArray());
    }
}
