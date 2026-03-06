<?php

namespace App\Forms\Blog;

/**
 * BlogViewForm
 *
 * Read-only blog view form for displaying blog post details in drawer/fullscreen layout
 * All fields are readonly for viewing purposes only
 */
class BlogViewForm
{
    /**
     * Create a read-only blog view form structure
     *
     * @param  string  $formId  Form identifier
     * @param  array  $masterData  Master data for display options
     * @param  string|null  $dataUrl  URL to fetch blog data
     * @return \Litepie\Layout\Components\FormComponent
     */
    public static function make($formId, $masterData, $dataUrl = null)
    {
        $form = \Litepie\Layout\Components\FormComponent::make($formId)
            ->method('GET')
            ->columns(2)
            ->gap('lg')
            ->meta([
                'width' => '900px',
            ]);

        if ($dataUrl) {
            $form->dataUrl($dataUrl)->dataKey('data');
        }

        $form
            ->columns(2)
            ->gap('lg')
            // ->readonly(true)
            ->layoutConfig([
                [
                    'columnsGrid' => 8,
                    'gap' => 'md',
                    'gridTemplateComponents' => [
                        ['key' => 'content-info', 'columnGrid' => 12],
                        ['key' => 'category-info', 'columnGrid' => 12],
                        ['key' => 'seo-info', 'columnGrid' => 12],
                    ],
                ],
                [
                    'columnsGrid' => 4,
                    'gap' => 'md',
                    'gridTemplateComponents' => [
                        ['key' => 'publication-info', 'columnGrid' => 12],
                        ['key' => 'media-info', 'columnGrid' => 12],
                        ['key' => 'settings-info', 'columnGrid' => 12],
                        ['key' => 'analytics-info', 'columnGrid' => 12],
                    ],
                ],
            ]);

        // === CONTENT SECTION ===
        $contentGroup = $form->group('content-info')
            ->title(__('layout.blog_content'))
            ->icon('documentfull')
            ->variant('bordered')
            ->columns(1);

        $contentGroup->text('title')
            ->label(__('layout.post_title'))
            ->readonly(true)
            ->width(12);

        $contentGroup->text('slug')
            ->label(__('layout.url_slug'))
            ->readonly(true)
            ->width(12);

        $contentGroup->textarea('excerpt')
            ->label(__('layout.excerpt'))
            ->readonly(true)
            ->rows(3)
            ->width(12);

        $contentGroup->richtext('content')
            ->label(__('layout.post_content'))
            ->readonly(true)
            ->height(400)
            ->editor('tinymce')
            ->width(12);

        // === PUBLICATION SECTION ===
        $publicationGroup = $form->group('publication-info')
            ->title(__('layout.publication_settings'))
            ->icon('calendar')
            ->variant('bordered')
            ->columns(1);

        $publicationGroup->text('status')
            ->label(__('layout.status'))
            ->readonly(true)
            ->width(12);

        $publicationGroup->text('visibility')
            ->label(__('layout.visibility'))
            ->readonly(true)
            ->width(12);

        $publicationGroup->text('published_at')
            ->label(__('layout.published_date'))
            ->readonly(true)
            ->width(12);

        $publicationGroup->text('scheduled_at')
            ->label(__('layout.scheduled_for'))
            ->readonly(true)
            ->width(12);

        $publicationGroup->text('expired_at')
            ->label(__('layout.expiry_date'))
            ->readonly(true)
            ->width(12);

        // === MEDIA SECTION ===
        $mediaGroup = $form->group('media-info')
            ->title(__('layout.media_attachments'))
            ->icon('camera')
            ->variant('bordered')
            ->columns(1);

        $mediaGroup->text('featured_image')
            ->label(__('layout.featured_image'))
            ->readonly(true)
            ->width(12);

        $mediaGroup->text('video_url')
            ->label(__('layout.video_url'))
            ->readonly(true)
            ->width(12);

        $mediaGroup->text('gallery')
            ->label(__('layout.images'))
            ->readonly(true)
            ->width(12);

        $mediaGroup->text('attachments')
            ->label(__('layout.attachments'))
            ->readonly(true)
            ->width(12);

        // === CATEGORIZATION SECTION ===
        $categoryGroup = $form->group('category-info')
            ->title(__('layout.categories_tags'))
            ->icon('tag')
            ->variant('bordered')
            ->columns(2);

        $categoryGroup->text('category')
            ->label(__('layout.primary_category'))
            ->readonly(true)
            ->width(6);

        $categoryGroup->text('language')
            ->label(__('layout.language'))
            ->readonly(true)
            ->width(6);

        $categoryGroup->text('categories')
            ->label(__('layout.additional_categories'))
            ->readonly(true)
            ->width(12);

        $categoryGroup->text('tags')
            ->label(__('layout.tags'))
            ->readonly(true)
            ->width(12);

        // === SEO SECTION ===
        $seoGroup = $form->group('seo-info')
            ->title(__('layout.seo_metadata'))
            ->icon('search')
            ->variant('bordered')
            ->columns(1);

        $seoGroup->text('seo_meta.title')
            ->label(__('layout.seo_title'))
            ->readonly(true)
            ->width(12);

        $seoGroup->textarea('seo_meta.description')
            ->label(__('layout.meta_description'))
            ->readonly(true)
            ->rows(2)
            ->width(12);

        $seoGroup->text('seo_meta.keywords')
            ->label(__('layout.keywords'))
            ->readonly(true)
            ->width(12);

        $seoGroup->text('seo_meta.canonical_url')
            ->label(__('layout.canonical_url'))
            ->readonly(true)
            ->width(12);

        $seoGroup->textarea('schema_markup.content')
            ->label(__('layout.schema_markup'))
            ->readonly(true)
            ->rows(4)
            ->width(12);

        // === SETTINGS SECTION ===
        $settingsGroup = $form->group('settings-info')
            ->title(__('layout.post_settings'))
            ->icon('settings')
            ->variant('bordered')
            ->columns(1);

        $settingsGroup->text('is_featured')
            ->label(__('layout.featured_post'))
            ->readonly(true)
            ->width(12);

        $settingsGroup->text('is_sticky')
            ->label(__('layout.sticky_post'))
            ->readonly(true)
            ->width(12);

        $settingsGroup->text('allow_comments')
            ->label(__('layout.allow_comments'))
            ->readonly(true)
            ->width(12);

        $settingsGroup->text('author_id')
            ->label(__('layout.author_id'))
            ->readonly(true)
            ->width(12);

        $settingsGroup->text('co_authors')
            ->label(__('layout.co_authors'))
            ->readonly(true)
            ->width(12);

        // === ANALYTICS SECTION ===
        $analyticsGroup = $form->group('analytics-info')
            ->title(__('layout.analytics_metrics'))
            ->icon('chartbar')
            ->variant('bordered')
            ->columns(2);

        $analyticsGroup->text('views_count')
            ->label(__('layout.views'))
            ->readonly(true)
            ->width(6);

        $analyticsGroup->text('likes_count')
            ->label(__('layout.likes'))
            ->readonly(true)
            ->width(6);

        $analyticsGroup->text('shares_count')
            ->label(__('layout.shares'))
            ->readonly(true)
            ->width(6);

        $analyticsGroup->text('comments_count')
            ->label(__('layout.comments'))
            ->readonly(true)
            ->width(6);

        $analyticsGroup->text('reading_time')
            ->label(__('layout.reading_time_mins'))
            ->readonly(true)
            ->width(12);

        // === AUDIT SECTION ===
        $auditGroup = $form->group('audit-info')
            ->title(__('layout.system_audit_trail'))
            ->icon('clock')
            ->variant('bordered')
            ->columns(2);

        $auditGroup->text('created_at')
            ->label(__('layout.created_at'))
            ->readonly(true)
            ->width(6);

        $auditGroup->text('updated_at')
            ->label(__('layout.last_updated'))
            ->readonly(true)
            ->width(6);

        $auditGroup->text('last_edited_at')
            ->label(__('layout.last_edited'))
            ->readonly(true)
            ->width(6);

        $auditGroup->text('last_edited_by')
            ->label(__('layout.last_edited_by'))
            ->readonly(true)
            ->width(6);

        return $form;
    }
}
