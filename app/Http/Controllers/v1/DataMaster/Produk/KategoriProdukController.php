<?php
namespace App\Http\Controllers\v1\DataMaster\Produk;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\v1\BaseController;
use App\Http\Services\v1\DataMaster\Produk\KategoriProdukServices;

class KategoriProdukController extends BaseController
{
    private $kategoriProdukServices;
    private $moduleName;

    public function __construct(KategoriProdukServices $kategoriProdukServices)
    {
        $this->kategoriProdukServices = $kategoriProdukServices;
        $this->moduleName = 'Kategori Produk';
    }

    public function list(Request $request)
    {
        // if ($this->checkPermissions($this->moduleName, 'view') == true) {
            try {
                $props = $this->getBaseQueryParams($request, []);
                $kategori = $this->kategoriProdukServices->fetchAll($props);

                return $this->returnResponse('success', self::HTTP_OK, 'Daftar kategori produk', $kategori);
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
                $kategori = $this->kategoriProdukServices->fetchLimit($props);

                return $this->returnResponse('success', self::HTTP_OK, 'Daftar kategori produk', $kategori);
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
                    'nama_kategori' => 'required|string|max:255|unique:kategori_produk,nama_kategori,NULL,id,deleted_at,NULL',
                    'keterangan'    => 'nullable',
                    'status'        => 'required',
                ];
                $validator = $this->returnValidator($request->all(), $rules);
                if ($validator->fails()) {
                    return $this->returnResponse('error', self::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
                }

                $kategori = $this->kategoriProdukServices->createKategori($request);
                return $this->returnResponse('success', self::HTTP_OK, 'Kategori produk berhasil dibuat', $kategori);
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
                $kategori = $this->kategoriProdukServices->fetchById($id);
                return $this->returnResponse('success', self::HTTP_OK, 'Detail kategori produk', $kategori);
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
                    'nama_kategori' => 'required|string|max:255|unique:kategori_produk,nama_kategori,'.$id.',id,deleted_at,NULL',
                    'keterangan'    => 'nullable',
                    'status'        => 'required',
                ];
                $validator = $this->returnValidator($request->all(), $rules);
                if ($validator->fails()) {
                    return $this->returnResponse('error', self::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
                }

                $kategori = $this->kategoriProdukServices->updateKategori($request, $id);
                return $this->returnResponse('success', self::HTTP_OK, 'Kategori produk berhasil diperbaharui', $kategori);
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
                $kategori = $this->kategoriProdukServices->destroyKategori($id);
                return $this->returnResponse('success', self::HTTP_OK, 'Catatan berhasil dihapus!', $kategori);
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
                $kategori = $this->kategoriProdukServices->destroyMultipleKategori($props);

                return $this->returnResponse('success', self::HTTP_OK, 'Catatan berhasil dihapus!', $kategori);
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
            $kategori = $this->kategoriProdukServices->fetchDataOptions($props);

            return $this->returnResponse('success', self::HTTP_OK, 'Daftar kategori produk', $kategori);
        } catch (Exception $ex) {
            return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
        }
    }
}
