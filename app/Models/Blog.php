<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
// Litepie Database Traits - ALL 14 FEATURES
use Litepie\Database\Traits\Aggregatable;
use Litepie\Database\Traits\Archivable;
use Litepie\Database\Traits\Batchable;
use Litepie\Database\Traits\Cacheable;
use Litepie\Database\Traits\Exportable;
use Litepie\Database\Traits\Importable;
use Litepie\Database\Traits\Measurable;
use Litepie\Database\Traits\Metable;
use Litepie\Database\Traits\Paginatable;
use Litepie\Database\Traits\Searchable;
use Litepie\Database\Traits\Sluggable;
use Litepie\Database\Traits\Sortable;
use Litepie\Database\Traits\Translatable;
use Litepie\Database\Traits\Versionable;

/**
 * Blog Model - Full Litepie Database Package Implementation
 *
 * This model demonstrates ALL 14 Litepie Database traits:
 *
 * 📦 Versionable - Track complete version history with rollback
 * 🏷️ Metable - WordPress-style flexible metadata storage
 * 🌍 Translatable - Multi-language content support
 * 🔍 Searchable - Powerful search (full-text, fuzzy, weighted, boolean)
 * ⚡ Cacheable - Smart caching with tags and invalidation
 * 🔗 Sluggable - Advanced slug generation with strategies
 * 📄 Paginatable - Cursor, seek, window, cached pagination
 * 📊 Aggregatable - Statistical analysis and reporting (23 methods)
 * 🗃️ Archivable - Soft archiving with reasons and user tracking
 * 📤 Exportable - Export to CSV, Excel, JSON formats
 * 📥 Importable - Import and validate data from CSV
 * 🔢 Sortable - Manual ordering and drag-drop support
 * 📋 Batchable - Efficient bulk operations for large datasets
 * 📏 Measurable - Query performance monitoring and optimization
 *
 * Plus standard features:
 * - SEO optimization (meta tags, keywords, schema)
 * - Advanced categorization and tagging
 * - Author and co-author management
 * - Publishing workflow (draft, review, published, archived)
 * - Analytics tracking
 * - Featured content support
 * - Social media integration
 * - Content scheduling
 */
class Blog extends Model
{
    use Aggregatable,
        Archivable,
        Batchable,
        Cacheable,
        Exportable,
        HasFactory,
        Importable,
        Measurable,
        Metable,
        Paginatable,
        Searchable,
        Sluggable,
        SoftDeletes,
        Sortable,
        Translatable,
        Versionable {
        // Resolve trait conflicts by specifying which methods to use from which trait
        Paginatable::generatePaginationCacheKey insteadof Cacheable;
        Exportable::formatBytes insteadof Importable, Batchable, Measurable;
    }

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'blogs';

    // ===============================================================
    // LITEPIE DATABASE TRAIT CONFIGURATIONS (All 14 Traits)
    // Note: We don't redeclare trait properties to avoid conflicts.
    // The traits will use their default values, or we can override
    // them using boot methods or accessor methods.
    // ===============================================================

    /**
     * Column names for archiving (Archivable Trait)
     */
    const ARCHIVED_AT = 'archived_at';

    const ARCHIVED_BY = 'archived_by';

    const ARCHIVED_REASON = 'archived_reason';

    /**
     * Create a new Eloquent model instance and initialize trait properties
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        // Initialize trait properties
        $this->initializeTraitProperties();
    }

    /**
     * Configure traits that require property definitions
     * (Defined dynamically to avoid trait conflicts)
     */
    protected function initializeTraitProperties()
    {
        // Sluggable configuration
        if (!property_exists($this, 'slugs') || empty($this->slugs)) {
            $this->slugs = ['slug' => 'title'];
        }

        // Translatable configuration
        if (!property_exists($this, 'translatable') || empty($this->translatable)) {
            $this->translatable = ['title', 'excerpt', 'content', 'seo_meta'];
        }

        // Searchable configuration
        if (!property_exists($this, 'searchable') || empty($this->searchable)) {
            $this->searchable = ['title', 'excerpt', 'content', 'tags', 'category'];
        }

        if (! property_exists($this, 'fullTextSearchable') || empty($this->fullTextSearchable)) {
            $this->fullTextSearchable = ['title', 'excerpt', 'content'];
        }

        if (!property_exists($this, 'searchWeights') || empty($this->searchWeights)) {
            $this->searchWeights = [
                'title' => 10,
                'excerpt' => 7,
                'content' => 5,
                'tags' => 8,
                'category' => 6,
            ];
        }
    }

    /**
     * Boot method to configure all Litepie traits
     */
    protected static function boot()
    {
        parent::boot();

        // Initialize model instance for property access
        static::retrieved(function ($blog) {
            $blog->initializeTraitProperties();
        });

        static::creating(function ($blog) {
            $blog->initializeTraitProperties();
        });

        // Configure traits using boot lifecycle
        static::bootLitepieTraits();

        // Auto-generate blog_id before creating
        static::creating(function ($blog) {
            if (empty($blog->blog_id)) {
                $blog->blog_id = 'BLOG-' . strtoupper(uniqid());
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
     * Configure all Litepie Database traits
     */
    protected static function bootLitepieTraits()
    {
        // If traits support boot{TraitName} methods, they'll be called automatically
        // Some traits might need property initialization here
    }

    // ===============================================================
    // FIXES FOR LITEPIE TRAIT COMPATIBILITY ISSUES
    // ===============================================================

    /**
     * Override getDateFormat() to fix Aggregatable trait compatibility
     * The trait incorrectly tries to use this as a static method
     *
     * @return string
     */
    public function getDateFormat()
    {
        return $this->dateFormat ?? parent::getDateFormat();
    }

    /**
     * Override getForeignKey() to fix Sortable trait access level issue
     * The trait defines this as protected, but Model requires public
     *
     * @return string
     */
    public function getForeignKey()
    {
        return parent::getForeignKey();
    }

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
        // Litepie Database fields
        'position',
        'meta_data',
        'version_count',
        'version_created_by',
        'last_exported_at',
        'last_imported_at',
        'performance_metrics',
        'cache_warmed_at',
        'archived_at',
        'archived_by',
        'archived_reason',
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
        // Litepie Database casts
        'position' => 'integer',
        'meta_data' => 'array',
        'version_count' => 'integer',
        'performance_metrics' => 'array',
        'last_exported_at' => 'datetime',
        'last_imported_at' => 'datetime',
        'cache_warmed_at' => 'datetime',
        'archived_at' => 'datetime',
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
}
