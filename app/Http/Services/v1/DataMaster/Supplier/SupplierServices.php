<?php
namespace App\Http\Services\v1\DataMaster\Supplier;

use Excel;
use Exception;
use App\Models\Supplier;
use App\Imports\BaseImport;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Services\v1\BaseServices;
use App\Http\Resources\v1\DataMaster\Supplier\SupplierResource;

class SupplierServices extends BaseServices
{
    /* PRIVATE VARIABLE */
    private $model;
    private $carbon;
    private $moduleName;
    private $oldValues;
    private $newValues;

    public function __construct()
    {
        $this->model = new Supplier;
        $this->carbon = $this->returnCarbon();
        $this->moduleName = 'Supplier';
    }

    /* FETCH ALL SUPPLIER */
    public function fetchAll($props){
        try {
            /* GET DATA WITH FILTER AS A MODEL */
            $datas = $this->dataFilterPagination($this->model, $props, null);

            /* RETRIEVE ALL ROW, CONVERT TO ARRAY AND FORMAT AS RESOURCE */
            $datas = $datas->get();
            $suppliers = SupplierResource::collection($datas);

            return $suppliers;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /* FETCH ALL SUPPLIER LIMIT */
    public function fetchLimit($props){
        try {
            /* GET DATA FOR PAGINATION AS A MODEL */
            $getAllData = $this->dataFilterPagination($this->model, [], null);
            $totalData = $getAllData->count();

            /* GET DATA WITH FILTER FOR PAGINATION AS A MODEL */
            $getFilterData = $this->dataFilterPagination($this->model, $props, null);
            $totalFiltered = $getFilterData->count();

            /* GET DATA WITH FILTER AS A MODEL */
            $datas = $this->dataFilter($this->model, $props, null);

            /* RETRIEVE ALL ROW, CONVERT TO ARRAY AND FORMAT AS RESOURCE */
            $datas = $datas->get();
            $datas = SupplierResource::collection($datas);
            $suppliers = [
                "total" => $totalData,
                "total_filter" => $totalFiltered,
                "per_page" => $props['take'],
                "current_page" => $props['skip'] == 0 ? 1 : ($props['skip'] + 1),
                "last_page" => ceil($totalFiltered / $props['take']),
                "from" => $totalFiltered === 0 ? 0 : ($props['skip'] != 0 ? ($props['skip'] * $props['take']) + 1 : 1),
                "to" => $totalFiltered === 0 ? 0 : ($props['skip'] * $props['take']) + $datas->count(),
                "show" => [
                    ["number" => 25, "name" => "25"], ["number" => 50, "name" => "50"], ["number" => 100, "name" => "100"]
                ],
                "data" => $datas
            ];

            return $suppliers;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /* FETCH SUPPLIER BY ID */
    public function fetchById($id){
        try {
            $supplier = $this->model::find($id);
            if ($supplier) {
                $supplier = SupplierResource::make($supplier);
                return $supplier;
            }

            throw new Exception('Catatan tidak ditemukan!');
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /* CREATE NEW SUPPLIER */
    public function createSupplier($props){
        try {
            $supplier = $this->model;
            $supplier->nama_supplier    = strtoupper($props['nama_supplier']);
            $supplier->alamat           = strtoupper($props['alamat']);
            $supplier->kota             = strtoupper($props['kota']);
            $supplier->kode_pos         = strtoupper($props['kode_pos']);
            $supplier->created_id       = $this->returnAuthUser()->id;
            $supplier->save();

            /* WRITE LOG */
            $this->newValues = $this->model::find($supplier->id);
            $logParameters = [
                'status'        => 'success',
                'module'        => $this->moduleName,
                'event'         => 'created',
                'description'   => 'Membuat supplier baru [ '.$supplier->id.' - '.$supplier->nama_supplier.' ]',
                'user_id'       => $this->returnAuthUser()->id ?? null,
                'old_values'    => $this->oldValues,
                'new_values'    => $this->newValues
            ];
            $this->writeActivityLog($logParameters);

            $supplier = SupplierResource::make($supplier);
            return $supplier;
        } catch (Exception $ex) {
            /* WRITE LOG */
            $logParameters = [
                'status'        => 'error',
                'module'        => $this->moduleName,
                'event'         => 'created',
                'description'   => 'Gagal membuat supplier [ '.$ex->getMessage().' ]',
                'user_id'       => $this->returnAuthUser()->id ?? null,
                'old_values'    => $this->oldValues,
                'new_values'    => $this->newValues
            ];
            $this->writeActivityLog($logParameters);

            throw $ex;
        }
    }

    /* UPDATE SUPPLIER */
    public function updateSupplier($props, $id){
        try {
            $this->oldValues = $this->model::find($id);
            $supplier = $this->model::find($id);
            if ($supplier) {
                $supplier->nama_supplier    = strtoupper($props['nama_supplier']);
                $supplier->alamat           = strtoupper($props['alamat']);
                $supplier->kota             = strtoupper($props['kota']);
                $supplier->kode_pos         = strtoupper($props['kode_pos']);
                $supplier->updated_id       = $this->returnAuthUser()->id;
                $supplier->update();

                /* WRITE LOG */
                $this->newValues = $this->model::find($supplier->id);
                $logParameters = [
                    'status'        => 'success',
                    'module'        => $this->moduleName,
                    'event'         => 'updated',
                    'description'   => 'Memperbaharui supplier [ '.$supplier->id.' - '.$supplier->nama_supplier.' ]',
                    'user_id'       => $this->returnAuthUser()->id ?? null,
                    'old_values'    => $this->oldValues,
                    'new_values'    => $this->newValues
                ];
                $this->writeActivityLog($logParameters);

                $supplier = SupplierResource::make($supplier);
                return $supplier;
            } else {
                throw new Exception('Catatan tidak ditemukan!');
            }
        } catch (Exception $ex) {
            /* WRITE LOG */
            $logParameters = [
                'status'        => 'error',
                'module'        => $this->moduleName,
                'event'         => 'updated',
                'description'   => 'Gagal memperbaharui supplier [ Pesan: '.$ex->getMessage().' ]',
                'user_id'       => $this->returnAuthUser()->id ?? null,
                'old_values'    => $this->oldValues,
                'new_values'    => $this->newValues
            ];
            $this->writeActivityLog($logParameters);

            throw $ex;
        }
    }

    /* DESTROY SUPPLIER */
    public function destroySupplier($id){
        try {
            $this->oldValues = $this->model::find($id);
            $supplier = $this->model::find($id);

            if ($supplier) {
                $supplier->delete();

                /* WRITE LOG */
                $logParameters = [
                    'status'        => 'success',
                    'module'        => $this->moduleName,
                    'event'         => 'deleted',
                    'description'   => 'Menghapus supplier [ '.$this->oldValues->id.' - '. $this->oldValues->nama_supplier.' ]',
                    'user_id'       => $this->returnAuthUser()->id ?? null,
                    'old_values'    => $this->oldValues,
                    'new_values'    => $this->newValues
                ];
                $this->writeActivityLog($logParameters);

                return null;
            }

            throw new Exception('Catatan tidak ditemukan!');
        } catch (Exception $ex) {
            /* WRITE LOG */
            $logParameters = [
                'status'        => 'error',
                'module'        => $this->moduleName,
                'event'         => 'deleted',
                'description'   => 'Gagal menghapus supplier [ Pesan: '.$ex->getMessage().' ]',
                'user_id'       => $this->returnAuthUser()->id ?? null,
                'old_values'    => $this->oldValues,
                'new_values'    => $this->newValues
            ];
            $this->writeActivityLog($logParameters);

            throw $ex;
        }
    }

    /* DESTROY SELECTED / MULTIPLE SUPPLIER */
    public function destroyMultipleSupplier($props){
        try {
            $this->oldValues = $this->model::whereIn('id', $props)->get();
            $suppliers = $this->model::whereIn('id', $props);

            if ($suppliers->count() > 0) {
                $suppliers->delete();

                /* WRITE LOG */
                $logParameters = [
                    'status'        => 'success',
                    'module'        => $this->moduleName,
                    'event'         => 'deleted',
                    'description'   => 'Menghapus user [ '.$this->oldValues.' ]',
                    'user_id'       => $this->returnAuthUser()->id ?? null,
                    'old_values'    => $this->oldValues,
                    'new_values'    => $this->newValues
                ];
                $this->writeActivityLog($logParameters);

                return null;
            }

            throw new Exception('Catatan tidak ditemukan!');
        } catch (Exception $ex) {
            /* WRITE LOG */
            $logParameters = [
                'status'        => 'error',
                'module'        => $this->moduleName,
                'event'         => 'deleted',
                'description'   => 'Gagal menghapus user [ Pesan: '.$ex->getMessage().' ]',
                'user_id'       => $this->returnAuthUser()->id ?? null,
                'old_values'    => $this->oldValues,
                'new_values'    => $this->newValues
            ];
            $this->writeActivityLog($logParameters);

            throw $ex;
        }
    }

    /* CHECK IS SUPPLIER ALREADY EXIST ON DATABASE*/
    public function checkExistSupplier($name){
        $data = $this->model::where('nama_supplier', '=', $name)->exists();
        if ($data) {
            return $data;
        }
        return null;
    }

    /* IMPORT SUPPLIER FROM EXCEL FILE */
    public function import($props){
        /* BEGIN DB TRANSACTION */
        DB::beginTransaction();

        /* PREPARE TO LOAD EXCEL DATA AS ARRAY */
        $import = new BaseImport;
        Excel::import($import, $props->file('file'));
        $rows = $import->getArray();

        /* DECLARE REQUIRE VARIABLES */
        $data = [];
        $exist = null;
        $incomplete = 0;
        $incompletes = [];
        $error = false;
        $errors = [];
        $message = null;

        /* RUNNING QUERY COMMAND */
        try {
            foreach ($rows as $row) {
                if (array_key_exists('nama_supplier', $row) && array_key_exists('alamat', $row) && array_key_exists('kode_pos', $row)
                    && array_key_exists('kota', $row)) {

                    /* CHECK EXIST SUPPLIER */
                    $exist = $this->checkExistSupplier($row['nama_supplier']);

                    if ($exist) {
                        $error = true;
                        $errors[] = [
                            'nama_supplier' => strtoupper($row['nama_supplier']),
                            'alamat'        => strtoupper($row['alamat']),
                            'kode_pos'      => strtoupper($row['kode_pos']),
                            'kota'          => strtoupper($row['kota'])
                        ];
                    } else {
                        if (!$row['nama_supplier']) {
                            $error = true;
                            $incomplete++;

                            $incompletes[] = [
                                'nama_supplier' => strtoupper($row['nama_supplier']),
                                'alamat'        => strtoupper($row['alamat']),
                                'kode_pos'      => strtoupper($row['kode_pos']),
                                'kota'          => strtoupper($row['kota'])
                            ];
                        } else {
                            $data[] = [
                                'nama_supplier' => strtoupper($row['nama_supplier']),
                                'alamat'        => strtoupper($row['alamat']) ?? null,
                                'kode_pos'      => strtoupper($row['kode_pos']) ?? null,
                                'kota'          => strtoupper($row['kota']) ?? null
                            ];
                        }
                    }
                } else {
                    /* RETURN RESPONSE */
                    throw new Exception('Konsep excel tidak valid! Silakan periksa file excel Anda.');
                }
            }

            if ($error) {
                if ($incomplete > 0) {
                    $message = "Beberapa baris data perlu dilengkapi!";
                    $errors = $incompletes;
                } elseif ($exist) {
                    $message = "Beberapa data supplier telah terdaftar di sistem!";
                } else {
                    $message = "Terjadi kesalahan tak terduga!";
                }

                $returnResponse = [
                    'status'        => 'error',
                    'status_code'   => Response::HTTP_BAD_REQUEST,
                    'message'       => $message,
                    'data'          => $errors
                ];
                return $returnResponse;
            } else {
                /* REMOVE DUPLICATE DATA */
                $data = array_unique($data, SORT_REGULAR);
                $props = [];
                foreach ($data as $item) {
                    $props[] = [
                        'nama_supplier' => strtoupper($item['nama_supplier']),
                        'alamat'        => strtoupper($item['alamat']) ?? null,
                        'kode_pos'      => strtoupper($item['kode_pos']) ?? null,
                        'kota'          => strtoupper($item['kota']) ?? null,
                        'created_id'    => $this->returnAuthUser()->id,
                        'created_at'    => $this->carbon::now(),
                        'updated_at'    => $this->carbon::now()
                    ];
                }

                /* MASS INSERT INTO TABLE */
                $this->model::insert($props);

                /* WRITE LOG */
                $this->newValues = $props;
                $logParameters = [
                    'status'        => 'success',
                    'module'        => $this->moduleName,
                    'event'         => 'created',
                    'description'   => 'Impor data supplier',
                    'user_id'       => $this->returnAuthUser()->id ?? null,
                    'old_values'    => $this->oldValues,
                    'new_values'    => $this->newValues
                ];
                $this->writeActivityLog($logParameters);

                /* COMMIT TRANSACTION */
                DB::commit();

                /* RETURN RESPONSE */
                $returnResponse = [
                    'status'        => 'success',
                    'status_code'   => Response::HTTP_OK,
                    'message'       => 'Impor data supplier berhasil',
                    'data'          => null
                ];
                return $returnResponse;
            }
        } catch (Exception $ex) {
            /* WRITE LOG */
            $logParameters = [
                'status'        => 'error',
                'module'        => $this->moduleName,
                'event'         => 'created',
                'description'   => 'Gagal impor data supplier [ '.$ex->getMessage().' ]',
                'user_id'       => $this->returnAuthUser()->id ?? null,
                'old_values'    => $this->oldValues,
                'new_values'    => $this->newValues
            ];
            $this->writeActivityLog($logParameters);

            /* ROLLBACK TRANSACTION */
            DB::rollback();

            /* RETURN RESPONSE */
            throw $ex;
        }
    }

    /* FETCH EXPORT DATA */
    public function fetchExportData($props){
        try {
            /* GET DATA WITH FILTER AS A MODEL */
            $datas = $this->dataFilterPagination($this->model, $props, null);

            /* RETRIEVE ALL ROW, CONVERT TO ARRAY AND FORMAT AS RESOURCE */
            $datas = $datas->get();
            $suppliers = SupplierResource::collection($datas);

            return $suppliers;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /* FETCH ALL SUPPLIER FOR OPTIONS */
    public function fetchDataOptions($props){
        try {
            /* GET DATA WITH FILTER AS A MODEL */
            $datas = $this->dataFilterPagination($this->model, $props, null);

            /* RETRIEVE ALL ROW, CONVERT TO ARRAY AND FORMAT AS RESOURCE */
            $suppliers = $datas->select('id', 'nama_supplier', 'alamat')->get();

            return $suppliers;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /* FETCH SUPPLIER BY NAME */
    /* INTERNAL USED (USED BY ANOTHER SERVICES) */
    public function fetchByName($name){
        $data = $this->model::where('nama_supplier', '=', $name)->first();
        if ($data) {
            return $data;
        }
        return null;
    }
}
