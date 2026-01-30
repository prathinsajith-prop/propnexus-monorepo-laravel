<?php

/**
 * Basic Form Example
 *
 * Demonstrates creating a simple contact form with validation.
 */

use Litepie\Layout\Facades\Layout;

// Create a contact form layout
$layout = Layout::create('contact-form')
    ->title('Contact Us')

    ->section('main', function ($section) {
        // Alert message
        $section->alert('info')
            ->content('Fill out the form below and we\'ll get back to you within 24 hours.')
            ->variant('info')
            ->icon('info');

        // Contact form
        $section->form('contact')
            ->title('Send us a message')
            ->action('/contact/submit')
            ->method('POST')

            // Name field
            ->addField('name', 'text', 'Your Name', [
                'placeholder' => 'John Doe',
                'required' => true,
                'help' => 'Please enter your full name',
            ])

            // Email field
            ->addField('email', 'email', 'Email Address', [
                'placeholder' => 'john@example.com',
                'required' => true,
            ])

            // Phone field (optional)
            ->addField('phone', 'tel', 'Phone Number', [
                'placeholder' => '+1 (555) 123-4567',
                'required' => false,
            ])

            // Subject field
            ->addField('subject', 'select', 'Subject', [
                'required' => true,
                'options' => [
                    'general' => 'General Inquiry',
                    'support' => 'Technical Support',
                    'sales' => 'Sales Question',
                    'billing' => 'Billing Issue',
                    'other' => 'Other',
                ],
            ])

            // Message field
            ->addField('message', 'textarea', 'Message', [
                'placeholder' => 'Please describe your inquiry...',
                'required' => true,
                'rows' => 6,
            ])

            // Privacy checkbox
            ->addField('privacy', 'checkbox', 'I agree to the privacy policy', [
                'required' => true,
            ])

            // Buttons
            ->addButton('submit', 'Send Message', 'submit')
            ->addButton('reset', 'Clear Form', 'reset')

            // Validation rules
            ->validationRules([
                'name' => 'required|min:2|max:100',
                'email' => 'required|email|max:255',
                'phone' => 'nullable|regex:/^[0-9+\-\s()]+$/',
                'subject' => 'required|in:general,support,sales,billing,other',
                'message' => 'required|min:10|max:1000',
                'privacy' => 'required|accepted',
            ]);
    });

// Render the layout
return $layout->render();
