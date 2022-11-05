<?php
namespace App\Http\Controllers\v1\Settings\TipePenyesuaian;

use Illuminate\Http\Request;
use App\Http\Controllers\v1\BaseController;
use App\Http\Services\v1\Settings\TipePenyesuaian\TipePenyesuaianServices;
use Exception;

class TipePenyesuaianController extends BaseController
{
    private $tipePenyesuaianServices;
    private $moduleName;

    public function __construct(TipePenyesuaianServices $tipePenyesuaianServices)
    {
        $this->tipePenyesuaianServices = $tipePenyesuaianServices;
        $this->moduleName = 'Tipe Penyesuaian';
    }

    public function list(Request $request)
    {
        // if ($this->checkPermissions($this->moduleName, 'view') == true) {
            try {
                $props = $this->getBaseQueryParams($request, []);
                $tipePenyesuaian = $this->tipePenyesuaianServices->fetchAll($props);

                return $this->returnResponse('success', self::HTTP_OK, 'Daftar tipe penyesuaian', $tipePenyesuaian);
            } catch (Exception $ex) {
                return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
            }
        // }

        // return $this->returnNoPermissionResponse();
    }

    public function index(Request $request)
    {
        // if ($this->checkPermissions($this->moduleName, 'view') == true) {
            try {
                $props = $this->getBaseQueryParams($request, []);
                $tipePenyesuaian = $this->tipePenyesuaianServices->fetchLimit($props);

                return $this->returnResponse('success', self::HTTP_OK, 'Daftar tipe penyesuaian', $tipePenyesuaian);
            } catch (Exception $ex) {
                return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
            }
        // }

        // return $this->returnNoPermissionResponse();
    }

    public function store(Request $request)
    {
        // if ($this->checkPermissions($this->moduleName, 'create') == true) {
            try {
                $rules = [
                    'deskripsi' => 'required|string|max:255|unique:tipe_penyesuaian',
                    'posisi'    => 'required',
                ];
                $validator = $this->returnValidator($request->all(), $rules);
                if ($validator->fails()) {
                    return $this->returnResponse('error', self::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
                }

                $tipePenyesuaian = $this->tipePenyesuaianServices->createTipePenyesuaian($request);
                return $this->returnResponse('success', self::HTTP_CREATED, 'Tipe penyesuaian berhasil dibuat', $tipePenyesuaian);
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
                $tipePenyesuaian = $this->tipePenyesuaianServices->fetchById($id);
                return $this->returnResponse('success', self::HTTP_OK, 'Detail tipe penyesuaian', $tipePenyesuaian);
            } catch (Exception $ex) {
                return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
            }
        // }

        // return $this->returnNoPermissionResponse();
    }

    public function update(Request $request, $id)
    {
        // if ($this->checkPermissions($this->moduleName, 'edit') == true) {
            try {
                $rules = [
                    'deskripsi' => 'required|string|max:255|unique:tipe_penyesuaian,deskripsi,'.$id.'',
                    'posisi'    => 'required',
                ];
                $validator = $this->returnValidator($request->all(), $rules);
                if ($validator->fails()) {
                    return $this->returnResponse('error', self::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
                }

                $tipePenyesuaian = $this->tipePenyesuaianServices->updateTipePenyesuaian($request, $id);
                return $this->returnResponse('success', self::HTTP_OK, 'Tipe penyesuaian berhasil diperbaharui', $tipePenyesuaian);
            } catch (Exception $ex) {
                return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
            }
        // }

        // return $this->returnNoPermissionResponse();
    }

    public function destroy($id)
    {
        // if ($this->checkPermissions($this->moduleName, 'delete') == true) {
            try {
                $tipePenyesuaian = $this->tipePenyesuaianServices->destroyTipePenyesuaian($id);
                return $this->returnResponse('success', self::HTTP_OK, 'Catatan berhasil dihapus!', $tipePenyesuaian);
            } catch (Exception $ex) {
                return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
            }
        // }

        // return $this->returnNoPermissionResponse();
    }

    public function destroyMultiple(Request $request)
    {
        // if ($this->checkPermissions($this->moduleName, 'delete') == true) {
            try {
                $props = $request->data;
                $tipePenyesuaian = $this->tipePenyesuaianServices->destroyMultipleTipePenyesuaian($props);

                return $this->returnResponse('success', self::HTTP_OK, 'Catatan berhasil dihapus!', $tipePenyesuaian);
            } catch (Exception $ex) {
                return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
            }
        // }

        // return $this->returnNoPermissionResponse();
    }

    public function fetchDataOptions(Request $request)
    {
        try {
            $props = $this->getBaseQueryParams($request, []);
            $tipePenyesuaian = $this->tipePenyesuaianServices->fetchDataOptions($props);

            return $this->returnResponse('success', self::HTTP_OK, 'Daftar tipe penyesuaian', $tipePenyesuaian);
        } catch (Exception $ex) {
            return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
        }
    }
}
