<?php

/**
 * Tabs Example - Clean 4-Level Architecture
 */

use Litepie\Layout\LayoutBuilder;
use Litepie\Layout\Sections\TabsSection;
use Litepie\Layout\Components\CardComponent;
use Litepie\Layout\Components\FormComponent;

$layout = LayoutBuilder::create('tabs-demo', 'view');

// Create tabs
$tabs = TabsSection::make('profile-tabs')
    ->addTab('info', 'Info', ['icon' => 'user'])
    ->addTab('settings', 'Settings', ['icon' => 'settings'])
    ->activeTab('info');

// Add content to slots
$tabs->slot('info')->add(CardComponent::make('card')->title('Personal Info'));
$tabs->slot('settings')->add(FormComponent::make('form')->title('Settings'));

$layout->addComponent($tabs);

return $layout->render();
