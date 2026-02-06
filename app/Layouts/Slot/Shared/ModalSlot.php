<?php

namespace App\Layouts\Slot\Shared;

use Litepie\Layout\Components\ButtonComponent;
use Litepie\Layout\Components\TextComponent;
use Litepie\Layout\Sections\DetailSection;
use Litepie\Layout\Sections\FooterSection;
use Litepie\Layout\Sections\GridSection;
use Litepie\Layout\Sections\HeaderSection;
use Litepie\Layout\Sections\RowSection;
use Litepie\Layout\SlotManager;

/**
 * Shared Modal Slot Builder
 * 
 * Provides ONLY generic, reusable modal configurations that can be used
 * across any module. Module-specific modals should be in their respective
 * slot folders (Blog, Listing, etc.)
 * 
 * Use this for:
 * - Generic confirmation modals
 * - Generic alert modals
 * - Generic info modals
 * - Any truly cross-module modal patterns
 * 
 * DO NOT add module-specific modals here!
 * 
 * @package App\Layouts\Slot\Shared
 */
class ModalSlot
{
    /**
     * Build generic confirmation modal
     *
     * This is a truly reusable modal that can be used by any module
     * for general confirmation actions.
     *
     * @param array $options [
     *   'title' => string,          // Modal title
     *   'message' => string,        // Confirmation message
     *   'confirmLabel' => string,   // Confirm button label (default: 'Confirm')
     *   'confirmAction' => string,  // Action to trigger (default: 'confirm')
     *   'confirmColor' => string,   // Button color (default: 'primary')
     *   'confirmIcon' => string,    // Optional icon for confirm button
     * ]
     * @return array Modal definition
     */
    public static function confirmation(array $options = []): array
    {
        $defaults = [
            'title' => 'Confirm Action',
            'message' => 'Are you sure you want to proceed?',
            'confirmLabel' => 'Confirm',
            'confirmAction' => 'confirm',
            'confirmColor' => 'primary',
            'confirmIcon' => null,
        ];

        $config = array_merge($defaults, $options);
        // Build header
        $centerSlot = SlotManager::make('confirm-modal-header-center');
        $centerSlot->setComponent(
            TextComponent::make('title')
                ->content($config['title'])
                ->variant('h4')
                ->meta(['fontWeight' => 'bold'])
        );

        $rightSlot = SlotManager::make('confirm-modal-header-right');
        $rightSlot->setComponent(
            ButtonComponent::make('close-btn')
                ->icon('cross')
                ->variant('text')
                ->meta(['action' => 'close'])
        );

        $headerSlot = SlotManager::make('header-slot');
        $headerSlot->setSection(
            HeaderSection::make('confirm-modal-header')
                ->setCenter($centerSlot)
                ->setRight($rightSlot)
                ->variant('elevated')
                ->padding('md')
        );

        // Build main content
        $mainGrid = GridSection::make('confirm-modal-main-grid', 1)
            ->rows(1)
            ->gap('md');
        $mainGrid->add(
            TextComponent::make('message')
                ->content($config['message'])
                ->variant('body1')
                ->meta(['color' => 'text-gray-700'])
        );

        // Build footer with action buttons
        $footerRightSlot = SlotManager::make('confirm-modal-footer-right')->setSection(
            RowSection::make('confirm-modal-footer-row')
                ->gap('xs')
                ->align('right')
                ->justify('end')
                ->add(
                    ButtonComponent::make('cancel-btn')
                        ->label('Cancel')
                        ->variant('outlined')
                        ->meta(['action' => 'close'])
                )
                ->add(
                    ($config['confirmIcon']
                        ? ButtonComponent::make('confirm-btn')->icon($config['confirmIcon'])
                        : ButtonComponent::make('confirm-btn')
                    )
                        ->label($config['confirmLabel'])
                        ->variant('contained')
                        ->meta([
                            'action' => $config['confirmAction'],
                            'color' => $config['confirmColor']
                        ])
                )
        );

        $footerSlot = SlotManager::make('footer-slot');
        $footerSlot->setSection(
            FooterSection::make('confirm-modal-footer')
                ->setRight($footerRightSlot)
                ->variant('elevated')
                ->padding('md')
        );

        return DetailSection::make('confirmation-modal')
            ->setHeader($headerSlot)
            ->setMain(
                SlotManager::make('confirm-modal-main-slot')
                    ->setSection($mainGrid)
            )
            ->setFooter($footerSlot)
            ->toArray();
    }

