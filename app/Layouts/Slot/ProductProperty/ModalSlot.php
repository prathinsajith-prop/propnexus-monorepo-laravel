<?php

namespace App\Layouts\Slot\ProductProperty;

use App\Forms\ProductProperty\ProductPropertyFollowUpsForm;
use App\Forms\ProductProperty\ProductPropertyForm;
use Litepie\Layout\Components\ButtonComponent;
use Litepie\Layout\Components\TextComponent;
use Litepie\Layout\Sections\DetailSection;
use Litepie\Layout\Sections\FooterSection;
use Litepie\Layout\Sections\GridSection;
use Litepie\Layout\Sections\HeaderSection;
use Litepie\Layout\SlotManager;

/**
 * Modal Slot — Product Property
 *
 * Provides reusable modal configurations for product property operations.
 * Follows the same array parameter pattern as ModalSlot for Listing.
 */
class ModalSlot
{
    /**
     * Build create product property modal.
     *
     * @param  array  $options  [
     *                          'masterData' => array,   // Master data for form
     *                          'apiUrl'     => string,  // API endpoint (default: /api/product-property)
     *                          'method'     => string,  // HTTP method (default: POST)
     *                          ]
     * @return array Modal definition
     */
    public static function createProperty(array $options = []): array
    {
        $config = array_merge([
            'masterData' => [],
            'apiUrl' => '/api/product-property',
            'method' => 'POST',
        ], $options);

        $formComponent = ProductPropertyForm::make(
            'create-property-form-modal',
            $config['method'],
            $config['apiUrl'],
            $config['masterData'],
            null,
            true
        );

        $mainGrid = GridSection::make('create-property-modal-main-grid', 1)
            ->rows(1)
            ->gap('md');
        $mainGrid->add($formComponent);

        // Header
        $centerSlot = SlotManager::make('create-property-modal-header-center')
            ->setConfig([
                'layout' => 'flex',
                'direction' => 'column',
                'gap' => '1',
                'justify' => 'center',
                'items' => 'start',
                'gridColumnSpan' => 6,
            ]);

        $centerSlot->setComponent(
            TextComponent::make('title')
                ->content(__('layout.create_new_property'))
                ->variant('h4')
                ->meta(['fontWeight' => 'bold'])
        );

        $rightSlot = SlotManager::make('create-property-modal-header-right')
            ->setConfig([
                'layout' => 'flex',
                'direction' => 'row',
                'gap' => '2',
                'justify' => 'end',
                'items' => 'center',
                'gridColumnSpan' => 6,
            ]);

        $rightSlot->setComponent(
            ButtonComponent::make('close-btn')
                ->icon('cross')
                ->variant('text')
                ->meta(['action' => 'close'])
        );

        $headerSlot = SlotManager::make('header-slot');
        $headerSlot->setSection(
            HeaderSection::make('create-property-modal-header')
                ->setCenter($centerSlot)
                ->setRight($rightSlot)
                ->variant('elevated')
                ->padding('md')
        );

        // Footer
        $footerRightSlot = SlotManager::make('create-property-modal-footer-right')
            ->setConfig([
                'layout' => 'flex',
                'direction' => 'row',
                'gap' => '2',
                'justify' => 'end',
                'items' => 'center',
                'gridColumnSpan' => 12,
            ]);

        $footerRightSlot->setComponent(
            ButtonComponent::make('cancel-btn')
                ->label(__('layout.cancel'))
                ->variant('outlined')
                ->meta(['action' => 'close'])
        );

        $footerRightSlot->setComponent(
            ButtonComponent::make('create-btn')
                ->label(__('layout.create_property'))
                ->icon('check')
                ->variant('contained')
                ->type('submit')
                ->color('primary')
                ->data('method', $config['method'])
                ->dataUrl($config['apiUrl'])
                ->meta(['action' => 'submit'])
        );

        $footerSlot = SlotManager::make('footer-slot');
        $footerSlot->setSection(
            FooterSection::make('create-property-modal-footer')
                ->setRight($footerRightSlot)
                ->variant('elevated')
                ->padding('md')
        );

        return DetailSection::make('create-property-modal')
            ->setHeader($headerSlot)
            ->setMain(
                SlotManager::make('create-property-modal-main-slot')
                    ->setSection($mainGrid)
            )
            ->setFooter($footerSlot)
            ->toArray();
    }

