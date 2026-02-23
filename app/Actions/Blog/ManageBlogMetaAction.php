<?php

namespace App\Actions\Blog;

use App\Models\Blog;
use Litepie\Actions\ActionResult;
use Litepie\Actions\BaseAction;

/**
 * ManageBlogMetaAction
 *
 * Manage blog metadata using the Metable trait
 * WordPress-style flexible key-value metadata storage
 */
class ManageBlogMetaAction extends BaseAction
{
    protected function rules(): array
    {
        return [
            'blog_id' => 'required|integer|exists:blogs,id',
            'action' => 'required|in:get,set,delete,increment,decrement,bulk_set',
            'meta_key' => 'required_unless:action,bulk_set|string',
            'meta_value' => 'required_if:action,set',
            'meta_data' => 'required_if:action,bulk_set|array',
            'amount' => 'sometimes|numeric',
        ];
    }

    public function handle(): ActionResult
    {
        $blog = Blog::findOrFail($this->data['blog_id']);
        $action = $this->data['action'];

        switch ($action) {
            case 'get':
                return $this->getMeta($blog);

            case 'set':
                return $this->setMeta($blog);

            case 'delete':
                return $this->deleteMeta($blog);

            case 'increment':
                return $this->incrementMeta($blog);

            case 'decrement':
                return $this->decrementMeta($blog);

            case 'bulk_set':
                return $this->bulkSetMeta($blog);

            default:
                return ActionResult::error('Invalid action', 400);
        }
    }

    protected function getMeta(Blog $blog): ActionResult
    {
        $key = $this->data['meta_key'];
        $value = $blog->getMeta($key);

        return ActionResult::success([
            'meta_key' => $key,
            'meta_value' => $value,
            'exists' => $blog->hasMeta($key),
        ]);
    }

    protected function setMeta(Blog $blog): ActionResult
    {
        $key = $this->data['meta_key'];
        $value = $this->data['meta_value'];

        $blog->setMeta($key, $value);

        return ActionResult::success([
            'message' => 'Meta value set successfully',
            'meta_key' => $key,
            'meta_value' => $value,
        ]);
    }

    protected function deleteMeta(Blog $blog): ActionResult
    {
        $key = $this->data['meta_key'];
        $blog->deleteMeta($key);

        return ActionResult::success([
            'message' => 'Meta value deleted successfully',
            'meta_key' => $key,
        ]);
    }

    protected function incrementMeta(Blog $blog): ActionResult
    {
        $key = $this->data['meta_key'];
        $amount = $this->data['amount'] ?? 1;

        $newValue = $blog->incrementMeta($key, $amount);

        return ActionResult::success([
            'message' => 'Meta value incremented successfully',
            'meta_key' => $key,
            'new_value' => $newValue,
        ]);
    }

    protected function decrementMeta(Blog $blog): ActionResult
    {
        $key = $this->data['meta_key'];
        $amount = $this->data['amount'] ?? 1;

        $newValue = $blog->decrementMeta($key, $amount);

        return ActionResult::success([
            'message' => 'Meta value decremented successfully',
            'meta_key' => $key,
            'new_value' => $newValue,
        ]);
    }

    protected function bulkSetMeta(Blog $blog): ActionResult
    {
        $metaData = $this->data['meta_data'];

        foreach ($metaData as $key => $value) {
            $blog->setMeta($key, $value);
        }

        return ActionResult::success([
            'message' => 'Bulk meta values set successfully',
            'count' => count($metaData),
        ]);
    }
}
