<?php
namespace App\Http\Traits\v1;

use Auth;
use App\Models\UserRoles;
use App\Models\Permissions;

trait CheckPermission
{
    public function checkPermissions($groupKey, $key)
    {
        $userId = Auth::user()->id;
        $isAdmin = Auth::user()->is_admin;
        $roles = UserRoles::select('role_id')->where('user_id', '=', $userId);
        $permission = Permissions::whereIn('role_id', $roles)->where('group_key', '=', $groupKey)->where('key', '=', $key)->first();

        if ($permission || $isAdmin) {
            return true;
        }
        return false;
    }
}
