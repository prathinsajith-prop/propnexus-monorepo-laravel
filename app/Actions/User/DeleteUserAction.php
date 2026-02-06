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

        $allUsers = json_decode(file_get_contents($jsonPath), true);

        if (!is_array($allUsers)) {
            return ActionResult::failure('Invalid data format', [], 500);
        }

        $identifier = $this->data['identifier'];
        $userIndex = null;

        foreach ($allUsers as $index => $user) {
            if ($user['id'] == $identifier || $user['user_id'] == $identifier) {
                $userIndex = $index;
                break;
            }
        }

        if ($userIndex === null) {
            return ActionResult::failure('User not found', [], 404);
        }

        $deletedUser = $allUsers[$userIndex];
        array_splice($allUsers, $userIndex, 1);

        file_put_contents($jsonPath, json_encode($allUsers, JSON_PRETTY_PRINT));

        return ActionResult::success($deletedUser, 'User deleted successfully');
    }

    public function getName(): string
    {
        return 'delete-user';
    }
}
