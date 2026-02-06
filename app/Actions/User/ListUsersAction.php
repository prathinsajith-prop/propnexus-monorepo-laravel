<?php

namespace App\Actions\User;

use Illuminate\Http\Request;
use Litepie\Actions\BaseAction;
use Litepie\Actions\ActionResult;

/**
 * ListUsersAction
 * 
 * Handles listing users with filtering, sorting, and pagination
 * 
 * @package App\Actions\User
 */
class ListUsersAction extends BaseAction
{
    protected function rules(): array
    {
        return [
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'sort_by' => 'sometimes|string',
            'sort_direction' => 'sometimes|in:asc,desc',
            'search' => 'sometimes|string',
            'filter_user_id' => 'sometimes|string',
            'filter_name' => 'sometimes|string',
            'filter_email' => 'sometimes|string',
            'filter_phone' => 'sometimes|string',
            'filter_department' => 'sometimes|string',
            'filter_designation' => 'sometimes|string',
            'filter_status' => 'sometimes|string',
            'filter_employee_type' => 'sometimes|string',
            'filter_joining_date' => 'sometimes|string',
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

        // Extract parameters with defaults
        $page = (int) ($this->data['page'] ?? 1);
        $perPage = max(1, min((int) ($this->data['per_page'] ?? 10), 100));
        $sortBy = $this->data['sort_by'] ?? 'user_id';
        $sortDirection = strtolower($this->data['sort_direction'] ?? 'asc');
        $search = $this->data['search'] ?? '';

        // Validate sort direction
        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'asc';
        }

        // Get filter parameters
        $filters = [
            'user_id' => $this->data['filter_user_id'] ?? '',
            'name' => $this->data['filter_name'] ?? '',
            'email' => $this->data['filter_email'] ?? '',
            'phone' => $this->data['filter_phone'] ?? '',
            'department' => $this->data['filter_department'] ?? '',
            'designation' => $this->data['filter_designation'] ?? '',
            'status' => $this->data['filter_status'] ?? '',
            'employee_type' => $this->data['filter_employee_type'] ?? '',
            'joining_date' => $this->data['filter_joining_date'] ?? '',
        ];

        // Apply search filter
        if (!empty($search)) {
            $allUsers = $this->applySearch($allUsers, $search);
        }

        // Apply column-specific filters
        $allUsers = $this->applyFilters($allUsers, $filters);

        // Apply sorting
        $allUsers = $this->applySorting($allUsers, $sortBy, $sortDirection);

        // Apply pagination
        $paginatedResult = $this->applyPagination($allUsers, $page, $perPage);

        return ActionResult::success([
            'data' => $paginatedResult['data'],
            'meta' => $paginatedResult['meta'],
            'filters' => $filters,
            'sort' => [
                'by' => $sortBy,
                'direction' => $sortDirection,
            ],
        ]);
    }

    protected function applySearch(array $users, string $search): array
    {
        $searchLower = strtolower($search);

        return array_filter($users, function ($user) use ($searchLower) {
            foreach ($user as $value) {
                if (stripos(strtolower((string) $value), $searchLower) !== false) {
                    return true;
                }
            }
            return false;
        });
    }

    protected function applyFilters(array $users, array $filters): array
    {
        foreach ($filters as $column => $filterValue) {
            if (!empty($filterValue)) {
                $users = array_filter($users, function ($user) use ($column, $filterValue) {
                    if (!isset($user[$column])) {
                        return false;
                    }
                    $userValue = strtolower((string) $user[$column]);
                    $filterLower = strtolower((string) $filterValue);
                    return stripos($userValue, $filterLower) !== false;
                });
            }
        }

        return $users;
    }

    protected function applySorting(array $users, string $sortBy, string $sortDirection): array
    {
        if (!empty($sortBy) && isset($users[0][$sortBy])) {
            usort($users, function ($a, $b) use ($sortBy, $sortDirection) {
                $aVal = $a[$sortBy] ?? '';
                $bVal = $b[$sortBy] ?? '';

                if ($sortDirection === 'asc') {
                    return $aVal <=> $bVal;
                } else {
                    return $bVal <=> $aVal;
                }
            });
        }

        return $users;
    }

    protected function applyPagination(array $users, int $page, int $perPage): array
    {
        $total = count($users);
        $offset = ($page - 1) * $perPage;
        $paginatedUsers = array_slice($users, $offset, $perPage);

        return [
            'data' => array_values($paginatedUsers),
            'meta' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'last_page' => ceil($total / $perPage),
                'from' => $offset + 1,
                'to' => min($offset + $perPage, $total),
            ],
        ];
    }

    public function getName(): string
    {
        return 'list-users';
    }
}
