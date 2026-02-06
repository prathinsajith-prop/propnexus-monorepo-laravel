<?php

namespace App\Forms\Blog;

use Litepie\Layout\Components\FormComponent;

/**
 * BlogForm
 * 
 * Comprehensive blog post form with sections for:
 * - Content (title, slug, excerpt, content)
 * - Publication (status, visibility, scheduling)
 * - Media (featured image, gallery, video)
 * - Categorization (categories, tags)
 * - SEO (meta title, description, keywords, schema)
 * - Settings (comments, featured, sticky)
 * 
 * @package App\Forms\Blog
 */
class BlogForm
{
    /**
     * Create blog form structure
     *
     * @param string $formId Form identifier
     * @param string $method HTTP method (POST/PUT)
     * @param string $action Form action URL
     * @param array $masterData Master data for dropdowns
     * @param string|null $dataUrl URL to fetch existing data
     * @return \Litepie\Layout\Components\FormComponent
     */
    public static function make($formId, $method, $action, $masterData, $dataUrl = null)
    {
        $form = FormComponent::make($formId)
            ->action($action)
            ->method($method)
            ->columns(2)
            ->gap('lg')
            ->meta([
                'width' => '900px',
            ]);

        if ($dataUrl) {
            $form->meta([
                'dataUrl' => $dataUrl,
                'dataKey' => 'data',
            ]);
        }

        /** @var \Litepie\Layout\Components\FormComponent $form */
        // === CONTENT SECTION ===
        $contentGroup = $form->group('content-info')
            ->title(__('layout.blog_content'))
            ->icon('documentfull')
            ->variant('bordered')
            ->columns(2);

        $contentGroup->text('title')
            ->label(__('layout.post_title'))
            ->placeholder(__('layout.post_title_placeholder'))
            ->required(true)
            ->width(8);

        $contentGroup->text('slug')
            ->label(__('layout.url_slug'))
            ->placeholder(__('layout.slug_placeholder'))
            ->width(4);

        $contentGroup->textarea('excerpt')
            ->label(__('layout.excerpt'))
            ->placeholder(__('layout.excerpt_placeholder'))
            ->rows(3)
            ->width(12);

        $contentGroup->richtext('content')
            ->label(__('layout.full_content'))
            ->placeholder(__('layout.full_content_placeholder'))
            ->required(true)
            ->height(400)
            ->editor('tinymce')
            ->width(12);

        // === PUBLICATION SECTION ===
        $publicationGroup = $form->group('publication-info')
            ->title(__('layout.publication_settings'))
            ->icon('calendar')
            ->variant('bordered')
            ->columns(3);

        $publicationGroup->select('status')
            ->label(__('layout.status'))
            ->options($masterData['statuses'] ?? [])
            ->width(4);

        $publicationGroup->select('visibility')
            ->label(__('layout.visibility'))
            ->options($masterData['visibilities'] ?? [])
            ->width(4);

        $publicationGroup->text('password')
            ->label(__('layout.password'))
            ->placeholder(__('layout.password_placeholder'))
            ->width(4);

        $publicationGroup->datetime('published_at')
            ->label(__('layout.publish_date'))
            ->width(4);

        $publicationGroup->datetime('scheduled_at')
            ->label(__('layout.scheduled_at'))
            ->width(4);

        $publicationGroup->datetime('expired_at')
            ->label(__('layout.expired_at'))
            ->width(4);

        // === MEDIA SECTION ===
        $mediaGroup = $form->group('media-info')
            ->title(__('layout.media_attachments'))
            ->icon('image')
            ->variant('bordered')
            ->columns(1);

        $mediaGroup->file('featured_image')
            ->label(__('layout.featured_image'))
            ->accept('image/*')
            ->maxSize(5120)
            ->uploadUrl('/api/upload/image')
            ->width(12);

        $mediaGroup->text('video_url')
            ->label(__('layout.video_url'))
            ->placeholder(__('layout.video_url_placeholder'))
            ->width(12);

        $mediaGroup->file('gallery')
            ->label(__('layout.gallery_images'))
            ->accept('image/*')
            ->multiple(true)
            ->maxSize(10240)
            ->uploadUrl('/api/upload/image')
            ->width(12);

        $mediaGroup->file('attachments')
            ->label(__('layout.attachments'))
            ->multiple(true)
            ->maxSize(20480)
            ->uploadUrl('/api/upload/attachment')
            ->width(12);

        // === CATEGORIZATION SECTION ===
        $categoryGroup = $form->group('category-info')
            ->title(__('layout.categories_tags'))
            ->icon('tag')
            ->variant('bordered')
            ->columns(2);

        $categoryGroup->select('category')
            ->label(__('layout.primary_category'))
            ->options($masterData['categories'] ?? [])
            ->width(6);

        $categoryGroup->select('language')
            ->label(__('layout.language'))
            ->options($masterData['languages'] ?? [])
            ->width(6);

        $categoryGroup->multiselect('categories')
            ->label(__('layout.additional_categories'))
            ->options($masterData['categories'] ?? [])
            ->width(6);

        $categoryGroup->tags('tags')
            ->label(__('layout.tags'))
            ->placeholder(__('layout.tags_placeholder'))
            ->width(6);

        // === SEO SECTION ===
        $seoGroup = $form->group('seo-info')
            ->title(__('layout.seo_metadata'))
            ->icon('search')
            ->variant('bordered')
            ->columns(1);

        $seoGroup->text('seo_meta.title')
            ->label(__('layout.seo_title'))
            ->placeholder(__('layout.seo_title_placeholder'))
            ->maxLength(60)
            ->width(12);

        $seoGroup->textarea('seo_meta.description')
            ->label(__('layout.meta_description'))
            ->placeholder(__('layout.meta_description_placeholder'))
            ->maxLength(160)
            ->rows(2)
            ->width(12);

        $seoGroup->tags('seo_meta.keywords')
            ->label(__('layout.meta_keywords'))
            ->placeholder(__('layout.meta_keywords_placeholder'))
            ->width(12);

        $seoGroup->text('seo_meta.canonical_url')
            ->label(__('layout.canonical_url'))
            ->placeholder(__('layout.canonical_url_placeholder'))
            ->width(12);

        $seoGroup->textarea('schema_markup.content')
            ->label(__('layout.schema_markup'))
            ->placeholder(__('layout.schema_markup_placeholder'))
            ->rows(4)
            ->width(12);

        // === SETTINGS SECTION ===
        $settingsGroup = $form->group('settings-info')
            ->title(__('layout.post_settings'))
            ->icon('settings')
            ->variant('bordered')
            ->columns(3);

        $settingsGroup->checkbox('is_featured')
            ->label(__('layout.featured_post'))
            ->width(4);

        $settingsGroup->checkbox('is_sticky')
            ->label(__('layout.sticky_post'))
            ->width(4);

        $settingsGroup->checkbox('allow_comments')
            ->label(__('layout.allow_comments'))
            ->width(4);

        $settingsGroup->number('author_id')
            ->label(__('layout.author_id'))
            ->required(true)
            ->width(6);

        $settingsGroup->multiselect('co_authors')
            ->label(__('layout.co_authors'))
            ->options($masterData['authors'] ?? [])
            ->width(6);

        // === ANALYTICS SECTION ===
        $analyticsGroup = $form->group('analytics-info')
            ->title(__('layout.analytics_metrics'))
            ->icon('chartbar')
            ->variant('bordered')
            ->columns(4);

        $analyticsGroup->number('views_count')
            ->label(__('layout.views'))
            ->disabled(true)
            ->width(3);

        $analyticsGroup->number('likes_count')
            ->label(__('layout.likes'))
            ->disabled(true)
            ->width(3);

        $analyticsGroup->number('shares_count')
            ->label(__('layout.shares'))
            ->disabled(true)
            ->width(3);

        $analyticsGroup->number('comments_count')
            ->label(__('layout.comments'))
            ->disabled(true)
            ->width(3);

        // === CUSTOM FIELDS SECTION ===
        $customGroup = $form->group('custom-info')
            ->title(__('layout.custom_fields'))
            ->icon('code')
            ->variant('bordered')
            ->columns(1);

        $customGroup->textarea('custom_fields.meta')
            ->label(__('layout.additional_metadata'))
            ->placeholder(__('layout.custom_fields_placeholder'))
            ->rows(4)
            ->width(12);

        return $form;
    }
}
