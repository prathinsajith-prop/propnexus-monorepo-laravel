<?php

/**
 * Divider Example
 *
 * Demonstrates the DividerComponent with various configurations:
 * - Horizontal and vertical dividers
 * - Different styles (solid, dashed, dotted, double, gradient)
 * - Thickness variations
 * - Dividers with labels and icons
 * - Decorative elements and effects
 * - Spacing and positioning
 */

use Litepie\Layout\Components\DividerComponent;
use Litepie\Layout\LayoutBuilder;

// Create divider showcase layout
$layout = LayoutBuilder::create('divider-showcase', 'showcase')
    ->title('Divider Component Examples')

    // Header
    ->section('header', function ($section) {
        $section->text('title')
            ->content('# Divider Component Showcase')
            ->align('center');

        $section->text('subtitle')
            ->content('Visual separators for organizing content')
            ->align('center')
            ->meta(['color' => 'muted']);
    })

    // Main content
    ->section('body', function ($section) {
        // ========================================================================
        // Basic Horizontal Dividers
        // ========================================================================
        $section->card('basic-horizontal')
            ->title('Basic Horizontal Dividers')
            ->subtitle('Simple horizontal separators');

        $section->text('before-divider-1')
            ->content('Content before divider');

        $section->divider('basic-solid')
            ->horizontal()
            ->solid()
            ->thin();

        $section->text('after-divider-1')
            ->content('Content after divider');

        $section->divider('spacer-1')
            ->spacing('lg');

        // ========================================================================
        // Style Variants
        // ========================================================================
        $section->card('style-variants')
            ->title('Style Variants')
            ->subtitle('Different line styles');

        $section->text('solid-label')
            ->content('**Solid Divider**');

        $section->divider('style-solid')
            ->solid()
            ->medium();

        $section->text('dashed-label')
            ->content('**Dashed Divider**');

        $section->divider('style-dashed')
            ->dashed()
            ->medium();

        $section->text('dotted-label')
            ->content('**Dotted Divider**');

        $section->divider('style-dotted')
            ->dotted()
            ->medium();

        $section->text('double-label')
            ->content('**Double Divider**');

        $section->divider('style-double')
            ->double()
            ->thick();

        $section->divider('spacer-2')
            ->spacing('xl');

        // ========================================================================
        // Thickness Variations
        // ========================================================================
        $section->card('thickness-variants')
            ->title('Thickness Variations')
            ->subtitle('Different divider thicknesses');

        $section->text('thin-label')
            ->content('Thin');

        $section->divider('thickness-thin')
            ->thin()
            ->solid();

        $section->text('medium-label')
            ->content('Medium');

        $section->divider('thickness-medium')
            ->medium()
            ->solid();

        $section->text('thick-label')
            ->content('Thick');

        $section->divider('thickness-thick')
            ->thick()
            ->solid();

        $section->text('custom-label')
            ->content('Custom (5px)');

        $section->divider('thickness-custom')
            ->customThickness('5px')
            ->solid();

        $section->divider('spacer-3')
            ->spacing('xl');

        // ========================================================================
        // Color Variants
        // ========================================================================
        $section->card('color-variants')
            ->title('Color Variants')
            ->subtitle('Different color styles');

        $section->text('default-color')
            ->content('Default Color');

        $section->divider('color-default')
            ->colorVariant('default')
            ->medium();

        $section->text('primary-color')
            ->content('Primary Color');

        $section->divider('color-primary')
            ->primary()
            ->medium();

        $section->text('secondary-color')
            ->content('Secondary Color');

        $section->divider('color-secondary')
            ->secondary()
            ->medium();

        $section->text('accent-color')
            ->content('Accent Color');

        $section->divider('color-accent')
            ->accent()
            ->medium();

        $section->text('custom-color')
            ->content('Custom Color (#10b981)');

        $section->divider('color-custom')
            ->color('#10b981')
            ->medium();

        $section->divider('spacer-4')
            ->spacing('xl');

        // ========================================================================
        // Dividers with Labels
        // ========================================================================
        $section->card('labeled-dividers')
            ->title('Dividers with Labels')
            ->subtitle('Text labels on dividers');

        $section->divider('label-center')
            ->label('OR')
            ->labelCenter()
            ->medium();

        $section->divider('label-left')
            ->label('Section Start')
            ->labelLeft()
            ->dashed();

        $section->divider('label-right')
            ->label('End of Section')
            ->labelRight()
            ->dashed();

        $section->divider('label-with-icon')
            ->label('Important Notice')
            ->labelIcon('alert-circle')
            ->labelCenter()
            ->primary()
            ->medium();

        $section->divider('icon-only-divider')
            ->labelIcon('star')
            ->iconOnly()
            ->labelCenter()
            ->accent()
            ->medium();

        $section->divider('spacer-5')
            ->spacing('xl');

        // ========================================================================
        // Gradient Dividers
        // ========================================================================
        $section->card('gradient-dividers')
            ->title('Gradient Dividers')
            ->subtitle('Dividers with gradient colors');

        $section->text('gradient-label-1')
            ->content('Blue to Purple');

        $section->divider('gradient-1')
            ->gradient()
            ->gradientColors('#3b82f6', '#8b5cf6', 'to-r')
            ->medium();

        $section->text('gradient-label-2')
            ->content('Green to Teal');

        $section->divider('gradient-2')
            ->gradient()
            ->gradientColors('#10b981', '#14b8a6', 'to-r')
            ->thick();

        $section->text('gradient-label-3')
            ->content('Orange to Red');

        $section->divider('gradient-3')
            ->gradient()
            ->gradientColors('#f59e0b', '#ef4444', 'to-r')
            ->medium();

        $section->divider('spacer-6')
            ->spacing('xl');

        // ========================================================================
        // Decorative Dividers
        // ========================================================================
        $section->card('decorative-dividers')
            ->title('Decorative Dividers')
            ->subtitle('Dividers with decorative elements');

        $section->text('dots-label')
            ->content('With Dots');

        $section->divider('with-dots')
            ->withDots()
            ->medium()
            ->primary();

        $section->text('circle-label')
            ->content('With Circle');

        $section->divider('with-circle')
            ->withCircle()
            ->medium()
            ->accent();

        $section->text('ornament-label')
            ->content('With Ornament');

        $section->divider('with-ornament')
            ->ornament('❖')
            ->medium()
            ->secondary();

        $section->divider('spacer-7')
            ->spacing('xl');

        // ========================================================================
        // Dividers with Effects
        // ========================================================================
        $section->card('effect-dividers')
            ->title('Dividers with Effects')
            ->subtitle('Shadow and glow effects');

        $section->text('shadow-label')
            ->content('With Shadow');

        $section->divider('with-shadow')
            ->withShadow()
            ->medium()
            ->primary();

        $section->text('glow-label')
            ->content('With Glow');

        $section->divider('with-glow')
            ->withGlow(true, '#8b5cf6')
            ->medium()
            ->color('#8b5cf6');

        $section->divider('spacer-8')
            ->spacing('xl');

        // ========================================================================
        // Spacing Examples
        // ========================================================================
        $section->card('spacing-examples')
            ->title('Spacing Examples')
            ->subtitle('Different margin configurations');

        $section->text('spacing-xs')
            ->content('Extra Small Spacing');

        $section->divider('spacing-xs-divider')
            ->spacing('xs');

        $section->text('spacing-sm')
            ->content('Small Spacing');

        $section->divider('spacing-sm-divider')
            ->spacing('sm');

        $section->text('spacing-md')
            ->content('Medium Spacing');

        $section->divider('spacing-md-divider')
            ->spacing('md');

        $section->text('spacing-lg')
            ->content('Large Spacing');

        $section->divider('spacing-lg-divider')
            ->spacing('lg');

        $section->text('spacing-xl')
            ->content('Extra Large Spacing');

        $section->divider('spacing-xl-divider')
            ->spacing('xl');

        $section->divider('spacer-9')
            ->spacing('xl');

        // ========================================================================
        // Width Variations
        // ========================================================================
        $section->card('width-variations')
            ->title('Width Variations')
            ->subtitle('Partial width dividers');

        $section->text('full-width-label')
            ->content('Full Width (Default)');

        $section->divider('full-width')
            ->fullWidth()
            ->medium();

        $section->text('75-width-label')
            ->content('75% Width');

        $section->divider('width-75')
            ->width('75%')
            ->medium();

        $section->text('50-width-label')
            ->content('50% Width');

        $section->divider('width-50')
            ->width('50%')
            ->medium();

        $section->text('25-width-label')
            ->content('25% Width');

        $section->divider('width-25')
            ->width('25%')
            ->medium();

        $section->divider('spacer-10')
            ->spacing('xl');

        // ========================================================================
        // Inset Dividers
        // ========================================================================
        $section->card('inset-dividers')
            ->title('Inset Dividers')
            ->subtitle('Dividers indented from edges');

        $section->text('inset-label-1')
            ->content('Small Inset');

        $section->divider('inset-sm')
            ->inset(true, '1rem')
            ->medium();

        $section->text('inset-label-2')
            ->content('Medium Inset');

        $section->divider('inset-md')
            ->inset(true, '2rem')
            ->medium();

        $section->text('inset-label-3')
            ->content('Large Inset');

        $section->divider('inset-lg')
            ->inset(true, '4rem')
            ->medium();

        $section->divider('spacer-11')
            ->spacing('xl');

        // ========================================================================
        // Advanced Examples
        // ========================================================================
        $section->card('advanced-examples')
            ->title('Advanced Examples')
            ->subtitle('Complex divider configurations');

        // Premium section divider
        $section->divider('premium-divider')
            ->label('Premium Features')
            ->labelIcon('crown')
            ->labelCenter()
            ->gradient()
            ->gradientColors('#fbbf24', '#f59e0b', 'to-r')
            ->thick()
            ->withGlow(true, '#fbbf24')
            ->spacing('lg');

        $section->text('premium-content')
            ->content('Premium features content here...')
            ->align('center');

        // Content separator with style
        $section->divider('styled-separator')
            ->label('Continue Reading')
            ->labelCenter()
            ->dashed()
            ->primary()
            ->medium()
            ->withDots()
            ->spacing('md');

        // End of content divider
        $section->divider('end-divider')
            ->ornament('***')
            ->double()
            ->thick()
            ->accent()
            ->withShadow()
            ->spacing('xl');

        // Section break with gradient
        $section->divider('section-break')
            ->label('New Section')
            ->labelIcon('chevron-down')
            ->gradient()
            ->gradientColors('#3b82f6', '#8b5cf6', 'to-r')
            ->thick()
            ->withCircle()
            ->spacing('lg');
    })

    // Vertical Divider Example
    ->section('aside', function ($section) {
        $section->card('vertical-dividers')
            ->title('Vertical Dividers')
            ->subtitle('For side-by-side content separation');

        $section->text('vertical-note')
            ->content('Note: Vertical dividers are typically used in layouts with multiple columns.');

        $section->divider('vertical-thin')
            ->vertical()
            ->thin()
            ->height('100px')
            ->primary();

        $section->divider('vertical-medium')
            ->vertical()
            ->medium()
            ->height('150px')
            ->accent();

        $section->divider('vertical-thick')
            ->vertical()
            ->thick()
            ->height('200px')
            ->secondary();
    })

    // Footer
    ->section('footer', function ($section) {
        $section->divider('footer-divider')
            ->double()
            ->thick()
            ->spacing('md');

        $section->text('footer-text')
            ->content('Divider Component - Comprehensive Examples')
            ->align('center')
            ->meta(['color' => 'muted']);
    });

// Render the layout
return $layout->render();