    /**
     * Build delete product property confirmation modal.
     *
     * @param  array  $options  [
     *                          'itemName' => string|null, // Optional property title for the message
     *                          'apiUrl'   => string,      // API endpoint (default: /api/product-property/:id)
     *                          'method'   => string,      // HTTP method (default: DELETE)
     *                          ]
     * @return array Modal definition
     */
    public static function deleteProperty(array $options = []): array
    {
        $config = array_merge([
            'itemName' => null,
            'apiUrl' => '/api/product-property/:id',
            'method' => 'DELETE',
        ], $options);

        $title = __('layout.delete_property');
        $message = $config['itemName']
            ? __('layout.delete_item_confirmation', ['item' => $config['itemName']])
            : __('layout.delete_property_confirmation');

        // Header
        $centerSlot = SlotManager::make('delete-property-modal-header-center')
            ->setConfig([
                'layout' => 'flex',
                'direction' => 'column',
                'gap' => '1',
                'justify' => 'center',
            ]);

        $centerSlot->setComponent(
            TextComponent::make('title')
                ->content($title)
                ->variant('h4')
                ->meta(['fontWeight' => 'bold', 'color' => 'text-red-600'])
        );

        $rightSlot = SlotManager::make('delete-property-modal-header-right')
            ->setConfig([
                'layout' => 'flex',
                'direction' => 'row',
                'gap' => '2',
                'justify' => 'end',
                'items' => 'center',
            ]);

        $rightSlot->setComponent(
            ButtonComponent::make('close-btn')
                ->icon('cross')
                ->variant('text')
                ->meta(['action' => 'close'])
        );

        $headerSlot = SlotManager::make('header-slot');
        $headerSlot->setSection(
            HeaderSection::make('delete-property-modal-header')
                ->setCenter($centerSlot)
                ->setRight($rightSlot)
                ->variant('elevated')
                ->padding('md')
        );

        // Body
        $bodySlot = SlotManager::make('delete-property-modal-body');
        $bodySlot->setComponent(
            TextComponent::make('message')
                ->content($message)
                ->variant('body1')
        );

        // Footer
        $footerRightSlot = SlotManager::make('delete-property-modal-footer-right')
            ->setConfig([
                'layout' => 'flex',
                'direction' => 'row',
                'gap' => '2',
                'justify' => 'end',
                'items' => 'center',
            ]);

        $footerRightSlot->setComponent(
            ButtonComponent::make('cancel-btn')
                ->label(__('layout.cancel'))
                ->variant('outlined')
                ->meta(['action' => 'close'])
        );

        $footerRightSlot->setComponent(
            ButtonComponent::make('delete-btn')
                ->label(__('layout.delete'))
                ->icon('binempty')
                ->variant('contained')
                ->color('error')
                ->data('method', $config['method'])
                ->dataUrl($config['apiUrl'])
                ->meta(['action' => 'submit'])
        );

        $footerSlot = SlotManager::make('footer-slot');
        $footerSlot->setSection(
            FooterSection::make('delete-property-modal-footer')
                ->setRight($footerRightSlot)
                ->variant('elevated')
                ->padding('md')
        );

        return DetailSection::make('delete-property-modal')
            ->setHeader($headerSlot)
            ->setMain($bodySlot)
            ->setFooter($footerSlot)
            ->toArray();
    }

    /**
     * Build create property follow-up modal.
     *
     * @param  array  $options  [
     *                          'apiUrl' => string,  // API endpoint (default: /api/product-property/:id/followups)
     *                          'method' => string,  // HTTP method (default: POST)
     *                          ]
     * @return array Modal definition
     */
    public static function createFollowup(array $options = []): array
    {
        $config = array_merge([
            'apiUrl' => '/api/product-property/:id/followups',
            'method' => 'POST',
        ], $options);

        $formComponent = ProductPropertyFollowUpsForm::make(
            'create-followup-form',
            $config['method'],
            $config['apiUrl']
        );

        $mainGrid = GridSection::make('create-followup-modal-main-grid', 1)
            ->rows(1)
            ->gap('md');
        $mainGrid->add($formComponent);

        // Header
        $centerSlot = SlotManager::make('create-followup-modal-header-center')
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
                ->content(__('layout.create_property_followup'))
                ->variant('h4')
                ->meta(['fontWeight' => 'bold'])
        );

        $headerRightSlot = SlotManager::make('create-followup-modal-header-right')
            ->setConfig([
                'layout' => 'flex',
                'direction' => 'row',
                'gap' => '2',
                'justify' => 'end',
                'items' => 'center',
                'gridColumnSpan' => 6,
            ]);

        $headerRightSlot->setComponent(
            ButtonComponent::make('close-btn')
                ->icon('cross')
                ->variant('text')
                ->meta(['action' => 'close'])
        );

        $headerSlot = SlotManager::make('header-slot');
        $headerSlot->setSection(
            HeaderSection::make('create-followup-modal-header')
                ->setCenter($centerSlot)
                ->setRight($headerRightSlot)
                ->variant('elevated')
                ->padding('md')
        );

