<?php

namespace App\Actions\User;

use Litepie\Actions\BaseAction;
use Litepie\Actions\ActionResult;

/**
 * CreateUserAction
 * 
 * Handles creating a new user
 * 
 * @package App\Actions\User
 */
class CreateUserAction extends BaseAction
{
    protected function rules(): array
    {
        return [
            'user_id' => 'nullable|string|max:50',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'designation' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'joining_date' => 'nullable|date',
            'employee_type' => 'nullable|string',
            'status' => 'nullable|string',
        ];
    }

    public function handle(): ActionResult
    {
        $jsonPath = storage_path('app/users.json');

        // Load existing data
        $allData = file_exists($jsonPath) ? json_decode(file_get_contents($jsonPath), true) : [];
        if (!is_array($allData)) {
            $allData = [];
        }

        // Generate new ID
        $maxId = collect($allData)->max('id') ?? 100;
        $newId = $maxId + 1;

        // Prepare user data
        $userData = $this->data;
        $userData['id'] = $newId;
        $userData['user_id'] = $userData['user_id'] ?? 'USR-' . str_pad($newId, 3, '0', STR_PAD_LEFT);
        $userData['created_at'] = now()->format('Y-m-d');
        $userData['updated_at'] = now()->format('Y-m-d');

        // Add to array
        $allData[] = $userData;

        // Save to file
        file_put_contents($jsonPath, json_encode($allData, JSON_PRETTY_PRINT));

        return ActionResult::success($userData, 'User created successfully');
    }

    public function getName(): string
    {
        return 'create-user';
    }
}
