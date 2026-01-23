<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Litepie\Database\Traits\Searchable;

/**
 * Blog Model
 * 
 * Comprehensive blog model with advanced features:
 * - SEO optimization (meta tags, keywords, schema)
 * - Multi-language support
 * - Content versioning
 * - Advanced categorization and tagging
 * - Author and co-author management
 * - Publishing workflow (draft, review, published, archived)
 * - Analytics tracking
 * - Featured content support
 * - Social media integration
 * - Content scheduling
 * 
 * @package App\Models
 * 
 * @property int $id
 * @property string $blog_id Unique blog identifier
 * @property string $title Blog title
 * @property string $slug URL-friendly slug
 * @property string $excerpt Short description
 * @property string $content Main blog content
 * @property string $status Publication status
 * @property string $visibility Public, private, or password protected
 * @property string|null $password Password for protected posts
 * @property int $author_id Primary author
 * @property array $co_authors Additional authors
 * @property string|null $category Primary category
 * @property array $categories All categories
 * @property array $tags Post tags
 * @property string|null $featured_image Featured image URL
 * @property array $gallery Image gallery
 * @property string|null $video_url Embedded video
 * @property array $attachments File attachments
 * @property string $language Content language
 * @property array $translations Translated versions
 * @property array $seo_meta SEO metadata
 * @property array $schema_markup Structured data
 * @property bool $is_featured Featured post flag
 * @property bool $is_sticky Sticky post flag
 * @property bool $allow_comments Comments enabled
 * @property int $comments_count Number of comments
 * @property int $views_count View counter
 * @property int $likes_count Like counter
 * @property int $shares_count Share counter
 * @property float $reading_time Estimated reading time in minutes
 * @property array $related_posts Related post IDs
 * @property \DateTime|null $published_at Publication date
 * @property \DateTime|null $scheduled_at Scheduled publication
 * @property \DateTime|null $expired_at Expiration date
 * @property \DateTime|null $last_edited_at Last edit timestamp
 * @property int|null $last_edited_by Last editor user ID
 * @property int $revision_number Content version number
 * @property array $custom_fields Additional metadata
 * @property array $analytics Analytics data
 * @property \DateTime $created_at
 * @property \DateTime $updated_at
 * @property \DateTime|null $deleted_at Soft delete timestamp
 */
class Blog extends Model
{
    use HasFactory, SoftDeletes, Searchable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'blogs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'blog_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'status',
        'visibility',
        'password',
        'author_id',
        'co_authors',
        'category',
        'categories',
        'tags',
        'featured_image',
        'gallery',
        'video_url',
        'attachments',
        'language',
        'translations',
        'seo_meta',
        'schema_markup',
        'is_featured',
        'is_sticky',
        'allow_comments',
        'comments_count',
        'views_count',
        'likes_count',
        'shares_count',
        'reading_time',
        'related_posts',
        'published_at',
        'scheduled_at',
        'expired_at',
        'last_edited_at',
        'last_edited_by',
        'revision_number',
        'custom_fields',
        'analytics',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'co_authors' => 'array',
        'categories' => 'array',
        'tags' => 'array',
        'gallery' => 'array',
        'attachments' => 'array',
        'translations' => 'array',
        'seo_meta' => 'array',
        'schema_markup' => 'array',
        'is_featured' => 'boolean',
        'is_sticky' => 'boolean',
        'allow_comments' => 'boolean',
        'comments_count' => 'integer',
        'views_count' => 'integer',
        'likes_count' => 'integer',
        'shares_count' => 'integer',
        'reading_time' => 'float',
        'related_posts' => 'array',
        'published_at' => 'datetime',
        'scheduled_at' => 'datetime',
        'expired_at' => 'datetime',
        'last_edited_at' => 'datetime',
        'last_edited_by' => 'integer',
        'revision_number' => 'integer',
        'custom_fields' => 'array',
        'analytics' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Default values for attributes
     *
     * @var array
     */
    protected $attributes = [
        'status' => 'draft',
        'visibility' => 'public',
        'language' => 'en',
        'is_featured' => false,
        'is_sticky' => false,
        'allow_comments' => true,
        'comments_count' => 0,
        'views_count' => 0,
        'likes_count' => 0,
        'shares_count' => 0,
        'reading_time' => 0,
        'revision_number' => 1,
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate blog_id before creating
        static::creating(function ($blog) {
            if (empty($blog->blog_id)) {
                $blog->blog_id = 'BLOG-' . strtoupper(uniqid());
            }

            // Auto-generate slug from title if not provided
            if (empty($blog->slug) && !empty($blog->title)) {
                $blog->slug = self::generateSlug($blog->title);
            }

            // Calculate reading time
            if (!empty($blog->content)) {
                $blog->reading_time = self::calculateReadingTime($blog->content);
            }
        });

        // Update timestamps and version on update
        static::updating(function ($blog) {
            $blog->last_edited_at = now();
            $blog->revision_number++;

            // Recalculate reading time if content changed
            if ($blog->isDirty('content')) {
                $blog->reading_time = self::calculateReadingTime($blog->content);
            }
        });
    }

