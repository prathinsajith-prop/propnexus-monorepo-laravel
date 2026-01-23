<?php

namespace App\Actions\User;

use Litepie\Actions\BaseAction;
use Litepie\Actions\ActionResult;

/**
 * DeleteUserAction
 * 
 * Handles deleting a user
 * 
 * @package App\Actions\User
 */
class DeleteUserAction extends BaseAction
{
    protected function rules(): array
    {
        return [
            'identifier' => 'required|string',
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

        $deletedUser = $allData[$userIndex];
        array_splice($allData, $userIndex, 1);

        file_put_contents($jsonPath, json_encode($allData, JSON_PRETTY_PRINT));

        return ActionResult::success($deletedUser, 'User deleted successfully');
    }

    public function getName(): string
    {
        return 'delete-user';
    }
}
