<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Layouts\UserLayout;
use App\Actions\User\ListUsersAction;
use App\Actions\User\CreateUserAction;
use App\Actions\User\GetUserAction;
use App\Actions\User\UpdateUserAction;
use App\Actions\User\DeleteUserAction;

/**
 * GeneralController
 * 
 * Main controller using Litepie Actions for business logic
 * All CRUD operations delegated to dedicated Action classes
 * 
 * @package App\Http\Controllers
 */
class GeneralController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('general.index');
    }

    /**
     * Display documentation page
     */
    public function documentation()
    {
        return view('general.documentation');
    }

    /**
     * Get page layout configuration
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function sample()
    {
        $masterData = $this->getMasterData();
        $layout = UserLayout::make($masterData);

        return response()->layout($layout);
    }

    /**
     * Get component section data by type and component name
     * Returns the specific section configuration for modals, drawers, etc.
     * Used by: /layouts/{type}/{component}
     *
     * @param Request $request
     * @param string $type
     * @param string $component
     * @return \Illuminate\Http\JsonResponse
     */
    public function getComponentSection(Request $request, $type = null, $component = null)
    {
        // Support both route parameters and query parameters
        $type = $type ?? $request->input('type');
        $component = $component ?? $request->input('component');

        if (!$type || !$component) {
            return response()->json([
                'error' => 'Missing required parameters: type and component',
            ], 400);
        }

        // Get master data for options
        $masterData = $this->getMasterData();

        // Build the section data based on type and component using UserLayout
        $sectionData = UserLayout::getComponentDefinition($type, $component, $masterData);

        if (!$sectionData) {
            return response()->json([
                'error' => 'Component definition not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $sectionData,
        ]);
    }

    /**
     * List users with filtering, sorting, and pagination
     * Uses ListUsersAction
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function users(Request $request)
    {
        $action = ListUsersAction::make(null, $request->all());
        $result = $action->run();

        if ($result->isSuccess()) {
            return response()->json($result->getData());
        }

        return response()->json([
            'error' => $result->getMessage(),
            'errors' => $result->getErrors(),
        ], $result->getCode() ?: 500);
    }

    /**
     * Create a new user
     * Uses CreateUserAction
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $action = CreateUserAction::make(null, $request->all());
        $result = $action->run();

        if ($result->isSuccess()) {
            return response()->json([
                'success' => true,
                'message' => $result->getMessage(),
                'data' => $result->getData(),
            ], 201);
        }

        return response()->json([
            'success' => false,
            'message' => $result->getMessage(),
            'errors' => $result->getErrors(),
        ], $result->getCode() ?: 422);
    }

    /**
     * Get individual user details
     * Uses GetUserAction
     *
     * @param Request $request
     * @param string $identifier
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUser(Request $request, $identifier)
    {
        $action = GetUserAction::make(null, ['identifier' => $identifier]);
        $result = $action->run();

        if ($result->isSuccess()) {
            return response()->json([
                'data' => $result->getData(),
            ]);
        }

        return response()->json([
            'error' => $result->getMessage(),
        ], $result->getCode() ?: 404);
    }

    /**
     * Get master data for sales orders
     * This endpoint returns all master data (customers, products, sales_reps, etc.)
     * for use in forms and filters
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function masterData()
    {
        $masterData = $this->getMasterData();

        return response()->json([
            'success' => true,
            'data' => $masterData,
        ]);
    }

    /**
     * Get master data for sales orders
     *
     * @return array
     */
    private function getMasterData()
    {
        return [
            'departments' => [
                ['value' => 'Engineering', 'label' => 'Engineering'],
                ['value' => 'Product', 'label' => 'Product'],
                ['value' => 'Design', 'label' => 'Design'],
                ['value' => 'Marketing', 'label' => 'Marketing'],
                ['value' => 'Sales', 'label' => 'Sales'],
                ['value' => 'Human Resources', 'label' => 'Human Resources'],
                ['value' => 'Analytics', 'label' => 'Analytics'],
                ['value' => 'Finance', 'label' => 'Finance'],
                ['value' => 'Operations', 'label' => 'Operations'],
                ['value' => 'Customer Support', 'label' => 'Customer Support'],
            ],
            'designations' => [
                ['value' => 'Senior Developer', 'label' => 'Senior Developer'],
                ['value' => 'Junior Developer', 'label' => 'Junior Developer'],
                ['value' => 'Product Manager', 'label' => 'Product Manager'],
                ['value' => 'UI/UX Designer', 'label' => 'UI/UX Designer'],
                ['value' => 'Marketing Specialist', 'label' => 'Marketing Specialist'],
                ['value' => 'DevOps Engineer', 'label' => 'DevOps Engineer'],
                ['value' => 'HR Manager', 'label' => 'HR Manager'],
                ['value' => 'Data Analyst', 'label' => 'Data Analyst'],
                ['value' => 'QA Engineer', 'label' => 'QA Engineer'],
                ['value' => 'Sales Manager', 'label' => 'Sales Manager'],
                ['value' => 'Team Lead', 'label' => 'Team Lead'],
                ['value' => 'Intern', 'label' => 'Intern'],
            ],
            'employee_types' => [
                ['value' => 'Full-time', 'label' => 'Full-time'],
                ['value' => 'Part-time', 'label' => 'Part-time'],
                ['value' => 'Contract', 'label' => 'Contract'],
                ['value' => 'Intern', 'label' => 'Intern'],
            ],
            'statuses' => [
                ['value' => 'Active', 'label' => 'Active', 'color' => 'success'],
                ['value' => 'Inactive', 'label' => 'Inactive', 'color' => 'warning'],
                ['value' => 'Suspended', 'label' => 'Suspended', 'color' => 'error'],
            ],
            'genders' => [
                ['value' => 'Male', 'label' => 'Male'],
                ['value' => 'Female', 'label' => 'Female'],
                ['value' => 'Other', 'label' => 'Other'],
            ],
            'blood_groups' => [
                ['value' => 'A+', 'label' => 'A+'],
                ['value' => 'A-', 'label' => 'A-'],
                ['value' => 'B+', 'label' => 'B+'],
                ['value' => 'B-', 'label' => 'B-'],
                ['value' => 'AB+', 'label' => 'AB+'],
                ['value' => 'AB-', 'label' => 'AB-'],
                ['value' => 'O+', 'label' => 'O+'],
                ['value' => 'O-', 'label' => 'O-'],
            ],
            'countries' => [
                ['value' => 'United States', 'label' => 'United States'],
                ['value' => 'Canada', 'label' => 'Canada'],
                ['value' => 'United Kingdom', 'label' => 'United Kingdom'],
                ['value' => 'India', 'label' => 'India'],
                ['value' => 'Australia', 'label' => 'Australia'],
            ],
            'customers' => [
                ['value' => 'CUST-001', 'label' => 'Acme Corp'],
                ['value' => 'CUST-002', 'label' => 'Global Industries'],
                ['value' => 'CUST-003', 'label' => 'Tech Solutions'],
            ],
            'sales_reps' => [
                ['value' => 'REP-001', 'label' => 'John Doe'],
                ['value' => 'REP-002', 'label' => 'Jane Smith'],
            ],
            'products' => [
                ['value' => 'PROD-001', 'label' => 'Premium Plan'],
                ['value' => 'PROD-002', 'label' => 'Standard Plan'],
                ['value' => 'PROD-003', 'label' => 'Basic Plan'],
            ],
            'payment_methods' => [
                ['value' => 'Credit Card', 'label' => 'Credit Card'],
                ['value' => 'Bank Transfer', 'label' => 'Bank Transfer'],
                ['value' => 'PayPal', 'label' => 'PayPal'],
            ],
            'skills' => [
                ['value' => 'php', 'label' => 'PHP'],
                ['value' => 'laravel', 'label' => 'Laravel'],
                ['value' => 'javascript', 'label' => 'JavaScript'],
                ['value' => 'vue', 'label' => 'Vue.js'],
                ['value' => 'react', 'label' => 'React'],
                ['value' => 'python', 'label' => 'Python'],
                ['value' => 'sql', 'label' => 'SQL'],
                ['value' => 'docker', 'label' => 'Docker'],
                ['value' => 'aws', 'label' => 'AWS'],
            ],
            'roles' => [
                ['value' => 'admin', 'label' => 'Administrator'],
                ['value' => 'user', 'label' => 'Standard User'],
                ['value' => 'manager', 'label' => 'Manager'],
                ['value' => 'editor', 'label' => 'Editor'],
            ],
        ];
    }

    /**
     * Update an existing user
     * Uses UpdateUserAction
     *
     * @param Request $request
     * @param string $identifier
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $identifier)
    {
        $data = array_merge($request->all(), ['identifier' => $identifier]);
        $action = UpdateUserAction::make(null, $data);
        $result = $action->run();

        if ($result->isSuccess()) {
            return response()->json([
                'success' => true,
                'message' => $result->getMessage(),
                'data' => $result->getData(),
            ]);
        }

        return response()->json([
            'success' => false,
            'error' => $result->getMessage(),
            'errors' => $result->getErrors(),
        ], $result->getCode() ?: 404);
    }

    /**
     * Delete a user
     * Uses DeleteUserAction
     *
     * @param Request $request
     * @param string $identifier
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $identifier)
    {
        $action = DeleteUserAction::make(null, ['identifier' => $identifier]);
        $result = $action->run();

        if ($result->isSuccess()) {
            return response()->json([
                'success' => true,
                'message' => $result->getMessage(),
                'data' => $result->getData(),
            ]);
        }

        return response()->json([
            'success' => false,
            'error' => $result->getMessage(),
        ], $result->getCode() ?: 404);
    }
}
