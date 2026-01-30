<?php

/**
 * Avatar Example
 *
 * Demonstrates the AvatarComponent with various configurations:
 * - Image avatars, text initials, and icon avatars
 * - Different sizes and shapes
 * - Status indicators and badges
 * - Avatar groups and stacks
 * - Clickable avatars with tooltips
 */

use Litepie\Layout\Components\AvatarComponent;
use Litepie\Layout\LayoutBuilder;

// Mock auth() helper for standalone script
if (! function_exists('auth')) {
    function auth()
    {
        return new class
        {
            public function id()
            {
                return 1;
            }

            public function user()
            {
                return (object) [
                    'id' => 1,
                    'name' => 'John Doe',
                    'email' => 'john@example.com',
                ];
            }
        };
    }
}

// Create avatar showcase layout
$layout = LayoutBuilder::create('avatar-showcase', 'showcase')
    ->title('Avatar Component Examples')

    // Header
    ->section('header', function ($section) {
        $section->text('title')
            ->content('# Avatar Component Showcase')
            ->align('center');

        $section->text('subtitle')
            ->content('Explore various avatar styles and configurations')
            ->align('center')
            ->meta(['color' => 'muted']);
    })

    // Main content
    ->section('body', function ($section) {
        // ========================================================================
        // Basic Avatars
        // ========================================================================
        $section->card('basic-avatars')
            ->title('Basic Avatars')
            ->subtitle('Different avatar sources and types');

        $section->grid('basic-grid')
            ->columns(4)
            ->gap('2rem')

            // Image avatar
            ->addComponent(
                AvatarComponent::make('image-avatar')
                    ->src('/images/avatars/john-doe.jpg')
                    ->alt('John Doe')
                    ->lg()
                    ->circle()
            )

            // Text initials avatar
            ->addComponent(
                AvatarComponent::make('initials-avatar')
                    ->initials('Jane Smith')
                    ->lg()
                    ->circle()
                    ->bgColor('#3b82f6')
                    ->textColor('#ffffff')
            )

            // Icon avatar
            ->addComponent(
                AvatarComponent::make('icon-avatar')
                    ->avatarIcon('user')
                    ->lg()
                    ->circle()
                    ->bgColor('#10b981')
                    ->textColor('#ffffff')
            )

            // Fallback avatar
            ->addComponent(
                AvatarComponent::make('fallback-avatar')
                    ->src('/images/broken-link.jpg')
                    ->alt('User')
                    ->lg()
                    ->circle()
                    ->fallbackType('icon')
                    ->fallbackIcon('user')
                    ->fallbackBgColor('#6b7280')
            );

        // ========================================================================
        // Size Variations
        // ========================================================================
        $section->card('size-variations')
            ->title('Size Variations')
            ->subtitle('Different avatar sizes from xs to 2xl');

        $section->grid('sizes-grid')
            ->columns(6)
            ->gap('1.5rem')

            ->addComponent(
                AvatarComponent::make('size-xs')
                    ->initials('XS')
                    ->xs()
                    ->bgColor('#ef4444')
                    ->textColor('#ffffff')
            )

            ->addComponent(
                AvatarComponent::make('size-sm')
                    ->initials('SM')
                    ->sm()
                    ->bgColor('#f59e0b')
                    ->textColor('#ffffff')
            )

            ->addComponent(
                AvatarComponent::make('size-md')
                    ->initials('MD')
                    ->md()
                    ->bgColor('#10b981')
                    ->textColor('#ffffff')
            )

            ->addComponent(
                AvatarComponent::make('size-lg')
                    ->initials('LG')
                    ->lg()
                    ->bgColor('#3b82f6')
                    ->textColor('#ffffff')
            )

            ->addComponent(
                AvatarComponent::make('size-xl')
                    ->initials('XL')
                    ->xl()
                    ->bgColor('#8b5cf6')
                    ->textColor('#ffffff')
            )

            ->addComponent(
                AvatarComponent::make('size-2xl')
                    ->initials('2X')
                    ->xxl()
                    ->bgColor('#ec4899')
                    ->textColor('#ffffff')
            );

        // ========================================================================
        // Shape Variations
        // ========================================================================
        $section->card('shape-variations')
            ->title('Shape Variations')
            ->subtitle('Circle, rounded, and square avatars');

        $section->grid('shapes-grid')
            ->columns(3)
            ->gap('2rem')

            ->addComponent(
                AvatarComponent::make('shape-circle')
                    ->text('AB')
                    ->lg()
                    ->circle()
                    ->bgColor('#3b82f6')
                    ->textColor('#ffffff')
            )

            ->addComponent(
                AvatarComponent::make('shape-rounded')
                    ->text('CD')
                    ->lg()
                    ->rounded()
                    ->bgColor('#10b981')
                    ->textColor('#ffffff')
            )

            ->addComponent(
                AvatarComponent::make('shape-square')
                    ->text('EF')
                    ->lg()
                    ->square()
                    ->bgColor('#8b5cf6')
                    ->textColor('#ffffff')
            );

        // ========================================================================
        // Status Indicators
        // ========================================================================
        $section->card('status-indicators')
            ->title('Status Indicators')
            ->subtitle('Avatars with online, offline, away, and busy status');

        $section->grid('status-grid')
            ->columns(5)
            ->gap('2rem')

            ->addComponent(
                AvatarComponent::make('status-online')
                    ->initials('ON')
                    ->lg()
                    ->bgColor('#3b82f6')
                    ->textColor('#ffffff')
                    ->online()
            )

            ->addComponent(
                AvatarComponent::make('status-offline')
                    ->initials('OF')
                    ->lg()
                    ->bgColor('#6b7280')
                    ->textColor('#ffffff')
                    ->offline()
            )

            ->addComponent(
                AvatarComponent::make('status-away')
                    ->initials('AW')
                    ->lg()
                    ->bgColor('#f59e0b')
                    ->textColor('#ffffff')
                    ->away()
            )

            ->addComponent(
                AvatarComponent::make('status-busy')
                    ->initials('BS')
                    ->lg()
                    ->bgColor('#ef4444')
                    ->textColor('#ffffff')
                    ->busy()
            )

            ->addComponent(
                AvatarComponent::make('status-dnd')
                    ->initials('DN')
                    ->lg()
                    ->bgColor('#7c3aed')
                    ->textColor('#ffffff')
                    ->dnd()
            );

        // ========================================================================
        // Badges
        // ========================================================================
        $section->card('badge-avatars')
            ->title('Avatars with Badges')
            ->subtitle('Notification badges on avatars');

        $section->grid('badge-grid')
            ->columns(4)
            ->gap('2rem')

            ->addComponent(
                AvatarComponent::make('badge-number')
                    ->initials('JD')
                    ->lg()
                    ->bgColor('#3b82f6')
                    ->textColor('#ffffff')
                    ->badge('5', 'error')
                    ->badgePosition('top-right')
            )

            ->addComponent(
                AvatarComponent::make('badge-new')
                    ->initials('JS')
                    ->lg()
                    ->bgColor('#10b981')
                    ->textColor('#ffffff')
                    ->badge('New', 'success')
                    ->badgePosition('top-right')
            )

            ->addComponent(
                AvatarComponent::make('badge-pro')
                    ->initials('AB')
                    ->lg()
                    ->bgColor('#8b5cf6')
                    ->textColor('#ffffff')
                    ->badge('Pro', 'warning')
                    ->badgePosition('bottom-right')
            )

            ->addComponent(
                AvatarComponent::make('badge-verified')
                    ->initials('CD')
                    ->lg()
                    ->bgColor('#ec4899')
                    ->textColor('#ffffff')
                    ->badge('✓', 'info')
                    ->badgePosition('bottom-left')
            );

        // ========================================================================
        // Style Variants
        // ========================================================================
        $section->card('style-variants')
            ->title('Style Variants')
            ->subtitle('Different visual styles and effects');

        $section->grid('variant-grid')
            ->columns(4)
            ->gap('2rem')

            ->addComponent(
                AvatarComponent::make('variant-default')
                    ->initials('DF')
                    ->lg()
                    ->variant('default')
                    ->bgColor('#3b82f6')
                    ->textColor('#ffffff')
            )

            ->addComponent(
                AvatarComponent::make('variant-outlined')
                    ->initials('OL')
                    ->lg()
                    ->outlined()
                    ->borderColor('#3b82f6')
                    ->borderWidth(2)
                    ->bgColor('#eff6ff')
                    ->textColor('#3b82f6')
            )

            ->addComponent(
                AvatarComponent::make('variant-bordered')
                    ->initials('BD')
                    ->lg()
                    ->bordered()
                    ->borderColor('#10b981')
                    ->borderWidth(3)
                    ->bgColor('#10b981')
                    ->textColor('#ffffff')
            )

            ->addComponent(
                AvatarComponent::make('variant-ring')
                    ->initials('RG')
                    ->lg()
                    ->bgColor('#8b5cf6')
                    ->textColor('#ffffff')
                    ->ring(true, '#8b5cf6')
            );

        // ========================================================================
        // Avatar Groups
        // ========================================================================
        $section->card('avatar-groups')
            ->title('Avatar Groups')
            ->subtitle('Stacked avatars for teams and groups');

        $section->grid('group-grid')
            ->columns(2)
            ->gap('2rem')

            // Horizontal stack
            ->addComponent(
                AvatarComponent::make('team-horizontal')
                    ->group()
                    ->addAvatar([
                        'src' => '/images/avatars/user1.jpg',
                        'alt' => 'User 1',
                    ])
                    ->addAvatar([
                        'text' => 'JS',
                        'bgColor' => '#3b82f6',
                        'textColor' => '#ffffff',
                    ])
                    ->addAvatar([
                        'text' => 'AB',
                        'bgColor' => '#10b981',
                        'textColor' => '#ffffff',
                    ])
                    ->addAvatar([
                        'text' => 'CD',
                        'bgColor' => '#8b5cf6',
                        'textColor' => '#ffffff',
                    ])
                    ->addAvatar([
                        'text' => 'EF',
                        'bgColor' => '#ef4444',
                        'textColor' => '#ffffff',
                    ])
                    ->maxVisible(3)
                    ->showCount(true)
                    ->stackDirection('horizontal')
            )

            // Vertical stack
            ->addComponent(
                AvatarComponent::make('team-vertical')
                    ->group()
                    ->addAvatar([
                        'text' => 'U1',
                        'bgColor' => '#ec4899',
                        'textColor' => '#ffffff',
                    ])
                    ->addAvatar([
                        'text' => 'U2',
                        'bgColor' => '#f59e0b',
                        'textColor' => '#ffffff',
                    ])
                    ->addAvatar([
                        'text' => 'U3',
                        'bgColor' => '#14b8a6',
                        'textColor' => '#ffffff',
                    ])
                    ->maxVisible(3)
                    ->showCount(false)
                    ->stackDirection('vertical')
            );

        // ========================================================================
        // Clickable Avatars
        // ========================================================================
        $section->card('clickable-avatars')
            ->title('Clickable Avatars')
            ->subtitle('Avatars with links and tooltips');

        $section->grid('clickable-grid')
            ->columns(3)
            ->gap('2rem')

            ->addComponent(
                AvatarComponent::make('profile-link')
                    ->initials('John Doe')
                    ->lg()
                    ->bgColor('#3b82f6')
                    ->textColor('#ffffff')
                    ->href('/profile/john-doe')
                    ->tooltip('View John\'s Profile', 'top')
            )

            ->addComponent(
                AvatarComponent::make('settings-link')
                    ->avatarIcon('settings')
                    ->lg()
                    ->bgColor('#10b981')
                    ->textColor('#ffffff')
                    ->href('/settings')
                    ->tooltip('Settings')
            )

            ->addComponent(
                AvatarComponent::make('messages-link')
                    ->avatarIcon('mail')
                    ->lg()
                    ->bgColor('#8b5cf6')
                    ->textColor('#ffffff')
                    ->href('/messages')
                    ->badge('3', 'error')
                    ->tooltip('3 New Messages')
            );

        // ========================================================================
        // Advanced Examples
        // ========================================================================
        $section->card('advanced-examples')
            ->title('Advanced Examples')
            ->subtitle('Complex avatar configurations');

        $section->grid('advanced-grid')
            ->columns(3)
            ->gap('2rem')

            // Full-featured avatar
            ->addComponent(
                AvatarComponent::make('vip-user')
                    ->src('/images/avatars/vip.jpg')
                    ->alt('VIP User')
                    ->xl()
                    ->circle()
                    ->bordered()
                    ->borderColor('#fbbf24')
                    ->borderWidth(3)
                    ->ring(true, '#fbbf24')
                    ->online()
                    ->badge('VIP', 'warning')
                    ->badgePosition('top-right')
                    ->href('/users/vip')
                    ->tooltip('Premium Member')
            )

            // User with notifications
            ->addComponent(
                AvatarComponent::make('notification-user')
                    ->initials('Admin')
                    ->xl()
                    ->circle()
                    ->bgColor('#ef4444')
                    ->textColor('#ffffff')
                    ->busy()
                    ->statusPosition('bottom-right')
                    ->badge('12', 'error')
                    ->badgePosition('top-right')
                    ->href('/admin/notifications')
                    ->tooltip('12 Pending Actions')
            )

            // Verified professional
            ->addComponent(
                AvatarComponent::make('verified-pro')
                    ->text('MP')
                    ->xl()
                    ->rounded()
                    ->bgColor('#8b5cf6')
                    ->textColor('#ffffff')
                    ->outlined()
                    ->borderColor('#8b5cf6')
                    ->borderWidth(2)
                    ->online()
                    ->badge('✓', 'success')
                    ->badgePosition('bottom-left')
                    ->href('/professionals/mike')
                    ->tooltip('Mike - Verified Professional')
            );
    })

    // Footer
    ->section('footer', function ($section) {
        $section->text('footer-text')
            ->content('Avatar Component - Comprehensive Examples')
            ->align('center')
            ->meta(['color' => 'muted']);
    });

// Render the layout
return $layout->render();
