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

        // === CONTENT SECTION ===
        $contentGroup = $form->group('content-info')
            ->title('Blog Content')
            ->icon('documentfull')
            ->variant('bordered')
            ->columns(2);

        $contentGroup->text('title')
            ->label('Post Title')
            ->placeholder('Enter blog post title')
            ->required(true)
            ->width(8);

        $contentGroup->text('slug')
            ->label('URL Slug')
            ->placeholder('auto-generated-from-title')
            ->width(4);

        $contentGroup->textarea('excerpt')
            ->label('Excerpt')
            ->placeholder('Brief description of the post...')
            ->rows(3)
            ->width(12);

        $contentGroup->richtext('content')
            ->label('Post Content')
            ->placeholder('Write your blog content here...')
            ->required(true)
            ->height(400)
            ->editor('tinymce')
            ->width(12);

        // === PUBLICATION SECTION ===
        $publicationGroup = $form->group('publication-info')
            ->title('Publication Settings')
            ->icon('calendar')
            ->variant('bordered')
            ->columns(3);

        $publicationGroup->select('status')
            ->label('Status')
            ->options($masterData['statuses'] ?? [])
            ->width(4);

        $publicationGroup->select('visibility')
            ->label('Visibility')
            ->options($masterData['visibilities'] ?? [])
            ->width(4);

        $publicationGroup->text('password')
            ->label('Password')
            ->placeholder('Leave empty for public')
            ->width(4);

        $publicationGroup->datetime('published_at')
            ->label('Publish Date')
            ->width(4);

        $publicationGroup->datetime('scheduled_at')
            ->label('Schedule For')
            ->width(4);

        $publicationGroup->datetime('expired_at')
            ->label('Expiry Date')
            ->width(4);

        // === MEDIA SECTION ===
        $mediaGroup = $form->group('media-info')
            ->title('Media & Attachments')
            ->icon('image')
            ->variant('bordered')
            ->columns(1);

        $mediaGroup->file('featured_image')
            ->label('Featured Image')
            ->accept('image/*')
            ->maxSize(5120)
            ->uploadUrl('/api/upload/image')
            ->width(12);

        $mediaGroup->text('video_url')
            ->label('Video URL')
            ->placeholder('https://youtube.com/watch?v=...')
            ->width(12);

        $mediaGroup->file('gallery')
            ->label('Gallery Images')
            ->accept('image/*')
            ->multiple(true)
            ->maxSize(10240)
            ->uploadUrl('/api/upload/image')
            ->width(12);

        $mediaGroup->file('attachments')
            ->label('Attachments')
            ->multiple(true)
            ->maxSize(20480)
            ->uploadUrl('/api/upload/attachment')
            ->width(12);

        // === CATEGORIZATION SECTION ===
        $categoryGroup = $form->group('category-info')
            ->title('Categories & Tags')
            ->icon('tag')
            ->variant('bordered')
            ->columns(2);

        $categoryGroup->select('category')
            ->label('Primary Category')
            ->options($masterData['categories'] ?? [])
            ->width(6);

        $categoryGroup->select('language')
            ->label('Language')
            ->options($masterData['languages'] ?? [])
            ->width(6);

        $categoryGroup->multiselect('categories')
            ->label('Additional Categories')
            ->options($masterData['categories'] ?? [])
            ->width(6);

        $categoryGroup->tags('tags')
            ->label('Tags')
            ->placeholder('Add tags...')
            ->width(6);

        // === SEO SECTION ===
        $seoGroup = $form->group('seo-info')
            ->title('SEO & Metadata')
            ->icon('search')
            ->variant('bordered')
            ->columns(1);

        $seoGroup->text('seo_meta.title')
            ->label('SEO Title')
            ->placeholder('Optimized title for search engines')
            ->maxLength(60)
            ->width(12);

        $seoGroup->textarea('seo_meta.description')
            ->label('Meta Description')
            ->placeholder('Brief description for search results')
            ->maxLength(160)
            ->rows(2)
            ->width(12);

        $seoGroup->tags('seo_meta.keywords')
            ->label('Keywords')
            ->placeholder('Add SEO keywords...')
            ->width(12);

        $seoGroup->text('seo_meta.canonical_url')
            ->label('Canonical URL')
            ->placeholder('https://example.com/blog/post-slug')
            ->width(12);

        $seoGroup->textarea('schema_markup.content')
            ->label('Schema Markup (JSON-LD)')
            ->placeholder('{"@context": "https://schema.org", ...}')
            ->rows(4)
            ->width(12);

        // === SETTINGS SECTION ===
        $settingsGroup = $form->group('settings-info')
            ->title('Post Settings')
            ->icon('settings')
            ->variant('bordered')
            ->columns(3);

        $settingsGroup->checkbox('is_featured')
            ->label('Featured Post')
            ->width(4);

        $settingsGroup->checkbox('is_sticky')
            ->label('Sticky Post')
            ->width(4);

        $settingsGroup->checkbox('allow_comments')
            ->label('Allow Comments')
            ->width(4);

        $settingsGroup->number('author_id')
            ->label('Author ID')
            ->required(true)
            ->width(6);

        $settingsGroup->multiselect('co_authors')
            ->label('Co-Authors')
            ->options($masterData['authors'] ?? [])
            ->width(6);

        // === ANALYTICS SECTION ===
        $analyticsGroup = $form->group('analytics-info')
            ->title('Analytics & Metrics')
            ->icon('chartbar')
            ->variant('bordered')
            ->columns(4);

        $analyticsGroup->number('views_count')
            ->label('Views')
            ->disabled(true)
            ->width(3);

        $analyticsGroup->number('likes_count')
            ->label('Likes')
            ->disabled(true)
            ->width(3);

        $analyticsGroup->number('shares_count')
            ->label('Shares')
            ->disabled(true)
            ->width(3);

        $analyticsGroup->number('comments_count')
            ->label('Comments')
            ->disabled(true)
            ->width(3);

        // === CUSTOM FIELDS SECTION ===
        $customGroup = $form->group('custom-info')
            ->title('Custom Fields')
            ->icon('code')
            ->variant('bordered')
            ->columns(1);

        $customGroup->textarea('custom_fields.meta')
            ->label('Additional Metadata (JSON)')
            ->placeholder('{"key": "value", ...}')
            ->rows(4)
            ->width(12);

        return $form;
    }
}
