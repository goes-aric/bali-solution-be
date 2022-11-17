<?php
namespace App\Http\Controllers\v1\DataMaster\Produk;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\v1\BaseController;
use App\Http\Services\v1\DataMaster\Produk\PaketProdukMaterialUpvcServices;

class PaketProdukMaterialUpvcController extends BaseController
{
    private $materialUpvcServices;
    private $moduleName;

    public function __construct(PaketProdukMaterialUpvcServices $materialUpvcServices)
    {
        $this->materialUpvcServices = $materialUpvcServices;
        $this->moduleName = 'Paket Produk';
    }

    public function list(Request $request)
    {
        // if ($this->checkPermissions($this->moduleName, 'view') == true) {
            try {
                $props = $this->getBaseQueryParams($request, []);
                $props += [
                    'paket_produk_id'   => $request['paket_produk_id'],
                    'tipe'              => $request['tipe']
                ];
                $material = $this->materialUpvcServices->fetchAll($props);

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
                    'paket_produk_id'   => $request['paket_produk_id'],
                    'tipe'              => $request['tipe']
                ];
                $material = $this->materialUpvcServices->fetchLimit($props);

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
                    'paket_produk_id'   => 'required|numeric',
                    'material'          => 'required',
                    'kode'              => 'required',
                    'nama_material'     => 'required',
                    'panjang'           => 'required|numeric',
                    'satuan'            => 'required',
                    'tipe'              => 'required',
                ];
                $validator = $this->returnValidator($request->all(), $rules);
                if ($validator->fails()) {
                    return $this->returnResponse('error', self::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
                }

                $material = $this->materialUpvcServices->createMaterial($request);
                return $this->returnResponse('success', self::HTTP_OK, 'Material berhasil ditambahkan', $material);
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
                $material = $this->materialUpvcServices->fetchById($id);
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
                    'paket_produk_id'   => 'required|numeric',
                    'material'          => 'required',
                    'kode'              => 'required',
                    'nama_material'     => 'required',
                    'panjang'           => 'required|numeric',
                    'satuan'            => 'required',
                    'tipe'              => 'required',
                ];
                $validator = $this->returnValidator($request->all(), $rules);
                if ($validator->fails()) {
                    return $this->returnResponse('error', self::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
                }

                $material = $this->materialUpvcServices->updateMaterial($request, $id);
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
                $material = $this->materialUpvcServices->destroyMaterial($id);
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
                $material = $this->materialUpvcServices->destroyMultipleMaterial($props);

                return $this->returnResponse('success', self::HTTP_OK, 'Catatan berhasil dihapus!', $material);
            } catch (Exception $ex) {
                return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
            }
        // }

        // return $this->returnNoPermissionResponse();
    }
}
