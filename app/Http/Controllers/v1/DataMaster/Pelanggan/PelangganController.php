<?php
namespace App\Http\Controllers\v1\DataMaster\Pelanggan;

use Exception;
use Illuminate\Http\Request;
use App\Jobs\Pelanggan\ExportPelanggan;
use App\Http\Controllers\v1\BaseController;
use App\Http\Services\v1\DataMaster\Pelanggan\PelangganServices;
use App\Jobs\Pelanggan\ExportDraftPelanggan;

class PelangganController extends BaseController
{
    private $pelangganServices;
    private $moduleName;

    public function __construct(PelangganServices $pelangganServices)
    {
        $this->pelangganServices = $pelangganServices;
        $this->moduleName = 'Pelanggan';
    }

    public function list(Request $request)
    {
        // if ($this->checkPermissions($this->moduleName, 'view') == true) {
            try {
                $props = $this->getBaseQueryParams($request, []);
                $props += [
                    'status' => $request['status']
                ];
                $pelanggan = $this->pelangganServices->fetchAll($props);

                return $this->returnResponse('success', self::HTTP_OK, 'Daftar pelanggan', $pelanggan);
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
                    'status' => $request['status']
                ];
                $pelanggan = $this->pelangganServices->fetchLimit($props);

                return $this->returnResponse('success', self::HTTP_OK, 'Daftar pelanggan', $pelanggan);
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
                    'nama_pelanggan'    => 'required|string|max:255',
                    'alamat'            => 'nullable',
                    'no_telp'           => 'nullable',
                    'email'             => 'nullable|email',
                    'status'            => 'required',
                ];
                $validator = $this->returnValidator($request->all(), $rules);
                if ($validator->fails()) {
                    return $this->returnResponse('error', self::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
                }

                $pelanggan = $this->pelangganServices->createPelanggan($request);
                return $this->returnResponse('success', self::HTTP_OK, 'Pelanggan berhasil dibuat', $pelanggan);
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
                $pelanggan = $this->pelangganServices->fetchById($id);
                return $this->returnResponse('success', self::HTTP_OK, 'Detail pelanggan', $pelanggan);
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
                    'nama_pelanggan'    => 'required|string|max:255',
                    'alamat'            => 'nullable',
                    'no_telp'           => 'nullable',
                    'email'             => 'nullable|email',
                    'status'            => 'required',
                ];
                $validator = $this->returnValidator($request->all(), $rules);
                if ($validator->fails()) {
                    return $this->returnResponse('error', self::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
                }

                $pelanggan = $this->pelangganServices->updatePelanggan($request, $id);
                return $this->returnResponse('success', self::HTTP_OK, 'Pelanggan berhasil diperbaharui', $pelanggan);
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
                $pelanggan = $this->pelangganServices->destroyPelanggan($id);
                return $this->returnResponse('success', self::HTTP_OK, 'Catatan berhasil dihapus!', $pelanggan);
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
                $pelanggan = $this->pelangganServices->destroyMultiplePelanggan($props);

                return $this->returnResponse('success', self::HTTP_OK, 'Catatan berhasil dihapus!', $pelanggan);
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
            $dataResponse = $this->pelangganServices->fetchExportData($props);
            $params = [
                'url'   => $broadcastUrl,
                'key'   => 'downloadPelanggan',
                'data'  => $dataResponse['data'],
                'props' => $props
            ];
            $pelanggan = new ExportPelanggan($params);
            $this->dispatch($pelanggan);

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
                'key'   => 'downloadDraftPelanggan',
                'data'  => null,
                'props' => null
            ];
            $draft = new ExportDraftPelanggan($params);
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
            $pelanggan = $this->pelangganServices->fetchDataOptions($props);

            return $this->returnResponse('success', self::HTTP_OK, 'Daftar pelanggan', $pelanggan);
        } catch (Exception $ex) {
            return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
        }
    }
}
