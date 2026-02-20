<?php

namespace App\Forms\User;

use Litepie\Layout\LayoutBuilder;

/**
 * UserForm
 * 
 * Comprehensive user management form with diverse field types
 * 
 * @package App\Forms\User
 */
class UserForm
{
    /**
     * Get a comprehensive user form structure with diverse field types
     */
    public static function make($formId, $method, $action, $masterData, $dataUrl = null)
    {
        $formLayout = LayoutBuilder::create($formId . '-layout', 'form');

        $formLayout->section('content', function ($section) use ($formId, $method, $action, $masterData, $dataUrl) {
            $form = $section->form($formId)
                ->action($action)
                ->method($method)
                ->columns(2)
                ->gap('lg');

            if ($dataUrl) {
                $form->dataUrl($dataUrl)->dataKey('data');
            }

            // --- Personal Information Section ---
            $personalGroup = $form->group('personal-info')
                ->title(__('layout.personal_information'))
                ->icon('user')
                ->variant('bordered')
                ->columns(2);

            $personalGroup->text('name')->label(__('layout.full_name'))->placeholder('John Doe')->required(true)->width(6);
            $personalGroup->email('email')->label(__('layout.email_address'))->placeholder('john@example.com')->required(true)->width(6);

            // Radio buttons for Gender
            $personalGroup->radio('gender')
                ->label(__('layout.gender'))
                ->options($masterData['genders'])
                ->inline()   // Makes it horizontal
                ->width(6);

            $personalGroup->date('date_of_birth')
                ->label(__('layout.date_of_birth'))
                ->width(6);

            $personalGroup->text('phone')->label(__('layout.phone_number'))->placeholder('+1-202-555-0123')->width(6);
            $personalGroup->select('blood_group')->label(__('layout.blood_group'))->options($masterData['blood_groups'])->width(6);

            // --- Employment Section ---
            $employmentGroup = $form->group('employment-info')
                ->title(__('layout.employment_info'))
                ->icon('briefcase')
                ->variant('bordered')
                ->columns(2);

            $employmentGroup->select('department')->label(__('layout.department'))->options($masterData['departments'])->width(6);
            $employmentGroup->select('designation')->label(__('layout.designation'))->options($masterData['designations'])->width(6);

            // Using a Switch for Status if it's binary, or stay with Select for multiple
            $employmentGroup->select('status')->label(__('layout.account_status'))->options($masterData['statuses'])->width(6);
            $employmentGroup->date('joining_date')->label(__('layout.joining_date'))->required(true)->width(6);

            // --- Media & Files ---
            $mediaGroup = $form->group('media-info')
                ->title(__('layout.documents_media'))
                ->icon('image')
                ->variant('bordered')
                ->columns(1);

            $mediaGroup->file('profile_image')
                ->label(__('layout.profile_image'))
                ->accept('image/*')
                ->maxSize(2048)
                ->uploadUrl('/api/user-upload/image');

            $mediaGroup->file('resume')
                ->label(__('layout.expertise_resume'))
                ->accept('.pdf')
                ->maxSize(5120)
                ->uploadUrl('/api/user-upload/document');

            // --- Account & Security ---
            $securityGroup = $form->group('security-info')
                ->title(__('layout.account_security'))
                ->icon('lock')
                ->variant('bordered')
                ->columns(2);

            $securityGroup->password('password')->label(__('layout.initial_password'))->placeholder('••••••••')->width(6);
            $securityGroup->select('roles')
                ->label(__('layout.assigned_roles'))
                ->options($masterData['roles'])
                ->multiple(true)
                ->width(6);

            // --- Technical Skills ---
            $skillsGroup = $form->group('skills-info')
                ->title(__('layout.technical_skills'))
                ->icon('star')
                ->variant('bordered')
                ->columns(3);

            foreach ($masterData['skills'] as $skill) {
                $skillsGroup->rating('skills[' . $skill['value'] . ']')
                    ->label($skill['label'])
                    ->max(5)
                    ->width(4);
            }

            // --- Address & Additional Info ---
            $addressGroup = $form->group('address-info')
                ->title(__('layout.address_preferences'))
                ->icon('mappin')
                ->variant('bordered')
                ->columns(2);

            $addressGroup->textarea('address')
                ->label(__('layout.home_address'))
                ->placeholder(__('layout.home_address_placeholder'))
                ->rows(3)
                ->width(12);

            $addressGroup->number('expected_salary')
                ->label(__('layout.expected_salary'))
                ->prefix('$')
                ->width(6);

            $addressGroup->range('experience')
                ->label(__('layout.years_of_experience'))
                ->min(0)
                ->max(40)
                ->step(1)
                ->width(6);

            $addressGroup->color('theme_preference')
                ->label(__('layout.ui_theme_color'))
                ->width(6);

            $addressGroup->url('linkedin_profile')
                ->label(__('layout.linkedin_profile'))
                ->placeholder('https://linkedin.com/in/...')
                ->width(6);

            $addressGroup->checkbox('notifications')
                ->label(__('layout.enable_email_notifications'))
                ->width(6);

            $addressGroup->textarea('notes')
                ->label(__('layout.administrative_notes'))
                ->placeholder(__('layout.administrative_notes_placeholder'))
                ->rows(2)
                ->width(12);
        });

        $builtForm = $formLayout->build();
        $formArray = $builtForm->toArray();

        return $formArray['sections']['content']['components'][0] ?? null;
    }
}
