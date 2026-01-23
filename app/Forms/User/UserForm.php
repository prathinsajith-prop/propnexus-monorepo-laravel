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
            if ($dataUrl) {
                $section->meta([
                    'dataUrl' => $dataUrl,
                    'dataKey' => 'data',
                ]);
            }

            $form = $section->form($formId)
                ->action($action)
                ->method($method)
                ->columns(2)
                ->gap('lg');

            // --- Personal Information Section ---
            $personalGroup = $form->group('personal-info')
                ->title('Personal Information')
                ->icon('LiUser')
                ->variant('bordered')
                ->columns(2);

            $personalGroup->text('name')->label('Full Name')->placeholder('John Doe')->required(true)->width(6);
            $personalGroup->email('email')->label('Email Address')->placeholder('john@example.com')->required(true)->width(6);

            // Radio buttons for Gender
            $personalGroup->radio('gender')
                ->label('Gender')
                ->options($masterData['genders'])
                ->inline()   // Makes it horizontal
                ->width(6);

            $personalGroup->date('date_of_birth')
                ->label('Date of Birth')
                ->width(6);

            $personalGroup->text('phone')->label('Phone Number')->placeholder('+1-202-555-0123')->width(6);
            $personalGroup->select('blood_group')->label('Blood Group')->options($masterData['blood_groups'])->width(6);

            // --- Employment Section ---
            $employmentGroup = $form->group('employment-info')
                ->title('Employment Info')
                ->icon('LiBriefcase')
                ->variant('bordered')
                ->columns(2);

            $employmentGroup->select('department')->label('Department')->options($masterData['departments'])->width(6);
            $employmentGroup->select('designation')->label('Designation')->options($masterData['designations'])->width(6);

            // Using a Switch for Status if it's binary, or stay with Select for multiple
            $employmentGroup->select('status')->label('Account Status')->options($masterData['statuses'])->width(6);
            $employmentGroup->date('joining_date')->label('Joining Date')->required(true)->width(6);

            // --- Media & Files ---
            $mediaGroup = $form->group('media-info')
                ->title('Documents & Media')
                ->icon('LiImage')
                ->variant('bordered')
                ->columns(1);

            $mediaGroup->file('profile_image')
                ->label('Profile Image')
                ->accept('image/*')
                ->maxSize(2048);

            $mediaGroup->file('resume')
                ->label('Expertise Resume (PDF)')
                ->accept('.pdf')
                ->maxSize(5120);

            // --- Account & Security ---
            $securityGroup = $form->group('security-info')
                ->title('Account & Security')
                ->icon('LiLock')
                ->variant('bordered')
                ->columns(2);

            $securityGroup->password('password')->label('Initial Password')->placeholder('••••••••')->width(6);
            $securityGroup->select('roles')
                ->label('Assigned Roles')
                ->options($masterData['roles'])
                ->multiple(true)
                ->width(6);

            // --- Technical Skills ---
            $skillsGroup = $form->group('skills-info')
                ->title('Technical Skills')
                ->icon('LiStar')
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
                ->title('Address & Preferences')
                ->icon('LiMapPin')
                ->variant('bordered')
                ->columns(2);

            $addressGroup->textarea('address')
                ->label('Home Address')
                ->placeholder('Enter full residential address...')
                ->rows(3)
                ->width(12);

            $addressGroup->number('expected_salary')
                ->label('Expected Salary')
                ->prefix('$')
                ->width(6);

            $addressGroup->range('experience')
                ->label('Years of Experience')
                ->min(0)
                ->max(40)
                ->step(1)
                ->width(6);

            $addressGroup->color('theme_preference')
                ->label('UI Theme Color')
                ->width(6);

            $addressGroup->url('linkedin_profile')
                ->label('LinkedIn Profile')
                ->placeholder('https://linkedin.com/in/...')
                ->width(6);

            $addressGroup->checkbox('notifications')
                ->label('Enable Email Notifications')
                ->width(6);

            $addressGroup->textarea('notes')
                ->label('Administrative Notes')
                ->placeholder('Add any relevant background information...')
                ->rows(2)
                ->width(12);
        });

        $builtForm = $formLayout->build();
        $formArray = $builtForm->toArray();

        return $formArray['sections']['content']['components'][0] ?? null;
    }
}
