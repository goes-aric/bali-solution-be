<?php
namespace App\Http\Controllers\v1\DataMaster\Material;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\v1\BaseController;
use App\Http\Services\v1\DataMaster\Material\PaketAksesorisMaterialServices;

class PaketAksesorisMaterialController extends BaseController
{
    private $paketMaterialServices;

    public function __construct(PaketAksesorisMaterialServices $paketMaterialServices)
    {
        $this->paketMaterialServices = $paketMaterialServices;
    }

    public function list(Request $request, $id)
    {
        try {
            $props = $this->getBaseQueryParams($request, []);
            $materials = $this->paketMaterialServices->fetchAll($props, $id);

            return $this->returnResponse('success', self::HTTP_OK, 'Daftar material paket aksesoris', $materials);
        } catch (Exception $ex) {
            return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
        }
    }

    public function index(Request $request, $id)
    {
        try {
            $props = $this->getBaseQueryParams($request, []);
            $materials = $this->paketMaterialServices->fetchLimit($props, $id);

            return $this->returnResponse('success', self::HTTP_OK, 'Daftar material paket aksesoris', $materials);
        } catch (Exception $ex) {
            return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
        }
    }

    public function store(Request $request)
    {
        try {
            $rules = [
                'paket_aksesoris_id'    => 'required',
                'aksesoris_id'          => 'required',
                'kode'                  => 'required',
                'nama_material'         => 'required',
                'tipe'                  => 'nullable',
                'satuan'                => 'required',
                'qty'                   => 'required|numeric',
            ];
            $validator = $this->returnValidator($request->all(), $rules);
            if ($validator->fails()) {
                return $this->returnResponse('error', self::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
            }

            $material = $this->paketMaterialServices->createMaterial($request);
            return $this->returnResponse('success', self::HTTP_OK, 'Material paket aksesoris berhasil dibuat', $material);
        } catch (Exception $ex) {
            return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
        }
    }

    public function show($id)
    {
        try {
            $material = $this->paketMaterialServices->fetchById($id);
            return $this->returnResponse('success', self::HTTP_OK, 'Detail material paket aksesoris', $material);
        } catch (Exception $ex) {
            return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $rules = [
                'paket_aksesoris_id'    => 'required',
                'aksesoris_id'          => 'required',
                'kode'                  => 'required',
                'nama_material'         => 'required',
                'tipe'                  => 'nullable',
                'satuan'                => 'required',
                'qty'                   => 'required|numeric',
            ];
            $validator = $this->returnValidator($request->all(), $rules);
            if ($validator->fails()) {
                return $this->returnResponse('error', self::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
            }

            $material = $this->paketMaterialServices->updateMaterial($request, $id);
            return $this->returnResponse('success', self::HTTP_OK, 'Material paket aksesoris berhasil diperbaharui', $material);
        } catch (Exception $ex) {
            return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
        }
    }

    public function destroy($id)
    {
        try {
            $material = $this->paketMaterialServices->destroyMaterial($id);
            return $this->returnResponse('success', self::HTTP_OK, 'Catatan berhasil dihapus!', $material);
        } catch (Exception $ex) {
            return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
        }
    }

    public function destroyMultiple(Request $request)
    {
        try {
            $props = $request->data;
            $materials = $this->paketMaterialServices->destroyMultipleMaterial($props);

            return $this->returnResponse('success', self::HTTP_OK, 'Catatan berhasil dihapus!', $materials);
        } catch (Exception $ex) {
            return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
        }
    }
}
