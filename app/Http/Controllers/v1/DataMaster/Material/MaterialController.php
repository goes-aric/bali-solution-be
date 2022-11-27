<?php
namespace App\Http\Controllers\v1\DataMaster\Material;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\v1\BaseController;
use App\Http\Services\v1\DataMaster\Material\MaterialServices;
use App\Jobs\Material\ExportDraftMaterials;
use App\Jobs\Material\ExportMaterials;

class MaterialController extends BaseController
{
    private $materialServices;
    private $moduleName;

    public function __construct(MaterialServices $materialServices)
    {
        $this->materialServices = $materialServices;
        $this->moduleName = 'Material';
    }

    public function list(Request $request)
    {
        // if ($this->checkPermissions($this->moduleName, 'view') == true) {
            try {
                $props = $this->getBaseQueryParams($request, []);
                $props += [
                    'status'    => $request['status']
                ];
                $material = $this->materialServices->fetchAll($props);

                return $this->returnResponse('success', self::HTTP_OK, 'Daftar material', $material);
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
                $props += [
                    'status'    => $request['status']
                ];
                $material = $this->materialServices->fetchLimit($props);

                return $this->returnResponse('success', self::HTTP_OK, 'Daftar material', $material);
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
                    'kode'                  => 'required|max:150|unique:material,kode,NULL,id,deleted_at,NULL',
                    'nama_material'         => 'required|max:255',
                    'panjang'               => 'nullable|numeric',
                    'satuan'                => 'required',
                    'warna'                 => 'nullable',
                    'gambar'                => 'nullable|mimes:jpeg,jpg,png|max:2048',
                    'harga_beli_terakhir'   => 'nullable|numeric',
                    'harga_beli_konversi'   => 'nullable|numeric',
                    'harga_jual'            => 'nullable|numeric',
                    'status'                => 'required',
                ];
                $validator = $this->returnValidator($request->all(), $rules);
                if ($validator->fails()) {
                    return $this->returnResponse('error', self::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
                }

                $material = $this->materialServices->createMaterial($request);
                return $this->returnResponse('success', self::HTTP_OK, 'Material berhasil dibuat', $material);
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
                $material = $this->materialServices->fetchById($id);
                return $this->returnResponse('success', self::HTTP_OK, 'Detail material', $material);
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
                    'kode'                  => 'required|max:150|unique:material,kode,'.$id.',id,deleted_at,NULL',
                    'nama_material'         => 'required|max:255',
                    'panjang'               => 'nullable|numeric',
                    'satuan'                => 'required',
                    'warna'                 => 'nullable',
                    'gambar'                => 'nullable|mimes:jpeg,jpg,png|max:2048',
                    'harga_beli_terakhir'   => 'nullable|numeric',
                    'harga_beli_konversi'   => 'nullable|numeric',
                    'harga_jual'            => 'nullable|numeric',
                    'status'                => 'required',
                ];
                $validator = $this->returnValidator($request->all(), $rules);
                if ($validator->fails()) {
                    return $this->returnResponse('error', self::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
                }

                $material = $this->materialServices->updateMaterial($request, $id);
                return $this->returnResponse('success', self::HTTP_OK, 'Material berhasil diperbaharui', $material);
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
                $material = $this->materialServices->destroyMaterial($id);
                return $this->returnResponse('success', self::HTTP_OK, 'Catatan berhasil dihapus!', $material);
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
                $material = $this->materialServices->destroyMultipleMaterial($props);

                return $this->returnResponse('success', self::HTTP_OK, 'Catatan berhasil dihapus!', $material);
            } catch (Exception $ex) {
                return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
            }
        // }

        // return $this->returnNoPermissionResponse();
    }

    public function export(Request $request)
    {
        try {
            $props = $this->getBaseQueryParams($request, []);
            $broadcastUrl = config('app.broadcast_url').'/global/post';
            $dataResponse = $this->materialServices->fetchExportData($props);
            $params = [
                'url'   => $broadcastUrl,
                'key'   => 'downloadMaterial',
                'data'  => $dataResponse['data'],
                'props' => $props
            ];
            $material = new ExportMaterials($params);
            $this->dispatch($material);

            return $this->returnResponse('success', self::HTTP_OK, 'Harap tunggu file yang Anda minta sedang disiapkan', null);
        } catch (Exception $ex) {
            return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
        }
    }

    public function exportDraft()
    {
        try {
            $broadcastUrl = config('app.broadcast_url').'/global/post';
            $params = [
                'url'   => $broadcastUrl,
                'key'   => 'downloadDraftMaterial',
                'data'  => null,
                'props' => null
            ];
            $draft = new ExportDraftMaterials($params);
            $this->dispatch($draft);

            return $this->returnResponse('success', self::HTTP_OK, 'Harap tunggu file yang Anda minta sedang disiapkan', null);
        } catch (Exception $ex) {
            return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
        }
    }

    public function fetchDataOptions(Request $request)
    {
        try {
            $props = $this->getBaseQueryParams($request, []);
            $material = $this->materialServices->fetchDataOptions($props);

            return $this->returnResponse('success', self::HTTP_OK, 'Daftar material', $material);
        } catch (Exception $ex) {
            return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
        }
    }
}