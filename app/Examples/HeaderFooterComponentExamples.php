<?php

namespace App\Examples;

use Litepie\Layout\Sections\HeaderSection;
use Litepie\Layout\Sections\FooterSection;
use Litepie\Layout\Components\ButtonComponent;
use Litepie\Layout\Components\TextComponent;
use Litepie\Layout\Components\LinkComponent;
use Litepie\Layout\Components\BadgeComponent;
use Litepie\Layout\Components\AvatarComponent;

/**
 * HeaderFooterComponentExamples
 * 
 * Comprehensive examples of using HeaderComponent and FooterComponent
 * in various contexts: modals, drawers, layouts, cards, etc.
 */
class HeaderFooterComponentExamples
{
    /**
     * Example 1: Simple Modal Header
     * Just a title and close button
     */
    public static function simpleModalHeader()
    {
        return HeaderSection::make('modal-header')
            ->center([
                TextComponent::make('title')
                    ->content('Confirm Action')
                    ->variant('h4'),
            ])
            ->right([
                ButtonComponent::make('close')
                    ->icon('LiX')
                    ->variant('text')
                    ->meta(['action' => 'close']),
            ])
            ->padding('md');
    }

    /**
     * Example 2: Simple Modal Footer
     * Just action buttons
     */
    public static function simpleModalFooter()
    {
        return FooterComponent::make('modal-footer')
            ->right([
                ButtonComponent::make('cancel')
                    ->label('Cancel')
                    ->variant('outlined')
                    ->meta(['action' => 'close']),
                ButtonComponent::make('confirm')
                    ->label('Confirm')
                    ->variant('contained')
                    ->meta(['action' => 'submit']),
            ])
            ->padding('md')
            ->withBorder();
    }

    /**
     * Example 3: Full Drawer Header
     * Back button, title with subtitle, and multiple action buttons
     */
    public static function fullDrawerHeader()
    {
        return HeaderSection::make('drawer-header')
            ->left([
                ButtonComponent::make('back')
                    ->icon('LiArrowLeft')
                    ->variant('text')
                    ->meta(['action' => 'back']),
            ])
            ->center([
                TextComponent::make('title')
                    ->content('Edit User Profile')
                    ->variant('h3')
                    ->meta(['subtitle' => 'Update user information and settings']),
            ])
            ->right([
                ButtonComponent::make('help')
                    ->icon('LiHelpCircle')
                    ->variant('text')
                    ->meta(['action' => 'help']),
                ButtonComponent::make('more')
                    ->icon('LiMoreVertical')
                    ->variant('text')
                    ->meta(['action' => 'menu']),
                ButtonComponent::make('close')
                    ->icon('LiX')
                    ->variant('text')
                    ->meta(['action' => 'close']),
            ])
            ->variant('elevated')
            ->padding('md')
            ->sticky(true);
    }

    /**
     * Example 4: Full Drawer Footer
     * Help link, status info, and action buttons
     */
    public static function fullDrawerFooter()
    {
        return FooterComponent::make('drawer-footer')
            ->left([
                LinkComponent::make('help')
                    ->text('Need help?')
                    ->href('/help')
                    ->variant('text'),
                TextComponent::make('status')
                    ->content('Last saved 2 mins ago')
                    ->variant('caption')
                    ->meta(['color' => 'text-gray-500']),
            ])
            ->right([
                ButtonComponent::make('save-draft')
                    ->label('Save Draft')
                    ->variant('outlined')
                    ->meta(['action' => 'draft']),
                ButtonComponent::make('cancel')
                    ->label('Cancel')
                    ->variant('outlined')
                    ->meta(['action' => 'close']),
                ButtonComponent::make('publish')
                    ->label('Publish')
                    ->icon('LiCheck')
                    ->variant('contained')
                    ->meta(['action' => 'submit']),
            ])
            ->variant('elevated')
            ->padding('md')
            ->sticky(true);
    }

    /**
     * Example 5: Card Header with Avatar
     * User profile card header
     */
    public static function userCardHeader()
    {
        return HeaderSection::make('user-card-header')
            ->left([
                AvatarComponent::make('avatar')
                    ->src('/images/user.jpg')
                    ->alt('John Doe')
                    ->size('md'),
                TextComponent::make('user-info')
                    ->content('John Doe')
                    ->variant('h5')
                    ->meta([
                        'subtitle' => 'Software Engineer',
                        'email' => 'john@example.com'
                    ]),
            ])
            ->right([
                BadgeComponent::make('status')
                    ->content('Active')
                    ->color('success')
                    ->variant('filled'),
                ButtonComponent::make('edit')
                    ->icon('LiEdit')
                    ->variant('text'),
                ButtonComponent::make('more')
                    ->icon('LiMoreVertical')
                    ->variant('text'),
            ])
            ->padding('md')
            ->withBorder();
    }

