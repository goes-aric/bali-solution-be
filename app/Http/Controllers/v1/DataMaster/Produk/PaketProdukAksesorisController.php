<?php
namespace App\Http\Controllers\v1\DataMaster\Produk;

use Illuminate\Http\Request;
use App\Http\Controllers\v1\BaseController;
use App\Http\Services\v1\DataMaster\Produk\PaketProdukAksesorisServices;
use Exception;

class PaketProdukAksesorisController extends BaseController
{
    private $aksesorisProdukServices;
    private $moduleName;

    public function __construct(PaketProdukAksesorisServices $aksesorisProdukServices)
    {
        $this->aksesorisProdukServices = $aksesorisProdukServices;
        $this->moduleName = 'Paket Produk';
    }

    public function list(Request $request)
    {
        // if ($this->checkPermissions($this->moduleName, 'view') == true) {
            try {
                $props = $this->getBaseQueryParams($request, []);
                $props += [
                    'paket_produk_id'   => $request['paket_produk_id']
                ];
                $paket = $this->aksesorisProdukServices->fetchAll($props);

                return $this->returnResponse('success', self::HTTP_OK, 'Daftar paket aksesoris', $paket);
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
                    'paket_produk_id'   => $request['paket_produk_id']
                ];
                $paket = $this->aksesorisProdukServices->fetchLimit($props);

                return $this->returnResponse('success', self::HTTP_OK, 'Daftar paket aksesoris', $paket);
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
                    'paket_aksesoris'   => 'required',
                    'nama_paket'        => 'required',
                    'minimal_lebar'     => 'required|numeric',
                    'maksimal_lebar'    => 'required|numeric',
                    'minimal_tinggi'    => 'required|numeric',
                    'maksimal_tinggi'   => 'required|numeric',
                    'jumlah_daun'       => 'required|numeric',
                ];
                $validator = $this->returnValidator($request->all(), $rules);
                if ($validator->fails()) {
                    return $this->returnResponse('error', self::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
                }

                $paket = $this->aksesorisProdukServices->createPaket($request);
                return $this->returnResponse('success', self::HTTP_CREATED, 'Paket aksesoris berhasil ditambahkan', $paket);
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
                $paket = $this->aksesorisProdukServices->fetchById($id);
                return $this->returnResponse('success', self::HTTP_OK, 'Detail paket aksesoris', $paket);
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
                    'paket_aksesoris'   => 'required',
                    'minimal_lebar'     => 'required|numeric',
                    'maksimal_lebar'    => 'required|numeric',
                    'minimal_tinggi'    => 'required|numeric',
                    'maksimal_tinggi'   => 'required|numeric',
                    'jumlah_daun'       => 'required|numeric',
                ];
                $validator = $this->returnValidator($request->all(), $rules);
                if ($validator->fails()) {
                    return $this->returnResponse('error', self::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
                }

                $paket = $this->aksesorisProdukServices->updatePaket($request, $id);
                return $this->returnResponse('success', self::HTTP_OK, 'Paket aksesoris berhasil diperbaharui', $paket);
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
                $paket = $this->aksesorisProdukServices->destroyPaket($id);
                return $this->returnResponse('success', self::HTTP_OK, 'Catatan berhasil dihapus!', $paket);
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
                $paket = $this->aksesorisProdukServices->destroyMultiplePaket($props);

                return $this->returnResponse('success', self::HTTP_OK, 'Catatan berhasil dihapus!', $paket);
            } catch (Exception $ex) {
                return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
            }
        // }

        // return $this->returnNoPermissionResponse();
    }
}
