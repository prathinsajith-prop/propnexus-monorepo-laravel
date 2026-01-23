<?php

namespace App\Actions\Blog;

use App\Models\Blog;
use Litepie\Actions\BaseAction;
use Litepie\Actions\ActionResult;

/**
 * ListBlogsAction
 * 
 * Handles listing blog posts with advanced filtering, sorting, and pagination
 * Supports search, category/tag filtering, status filtering, and date range queries
 * 
 * @package App\Actions\Blog
 */
class ListBlogsAction extends BaseAction
{
    protected function rules(): array
    {
        return [
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'sort_by' => 'sometimes|string',
            'sort_direction' => 'sometimes|in:asc,desc',
            'search' => 'sometimes|string',
            'filter_blog_id' => 'sometimes|string',
            'filter_title' => 'sometimes|string',
            'filter_status' => 'sometimes|string',
            'filter_category' => 'sometimes|string',
            'filter_tag' => 'sometimes|string',
            'filter_author_id' => 'sometimes|integer',
            'filter_language' => 'sometimes|string',
            'filter_is_featured' => 'sometimes|boolean',
            'filter_is_sticky' => 'sometimes|boolean',
            'filter_visibility' => 'sometimes|string',
            'filter_date_from' => 'sometimes|date',
            'filter_date_to' => 'sometimes|date',
        ];
    }

    public function handle(): ActionResult
    {
        $query = Blog::query();

        // Apply search
        if (!empty($this->data['search'])) {
            $search = $this->data['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('excerpt', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%")
                    ->orWhere('blog_id', 'like', "%{$search}%")
                    ->orWhereJsonContains('tags', $search);
            });
        }

        // Apply filters
        if (!empty($this->data['filter_blog_id'])) {
            $query->where('blog_id', 'like', '%' . $this->data['filter_blog_id'] . '%');
        }

        if (!empty($this->data['filter_title'])) {
            $query->where('title', 'like', '%' . $this->data['filter_title'] . '%');
        }

        if (!empty($this->data['filter_status'])) {
            $query->where('status', $this->data['filter_status']);
        }

        if (!empty($this->data['filter_category'])) {
            $query->where(function ($q) {
                $category = $this->data['filter_category'];
                $q->where('category', $category)
                    ->orWhereJsonContains('categories', $category);
            });
        }

        if (!empty($this->data['filter_tag'])) {
            $query->whereJsonContains('tags', $this->data['filter_tag']);
        }

        if (!empty($this->data['filter_author_id'])) {
            $query->where('author_id', $this->data['filter_author_id']);
        }

        if (!empty($this->data['filter_language'])) {
            $query->where('language', $this->data['filter_language']);
        }

        if (isset($this->data['filter_is_featured'])) {
            $query->where('is_featured', (bool)$this->data['filter_is_featured']);
        }

        if (isset($this->data['filter_is_sticky'])) {
            $query->where('is_sticky', (bool)$this->data['filter_is_sticky']);
        }

        if (!empty($this->data['filter_visibility'])) {
            $query->where('visibility', $this->data['filter_visibility']);
        }

        // Date range filters
        if (!empty($this->data['filter_date_from'])) {
            $query->where('created_at', '>=', $this->data['filter_date_from']);
        }

        if (!empty($this->data['filter_date_to'])) {
            $query->where('created_at', '<=', $this->data['filter_date_to']);
        }

        // Apply sorting
        $sortBy = $this->data['sort_by'] ?? 'created_at';
        $sortDirection = $this->data['sort_direction'] ?? 'desc';
        $query->orderBy($sortBy, $sortDirection);

        // Apply pagination
        $perPage = $this->data['per_page'] ?? 10;
        $page = $this->data['page'] ?? 1;

        $total = $query->count();
        $blogs = $query->skip(($page - 1) * $perPage)->take($perPage)->get();

        return ActionResult::success([
            'data' => $blogs->toArray(),
            'meta' => [
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => ceil($total / $perPage),
                'from' => $total > 0 ? (($page - 1) * $perPage) + 1 : 0,
                'to' => $total > 0 ? min($page * $perPage, $total) : 0,
            ],
        ]);
    }
}
