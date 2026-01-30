<?php

namespace App\Actions\Blog;

use App\Models\Blog;
use Litepie\Actions\BaseAction;
use Litepie\Actions\ActionResult;

/**
 * DeleteBlogAction
 * 
 * Deletes a blog post by ID (numeric or encoded hashid)
 * 
 * IMPORTANT: The 'id' parameter can be:
 * - Numeric ID: Direct blog table ID (e.g., 1, 42, 123)
 * - Encoded Hashid: Encoded blog table ID (e.g., "jR", "9x", "YEz")
 * 
 * Hashids are ALWAYS decoded to the blog table's 'id' column only.
 * No fallback to blog_id or slug when using hashids.
 * 
 * Supports soft delete by changing status to 'trash'
 * 
 * @package App\Actions\Blog
 */
class DeleteBlogAction extends BaseAction
{
    protected function rules(): array
    {
        return [
            'id' => 'required',
            'soft_delete' => 'sometimes|boolean',
        ];
    }

    public function handle(): ActionResult
    {
        $id = $this->data['id'];
        $softDelete = $this->data['soft_delete'] ?? true;
        $numericId = $id;

        // Decode hashid to get the numeric blog table ID
        if (is_string($id) && !is_numeric($id)) {
            try {
                $decoded = hashids_decode($id);
                if ($decoded && is_numeric($decoded)) {
                    // Hashid successfully decoded - use it as the blog.id
                    $numericId = $decoded;
                } else {
                    // Invalid hashid
                    return ActionResult::failure('Invalid blog ID format', [], 400);
                }
            } catch (\Exception $e) {
                return ActionResult::failure('Invalid blog ID format', [], 400);
            }
        }

        // Find by numeric ID only (from blog table's id column)
        $blog = Blog::find($numericId);

        if (!$blog) {
            return ActionResult::failure('Blog post not found', [], 404);
        }

        try {
            if ($softDelete) {
                $blog->delete(); // Soft delete using SoftDeletes trait
                $message = 'Blog post moved to trash';
            } else {
                $blog->forceDelete(); // Permanent delete
                $message = 'Blog post deleted permanently';
            }

            return ActionResult::success([], $message);
        } catch (\Exception $e) {
            return ActionResult::failure('Failed to delete blog post: ' . $e->getMessage(), [], 500);
        }
    }
}
