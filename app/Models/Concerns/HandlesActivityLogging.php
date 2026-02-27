<?php

declare(strict_types=1);

namespace App\Models\Concerns;

/**
 * Overrides the LogsActivity trait's logActivity() to safely handle
 * unauthenticated contexts where getActivityCauser() returns null.
 *
 * The base ActivityLogService::causedBy() does not accept null, so we
 * conditionally skip the causedBy() call when no causer is available.
 */
trait HandlesActivityLogging
{
    public function logActivity(string $event): void
    {
        $causer = $this->getActivityCauser();
        $service = app('activity-log')
            ->useLog($this->getLogNameToUse($event))
            ->performedOn($this)
            ->event($event)
            ->withProperties($this->getActivityProperties($event));

        if ($causer !== null) {
            $service->causedBy($causer);
        }

        $service->log($this->getActivityDescription($event));
    }
}
