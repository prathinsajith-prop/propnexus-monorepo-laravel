<?php

namespace App\Layouts\Slot\ProductProperty;

use App\Enums\ProductPropertyStatus;
use App\Forms\ProductProperty\ProductPropertyForm;
use Litepie\Layout\Components\BadgeComponent;
use Litepie\Layout\Components\ButtonComponent;
use Litepie\Layout\Components\CardComponent;
use Litepie\Layout\Components\CommentComponent;
use Litepie\Layout\Components\MediaComponent;
use Litepie\Layout\Components\TextComponent;
use Litepie\Layout\Sections\DetailSection;
use Litepie\Layout\Sections\FooterSection;
use Litepie\Layout\Sections\GridSection;
use Litepie\Layout\Sections\HeaderSection;
use Litepie\Layout\SlotManager;

/**
 * View Aside Slot — Product Property
 *
 * Builds the aside drawer for viewing a product property.
 */
class ViewAsideSlot
{
    /**
     * Build view product property aside.
     *
     * @param  array  $masterData  Master data for form dropdowns
     * @param  bool  $fullscreen  Whether to display fullscreen
     */
    public static function make(array $masterData = [], bool $fullscreen = false): array
    {
        $formComponent = ProductPropertyForm::make(
            'view-property-form',
            'GET',
            '/api/product-property/:id',
            $masterData,
            '/api/product-property/:id'
        )
            ->dataUrl('/api/product-property/:id')
            ->dataParams(['id' => ':id']);

        // Property overview card
        $overview = CardComponent::make('property-overview-card')
            ->addComponent(
                MediaComponent::make('property-image')
                    ->gallery()
                    ->grid()
                    ->columns(1)
                    ->lightbox(true)
                    ->captions(true)
                    ->aspectRatio('1:1')
                    ->width('300')
                    ->height('150')
                    ->gridColumnSpan(4)
            )
            ->addComponent(
                TextComponent::make('property-title')
                    ->content('')
                    ->variant('h5')
                    ->title(__('layout.property_title'))
                    ->meta(['fontWeight' => 'bold'])
                    ->gridColumnSpan(8)
            );

        $mainGrid = GridSection::make('view-property-main-grid')
            ->rows(1)
            ->gap('md');

        $mainGrid->add($overview);
        $mainGrid->add($formComponent);
        // $mainGrid->add(self::buildFollowupsCard());
        // $mainGrid->add(self::buildNotesCard());

        $aside = DetailSection::make('view-property')
            ->setHeader(self::buildHeader())
            ->setMain(
                SlotManager::make('view-property-main-slot')
                    ->setSection($mainGrid)
            )
            ->setFooter(self::buildFooter())
            ->toArray();

        if ($fullscreen && is_array($aside)) {
            $aside['width'] = '100vw';
            $aside['height'] = '100vh';
        }

        return $aside;
    }

    // ─────────────────────────────────────────────────────────────────────────

    private static function buildFollowupsCard(): CardComponent
    {
        $itemTemplate = CardComponent::make('followup-item')
            ->title(':{followup_title}')
            ->addComponent(
                TextComponent::make('description')
                    ->content(':{description}')
                    ->variant('caption')
                    ->meta(['key' => 'description', 'color' => 'text-gray-600'])
            )
            ->addComponent(
                TextComponent::make('followup-date')
                    ->content(':{followup_date_formatted}')
                    ->variant('caption')
                    ->meta([
                        'key' => 'followup_date_formatted',
                        'color' => 'text-gray-500',
                        'icon' => 'clock',
                        'iconPosition' => 'left',
                        'iconSize' => 'xs',
                    ])
            )
            ->addHeaderAction('', '#', [
                'icon' => 'pen',
                'iconOnly' => true,
                'variant' => 'text',
                'size' => 'sm',
                'data' => [
                    'component' => 'edit-property-followup',
                    'type' => 'modal',
                    'action' => 'edit',
                    'hasParent' => true,
                    'config' => [
                        'width' => '500px',
                        'height' => '100vh',
                        'anchor' => 'right',
                        'backdrop' => true,
                    ],
                    'params' => ['id' => ':property_id', 'followup_id' => ':eid'],
                    'url' => '/api/product-property/:id/followups/:followup_id',
                ],
                'meta' => ['tooltip' => __('layout.edit_followup')],
            ])
            ->addHeaderAction('', '#', [
                'icon' => 'binempty',
                'iconOnly' => true,
                'variant' => 'text',
                'size' => 'sm',
                'color' => 'danger',
                'data' => [
                    'component' => 'delete-property-followup',
                    'type' => 'confirm',
                    'action' => 'delete',
                    'hasParent' => true,
                    'method' => 'DELETE',
                    'url' => '/api/product-property/:id/followups/:followup_id',
                    'config' => [
                        'width' => '400px',
                        'height' => 'auto',
                        'anchor' => 'center',
                        'backdrop' => true,
                    ],
                    'params' => ['id' => ':property_id', 'followup_id' => ':eid'],
                ],
                'meta' => ['tooltip' => __('layout.delete_followup')],
            ])
            ->toArray();

        return CardComponent::make('followups-card')
            ->title(__('layout.create_followups'))
            ->variant('outlined')
            ->dataUrl('/api/product-property/:id/followups')
            ->dataParams(['id' => ':eid'])
            ->addHeaderButton(
                ButtonComponent::make('add-followup-btn')
                    ->icon('plus')
                    ->variant('outlined')
                    ->size('sm')
                    ->isIconButton(true)
                    ->data('component', 'create-property-followup')
                    ->data('type', 'modal')
                    ->data('action', 'create')
                    ->data('hasParent', true)
                    ->data('config', [
                        'width' => '500px',
                        'height' => '100vh',
                        'anchor' => 'right',
                        'backdrop' => true,
                    ])
                    ->data('params', ['id' => ':eid'])
                    ->meta(['tooltip' => __('layout.add_followup')])
            )
            ->meta([
                'emptyIcon' => 'listcheck',
                'emptyText' => __('layout.tasks_empty'),
                'emptySubtext' => __('layout.tasks_empty_hint'),
                'template' => $itemTemplate,
            ]);
    }

