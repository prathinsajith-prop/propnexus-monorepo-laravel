<?php

namespace App\Forms\User;

use Litepie\Layout\LayoutBuilder;

/**
 * UserViewForm
 *
 * Read-only user view form for displaying user details in drawer/fullscreen layout
 * All fields are readonly for viewing purposes only
 */
class UserViewForm
{
    /**
     * Create a read-only user view form structure
     *
     * @param  string  $formId  Form identifier
     * @param  array  $masterData  Master data for display options
     * @param  string|null  $dataUrl  URL to fetch user data
     * @return array|null Form component array
     */
    public static function make($formId, $masterData, $dataUrl = null)
    {
        $formLayout = LayoutBuilder::create($formId.'-layout', 'form');

        $formLayout->section('content', function ($section) use ($formId, $dataUrl) {
            $form = $section->form($formId)
                ->columns(2)
                ->gap('lg');

            if ($dataUrl) {
                $form->dataUrl($dataUrl)->dataKey('data');
            }

            $form
                // ->readonly(true)
                ->layoutConfig([
                    [
                        'columnsGrid' => 8,
                        'gap' => 'md',
                        'gridTemplateComponents' => [
                            ['key' => 'personal-info', 'columnGrid' => 12],
                            ['key' => 'employment-info', 'columnGrid' => 12],
                            ['key' => 'skills-info', 'columnGrid' => 12],
                            ['key' => 'address-info', 'columnGrid' => 12],
                        ],
                    ],
                    [
                        'columnsGrid' => 4,
                        'gap' => 'md',
                        'gridTemplateComponents' => [
                            ['key' => 'media-info', 'columnGrid' => 12],
                            ['key' => 'security-info', 'columnGrid' => 12],
                            ['key' => 'additional-info', 'columnGrid' => 12],
                            ['key' => 'audit-info', 'columnGrid' => 12],
                        ],
                    ],
                ]);

            // === PERSONAL INFORMATION ===
            $personalGroup = $form->group('personal-info')
                ->title('Personal Information')
                ->icon('user')
                ->variant('bordered')
                ->columns(2);

            $personalGroup->text('name')
                ->label('Full Name')
                ->readonly(true)
                ->width(6);

            $personalGroup->text('email')
                ->label('Email Address')
                ->readonly(true)
                ->width(6);

            $personalGroup->text('gender')
                ->label('Gender')
                ->readonly(true)
                ->width(6);

            $personalGroup->text('date_of_birth')
                ->label('Date of Birth')
                ->readonly(true)
                ->width(6);

            $personalGroup->text('phone')
                ->label('Phone Number')
                ->readonly(true)
                ->width(6);

            $personalGroup->text('blood_group')
                ->label('Blood Group')
                ->readonly(true)
                ->width(6);

            // === EMPLOYMENT INFORMATION ===
            $employmentGroup = $form->group('employment-info')
                ->title('Employment Information')
                ->icon('briefcase')
                ->variant('bordered')
                ->columns(2);

            $employmentGroup->text('department')
                ->label('Department')
                ->readonly(true)
                ->width(6);

            $employmentGroup->text('designation')
                ->label('Designation')
                ->readonly(true)
                ->width(6);

            $employmentGroup->text('status')
                ->label('Account Status')
                ->readonly(true)
                ->width(6);

            $employmentGroup->text('joining_date')
                ->label('Joining Date')
                ->readonly(true)
                ->width(6);

            // === DOCUMENTS & MEDIA ===
            $mediaGroup = $form->group('media-info')
                ->title('Documents & Media')
                ->icon('image')
                ->variant('bordered')
                ->columns(1);

            $mediaGroup->text('profile_image')
                ->label('Profile Image')
                ->readonly(true)
                ->width(12);

            $mediaGroup->text('resume')
                ->label('Resume')
                ->readonly(true)
                ->width(12);

            // === ACCOUNT & SECURITY ===
            $securityGroup = $form->group('security-info')
                ->title('Account & Security')
                ->icon('lock')
                ->variant('bordered')
                ->columns(2);

            $securityGroup->text('roles')
                ->label('Roles')
                ->readonly(true)
                ->width(6);

            $securityGroup->text('permissions')
                ->label('Permissions')
                ->readonly(true)
                ->width(6);

            // === SKILLS & EXPERTISE ===
            $skillsGroup = $form->group('skills-info')
                ->title('Skills & Expertise')
                ->icon('award')
                ->variant('bordered')
                ->columns(1);

            $skillsGroup->text('skills')
                ->label('Skills')
                ->readonly(true)
                ->width(12);

            $skillsGroup->textarea('bio')
                ->label('Biography')
                ->readonly(true)
                ->rows(4)
                ->width(12);

            // === ADDRESS INFORMATION ===
            $addressGroup = $form->group('address-info')
                ->title('Address')
                ->icon('mappin')
                ->variant('bordered')
                ->columns(2);

            $addressGroup->text('address_line_1')
                ->label('Address Line 1')
                ->readonly(true)
                ->width(12);

            $addressGroup->text('address_line_2')
                ->label('Address Line 2')
                ->readonly(true)
                ->width(12);

            $addressGroup->text('city')
                ->label('City')
                ->readonly(true)
                ->width(4);

            $addressGroup->text('state')
                ->label('State/Province')
                ->readonly(true)
                ->width(4);

            $addressGroup->text('postal_code')
                ->label('Postal Code')
                ->readonly(true)
                ->width(4);

            $addressGroup->text('country')
                ->label('Country')
                ->readonly(true)
                ->width(6);

            // === ADDITIONAL INFORMATION ===
            $additionalGroup = $form->group('additional-info')
                ->title('Additional Information')
                ->icon('info')
                ->variant('bordered')
                ->columns(2);

            $additionalGroup->text('emergency_contact_name')
                ->label('Emergency Contact Name')
                ->readonly(true)
                ->width(6);

            $additionalGroup->text('emergency_contact_phone')
                ->label('Emergency Contact Phone')
                ->readonly(true)
                ->width(6);

            $additionalGroup->text('employee_id')
                ->label('Employee ID')
                ->readonly(true)
                ->width(6);

            $additionalGroup->text('social_security_number')
                ->label('SSN/National ID')
                ->readonly(true)
                ->width(6);

            // === SYSTEM & AUDIT ===
            $auditGroup = $form->group('audit-info')
                ->title('System & Audit Trail')
                ->icon('clock')
                ->variant('bordered')
                ->columns(2);

            $auditGroup->text('created_at')
                ->label('Created At')
                ->readonly(true)
                ->width(6);

            $auditGroup->text('updated_at')
                ->label('Last Updated')
                ->readonly(true)
                ->width(6);

            $auditGroup->text('last_login_at')
                ->label('Last Login')
                ->readonly(true)
                ->width(6);

            $auditGroup->text('last_login_ip')
                ->label('Last Login IP')
                ->readonly(true)
                ->width(6);
        });

        // Build and return the layout
        $builtContent = $formLayout->build();
        $contentArray = $builtContent->toArray();

        return $contentArray['sections']['content']['components'][0] ?? null;
    }
}
