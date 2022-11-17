<?php
namespace App\Http\Controllers\v1\DataMaster\Produk;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\v1\BaseController;
use App\Http\Services\v1\DataMaster\Produk\PaketProdukServices;

class PaketProdukController extends BaseController
{
    private $paketProdukServices;
    private $moduleName;

    public function __construct(PaketProdukServices $paketProdukServices)
    {
        $this->paketProdukServices = $paketProdukServices;
        $this->moduleName = 'Paket Produk';
    }

    public function list(Request $request)
    {
        // if ($this->checkPermissions($this->moduleName, 'view') == true) {
            try {
                $props = $this->getBaseQueryParams($request, []);
                $produk = $this->paketProdukServices->fetchAll($props);

                return $this->returnResponse('success', self::HTTP_OK, 'Daftar paket produk', $produk);
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
                $produk = $this->paketProdukServices->fetchLimit($props);

                return $this->returnResponse('success', self::HTTP_OK, 'Daftar paket produk', $produk);
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
                    'kategori_produk'   => 'required',
                    'nama_paket_produk' => 'required|string|max:255|unique:paket_produk,nama_paket_produk,NULL,id,deleted_at,NULL',
                    'warna'             => 'required',
                    'satuan'            => 'required',
                    'gambar'            => 'nullable|mimes:jpeg,jpg,png|max:2048',
                ];
                $validator = $this->returnValidator($request->all(), $rules);
                if ($validator->fails()) {
                    return $this->returnResponse('error', self::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
                }

                $produk = $this->paketProdukServices->createPaketProduk($request);
                return $this->returnResponse('success', self::HTTP_OK, 'Paket produk berhasil dibuat', $produk);
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
                $produk = $this->paketProdukServices->fetchById($id);
                return $this->returnResponse('success', self::HTTP_OK, 'Detail paket produk', $produk);
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
                    'kategori_produk'   => 'required',
                    'nama_paket_produk' => 'required|string|max:255|unique:paket_produk,nama_paket_produk,'.$id.',id,deleted_at,NULL',
                    'warna'             => 'required',
                    'satuan'            => 'required',
                    'gambar'            => 'nullable|mimes:jpeg,jpg,png|max:2048',
                ];
                $validator = $this->returnValidator($request->all(), $rules);
                if ($validator->fails()) {
                    return $this->returnResponse('error', self::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
                }

                $produk = $this->paketProdukServices->updatePaketProduk($request, $id);
                return $this->returnResponse('success', self::HTTP_OK, 'Paket produk berhasil diperbaharui', $produk);
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
                $produk = $this->paketProdukServices->destroyPaketProduk($id);
                return $this->returnResponse('success', self::HTTP_OK, 'Catatan berhasil dihapus!', $produk);
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
                $produk = $this->paketProdukServices->destroyMultiplePaketProduk($props);

                return $this->returnResponse('success', self::HTTP_OK, 'Catatan berhasil dihapus!', $produk);
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
            $produk = $this->paketProdukServices->fetchDataOptions($props);

            return $this->returnResponse('success', self::HTTP_OK, 'Daftar paket produk', $produk);
        } catch (Exception $ex) {
            return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
        }
    }
}
