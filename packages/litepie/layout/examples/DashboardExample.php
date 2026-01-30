<?php

/**
 * Dashboard Example - Clean 4-Level Architecture
 *
 * Demonstrates: Layout → Section → Slot → Component
 */

use Litepie\Layout\Components\StatsComponent;
use Litepie\Layout\Components\ChartComponent;
use Litepie\Layout\Components\TableComponent;
use Litepie\Layout\LayoutBuilder;
use Litepie\Layout\Sections\GridSection;
use Litepie\Layout\Sections\HeaderSection;

// Create dashboard layout
$layout = LayoutBuilder::create('admin-dashboard', 'dashboard')
    ->title('Dashboard');

// Header
$header = HeaderSection::make('main-header');
$header->slot('left')->breadcrumb('nav')->addItem('Home', '/')->addItem('Dashboard');
$layout->addComponent($header);

// Stats Grid
$statsGrid = GridSection::make('stats')->columns(4)->gap('lg');
$statsGrid->slot('items')
    ->add(StatsComponent::make('users')->title('Users')->value(1234)->trend('up'))
    ->add(StatsComponent::make('revenue')->title('Revenue')->value(98650)->prefix('$'))
    ->add(StatsComponent::make('orders')->title('Orders')->value(456))
    ->add(StatsComponent::make('growth')->title('Growth')->value('+12%'));
$layout->addComponent($statsGrid);

// Main Content Grid
$mainGrid = GridSection::make('main')->columns(2);
$mainGrid->slot('items')
    ->add(ChartComponent::make('chart')->title('Revenue')->type('line'))
    ->add(TableComponent::make('users')->title('Recent Users'));
$layout->addComponent($mainGrid);

// Render
return $layout->render();
