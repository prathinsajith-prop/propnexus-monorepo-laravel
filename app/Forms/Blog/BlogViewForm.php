<?php

namespace App\Forms\Blog;

use Litepie\Layout\LayoutBuilder;

/**
 * BlogViewForm
 * 
 * Read-only blog view form for displaying blog post details in drawer/fullscreen layout
 * All fields are readonly for viewing purposes only
 * 
 * @package App\Forms\Blog
 */
class BlogViewForm
{
    /**
     * Create a read-only blog view form structure
     *
     * @param string $formId Form identifier
     * @param array $masterData Master data for display options
     * @param string|null $dataUrl URL to fetch blog data
     * @return array|null Form component array
     */
    public static function make($formId, $masterData, $dataUrl = null)
    {
        $formLayout = LayoutBuilder::create($formId . '-layout', 'form');

        $formLayout->section('content', function ($section) use ($formId, $masterData, $dataUrl) {
            if ($dataUrl) {
                $section->meta([
                    'dataUrl' => $dataUrl,
                    'dataKey' => 'data',
                ]);
            }

            $form = $section->form($formId)
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
                    ]
                ]);

            // === CONTENT SECTION ===
            $contentGroup = $form->group('content-info')
                ->title('Blog Content')
                ->icon('documentfull')
                ->variant('bordered')
                ->columns(1);

            $contentGroup->text('title')
                ->label('Post Title')
                ->readonly(true)
                ->width(12);

            $contentGroup->text('slug')
                ->label('URL Slug')
                ->readonly(true)
                ->width(12);

            $contentGroup->textarea('excerpt')
                ->label('Excerpt')
                ->readonly(true)
                ->rows(3)
                ->width(12);

            $contentGroup->textarea('content')
                ->label('Post Content')
                ->readonly(true)
                ->rows(12)
                ->width(12);

            // === PUBLICATION SECTION ===
            $publicationGroup = $form->group('publication-info')
                ->title('Publication Settings')
                ->icon('calendar')
                ->variant('bordered')
                ->columns(1);

            $publicationGroup->text('status')
                ->label('Status')
                ->readonly(true)
                ->width(12);

            $publicationGroup->text('visibility')
                ->label('Visibility')
                ->readonly(true)
                ->width(12);

            $publicationGroup->text('published_at')
                ->label('Published Date')
                ->readonly(true)
                ->width(12);

            $publicationGroup->text('scheduled_at')
                ->label('Scheduled For')
                ->readonly(true)
                ->width(12);

            $publicationGroup->text('expired_at')
                ->label('Expiry Date')
                ->readonly(true)
                ->width(12);

            // === MEDIA SECTION ===
            $mediaGroup = $form->group('media-info')
                ->title('Media & Attachments')
                ->icon('image')
                ->variant('bordered')
                ->columns(1);

            $mediaGroup->text('featured_image')
                ->label('Featured Image')
                ->readonly(true)
                ->width(12);

            $mediaGroup->text('video_url')
                ->label('Video URL')
                ->readonly(true)
                ->width(12);

            $mediaGroup->text('gallery')
                ->label('Gallery Images')
                ->readonly(true)
                ->width(12);

            $mediaGroup->text('attachments')
                ->label('Attachments')
                ->readonly(true)
                ->width(12);

            // === CATEGORIZATION SECTION ===
            $categoryGroup = $form->group('category-info')
                ->title('Categories & Tags')
                ->icon('tag')
                ->variant('bordered')
                ->columns(2);

            $categoryGroup->text('category')
                ->label('Primary Category')
                ->readonly(true)
                ->width(6);

            $categoryGroup->text('language')
                ->label('Language')
                ->readonly(true)
                ->width(6);

            $categoryGroup->text('categories')
                ->label('Additional Categories')
                ->readonly(true)
                ->width(12);

            $categoryGroup->text('tags')
                ->label('Tags')
                ->readonly(true)
                ->width(12);

            // === SEO SECTION ===
            $seoGroup = $form->group('seo-info')
                ->title('SEO & Metadata')
                ->icon('search')
                ->variant('bordered')
                ->columns(1);

            $seoGroup->text('seo_meta.title')
                ->label('SEO Title')
                ->readonly(true)
                ->width(12);

            $seoGroup->textarea('seo_meta.description')
                ->label('Meta Description')
                ->readonly(true)
                ->rows(2)
                ->width(12);

            $seoGroup->text('seo_meta.keywords')
                ->label('Keywords')
                ->readonly(true)
                ->width(12);

            $seoGroup->text('seo_meta.canonical_url')
                ->label('Canonical URL')
                ->readonly(true)
                ->width(12);

            $seoGroup->textarea('schema_markup.content')
                ->label('Schema Markup (JSON-LD)')
                ->readonly(true)
                ->rows(4)
                ->width(12);

            // === SETTINGS SECTION ===
            $settingsGroup = $form->group('settings-info')
                ->title('Post Settings')
                ->icon('settings')
                ->variant('bordered')
                ->columns(1);

            $settingsGroup->text('is_featured')
                ->label('Featured Post')
                ->readonly(true)
                ->width(12);

            $settingsGroup->text('is_sticky')
                ->label('Sticky Post')
                ->readonly(true)
                ->width(12);

            $settingsGroup->text('allow_comments')
                ->label('Allow Comments')
                ->readonly(true)
                ->width(12);

            $settingsGroup->text('author_id')
                ->label('Author ID')
                ->readonly(true)
                ->width(12);

            $settingsGroup->text('co_authors')
                ->label('Co-Authors')
                ->readonly(true)
                ->width(12);

            // === ANALYTICS SECTION ===
            $analyticsGroup = $form->group('analytics-info')
                ->title('Analytics & Metrics')
                ->icon('barchart')
                ->variant('bordered')
                ->columns(2);

            $analyticsGroup->text('views_count')
                ->label('Views')
                ->readonly(true)
                ->width(6);

            $analyticsGroup->text('likes_count')
                ->label('Likes')
                ->readonly(true)
                ->width(6);

            $analyticsGroup->text('shares_count')
                ->label('Shares')
                ->readonly(true)
                ->width(6);

            $analyticsGroup->text('comments_count')
                ->label('Comments')
                ->readonly(true)
                ->width(6);

            $analyticsGroup->text('reading_time')
                ->label('Reading Time (mins)')
                ->readonly(true)
                ->width(12);

            // === AUDIT SECTION ===
            $auditGroup = $form->group('audit-info')
                ->title('System & Audit Trail')
                ->icon('clock')
                ->variant('bordered')
                ->columns(2);

            $auditGroup->text('created_at')
                ->label('Created At')
                ->readonly(true)
                ->width(6);

            $auditGroup->text('updated_at')
                ->label('Last Updated')
                ->readonly(true)
                ->width(6);

            $auditGroup->text('last_edited_at')
                ->label('Last Edited')
                ->readonly(true)
                ->width(6);

            $auditGroup->text('last_edited_by')
                ->label('Last Edited By')
                ->readonly(true)
                ->width(6);
        });

        // Build and return the layout
        $builtContent = $formLayout->build();
        $contentArray = $builtContent->toArray();

        return $contentArray['sections']['content']['components'][0] ?? null;
    }
}
