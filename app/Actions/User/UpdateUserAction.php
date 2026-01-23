<?php

namespace App\Actions\User;

use Litepie\Actions\BaseAction;
use Litepie\Actions\ActionResult;

/**
 * UpdateUserAction
 * 
 * Handles updating an existing user
 * 
 * @package App\Actions\User
 */
class UpdateUserAction extends BaseAction
{
    protected function rules(): array
    {
        return [
            'identifier' => 'required|string',
            'user_id' => 'sometimes|string|max:50',
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|max:255',
        ];
    }

    public function handle(): ActionResult
    {
        $jsonPath = storage_path('app/users.json');

        if (!file_exists($jsonPath)) {
            return ActionResult::failure('Users data not found', [], 404);
        }

        $allData = json_decode(file_get_contents($jsonPath), true);

        if (!is_array($allData)) {
            return ActionResult::failure('Invalid data format', [], 500);
        }

        $identifier = $this->data['identifier'];
        $userIndex = null;

        foreach ($allData as $index => $item) {
            if ($item['id'] == $identifier || $item['user_id'] == $identifier) {
                $userIndex = $index;
                break;
            }
        }

        if ($userIndex === null) {
            return ActionResult::failure('User not found', [], 404);
        }

        // Remove identifier from update data
        $updateData = $this->data;
        unset($updateData['identifier']);

        // Merge existing user data with update data
        $allData[$userIndex] = array_merge($allData[$userIndex], $updateData);
        $allData[$userIndex]['updated_at'] = now()->format('Y-m-d');

        file_put_contents($jsonPath, json_encode($allData, JSON_PRETTY_PRINT));

        return ActionResult::success($allData[$userIndex], 'User updated successfully');
    }

    public function getName(): string
    {
        return 'update-user';
    }
}
