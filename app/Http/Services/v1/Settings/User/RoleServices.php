<?php
namespace App\Http\Services\v1\Settings\User;

use Exception;
use App\Models\Roles;
use App\Models\Permissions;
use Illuminate\Support\Facades\DB;
use App\Http\Services\v1\BaseServices;
use App\Http\Services\v1\Settings\User\PermissionServices;
use App\Http\Resources\v1\Settings\User\RoleResource;

class RoleServices extends BaseServices
{
    /* PRIVATE VARIABLE */
    private $model;
    private $permissionModel;
    private $carbon;
    private $permissionServices;
    private $moduleName;
    private $oldValues;
    private $newValues;
    private $masterPermissions;

    public function __construct()
    {
        $this->model = new Roles();
        $this->permissionModel = new Permissions();
        $this->carbon = $this->returnCarbon();
        $this->permissionServices = new PermissionServices;
        $this->moduleName = 'Kelompok User';
    }

    /* FETCH ALL ROLE */
    public function fetchAll($props){
        try {
            /* GET DATA WITH FILTER AS A MODEL */
            $datas = $this->dataFilterPagination($this->model, $props, null);

            /* RETRIEVE ALL ROW, CONVERT TO ARRAY AND FORMAT AS RESOURCE */
            $datas = $datas->get();
            $datas = RoleResource::collection($datas);
            return $datas;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /* FETCH ALL ROLE LIMIT */
    public function fetchLimit($props){
        try {
            /* GET DATA FOR PAGINATION AS A MODEL */
            $getAllData = $this->dataFilterPagination($this->model, [], null);

            /* GET DATA WITH FILTER FOR PAGINATION AS A MODEL */
            $getFilterData = $this->dataFilterPagination($this->model, $props, null);
            $totalFiltered = $getFilterData->count();

            /* GET DATA WITH FILTER AS A MODEL */
            $datas = $this->dataFilter($this->model, $props, null);

            /* RETRIEVE ALL ROW, CONVERT TO ARRAY AND FORMAT AS RESOURCE */
            $datas = $datas->get();
            $datas = RoleResource::collection($datas);
            $roles = [
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

            return $roles;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /* FETCH ROLE BY ID */
    public function fetchById($id){
        try {
            $role = $this->model::with('permissions')->find($id);
            if ($role) {
                $role = RoleResource::make($role);
                return $role;
            }

            throw new Exception('Catatan tidak ditemukan!');
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /* CREATE NEW ROLE */
    public function createRole($props){
        /* BEGIN DB TRANSACTION */
        DB::beginTransaction();

        try {
            if (!empty($props['permissions'])) {
                $role = $this->model;
                $role->role_name    = $props['nama_kelompok_user'];
                $role->description  = $props['deskripsi'];
                $role->created_id   = $this->returnAuthUser()->id;
                $role->save();

                /* LOOPING CHECKED PERMISSIONS */
                $permissions = [];
                foreach ($props['permissions'] as $permission) {
                    $permissionItem = explode('-', $permission);
                    $groupKey = $permissionItem[0];
                    $key = $permissionItem[1];

                    /* GET PERMISSION DETAILS */
                    $item = $this->permissionServices->getPermissions($groupKey, $key);

                    /* POPULATE ROLE PERMISSIONS */
                    $permissions[] = [
                        'permission_name'   => $item['permission_name'],
                        'key'               => $item['key'],
                        'group_key'         => $item['group_key'],
                        'master_group'      => $item['master_group'],
                        'role_id'           => $role->id,
                        'created_at'        => $this->carbon::now(),
                        'updated_at'        => $this->carbon::now(),
                    ];
                }
                /* INSERT ROLE PERMISSIONS  */
                $this->permissionModel::insert($permissions);

                /* WRITE LOG */
                $this->newValues = $this->model::with('permissions')->find($role->id);
                $logParameters = [
                    'status'        => 'success',
                    'module'        => $this->moduleName,
                    'event'         => 'created',
                    'description'   => 'Create new role [ '.$role->id.' - '.$role->role_name.' ]',
                    'user_id'       => $this->returnAuthUser()->id ?? null,
                    'old_values'    => $this->oldValues,
                    'new_values'    => $this->newValues
                ];
                $this->writeActivityLog($logParameters);

                /* COMMIT DB TRANSACTION */
                DB::commit();

                return $role;
            } else {
                throw new Exception('Silakan pilih minimal 1 hak akses');
            }
        } catch (Exception $ex) {
            /* ROLLBACK DB TRANSACTION */
            DB::rollback();

            /* WRITE LOG */
            $logParameters = [
                'status'        => 'error',
                'module'        => $this->moduleName,
                'event'         => 'created',
                'description'   => 'Gagal membuat role [ Pesan: '.$ex->getMessage().' ]',
                'user_id'       => $this->returnAuthUser()->id ?? null,
                'old_values'    => $this->oldValues,
                'new_values'    => $this->newValues
            ];
            $this->writeActivityLog($logParameters);

            throw $ex;
        }
    }

    /* UPDATE ROLE */
    public function updateRole($props, $id){
        /* BEGIN DB TRANSACTION */
        DB::beginTransaction();

        try {
            if (!empty($props['permissions'])) {
                $this->oldValues = $this->model::with('permissions')->find($id);
                $role = $this->model::find($id);
                if ($role) {
                    /* DELETE PREVIOUS PERMISSIONS */
                    $this->permissionModel::where('role_id', '=', $role->id)->delete();

                    /* UPDATE ROLE */
                    $role->role_name    = $props['nama_kelompok_user'];
                    $role->description  = $props['deskripsi'];
                    $role->updated_id   = $this->returnAuthUser()->id;
                    $role->update();

                    /* LOOPING CHECKED PERMISSIONS */
                    $permissions = [];
                    foreach ($props['permissions'] as $permission) {
                        $permissionItem = explode('-', $permission);
                        $groupKey = $permissionItem[0];
                        $key = $permissionItem[1];

                        /* GET PERMISSION DETAILS */
                        $item = $this->permissionServices->getPermissions($groupKey, $key);

                        /* POPULATE ROLE PERMISSIONS */
                        $permissions[] = [
                            'permission_name'   => $item['permission_name'],
                            'key'               => $item['key'],
                            'group_key'         => $item['group_key'],
                            'master_group'      => $item['master_group'],
                            'role_id'           => $role->id,
                            'created_at'        => $this->carbon::now(),
                            'updated_at'        => $this->carbon::now(),
                        ];
                    }
                    /* INSERT ROLE PERMISSIONS  */
                    $this->permissionModel::insert($permissions);

                    /* WRITE LOG */
                    $this->newValues = $this->model::with('permissions')->find($role->id);
                    $logParameters = [
                        'status'        => 'success',
                        'module'        => $this->moduleName,
                        'event'         => 'updated',
                        'description'   => 'Memperbaharui role [ '.$role->id.' - '.$role->role_name.' ]',
                        'user_id'       => $this->returnAuthUser()->id ?? null,
                        'old_values'    => $this->oldValues,
                        'new_values'    => $this->newValues
                    ];
                    $this->writeActivityLog($logParameters);

                    /* COMMIT DB TRANSACTION */
                    DB::commit();

                    return $role;
                } else {
                    throw new Exception('Catatan tidak ditemukan!');
                }
            } else {
                throw new Exception('Silakan pilih minimal 1 hak akses');
            }
        } catch (Exception $ex) {
            /* ROLLBACK DB TRANSACTION */
            DB::rollback();

            /* WRITE LOG */
            $logParameters = [
                'status'        => 'error',
                'module'        => $this->moduleName,
                'event'         => 'updated',
                'description'   => 'Gagal memperbaharui role [ Pesan: '.$ex->getMessage().' ]',
                'user_id'       => $this->returnAuthUser()->id ?? null,
                'old_values'    => $this->oldValues,
                'new_values'    => $this->newValues
            ];
            $this->writeActivityLog($logParameters);

            throw $ex;
        }
    }

    /* DESTROY ROLE */
    public function destroyRole($id){
        /* BEGIN DB TRANSACTION */
        DB::beginTransaction();

        try {
            $this->oldValues = $this->model::find($id);
            $role = $this->model::find($id);

            if ($role) {
                $role->delete();

                /* WRITE LOG */
                $logParameters = [
                    'status'        => 'success',
                    'module'        => $this->moduleName,
                    'event'         => 'deleted',
                    'description'   => 'Menghapus role [ '.$this->oldValues->id.' - '. $this->oldValues->role_name.' ]',
                    'user_id'       => $this->returnAuthUser()->id ?? null,
                    'old_values'    => $this->oldValues,
                    'new_values'    => $this->newValues
                ];
                $this->writeActivityLog($logParameters);

                return null;
            }

            throw new Exception('Catatan tidak ditemukan!');
        } catch (Exception $ex) {
            /* WRITE LOG */
            $logParameters = [
                'status'        => 'error',
                'module'        => $this->moduleName,
                'event'         => 'deleted',
                'description'   => 'Gagal menghapus role [ Pesan: '.$ex->getMessage().' ]',
                'user_id'       => $this->returnAuthUser()->id ?? null,
                'old_values'    => $this->oldValues,
                'new_values'    => $this->newValues
            ];
            $this->writeActivityLog($logParameters);

            throw $ex;
        }
    }

    public function fetchAppPermissions(){
        try {
            $permissions = [];
            $this->masterPermissions = $this->getMasterPermissions();
            foreach ($this->masterPermissions as $masterItem) {
                $parent = [];
                $parentPermissions = $this->getParentPermissions($masterItem);
                foreach ($parentPermissions as $parentItem) {
                    $childPermissions = [];
                    foreach ($this->permissionServices->appPermissions() as $permissionItem) {
                        if ($parentItem === $permissionItem['group_key']) {
                            $childPermissions[] = $permissionItem;
                        }
                    }

                    $parent[] = [
                        'name'  => $parentItem,
                        'items' => $childPermissions
                    ];
                }

                $permissions[] = [
                    'name'  => $masterItem,
                    'items' => $parent
                ];
            }

            /* RETURN RESPONSE USER PERMISSIONS */
            return $permissions;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    public function getMasterPermissions(){
        try {
            $masterPermissions = [];
            foreach ($this->permissionServices->appPermissions() as $permissionItem) {
                if (isset($masterPermissions[$permissionItem['master_group']])) {
                    $masterPermissions[$permissionItem['master_group']] = [];
                }
                $masterPermissions[$permissionItem['master_group']] = $permissionItem['master_group'];
            }

            return $masterPermissions;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    public function getParentPermissions($masterKey){
        try {
            $permissions = [];
            foreach ($this->permissionServices->appPermissions() as $permissionItem) {
                if ($masterKey === $permissionItem['master_group']) {
                    if (isset($permissions[$permissionItem['group_key']])) {
                        $permissions[$permissionItem['group_key']] = [];
                    }
                    $permissions[$permissionItem['group_key']] = $permissionItem['group_key'];
                }
            }

            return $permissions;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
}
