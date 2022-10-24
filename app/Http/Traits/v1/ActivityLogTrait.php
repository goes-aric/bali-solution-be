<?php
namespace App\Http\Traits\v1;

use Request;
use App\Models\ActivityLog;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;

trait ActivityLogTrait
{
    public function writeActivityLog($props)
    {
        $activityLog  = new ActivityLog;
        $activityLog->user_id       = $props['user_id'];
        $activityLog->module        = Str::upper($props['module']);
        $activityLog->event         = $props['event'];
        $activityLog->description   = Str::upper($props['description']);
        $activityLog->status        = $props['status'];
        $activityLog->old_values    = $props['old_values'];
        $activityLog->new_values    = $props['new_values'];
        $activityLog->url           = URL::current();
        $activityLog->ip_address    = Request::getClientIp();
        $activityLog->user_agent    = Request::userAgent();
        $activityLog->save();
    }
}