    /**
     * Build generic alert/info modal
     *
     * Simple modal for displaying information or alerts to the user.
     * No form, just a message with a close button.
     *
     * @param array $options [
     *   'title' => string,        // Modal title (required)
     *   'message' => string,      // Alert message (required)
     *   'variant' => string,      // Message variant: info, success, warning, error (default: 'info')
     *   'buttonLabel' => string,  // Button label (default: 'OK')
     * ]
     * @return array Modal definition
     */
    public static function alert(array $options = []): array
    {
        $defaults = [
            'title' => 'Notice',
            'message' => '',
            'variant' => 'info',
            'buttonLabel' => 'OK',
        ];

        $config = array_merge($defaults, $options);

        // Color mapping for variants
        $colorMap = [
            'info' => ['title' => 'text-blue-600', 'text' => 'text-gray-700'],
            'success' => ['title' => 'text-green-600', 'text' => 'text-gray-700'],
            'warning' => ['title' => 'text-yellow-600', 'text' => 'text-gray-700'],
            'error' => ['title' => 'text-red-600', 'text' => 'text-gray-700'],
        ];

        $colors = $colorMap[$config['variant']] ?? $colorMap['info'];

        // Build header
        $centerSlot = SlotManager::make('alert-modal-header-center');
        $centerSlot->setComponent(
            TextComponent::make('title')
                ->content($config['title'])
                ->variant('h4')
                ->meta(['fontWeight' => 'bold', 'color' => $colors['title']])
        );

        $rightSlot = SlotManager::make('alert-modal-header-right');
        $rightSlot->setComponent(
            ButtonComponent::make('close-btn')
                ->icon('cross')
                ->variant('text')
                ->meta(['action' => 'close'])
        );

        $headerSlot = SlotManager::make('header-slot');
        $headerSlot->setSection(
            HeaderSection::make('alert-modal-header')
                ->setCenter($centerSlot)
                ->setRight($rightSlot)
                ->variant('elevated')
                ->padding('md')
        );

        // Build main content
        $mainGrid = GridSection::make('alert-modal-main-grid', 1)
            ->rows(1)
            ->gap('md');
        $mainGrid->add(
            TextComponent::make('message')
                ->content($config['message'])
                ->variant('body1')
                ->meta(['color' => $colors['text']])
        );

        // Build footer with single OK button
        $footerRightSlot = SlotManager::make('alert-modal-footer-right')->setSection(
            RowSection::make('alert-modal-footer-row')
                ->gap('xs')
                ->align('right')
                ->justify('end')
                ->add(
                    ButtonComponent::make('ok-btn')
                        ->label($config['buttonLabel'])
                        ->variant('contained')
                        ->meta([
                            'action' => 'close',
                            'color' => $config['variant'] === 'error' ? 'danger' : 'primary'
                        ])
                )
        );

        $footerSlot = SlotManager::make('footer-slot');
        $footerSlot->setSection(
            FooterSection::make('alert-modal-footer')
                ->setRight($footerRightSlot)
                ->variant('elevated')
                ->padding('md')
        );

        return DetailSection::make('alert-modal')
            ->setHeader($headerSlot)
            ->setMain(
                SlotManager::make('alert-modal-main-slot')
                    ->setSection($mainGrid)
            )
            ->setFooter($footerSlot)
            ->toArray();
    }
}
