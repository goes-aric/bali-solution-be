<?php
namespace App\Http\Controllers\v1\DataMaster\Produk;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\v1\BaseController;
use App\Http\Services\v1\DataMaster\Produk\PaketProdukMaterialKacaServices;

class PaketProdukMaterialKacaController extends BaseController
{
    private $materialKacaServices;
    private $moduleName;

    public function __construct(PaketProdukMaterialKacaServices $materialKacaServices)
    {
        $this->materialKacaServices = $materialKacaServices;
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
                    'paket_produk_id'   => $request['paket_produk_id'],
                    'tipe'              => $request['tipe']
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
                    'paket_produk_id'   => 'required|numeric',
                    'material'          => 'required',
                    'kode'              => 'required',
                    'nama_material'     => 'required',
                    'panjang'           => 'required|numeric',
                    'lebar'             => 'required|numeric',
                    'tebal'             => 'required|numeric',
                    'satuan'            => 'required',
                ];
                $validator = $this->returnValidator($request->all(), $rules);
                if ($validator->fails()) {
                    return $this->returnResponse('error', self::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
                }

                $Material = $this->materialKacaServices->createMaterial($request);
                return $this->returnResponse('success', self::HTTP_OK, 'Material berhasil ditambahkan', $Material);
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
                    'paket_produk_id'   => 'required|numeric',
                    'material'          => 'required',
                    'kode'              => 'required',
                    'nama_material'     => 'required',
                    'panjang'           => 'required|numeric',
                    'lebar'             => 'required|numeric',
                    'tebal'             => 'required|numeric',
                    'satuan'            => 'required',
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
}
