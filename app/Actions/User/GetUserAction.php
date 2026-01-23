<?php

namespace App\Actions\User;

use Litepie\Actions\BaseAction;
use Litepie\Actions\ActionResult;

/**
 * GetUserAction
 * 
 * Handles retrieving a single user by ID or user_id
 * 
 * @package App\Actions\User
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

        if (!file_exists($jsonPath)) {
            return ActionResult::failure('Users data not found', [], 404);
        }

        $allData = json_decode(file_get_contents($jsonPath), true);

        if (!is_array($allData)) {
            return ActionResult::failure('Invalid data format', [], 500);
        }

        $identifier = $this->data['identifier'];

        // Find the user by ID or user_id
        $user = collect($allData)->first(function ($item) use ($identifier) {
            return $item['id'] == $identifier || $item['user_id'] == $identifier;
        });

        if (!$user) {
            return ActionResult::failure('User not found', [], 404);
        }

        return ActionResult::success($user);
    }

    public function getName(): string
    {
        return 'get-user';
    }
}
