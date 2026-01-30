<?php

/**
 * Wizard Example - Clean 4-Level Architecture
 */

use Litepie\Layout\LayoutBuilder;
use Litepie\Layout\Sections\WizardSection;
use Litepie\Layout\Components\FormComponent;

$layout = LayoutBuilder::create('wizard-demo', 'wizard');

// Create wizard
$wizard = WizardSection::make('onboarding')
    ->addStep('account', 'Account', ['icon' => 'user'])
    ->addStep('profile', 'Profile', ['icon' => 'user-circle'])
    ->addStep('done', 'Done', ['icon' => 'check'])
    ->linear(true);

// Add content to step slots
$wizard->slot('account')->add(FormComponent::make('signup'));
$wizard->slot('profile')->add(FormComponent::make('profile'));
$wizard->slot('done')->add(FormComponent::make('complete'));

$layout->addComponent($wizard);

return $layout->render();
