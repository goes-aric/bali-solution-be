<?php
namespace App\Http\Controllers\v1\Settings\ActivityLog;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\v1\BaseController;
use App\Http\Services\v1\Settings\ActivityLog\ActivityLogServices;

class ActivityLogController extends BaseController
{
    private $activityLogServices;
    private $moduleName;

    public function __construct(ActivityLogServices $activityLogServices)
    {
        $this->activityLogServices = $activityLogServices;
        $this->moduleName = 'Activity Logs';
    }

    public function index(Request $request)
    {
        // if ($this->checkPermissions($this->moduleName, 'view') == true) {
            try {
                $props = $this->getBaseQueryParams($request, []);
                $props += [
                    'module'    => $request['module'] ?? null,
                    'user'      => $request['user'] ?? null,
                    'event'     => $request['event'] ?? null
                ];
                $logs = $this->activityLogServices->fetchLimit($props);

                return $this->returnResponse('success', self::HTTP_OK, 'Daftar riwayat aktivitas', $logs);
            } catch (Exception $ex) {
                return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
            }
        // }

        // return $this->returnNoPermissionResponse();
    }

    public function show($id)
    {
        // if ($this->checkPermissions($this->moduleName, 'view') == true) {
            try {
                $log = $this->activityLogServices->fetchById($id);
                return $this->returnResponse('success', self::HTTP_OK, 'Detail riwayat aktivitas', $log);
            } catch (Exception $ex) {
                return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
            }
        // }

        // return $this->returnNoPermissionResponse();
    }
}
