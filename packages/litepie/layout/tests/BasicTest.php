<?php

namespace Litepie\Layout\Tests;

use Litepie\Layout\Field;
use Litepie\Layout\Layout;
use Litepie\Layout\LayoutBuilder;
use Litepie\Layout\Section;
use Litepie\Layout\Subsection;

/**
 * Basic test examples for the Layout package
 *
 * Note: This is a simple example. For production use, install PHPUnit
 * and use proper test framework integration.
 */

// Test Field Creation
echo "Testing Field Creation...\n";
$field = Field::make('email')
    ->type('email')
    ->label('Email Address')
    ->required()
    ->placeholder('your@email.com');

assert($field->getName() === 'email', 'Field name should be "email"');
assert($field->getType() === 'email', 'Field type should be "email"');
assert($field->isRequired() === true, 'Field should be required');
assert($field->getPlaceholder() === 'your@email.com', 'Placeholder should match');
echo "✓ Field creation tests passed\n\n";

// Test Subsection
echo "Testing Subsection...\n";
$subsection = Subsection::make('contact_info')
    ->label('Contact Information')
    ->description('Your contact details');

$subsection->addField($field);
assert($subsection->getName() === 'contact_info', 'Subsection name should match');
assert(count($subsection->getFields()) === 1, 'Should have 1 field');
assert($subsection->getField('email') !== null, 'Should find email field');
echo "✓ Subsection tests passed\n\n";

// Test Section
echo "Testing Section...\n";
$section = Section::make('personal_info')
    ->label('Personal Information')
    ->icon('user');

$section->addSubsection($subsection);
assert($section->getName() === 'personal_info', 'Section name should match');
assert(count($section->getSubsections()) === 1, 'Should have 1 subsection');
assert($section->getIcon() === 'user', 'Icon should match');
echo "✓ Section tests passed\n\n";

// Test Layout Builder
echo "Testing Layout Builder...\n";
$builder = LayoutBuilder::create('user', 'profile')
    ->section('info')
    ->label('User Info')
    ->subsection('basic')
    ->field('name')->type('text')->label('Name')->end()
    ->field('email')->type('email')->label('Email')->end()
    ->endSubsection()
    ->endSection();

$layout = $builder->build();
assert($layout->getModule() === 'user', 'Module should be "user"');
assert($layout->getContext() === 'profile', 'Context should be "profile"');
assert(count($layout->getSections()) === 1, 'Should have 1 section');
assert(count($layout->getAllFields()) === 2, 'Should have 2 fields total');
echo "✓ Layout builder tests passed\n\n";

// Test toArray Conversion
echo "Testing toArray Conversion...\n";
$array = $layout->toArray();
assert(is_array($array), 'Should return array');
assert(isset($array['module']), 'Should have module key');
assert(isset($array['context']), 'Should have context key');
assert(isset($array['sections']), 'Should have sections key');
echo "✓ Array conversion tests passed\n\n";

// Test Field Retrieval
echo "Testing Field Retrieval...\n";
$nameField = $layout->getFieldByName('name');
assert($nameField !== null, 'Should find name field');
assert($nameField->getLabel() === 'Name', 'Field label should match');

$emailField = $layout->getField('info', 'basic', 'email');
assert($emailField !== null, 'Should find email field via section/subsection');
assert($emailField->getType() === 'email', 'Email field type should match');
echo "✓ Field retrieval tests passed\n\n";

// Test Validation Rules
echo "Testing Validation Rules...\n";
$passwordField = Field::make('password')
    ->type('password')
    ->required()
    ->minLength(8)
    ->maxLength(50);

$rules = $passwordField->getRules();
assert(in_array('required', $rules), 'Should have required rule');
assert(in_array('min:8', $rules), 'Should have min length rule');
assert(in_array('max:50', $rules), 'Should have max length rule');
echo "✓ Validation rules tests passed\n\n";

// Test Select Field Options
echo "Testing Select Field Options...\n";
$selectField = Field::make('country')
    ->type('select')
    ->options([
        'us' => 'United States',
        'uk' => 'United Kingdom',
        'ca' => 'Canada',
    ])
    ->default('us');

$options = $selectField->getOptions();
assert(count($options) === 3, 'Should have 3 options');
assert($options['us'] === 'United States', 'US option should match');
assert($selectField->getDefault() === 'us', 'Default should be "us"');
echo "✓ Select field tests passed\n\n";

// Test Field Attributes
echo "Testing Field Attributes...\n";
$textField = Field::make('username')
    ->attribute('class', 'form-control')
    ->attribute('data-validation', 'username')
    ->attributes(['autocomplete' => 'off']);

$attrs = $textField->getAttributes();
assert($attrs['class'] === 'form-control', 'Class attribute should match');
assert($attrs['data-validation'] === 'username', 'Data attribute should match');
assert($attrs['autocomplete'] === 'off', 'Autocomplete should be off');
echo "✓ Field attributes tests passed\n\n";

// Test Visibility
echo "Testing Visibility Controls...\n";
$visibleField = Field::make('visible')->visible(true);
$hiddenField = Field::make('hidden')->hidden();
$visibleSection = Section::make('visible_section')->visible(true);
$hiddenSection = Section::make('hidden_section')->hidden();

assert($visibleField->isVisible() === true, 'Visible field should be visible');
assert($hiddenField->isVisible() === false, 'Hidden field should not be visible');
assert($visibleSection->isVisible() === true, 'Visible section should be visible');
assert($hiddenSection->isVisible() === false, 'Hidden section should not be visible');
echo "✓ Visibility tests passed\n\n";

// Test Meta Data
echo "Testing Meta Data...\n";
$fieldWithMeta = Field::make('custom')
    ->meta(['custom_key' => 'custom_value', 'help_text' => 'Help message']);

$meta = $fieldWithMeta->getMeta();
assert($meta['custom_key'] === 'custom_value', 'Meta data should match');
assert($meta['help_text'] === 'Help message', 'Help text should match');
echo "✓ Meta data tests passed\n\n";

// Test Field Ordering
echo "Testing Field Ordering...\n";
$field1 = Field::make('field1')->order(3);
$field2 = Field::make('field2')->order(1);
$field3 = Field::make('field3')->order(2);

assert($field1->getOrder() === 3, 'Field 1 order should be 3');
assert($field2->getOrder() === 1, 'Field 2 order should be 1');
assert($field3->getOrder() === 2, 'Field 3 order should be 2');
echo "✓ Field ordering tests passed\n\n";

echo "\n=================================\n";
echo "All tests passed successfully! ✓\n";
echo "=================================\n";
