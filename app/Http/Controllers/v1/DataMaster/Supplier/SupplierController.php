<?php
namespace App\Http\Controllers\v1\DataMaster\Supplier;

use Illuminate\Http\Request;
use App\Jobs\Supplier\ExportSupplier;
use App\Jobs\Supplier\ExportDraftSupplier;
use App\Http\Controllers\v1\BaseController;
use App\Http\Services\v1\DataMaster\Supplier\SupplierServices;
use Exception;

class SupplierController extends BaseController
{
    private $supplierServices;
    private $moduleName;

    public function __construct(SupplierServices $supplierServices)
    {
        $this->supplierServices = $supplierServices;
        $this->moduleName = 'Supplier';
    }

    public function list(Request $request)
    {
        // if ($this->checkPermissions($this->moduleName, 'view') == true) {
            try {
                $props = $this->getBaseQueryParams($request, []);
                $suppliers = $this->supplierServices->fetchAll($props);

                return $this->returnResponse('success', self::HTTP_OK, 'Daftar supplier', $suppliers);
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
                $suppliers = $this->supplierServices->fetchLimit($props);

                return $this->returnResponse('success', self::HTTP_OK, 'Daftar supplier', $suppliers);
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
                    'nama_supplier' => 'required|string|max:255|unique:supplier',
                    'alamat'        => 'nullable',
                    'kota'          => 'nullable',
                    'kode_pos'      => 'nullable',
                ];
                $validator = $this->returnValidator($request->all(), $rules);
                if ($validator->fails()) {
                    return $this->returnResponse('error', self::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
                }

                $supplier = $this->supplierServices->createSupplier($request);
                return $this->returnResponse('success', self::HTTP_CREATED, 'Supplier berhasil dibuat', $supplier);
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
                $supplier = $this->supplierServices->fetchById($id);
                return $this->returnResponse('success', self::HTTP_OK, 'Detail supplier', $supplier);
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
                    'nama_supplier' => 'required|string|max:255|unique:supplier,nama_supplier,'.$id.'',
                    'alamat'        => 'nullable',
                    'kota'          => 'nullable',
                    'kode_pos'      => 'nullable',
                ];
                $validator = $this->returnValidator($request->all(), $rules);
                if ($validator->fails()) {
                    return $this->returnResponse('error', self::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
                }

                $supplier = $this->supplierServices->updateSupplier($request, $id);
                return $this->returnResponse('success', self::HTTP_OK, 'Supplier berhasil diperbaharui', $supplier);
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
                $supplier = $this->supplierServices->destroySupplier($id);
                return $this->returnResponse('success', self::HTTP_OK, 'Catatan berhasil dihapus!', $supplier);
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
                $suppliers = $this->supplierServices->destroyMultipleSupplier($props);

                return $this->returnResponse('success', self::HTTP_OK, 'Catatan berhasil dihapus!', $suppliers);
            } catch (Exception $ex) {
                return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
            }
        // }

        // return $this->returnNoPermissionResponse();
    }

    public function import(Request $request)
    {
        // if ($this->checkPermissions($this->moduleName, 'create') == true) {
            try {
                $rules = [
                    'file'  => 'required',
                ];
                $validator = $this->returnValidator($request->all(), $rules);
                if ($validator->fails()) {
                    return $this->returnResponse('error', self::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
                }

                $suppliers = $this->supplierServices->import($request);
                return $this->returnResponse('success', self::HTTP_OK, 'Berhasil', $suppliers);
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
            $dataResponse = $this->supplierServices->fetchExportData($props);
            $params = [
                'url'   => $broadcastUrl,
                'key'   => 'downloadSupplier',
                'data'  => $dataResponse['data'],
                'props' => $props
            ];
            $suppliers = new ExportSupplier($params);
            $this->dispatch($suppliers);

            return $this->returnResponse('success', self::HTTP_OK, 'Silakan tunggu, file yang anda minta sedang disiapkan');
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
                'key'   => 'downloadDraftSupplier',
                'data'  => null,
                'props' => null
            ];
            $draft = new ExportDraftSupplier($params);
            $this->dispatch($draft);

            return $this->returnResponse('success', self::HTTP_OK, 'Silakan tunggu, file yang anda minta sedang disiapkan');
        } catch (Exception $ex) {
            return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);

        }
    }

    public function fetchDataOptions(Request $request)
    {
        try {
            $props = $this->getBaseQueryParams($request, []);
            $suppliers = $this->supplierServices->fetchDataOptions($props);

            return $this->returnResponse('success', self::HTTP_OK, 'Daftar supplier', $suppliers);
        } catch (Exception $ex) {
            return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
        }
    }
}
