<?php
namespace App\Http\Controllers\v1\Settings\User;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\v1\BaseController;
use App\Http\Services\v1\Settings\User\RoleServices;

class RoleController extends BaseController
{
    private $roleServices;

    public function __construct(RoleServices $roleServices)
    {
        $this->roleServices = $roleServices;
    }

    public function list(Request $request)
    {
        try {
            $props = $this->getBaseQueryParams($request, []);
            $roles = $this->roleServices->fetchAll($props);

            return $this->returnResponse('success', self::HTTP_OK, 'Daftar kelompok user', $roles);
        } catch (Exception $ex) {
            return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
        }
    }

    public function index(Request $request)
    {
        try {
            $props = $this->getBaseQueryParams($request, []);
            $roles = $this->roleServices->fetchLimit($props);

            return $this->returnResponse('success', self::HTTP_OK, 'Daftar kelompok user', $roles);
        } catch (Exception $ex) {
            return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
        }
    }

    public function store(Request $request)
    {
        try {
            $rules = [
                'nama_kelompok_user'  => 'required|string|max:150|unique:roles,role_name,NULL,id,deleted_at,NULL',
            ];
            $validator = $this->returnValidator($request->all(), $rules);
            if ($validator->fails()) {
                return $this->returnResponse('error', self::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
            }

            $role = $this->roleServices->createRole($request);
            return $this->returnResponse('success', self::HTTP_CREATED, 'Kelompok user berhasil dibuat', $role);
        } catch (Exception $ex) {
            return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
        }
    }

    public function show($id)
    {
        try {
            $role = $this->roleServices->fetchById($id);
            return $this->returnResponse('success', self::HTTP_OK, 'Detail kelompok user', $role);
        } catch (Exception $ex) {
            return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $rules = [
                'nama_kelompok_user'  => 'required|string|max:25|unique:roles,role_name,'.$id.',id,deleted_at,NULL',
            ];
            $validator = $this->returnValidator($request->all(), $rules);
            if ($validator->fails()) {
                return $this->returnResponse('error', self::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
            }

            $role = $this->roleServices->updateRole($request, $id);
            return $this->returnResponse('success', self::HTTP_OK, 'Kelompok user berhasil diperbaharui', $role);
        } catch (Exception $ex) {
            return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
        }
    }

    public function destroy($id)
    {
        try {
            $role = $this->roleServices->destroyRole($id);
            return $this->returnResponse('success', self::HTTP_OK, 'Catatan berhasil dihapus!', $role);
        } catch (Exception $ex) {
            return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
        }
    }

    public function fetchPermissionOptions()
    {
        try {
            $appPermissions = $this->roleServices->fetchAppPermissions();
            return $this->returnResponse('success', self::HTTP_OK, 'User permissions', $appPermissions);
        } catch (Exception $ex) {
            return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
        }
    }
}
