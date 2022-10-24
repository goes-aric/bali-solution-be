<?php
namespace App\Http\Controllers\v1\DataMaster\Material;

use Illuminate\Http\Request;
use App\Jobs\Supplier\ExportSupplier;
use App\Http\Controllers\v1\BaseController;
use App\Http\Services\v1\DataMaster\Material\PaketAksesorisServices;
use Exception;

class PaketAksesorisController extends BaseController
{
    private $paketAksesorisServices;
    private $moduleName;

    public function __construct(PaketAksesorisServices $paketAksesorisServices)
    {
        $this->paketAksesorisServices = $paketAksesorisServices;
        $this->moduleName = 'Paket Aksesoris';
    }

    public function list(Request $request)
    {
        // if ($this->checkPermissions($this->moduleName, 'view') == true) {
            try {
                $props = $this->getBaseQueryParams($request, []);
                $paket = $this->paketAksesorisServices->fetchAll($props);

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
                $paket = $this->paketAksesorisServices->fetchLimit($props);

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
                    'nama_paket'        => 'required|string|max:255|unique:paket_aksesoris',
                    'keterangan'        => 'nullable',
                    'minimal_lebar'     => 'required|numeric',
                    'maksimal_lebar'    => 'nullable|numeric',
                    'minimal_tinggi'    => 'required|numeric',
                    'maksimal_tinggi'   => 'nullable|numeric',
                    'jumlah_daun'       => 'nullable|numeric',
                ];
                $validator = $this->returnValidator($request->all(), $rules);
                if ($validator->fails()) {
                    return $this->returnResponse('error', self::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
                }

                $paket = $this->paketAksesorisServices->createPaket($request);
                return $this->returnResponse('success', self::HTTP_CREATED, 'Paket aksesoris berhasil dibuat', $paket);
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
                $paket = $this->paketAksesorisServices->fetchById($id);
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
                    'nama_paket'        => 'required|string|max:255|unique:paket_aksesoris,nama_paket,'.$id.'',
                    'keterangan'        => 'nullable',
                    'minimal_lebar'     => 'required|numeric',
                    'maksimal_lebar'    => 'nullable|numeric',
                    'minimal_tinggi'    => 'required|numeric',
                    'maksimal_tinggi'   => 'nullable|numeric',
                    'jumlah_daun'       => 'nullable|numeric',
                ];
                $validator = $this->returnValidator($request->all(), $rules);
                if ($validator->fails()) {
                    return $this->returnResponse('error', self::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
                }

                $paket = $this->paketAksesorisServices->updatePaket($request, $id);
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
                $paket = $this->paketAksesorisServices->destroyPaket($id);
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
                $paket = $this->paketAksesorisServices->destroyMultiplePaket($props);

                return $this->returnResponse('success', self::HTTP_OK, 'Catatan berhasil dihapus!', $paket);
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
            $dataResponse = $this->paketAksesorisServices->fetchExportData($props);
            $params = [
                'url'   => $broadcastUrl,
                'key'   => 'downloadPaketAksesoris',
                'data'  => $dataResponse['data'],
                'props' => $props
            ];
            $paket = new ExportSupplier($params);
            $this->dispatch($paket);

            return $this->returnResponse('success', self::HTTP_OK, 'Silakan tunggu, file yang anda minta sedang disiapkan');
        } catch (Exception $ex) {
            return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);

        }
    }

    public function fetchDataOptions(Request $request)
    {
        try {
            $props = $this->getBaseQueryParams($request, []);
            $paket = $this->paketAksesorisServices->fetchDataOptions($props);

            return $this->returnResponse('success', self::HTTP_OK, 'Daftar paket aksesoris', $paket);
        } catch (Exception $ex) {
            return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
        }
    }
}
