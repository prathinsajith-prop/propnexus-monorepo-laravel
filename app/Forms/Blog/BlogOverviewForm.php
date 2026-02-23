<?php

namespace App\Forms\Blog;

use Litepie\Layout\Components\FormComponent;

/**
 * BlogOverviewForm
 *
 * Read-only overview form for displaying blog post details in a 3-column grid.
 * Used in cards and overview sections.
 */
class BlogOverviewForm
{
    /**
     * Create blog overview form structure
     *
     * @param  string  $formId  Form identifier
     * @param  string|null  $dataUrl  URL to fetch data from
     * @return \Litepie\Layout\Components\FormComponent
     */
    public static function make(string $formId = 'blog-overview-form', ?string $dataUrl = null)
    {
        $form = FormComponent::make($formId)
            ->columns(3)
            ->gap('md')
            ->meta(['readOnly' => true]);

        if ($dataUrl) {
            $form->dataUrl($dataUrl);
        }

        // Row 1: Title (full width)
        $form->text('title')
            ->label(__('layout.post_title'))
            ->disabled(true)
            ->width(12);

        // Row 2: Category, Status, Author
        $form->text('category')
            ->label(__('layout.category'))
            ->disabled(true)
            ->width(4);

        $form->text('status')
            ->label(__('layout.status'))
            ->disabled(true)
            ->width(4);

        $form->text('author_id')
            ->label(__('layout.author'))
            ->disabled(true)
            ->width(4);

        // Row 3: Views, Likes, Comments
        $form->text('views_count')
            ->label(__('layout.views'))
            ->disabled(true)
            ->width(4);

        $form->text('likes_count')
            ->label(__('layout.likes'))
            ->disabled(true)
            ->width(4);

        $form->text('comments_count')
            ->label(__('layout.comments'))
            ->disabled(true)
            ->width(4);

        // Row 4: Published Date, Language
        $form->text('published_at')
            ->label(__('layout.published_date'))
            ->disabled(true)
            ->width(6);

        $form->text('language')
            ->label(__('layout.language'))
            ->disabled(true)
            ->width(6);

        // Row 5: Excerpt (full width)
        $form->textarea('excerpt')
            ->label(__('layout.excerpt'))
            ->disabled(true)
            ->width(12)
            ->rows(2);

        return $form;
    }

    /**
     * Create a compact version with fewer fields
     *
     * @param  string  $formId  Form identifier
     * @param  string|null  $dataUrl  URL to fetch data from
     * @return \Litepie\Layout\Components\FormComponent
     */
    public static function makeCompact(string $formId = 'blog-overview-compact', ?string $dataUrl = null)
    {
        $form = FormComponent::make($formId)
            ->columns(3)
            ->gap('sm')
            ->meta(['readOnly' => true]);

        if ($dataUrl) {
            $form->dataUrl($dataUrl);
        }

        $form->text('title')
            ->label(__('layout.post_title'))
            ->disabled(true)
            ->width(12);

        $form->text('status')
            ->label(__('layout.status'))
            ->disabled(true)
            ->width(4);

        $form->text('views_count')
            ->label(__('layout.views'))
            ->disabled(true)
            ->width(4);

        $form->text('published_at')
            ->label(__('layout.published'))
            ->disabled(true)
            ->width(4);

        return $form;
    }
}
