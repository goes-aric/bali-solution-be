<?php
namespace App\Http\Controllers\v1\DataMaster\Material;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\v1\BaseController;
use App\Http\Services\v1\DataMaster\Material\MaterialKacaServices;
use App\Jobs\Material\ExportMaterials;

class MaterialKacaController extends BaseController
{
    private $materialKacaServices;
    private $moduleName;

    public function __construct(MaterialKacaServices $materialKacaServices)
    {
        $this->materialKacaServices = $materialKacaServices;
        $this->moduleName = 'Material Kaca';
    }

    public function list(Request $request)
    {
        // if ($this->checkPermissions($this->moduleName, 'view') == true) {
            try {
                $props = $this->getBaseQueryParams($request, []);
                $props += [
                    'status'    => $request['status']
                ];
                $Material = $this->materialKacaServices->fetchAll($props);

                return $this->returnResponse('success', self::HTTP_OK, 'Daftar material', $Material);
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
                $Material = $this->materialKacaServices->fetchLimit($props);

                return $this->returnResponse('success', self::HTTP_OK, 'Daftar material', $Material);
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
                    'kode'                  => 'required|max:150|unique:material_kaca,kode,NULL,id,deleted_at,NULL',
                    'nama_material'         => 'required|max:255|unique:material_kaca,nama_material,NULL,id,deleted_at,NULL',
                    'panjang'               => 'nullable|numeric',
                    'lebar'                 => 'nullable|numeric',
                    'tebal'                 => 'nullable|numeric',
                    'satuan'                => 'required',
                    'gambar'                => 'nullable|mimes:jpeg,jpg,png|max:2048',
                    'harga_beli_terakhir'   => 'nullable|numeric',
                    'status'                => 'required',
                ];
                $validator = $this->returnValidator($request->all(), $rules);
                if ($validator->fails()) {
                    return $this->returnResponse('error', self::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
                }

                $Material = $this->materialKacaServices->createMaterial($request);
                return $this->returnResponse('success', self::HTTP_OK, 'Material berhasil dibuat', $Material);
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
                $Material = $this->materialKacaServices->fetchById($id);
                return $this->returnResponse('success', self::HTTP_OK, 'Detail material', $Material);
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
                    'kode'                  => 'required|max:150|unique:material_kaca,kode,'.$id.',id,deleted_at,NULL',
                    'nama_material'         => 'required|max:255|unique:material_kaca,nama_material,'.$id.',id,deleted_at,NULL',
                    'panjang'               => 'nullable|numeric',
                    'lebar'                 => 'nullable|numeric',
                    'tebal'                 => 'nullable|numeric',
                    'satuan'                => 'required',
                    'gambar'                => 'nullable|mimes:jpeg,jpg,png|max:2048',
                    'harga_beli_terakhir'   => 'nullable|numeric',
                    'status'                => 'required',
                ];
                $validator = $this->returnValidator($request->all(), $rules);
                if ($validator->fails()) {
                    return $this->returnResponse('error', self::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
                }

                $Material = $this->materialKacaServices->updateMaterial($request, $id);
                return $this->returnResponse('success', self::HTTP_OK, 'Material berhasil diperbaharui', $Material);
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
                $Material = $this->materialKacaServices->destroyMaterial($id);
                return $this->returnResponse('success', self::HTTP_OK, 'Catatan berhasil dihapus!', $Material);
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
                $Material = $this->materialKacaServices->destroyMultipleMaterial($props);

                return $this->returnResponse('success', self::HTTP_OK, 'Catatan berhasil dihapus!', $Material);
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
            $dataResponse = $this->materialKacaServices->fetchExportData($props);
            $params = [
                'url'   => $broadcastUrl,
                'key'   => 'downloadMaterialKaca',
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

    public function fetchDataOptions(Request $request)
    {
        try {
            $props = $this->getBaseQueryParams($request, []);
            $Material = $this->materialKacaServices->fetchDataOptions($props);

            return $this->returnResponse('success', self::HTTP_OK, 'Daftar material', $Material);
        } catch (Exception $ex) {
            return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
        }
    }
}
