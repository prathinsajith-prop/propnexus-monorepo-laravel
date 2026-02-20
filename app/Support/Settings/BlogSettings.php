<?php

namespace App\Support\Settings;

/**
 * BlogSettings
 *
 * Pure configuration provider for blog form groups and fields.
 * Context-aware visibility logic lives in Blog::getSettings().
 *
 * @package App\Support\Settings
 */
class BlogSettings
{
    // ─── Form IDs ─────────────────────────────────────────────────────────────

    public const FORM_ACTIVITY = 'blog-form-activity';
    public const FORM_VIEW     = 'blog-view-activity';

    // ─── Group IDs ────────────────────────────────────────────────────────────

    public const GROUPS = [
        'content-info',
        'publication-info',
        'media-info',
        'category-info',
        'seo-info',
        'settings-info',
        'analytics-info',
        'custom-info',
    ];

    // ─── Defaults — every group and field ON and editable ─────────────────────

    public static function defaults(): array
    {
        return [
            'groups' => [
                'form.blog-form-activity.content-info'     => ['show' => true, 'edit' => true],
                'form.blog-form-activity.publication-info' => ['show' => true, 'edit' => true],
                'form.blog-form-activity.media-info'       => ['show' => true, 'edit' => true],
                'form.blog-form-activity.category-info'    => ['show' => true, 'edit' => true],
                'form.blog-form-activity.seo-info'         => ['show' => true, 'edit' => true],
                'form.blog-form-activity.settings-info'    => ['show' => true, 'edit' => true],
                'form.blog-form-activity.analytics-info'   => ['show' => true, 'edit' => true],
                'form.blog-form-activity.custom-info'      => ['show' => true, 'edit' => true],
            ],
            'fields' => [
                // Content
                'title'          => ['show' => true, 'edit' => true],
                'slug'           => ['show' => true, 'edit' => true],
                'excerpt'        => ['show' => true, 'edit' => true],
                'content'        => ['show' => true, 'edit' => true],
                // Publication
                'status'         => ['show' => true, 'edit' => true],
                'visibility'     => ['show' => true, 'edit' => true],
                'password'       => ['show' => true, 'edit' => true],
                'published_at'   => ['show' => true, 'edit' => true],
                'scheduled_at'   => ['show' => true, 'edit' => true],
                'expired_at'     => ['show' => true, 'edit' => true],
                // Media
                'featured_image' => ['show' => true, 'edit' => true],
                'gallery'        => ['show' => true, 'edit' => true],
                'video_url'      => ['show' => true, 'edit' => true],
                'attachments'    => ['show' => true, 'edit' => true],
                // Category
                'category'       => ['show' => true, 'edit' => true],
                'categories'     => ['show' => true, 'edit' => true],
                'tags'           => ['show' => true, 'edit' => true],
                'is_featured'    => ['show' => true, 'edit' => true],
                'is_sticky'      => ['show' => true, 'edit' => true],
                // SEO
                'seo_meta'       => ['show' => true, 'edit' => true],
                'schema_markup'  => ['show' => true, 'edit' => true],
                // Settings
                'allow_comments' => ['show' => true, 'edit' => true],
                'language'       => ['show' => true, 'edit' => true],
                'translations'   => ['show' => true, 'edit' => true],
                // Analytics (display-only)
                'analytics'      => ['show' => true, 'edit' => false],
                'views_count'    => ['show' => true, 'edit' => false],
                'likes_count'    => ['show' => true, 'edit' => false],
                'shares_count'   => ['show' => true, 'edit' => false],
                'comments_count' => ['show' => true, 'edit' => false],
                'reading_time'   => ['show' => true, 'edit' => false],
                // Authors / Custom
                'author_id'      => ['show' => true, 'edit' => true],
                'co_authors'     => ['show' => true, 'edit' => true],
                'custom_fields'  => ['show' => true, 'edit' => true],
            ],
        ];
    }

    // ─── Merge ────────────────────────────────────────────────────────────────

    /** Deep-merge $overrides onto defaults. */
    public static function get(array $overrides = []): array
    {
        return array_replace_recursive(self::defaults(), $overrides);
    }

    // ─── Generic helpers ──────────────────────────────────────────────────────

    /** Hide groups (show: false, edit: false). */
    public static function hideGroups(array $keys): array
    {
        $overrides = ['groups' => []];
        foreach ($keys as $key) {
            $overrides['groups'][$key] = ['show' => false, 'edit' => false];
        }
        return self::get($overrides);
    }

    /** Make groups read-only (show: true, edit: false). */
    public static function readOnlyGroups(array $keys): array
    {
        $overrides = ['groups' => []];
        foreach ($keys as $key) {
            $overrides['groups'][$key] = ['show' => true, 'edit' => false];
        }
        return self::get($overrides);
    }

    /** Hide specific fields (show: false, edit: false). */
    public static function hideFields(array $keys): array
    {
        $overrides = ['fields' => []];
        foreach ($keys as $key) {
            $overrides['fields'][$key] = ['show' => false, 'edit' => false];
        }
        return self::get($overrides);
    }

    /** Make specific fields read-only (show: true, edit: false). */
    public static function readOnlyFields(array $keys): array
    {
        $overrides = ['fields' => []];
        foreach ($keys as $key) {
            $overrides['fields'][$key] = ['show' => true, 'edit' => false];
        }
        return self::get($overrides);
    }
}