        // Footer
        $footerRightSlot = SlotManager::make('create-followup-modal-footer-right')
            ->setConfig([
                'layout' => 'flex',
                'direction' => 'row',
                'gap' => '2',
                'justify' => 'end',
                'items' => 'center',
                'gridColumnSpan' => 12,
            ]);

        $footerRightSlot->setComponent(
            ButtonComponent::make('cancel-btn')
                ->label(__('layout.cancel'))
                ->variant('outlined')
                ->meta(['action' => 'close'])
        );

        $footerRightSlot->setComponent(
            ButtonComponent::make('save-btn')
                ->label(__('layout.save_followup'))
                ->icon('check')
                ->variant('contained')
                ->type('submit')
                ->color('primary')
                ->data('method', $config['method'])
                ->dataUrl($config['apiUrl'])
                ->dataParams(['id' => ':id'])
                ->meta(['action' => 'submit'])
        );

        $footerSlot = SlotManager::make('footer-slot');
        $footerSlot->setSection(
            FooterSection::make('create-followup-modal-footer')
                ->setRight($footerRightSlot)
                ->variant('elevated')
                ->padding('md')
        );

        return DetailSection::make('create-property-followup')
            ->setHeader($headerSlot)
            ->setMain(
                SlotManager::make('create-followup-modal-main-slot')
                    ->setSection($mainGrid)
            )
            ->setFooter($footerSlot)
            ->toArray();
    }

    /**
     * Build edit product property follow-up modal.
     *
     * @param  array  $options  [
     *                          'apiUrl'  => string, // PUT endpoint
     *                          'dataUrl' => string, // GET endpoint to pre-populate form
     *                          'method'  => string, // HTTP method (default: PUT)
     *                          ]
     * @return array Modal definition
     */
    public static function editFollowup(array $options = []): array
    {
        $config = array_merge([
            'apiUrl' => '/api/product-property/:id/followups/:followup_id',
            'dataUrl' => '/api/product-property/:id/followups/:followup_id',
            'method' => 'PUT',
        ], $options);

        $formComponent = ProductPropertyFollowUpsForm::make(
            'edit-followup-form',
            $config['method'],
            $config['apiUrl'],
            $config['dataUrl']
        );

        $mainGrid = GridSection::make('edit-followup-modal-main-grid', 1)
            ->rows(1)
            ->gap('md');
        $mainGrid->add($formComponent);

        // Header
        $centerSlot = SlotManager::make('edit-followup-modal-header-center')
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
                ->content(__('layout.edit_property_followup'))
                ->variant('h4')
                ->meta(['fontWeight' => 'bold'])
        );

        $headerRightSlot = SlotManager::make('edit-followup-modal-header-right')
            ->setConfig([
                'layout' => 'flex',
                'direction' => 'row',
                'gap' => '2',
                'justify' => 'end',
                'items' => 'center',
                'gridColumnSpan' => 6,
            ]);

        $headerRightSlot->setComponent(
            ButtonComponent::make('close-btn')
                ->icon('cross')
                ->variant('text')
                ->meta(['action' => 'close'])
        );

        $headerSlot = SlotManager::make('edit-followup-header-slot');
        $headerSlot->setSection(
            HeaderSection::make('edit-followup-modal-header')
                ->setCenter($centerSlot)
                ->setRight($headerRightSlot)
                ->variant('elevated')
                ->padding('md')
        );

        // Footer
        $footerRightSlot = SlotManager::make('edit-followup-modal-footer-right')
            ->setConfig([
                'layout' => 'flex',
                'direction' => 'row',
                'gap' => '2',
                'justify' => 'end',
                'items' => 'center',
                'gridColumnSpan' => 12,
            ]);

        $footerRightSlot->setComponent(
            ButtonComponent::make('cancel-btn')
                ->label(__('layout.cancel'))
                ->variant('outlined')
                ->meta(['action' => 'close'])
        );

        $footerRightSlot->setComponent(
            ButtonComponent::make('update-btn')
                ->label(__('layout.update_followup'))
                ->icon('check')
                ->variant('contained')
                ->type('submit')
                ->color('primary')
                ->data('method', $config['method'])
                ->dataUrl($config['apiUrl'])
                ->dataParams(['id' => ':id', 'followup_id' => ':followup_id'])
                ->meta(['action' => 'submit'])
        );

        $footerSlot = SlotManager::make('edit-followup-footer-slot');
        $footerSlot->setSection(
            FooterSection::make('edit-followup-modal-footer')
                ->setRight($footerRightSlot)
                ->variant('elevated')
                ->padding('md')
        );

        return DetailSection::make('edit-property-followup')
            ->setHeader($headerSlot)
            ->setMain(
                SlotManager::make('edit-followup-modal-main-slot')
                    ->setSection($mainGrid)
            )
            ->setFooter($footerSlot)
            ->toArray();
    }
}
