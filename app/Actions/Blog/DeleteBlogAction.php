<?php

namespace App\Actions\Blog;

use App\Models\Blog;
use Litepie\Actions\BaseAction;
use Litepie\Actions\ActionResult;

/**
 * DeleteBlogAction
 * 
 * Deletes a blog post from storage
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

        // Find the blog
        $blog = Blog::where('id', $id)
            ->orWhere('blog_id', $id)
            ->orWhere('slug', $id)
            ->first();

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