    /**
     * Example 6: Card Footer with Stats
     * Stats card footer
     */
    public static function statsCardFooter()
    {
        return FooterComponent::make('stats-footer')
            ->left([
                TextComponent::make('updated')
                    ->content('Updated 1 hour ago')
                    ->variant('caption'),
            ])
            ->center([
                TextComponent::make('trend')
                    ->content('↑ 12.5%')
                    ->variant('body2')
                    ->meta(['color' => 'text-green-600']),
            ])
            ->right([
                LinkComponent::make('view-details')
                    ->text('View Details')
                    ->href('/stats/details')
                    ->icon('LiArrowRight'),
            ])
            ->padding('sm')
            ->withBorder();
    }

    /**
     * Example 7: Layout Page Header
     * Main page header with breadcrumbs and actions
     */
    public static function pageLayoutHeader()
    {
        return HeaderSection::make('page-header')
            ->left([
                TextComponent::make('breadcrumbs')
                    ->content('Dashboard / Users / Profile')
                    ->variant('caption')
                    ->meta(['separator' => '/']),
            ])
            ->center([
                TextComponent::make('page-title')
                    ->content('User Management')
                    ->variant('h2'),
            ])
            ->right([
                ButtonComponent::make('export')
                    ->label('Export')
                    ->icon('LiDownload')
                    ->variant('outlined'),
                ButtonComponent::make('import')
                    ->label('Import')
                    ->icon('LiUpload')
                    ->variant('outlined'),
                ButtonComponent::make('create')
                    ->label('Create User')
                    ->icon('LiPlus')
                    ->variant('contained'),
            ])
            ->variant('elevated')
            ->padding('lg')
            ->backgroundColor('white')
            ->shadow(true);
    }

    /**
     * Example 8: Layout Page Footer
     * Main page footer with copyright and links
     */
    public static function pageLayoutFooter()
    {
        return FooterComponent::make('page-footer')
            ->left([
                TextComponent::make('copyright')
                    ->content('© 2026 Your Company. All rights reserved.')
                    ->variant('caption'),
            ])
            ->center([
                LinkComponent::make('privacy')
                    ->text('Privacy Policy')
                    ->href('/privacy'),
                TextComponent::make('separator-1')
                    ->content('•')
                    ->variant('caption'),
                LinkComponent::make('terms')
                    ->text('Terms of Service')
                    ->href('/terms'),
                TextComponent::make('separator-2')
                    ->content('•')
                    ->variant('caption'),
                LinkComponent::make('contact')
                    ->text('Contact Us')
                    ->href('/contact'),
            ])
            ->right([
                TextComponent::make('version')
                    ->content('v1.2.3')
                    ->variant('caption')
                    ->meta(['color' => 'text-gray-500']),
            ])
            ->padding('md')
            ->withBorder()
            ->backgroundColor('gray-50');
    }

    /**
     * Example 9: Transparent Header
     * Overlay header for hero sections
     */
    public static function transparentHeroHeader()
    {
        return HeaderSection::make('hero-header')
            ->left([
                TextComponent::make('logo')
                    ->content('Your Brand')
                    ->variant('h5')
                    ->meta(['fontWeight' => 'bold']),
            ])
            ->right([
                LinkComponent::make('about')
                    ->text('About')
                    ->href('/about'),
                LinkComponent::make('features')
                    ->text('Features')
                    ->href('/features'),
                LinkComponent::make('pricing')
                    ->text('Pricing')
                    ->href('/pricing'),
                ButtonComponent::make('get-started')
                    ->label('Get Started')
                    ->variant('contained'),
            ])
            ->transparent()
            ->padding('lg')
            ->sticky(true);
    }

