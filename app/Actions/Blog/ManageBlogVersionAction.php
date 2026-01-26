<?php

namespace App\Actions\Blog;

use App\Models\Blog;
use Litepie\Actions\BaseAction;
use Litepie\Actions\ActionResult;

/**
 * ManageBlogVersionAction
 * 
 * Handle blog versioning operations using Versionable trait
 * 
 * @package App\Actions\Blog
 */
class ManageBlogVersionAction extends BaseAction
{
    protected function rules(): array
    {
        return [
            'blog_id' => 'required|integer|exists:blogs,id',
            'action' => 'required|in:list,create,rollback,compare,restore',
            'version_number' => 'required_if:action,rollback,restore|integer',
            'compare_from' => 'required_if:action,compare|integer',
            'compare_to' => 'required_if:action,compare|integer',
            'reason' => 'sometimes|string',
        ];
    }

    public function handle(): ActionResult
    {
        $blog = Blog::findOrFail($this->data['blog_id']);
        $action = $this->data['action'];

        switch ($action) {
            case 'list':
                return $this->listVersions($blog);

            case 'create':
                return $this->createVersion($blog);

            case 'rollback':
                return $this->rollbackVersion($blog);

            case 'compare':
                return $this->compareVersions($blog);

            case 'restore':
                return $this->restoreVersion($blog);

            default:
                return ActionResult::error('Invalid action', 400);
        }
    }

    protected function listVersions(Blog $blog): ActionResult
    {
        $history = $blog->getVersionHistory();
        $currentVersion = $blog->revision_number;

        return ActionResult::success([
            'current_version' => $currentVersion,
            'version_count' => $blog->version_count,
            'versions' => $history,
        ]);
    }

    protected function createVersion(Blog $blog): ActionResult
    {
        $reason = $this->data['reason'] ?? 'Manual version creation';
        $user = auth()->user();

        $version = $blog->createVersion($reason, $user);

        return ActionResult::success([
            'message' => 'Version created successfully',
            'version' => $version,
            'version_number' => $blog->version_count,
        ]);
    }

    protected function rollbackVersion(Blog $blog): ActionResult
    {
        $versionNumber = $this->data['version_number'];

        $success = $blog->rollbackToVersion($versionNumber);

        if ($success) {
            return ActionResult::success([
                'message' => "Successfully rolled back to version {$versionNumber}",
                'current_version' => $blog->fresh()->revision_number,
            ]);
        }

        return ActionResult::error("Failed to rollback to version {$versionNumber}", 400);
    }

    protected function compareVersions(Blog $blog): ActionResult
    {
        $from = $this->data['compare_from'];
        $to = $this->data['compare_to'];

        $comparison = $blog->compareVersions($from, $to);

        return ActionResult::success([
            'comparison' => $comparison,
            'from_version' => $from,
            'to_version' => $to,
        ]);
    }

    protected function restoreVersion(Blog $blog): ActionResult
    {
        $versionNumber = $this->data['version_number'];

        // Similar to rollback but creates a new version
        $versionData = $blog->getVersionData($versionNumber);

        if ($versionData) {
            $blog->fill($versionData);
            $blog->save();
            $blog->createVersion("Restored from version {$versionNumber}", auth()->user());

            return ActionResult::success([
                'message' => "Successfully restored version {$versionNumber}",
                'new_version' => $blog->version_count,
            ]);
        }

        return ActionResult::error("Version {$versionNumber} not found", 404);
    }
}
