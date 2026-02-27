<?php

/**
 * This file fixes two bugs in the litepie/logs package:
 *
 * 1. PSR-4 autoloading: ActivityLogCreating, ActivityLogCreated, and
 *    ActivityLogged are all defined inside a single file (ActivityLogEvents.php)
 *    but the PSR-4 map expects each class in its own file.
 *
 * 2. Wrong constructor signature: ActivityLogCreating::__construct() was
 *    typed as `array $attributes` but Laravel's $dispatchesEvents mechanism
 *    passes the model instance, not an attributes array.
 *
 * A custom autoloader registered in AppServiceProvider intercepts requests
 * for any of these three class names and loads this file instead, so no
 * vendor files are modified and the fix survives `composer install`.
 */

namespace Litepie\Logs\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Litepie\Logs\Models\ActivityLog;

if (! class_exists(ActivityLogCreating::class)) {
    class ActivityLogCreating
    {
        use Dispatchable, InteractsWithSockets, SerializesModels;

        public function __construct(
            public ActivityLog $activityLog
        ) {}
    }
}

if (! class_exists(ActivityLogCreated::class)) {
    class ActivityLogCreated
    {
        use Dispatchable, InteractsWithSockets, SerializesModels;

        public function __construct(
            public ActivityLog $activityLog
        ) {}
    }
}

if (! class_exists(ActivityLogged::class)) {
    class ActivityLogged
    {
        use Dispatchable, InteractsWithSockets, SerializesModels;

        public function __construct(
            public ActivityLog $activityLog
        ) {}
    }
}