    /**
     * Example 10: Form Section Header
     * Section divider within forms
     */
    public static function formSectionHeader()
    {
        return HeaderSection::make('form-section-header')
            ->left([
                TextComponent::make('section-title')
                    ->content('Personal Information')
                    ->variant('h5'),
                TextComponent::make('section-description')
                    ->content('Basic details about the user')
                    ->variant('caption')
                    ->meta(['color' => 'text-gray-600']),
            ])
            ->right([
                ButtonComponent::make('edit')
                    ->icon('LiEdit')
                    ->variant('text')
                    ->meta(['action' => 'edit', 'tooltip' => 'Edit section']),
                ButtonComponent::make('duplicate')
                    ->icon('LiCopy')
                    ->variant('text')
                    ->meta(['action' => 'duplicate', 'tooltip' => 'Duplicate section']),
                ButtonComponent::make('delete')
                    ->icon('LiTrash2')
                    ->variant('text')
                    ->meta(['action' => 'delete', 'tooltip' => 'Delete section']),
                ButtonComponent::make('more')
                    ->icon('LiMoreVertical')
                    ->variant('text')
                    ->meta(['action' => 'menu', 'tooltip' => 'More options']),
                ButtonComponent::make('collapse')
                    ->icon('LiChevronUp')
                    ->variant('text')
                    ->meta(['action' => 'toggle', 'tooltip' => 'Collapse section']),
            ])
            ->padding('sm')
            ->withBorder()
            ->configSections([
                'left' => [
                    'flex' => '1 1 auto',
                ],
                'right' => [
                    'display' => 'flex',
                    'gap' => '8px',
                    'alignItems' => 'center',
                ],
            ]);
    }

    /**
     * Example 11: Dashboard Widget Header
     * Widget/card header with refresh action
     */
    public static function dashboardWidgetHeader()
    {
        return HeaderSection::make('widget-header')
            ->left([
                TextComponent::make('widget-title')
                    ->content('Recent Activity')
                    ->variant('h6'),
            ])
            ->right([
                ButtonComponent::make('refresh')
                    ->icon('LiRefreshCw')
                    ->variant('text')
                    ->meta(['action' => 'refresh']),
                ButtonComponent::make('settings')
                    ->icon('LiSettings')
                    ->variant('text')
                    ->meta(['action' => 'settings']),
            ])
            ->padding('sm')
            ->withBorder();
    }

    /**
     * Example 12: Centered Modal Header
     * For important confirmations/alerts
     */
    public static function centeredAlertHeader()
    {
        return HeaderSection::make('alert-header')
            ->center([
                TextComponent::make('icon')
                    ->content('⚠️')
                    ->variant('h2'),
                TextComponent::make('title')
                    ->content('Are you sure?')
                    ->variant('h4'),
                TextComponent::make('message')
                    ->content('This action cannot be undone')
                    ->variant('body1')
                    ->meta(['color' => 'text-gray-600']),
            ])
            ->padding('lg');
    }

    /**
     * Example 13: Three-section Footer
     * All sections utilized
     */
    public static function fullWidthFooter()
    {
        return FooterComponent::make('full-footer')
            ->left([
                TextComponent::make('item-count')
                    ->content('Showing 1-10 of 245 items')
                    ->variant('body2'),
            ])
            ->center([
                ButtonComponent::make('prev')
                    ->icon('LiChevronLeft')
                    ->variant('outlined')
                    ->meta(['action' => 'prev']),
                TextComponent::make('page')
                    ->content('Page 1 of 25')
                    ->variant('body2'),
                ButtonComponent::make('next')
                    ->icon('LiChevronRight')
                    ->variant('outlined')
                    ->meta(['action' => 'next']),
            ])
            ->right([
                ButtonComponent::make('jump')
                    ->label('Jump to Page')
                    ->variant('text')
                    ->meta(['action' => 'jump']),
            ])
            ->padding('md')
            ->withBorder();
    }

    /**
     * Example 14: Using with DrawerSection
     * Complete drawer with header and footer
     */
    public static function completeDrawerExample()
    {
        return [
            'drawer' => [
                'name' => 'edit-user-drawer',
                'anchor' => 'right',
                'width' => '600px',
                'sections' => [
                    'header' => self::fullDrawerHeader()->toArray(),
                    'main' => [
                        // Your form or content here
                    ],
                    'footer' => self::fullDrawerFooter()->toArray(),
                ],
            ],
        ];
    }

    /**
     * Example 15: Using with ModalComponent
     * Complete modal with header and footer
     */
    public static function completeModalExample()
    {
        return [
            'modal' => [
                'name' => 'confirm-modal',
                'size' => 'md',
                'sections' => [
                    'header' => self::centeredAlertHeader()->toArray(),
                    'content' => [
                        // Your content here
                    ],
                    'footer' => self::simpleModalFooter()->toArray(),
                ],
            ],
        ];
    }
}