    private static function buildNotesCard(): CardComponent
    {
        $card = CardComponent::make('notes-card')
            ->title(__('layout.notes'))
            ->variant('outlined');

        $card->addComponent(
            CommentComponent::make('notes-comments')
                ->editing(true)
                ->deleting(true)
                ->markdown(false)
                ->fieldName('note')
                ->dataUrl('/api/product-property/:id/notes')
                ->dataParams(['id' => ':eid'])
                ->meta([
                    'emptyIcon' => 'chat',
                    'emptyText' => __('layout.notes_empty'),
                    'emptySubtext' => __('layout.notes_empty_hint'),
                ])
                ->gridColumnSpan(12)
        );

        return $card;
    }

    // ─────────────────────────────────────────────────────────────────────────

    private static function buildHeader(): SlotManager
    {
        $centerSlot = SlotManager::make('view-property-header-center')
            ->setConfig([
                'layout' => 'flex',
                'direction' => 'column',
                'gap' => '1',
                'justify' => 'start',
                'items' => 'start',
                'gridColumnSpan' => 6,
            ]);

        $centerSlot->setComponent(
            TextComponent::make('title')
                ->content(__('layout.property_details'))
                ->variant('h4')
                ->meta(['fontWeight' => 'bold'])
        );
        $centerSlot->setComponent(
            TextComponent::make('subtitle')
                ->content(__('layout.view_complete_property_info'))
                ->variant('caption')
                ->meta(['color' => 'text-gray-600'])
        );

        $rightSlot = SlotManager::make('view-property-header-right')
            ->setConfig([
                'layout' => 'flex',
                'direction' => 'row',
                'gap' => '2',
                'justify' => 'end',
                'items' => 'center',
                'gridColumnSpan' => 6,
            ]);

        $rightSlot->setComponent(
            BadgeComponent::make('status-badge')
                ->content(__('layout.active'))
                ->color('success')
                ->variant('standard')
                ->badgeConfig(ProductPropertyStatus::badgeConfig())
                ->meta(['size' => 'sm'])
        );

        $rightSlot->setComponent(
            ButtonComponent::make('edit-btn')
                ->icon('pen')
                ->variant('outlined')
                ->size('sm')
                ->isIconButton(true)
                ->data('component', 'edit-property-full')
                ->data('type', 'aside')
                ->data('action', 'edit')
                ->data('config', [
                    'width' => '800px',
                    'height' => '100vh',
                    'anchor' => 'right',
                    'backdrop' => true,
                ])
                ->dataParams(['id' => ':id'])
                ->dataUrl('/api/product-property/:id')
                ->meta([
                    'action' => 'edit',
                    'type' => 'aside',
                    'component' => 'edit-property-full',
                    'tooltip' => __('layout.tooltip_edit_property'),
                ])
        );

        $rightSlot->setComponent(
            ButtonComponent::make('delete-btn')
                ->icon('binempty')
                ->variant('outlined')
                ->size('sm')
                ->isIconButton(true)
                ->confirm([
                    'title' => __('layout.delete_property'),
                    'message' => __('layout.delete_property_confirmation'),
                    'confirmLabel' => __('layout.delete'),
                    'cancelLabel' => __('layout.cancel'),
                    'action' => 'delete',
                    'dataUrl' => '/api/product-property/:id',
                    'method' => 'delete',
                ])
                ->meta([
                    'action' => 'delete',
                    'tooltip' => __('layout.tooltip_delete_property'),
                    'color' => 'error',
                ])
        );

        $rightSlot->setComponent(
            ButtonComponent::make('close-btn')
                ->icon('cross')
                ->variant('outlined')
                ->meta(['action' => 'close'])
        );

        $headerSlot = SlotManager::make('header-slot');
        $headerSlot->setSection(
            HeaderSection::make('view-property-aside-header')
                ->setCenter($centerSlot)
                ->setRight($rightSlot)
                ->variant('elevated')
                ->padding('md')
        );

        return $headerSlot;
    }

    private static function buildFooter(): SlotManager
    {
        $footerRightSlot = SlotManager::make('view-property-footer-right')
            ->setConfig([
                'layout' => 'flex',
                'direction' => 'row',
                'gap' => '2',
                'justify' => 'end',
                'items' => 'center',
                'gridColumnSpan' => 12,
            ]);

        $footerRightSlot->setComponent(
            ButtonComponent::make('close-btn')
                ->label(__('layout.close'))
                ->variant('outlined')
                ->meta(['action' => 'close'])
        );

        $footerRightSlot->setComponent(
            ButtonComponent::make('fullscreen-btn')
                ->label(__('layout.fullscreen'))
                ->icon('expand')
                ->variant('text')
                ->data('component', 'view-property-full')
                ->data('type', 'aside')
                ->data('config', ['width' => '100vw', 'height' => '100vh', 'anchor' => 'right'])
                ->dataParams(['id' => ':id'])
                ->dataUrl('/api/product-property/:id')
                ->meta(['action' => 'open', 'tooltip' => __('layout.view_fullscreen')])
        );

        $footerSlot = SlotManager::make('footer-slot');
        $footerSlot->setSection(
            FooterSection::make('view-property-aside-footer')
                ->setRight($footerRightSlot)
                ->variant('elevated')
                ->padding('md')
        );

        return $footerSlot;
    }
}