    /**
     * Generate a unique slug from title
     *
     * @param string $title
     * @return string
     */
    public static function generateSlug(string $title): string
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
        $originalSlug = $slug;
        $count = 1;

        // Ensure uniqueness
        while (self::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        return $slug;
    }

    /**
     * Calculate reading time based on content
     * Average reading speed: 200 words per minute
     *
     * @param string $content
     * @return float
     */
    public static function calculateReadingTime(string $content): float
    {
        $wordCount = str_word_count(strip_tags($content));
        return round($wordCount / 200, 1);
    }

    /**
     * Scope: Get published posts
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->where(function ($q) {
                $q->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            });
    }

    /**
     * Scope: Get featured posts
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope: Get posts by status
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope: Get posts by category
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category)
            ->orWhereJsonContains('categories', $category);
    }

    /**
     * Scope: Get posts by tag
     */
    public function scopeByTag($query, string $tag)
    {
        return $query->whereJsonContains('tags', $tag);
    }

    /**
     * Scope: Get posts by language
     */
    public function scopeByLanguage($query, string $language)
    {
        return $query->where('language', $language);
    }

    /**
     * Scope: Search posts
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
                ->orWhere('excerpt', 'like', "%{$search}%")
                ->orWhere('content', 'like', "%{$search}%")
                ->orWhereJsonContains('tags', $search);
        });
    }

    /**
     * Get full URL for the blog post
     */
    public function url(): Attribute
    {
        return Attribute::make(
            get: fn() => url('/blog/' . $this->slug)
        );
    }

    /**
     * Check if post is published
     */
    public function isPublished(): bool
    {
        return $this->status === 'published' &&
            ($this->published_at === null || $this->published_at <= now());
    }

    /**
     * Check if post is scheduled
     */
    public function isScheduled(): bool
    {
        return $this->status === 'published' &&
            $this->scheduled_at &&
            $this->scheduled_at > now();
    }

    /**
     * Check if post is expired
     */
    public function isExpired(): bool
    {
        return $this->expired_at && $this->expired_at < now();
    }

    /**
     * Increment view count
     */
    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    /**
     * Increment like count
     */
    public function incrementLikes(): void
    {
        $this->increment('likes_count');
    }

    /**
     * Increment share count
     */
    public function incrementShares(): void
    {
        $this->increment('shares_count');
    }

    /**
     * Get SEO title
     */
    public function getSeoTitle(): string
    {
        return $this->seo_meta['title'] ?? $this->title;
    }

    /**
     * Get SEO description
     */
    public function getSeoDescription(): string
    {
        return $this->seo_meta['description'] ?? $this->excerpt;
    }

    /**
     * Get SEO keywords
     */
    public function getSeoKeywords(): array
    {
        return $this->seo_meta['keywords'] ?? $this->tags ?? [];
    }

    /**
     * Scope to apply structured filters.
     * 
     * Parses filter string format: field:OPERATOR(value);field2:OPERATOR(value2)
     * Supports operators: EQ, NE, GT, GTE, LT, LTE, LIKE, IN, NIN, BETWEEN, NULL, NOTNULL
     *
     * Example: status:EQ(published);category:IN(Technology,Science);author_id:GT(5)
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array|string $filters Array of filters or semicolon-separated string
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilter($query, $filters)
    {
        if (empty($filters)) {
            return $query;
        }

        // If filters is a string, parse it
        if (is_string($filters)) {
            $filters = $this->parseFilterString($filters);
        }

        // Apply each filter
        foreach ($filters as $field => $condition) {
            // Skip if field not in fillable or searchable
            if (!in_array($field, $this->fillable) && !in_array($field, $this->searchable ?? [])) {
                continue;
            }

            // Parse condition if it's a string with operator
            if (is_string($condition)) {
                $this->applyFilterCondition($query, $field, $condition);
            } elseif (is_array($condition)) {
                // Handle array values as IN condition
                $query->whereIn($field, $condition);
            } else {
                // Simple equality
                $query->where($field, $condition);
            }
        }

        return $query;
    }

    /**
     * Parse filter string into array of conditions
     * Format: field:OPERATOR(value);field2:OPERATOR(value2)
     *
     * @param string $filterString
     * @return array
     */
    protected function parseFilterString(string $filterString): array
    {
        $filters = [];
        $parts = explode(';', $filterString);

        foreach ($parts as $part) {
            $part = trim($part);
            if (empty($part)) {
                continue;
            }

            // Match pattern: field:OPERATOR(value)
            if (preg_match('/^([^:]+):(.+)$/', $part, $matches)) {
                $field = trim($matches[1]);
                $condition = trim($matches[2]);
                $filters[$field] = $condition;
            } elseif (strpos($part, '=') !== false) {
                // Simple key=value format
                list($field, $value) = explode('=', $part, 2);
                $filters[trim($field)] = trim($value);
            }
        }

        return $filters;
    }

