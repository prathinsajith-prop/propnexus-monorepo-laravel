<?php

namespace App\Actions\User;

use Litepie\Actions\ActionResult;
use Litepie\Actions\BaseAction;

/**
 * GetUserAction
 *
 * Handles retrieving a single user by ID or user_id
 */
class GetUserAction extends BaseAction
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

        if (! file_exists($jsonPath)) {
            return ActionResult::failure('Users data not found');
        }

        $allUsers = json_decode(file_get_contents($jsonPath), true);

        if (! is_array($allUsers)) {
            return ActionResult::failure('Invalid data format');
        }

        $identifier = $this->data['identifier'];

        // Find the user by ID or user_id
        $user = collect($allUsers)->first(function ($user) use ($identifier) {
            return $user['id'] == $identifier || $user['user_id'] == $identifier;
        });

        if (! $user) {
            return ActionResult::failure('User not found');
        }

        return ActionResult::success($user);
    }

    public function getName(): string
    {
        return 'get-user';
    }
}
