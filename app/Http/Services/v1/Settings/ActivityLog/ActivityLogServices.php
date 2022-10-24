<?php
namespace App\Http\Services\v1\Settings\ActivityLog;

use Exception;
use App\Http\Resources\v1\Settings\ActivityLog\ActivityLogResource;
use App\Http\Services\v1\BaseServices;
use App\Models\ActivityLog;

class ActivityLogServices extends BaseServices
{
    private $model;

    public function __construct()
    {
        $this->model = new ActivityLog();
    }

    /* FETCH ALL ACTIVITY LOG LIMIT */
    public function fetchLimit($props)
    {
        try {
            /* GET DATA FOR PAGINATION AS A MODEL */
            $getAllData = $this->dataFilterPagination($this->model, [], null);

            /* GET DATA WITH FILTER FOR PAGINATION AS A MODEL */
            $getFilterData = $this->dataFilterPagination($this->model, $props, null);

            /* ADD FILTER BY USER & EVENT IF AVAILABLE */
            if (isset($props['user'])) {
                $getFilterData->where('user_id', '=', $props['user']);
            }

            if (isset($props['event'])) {
                $getFilterData->where('event', '=', $props['event']);
            }
            $totalFiltered = $getFilterData->count();

            /* GET DATA WITH FILTER AS A MODEL */
            $datas = $this->dataFilter($this->model, $props, null);

            /* ADD FILTER BY MODULE, USER & EVENT IF AVAILABLE */
            if (isset($props['module'])) {
                $datas->where('module', '=', $props['module']);
            }

            if (isset($props['user'])) {
                $datas->where('user_id', '=', $props['user']);
            }

            if (isset($props['event'])) {
                $datas->where('event', '=', $props['event']);
            }

            /* RETRIEVE ALL ROW, CONVERT TO ARRAY AND FORMAT AS RESOURCE */
            $datas = $datas->with('user')->get();
            $datas = ActivityLogResource::collection($datas);
            $activity = [
                "total" => $getAllData->count(),
                "total_filter" => $totalFiltered,
                "per_page" => $props['take'],
                "current_page" => $props['skip'] == 0 ? 1 : ($props['skip'] + 1),
                "last_page" => ceil($totalFiltered / $props['take']),
                "from" => $totalFiltered === 0 ? 0 : ($props['skip'] != 0 ? ($props['skip'] * $props['take']) + 1 : 1),
                "to" => $totalFiltered === 0 ? 0 : ($props['skip'] * $props['take']) + $datas->count(),
                "show" => [
                    ["number" => 25, "name" => "25"], ["number" => 50, "name" => "50"], ["number" => 100, "name" => "100"]
                ],
                "data" => $datas
            ];

            return $activity;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /* FETCH ACTIVITY LOG BY ID */
    public function fetchById($id){
        try {
            $activityLog = $this->model::with('user')->find($id);
            if ($activityLog) {
                $activityLog = ActivityLogResource::make($activityLog);
                return $activityLog;
            }

            throw new Exception('Record not found!');
        } catch (Exception $ex) {
            throw $ex;
        }
    }
}