    /**
     * Apply a filter condition with operator
     *
     * Supported operators:
     * - Text: LIKE, NOT_LIKE, EQ, NEQ, STARTS_WITH, ENDS_WITH
     * - Comparison: GT, GTE, LT, LTE
     * - Array: IN, NOT_IN
     * - Range: BETWEEN, NOT_BETWEEN
     * - Date: DATE_EQ, DATE_GT, DATE_GTE, DATE_LT, DATE_LTE, DATE_BETWEEN
     * - Null: IS_NULL, IS_NOT_NULL
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $field
     * @param string $condition Format: OPERATOR(value)
     * @return void
     */
    protected function applyFilterCondition($query, string $field, string $condition): void
    {
        // Match operator and value: OPERATOR(value)
        if (preg_match('/^([A-Z_]+)\((.*)?\)$/s', $condition, $matches)) {
            $operator = $matches[1];
            $value = $matches[2] ?? '';

            switch ($operator) {
                // Equality operators
                case 'EQ':
                    $query->where($field, '=', $value);
                    break;
                case 'NEQ':
                case 'NE':
                    $query->where($field, '!=', $value);
                    break;

                // Comparison operators
                case 'GT':
                    $query->where($field, '>', $value);
                    break;
                case 'GTE':
                    $query->where($field, '>=', $value);
                    break;
                case 'LT':
                    $query->where($field, '<', $value);
                    break;
                case 'LTE':
                    $query->where($field, '<=', $value);
                    break;

                // Text operators
                case 'LIKE':
                    $query->where($field, 'LIKE', "%{$value}%");
                    break;
                case 'NOT_LIKE':
                    $query->where($field, 'NOT LIKE', "%{$value}%");
                    break;
                case 'STARTS_WITH':
                    $query->where($field, 'LIKE', "{$value}%");
                    break;
                case 'ENDS_WITH':
                    $query->where($field, 'LIKE', "%{$value}");
                    break;

                // Array operators
                case 'IN':
                    $values = is_array($value) ? $value : array_map('trim', explode(',', $value));
                    $query->whereIn($field, $values);
                    break;
                case 'NOT_IN':
                case 'NIN':
                    $values = is_array($value) ? $value : array_map('trim', explode(',', $value));
                    $query->whereNotIn($field, $values);
                    break;

                // Range operators
                case 'BETWEEN':
                    $values = is_array($value) ? $value : array_map('trim', explode(',', $value));
                    if (count($values) === 2) {
                        $query->whereBetween($field, $values);
                    }
                    break;
                case 'NOT_BETWEEN':
                    $values = is_array($value) ? $value : array_map('trim', explode(',', $value));
                    if (count($values) === 2) {
                        $query->whereNotBetween($field, $values);
                    }
                    break;

                // Date operators
                case 'DATE_EQ':
                    $query->whereDate($field, '=', $value);
                    break;
                case 'DATE_GT':
                    $query->whereDate($field, '>', $value);
                    break;
                case 'DATE_GTE':
                    $query->whereDate($field, '>=', $value);
                    break;
                case 'DATE_LT':
                    $query->whereDate($field, '<', $value);
                    break;
                case 'DATE_LTE':
                    $query->whereDate($field, '<=', $value);
                    break;
                case 'DATE_BETWEEN':
                    $values = is_array($value) ? $value : array_map('trim', explode(',', $value));
                    if (count($values) === 2) {
                        $query->whereBetween($field, $values);
                    }
                    break;

                // Null operators
                case 'IS_NULL':
                case 'NULL':
                    $query->whereNull($field);
                    break;
                case 'IS_NOT_NULL':
                case 'NOTNULL':
                    $query->whereNotNull($field);
                    break;

                // Default fallback
                default:
                    $query->where($field, '=', $value);
            }
        } else {
            // No operator found, treat as equality
            $query->where($field, '=', $condition);
        }
    }
}
