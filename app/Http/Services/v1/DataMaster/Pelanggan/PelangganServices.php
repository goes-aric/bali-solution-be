<?php
namespace App\Http\Services\v1\DataMaster\Pelanggan;

use Exception;
use App\Models\Pelanggan;
use App\Http\Services\v1\BaseServices;
use App\Http\Resources\v1\DataMaster\Pelanggan\PelangganResource;

class PelangganServices extends BaseServices
{
    /* PRIVATE VARIABLE */
    private $model;
    private $moduleName;
    private $oldValues;
    private $newValues;

    public function __construct()
    {
        $this->model = new Pelanggan;
        $this->moduleName = 'Pelanggan';
    }

    /* FETCH ALL PELANGGAN */
    public function fetchAll($props){
        try {
            /* GET DATA WITH FILTER AS A MODEL */
            $datas = $this->dataFilterPagination($this->model, $props, null);

            /* ADD FILTER BY STATUS IF AVAILABLE */
            if (isset($props['status'])) {
                $datas->where('status', '=', $props['status']);
            }

            /* RETRIEVE ALL ROW, CONVERT TO ARRAY AND FORMAT AS RESOURCE */
            $datas = $datas->get();
            $pelanggan = PelangganResource::collection($datas);

            return $pelanggan;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /* FETCH ALL PELANGGAN LIMIT */
    public function fetchLimit($props){
        try {
            /* GET DATA FOR PAGINATION AS A MODEL */
            $getAllData = $this->dataFilterPagination($this->model, [], null);

            /* ADD FILTER BY STATUS IF AVAILABLE */
            if (isset($props['status'])) {
                $getAllData->where('status', '=', $props['status']);
            }
            $totalData = $getAllData->count();

            /* GET DATA WITH FILTER FOR PAGINATION AS A MODEL */
            $getFilterData = $this->dataFilterPagination($this->model, $props, null);

            /* ADD FILTER BY STATUS IF AVAILABLE */
            if (isset($props['status'])) {
                $getFilterData->where('status', '=', $props['status']);
            }
            $totalFiltered = $getFilterData->count();

            /* GET DATA WITH FILTER AS A MODEL */
            $datas = $this->dataFilter($this->model, $props, null);

            /* ADD FILTER BY STATUS IF AVAILABLE */
            if (isset($props['status'])) {
                $datas->where('status', '=', $props['status']);
            }

            /* RETRIEVE ALL ROW, CONVERT TO ARRAY AND FORMAT AS RESOURCE */
            $datas = $datas->get();
            $datas = PelangganResource::collection($datas);
            $pelanggan = [
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

            return $pelanggan;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /* FETCH PELANGGAN BY ID */
    public function fetchById($id){
        try {
            $pelanggan = $this->model::find($id);
            if ($pelanggan) {
                $pelanggan = PelangganResource::make($pelanggan);
                return $pelanggan;
            }

            throw new Exception('Catatan tidak ditemukan!');
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /* CREATE NEW PELANGGAN */
    public function createPelanggan($props){
        try {
            $pelanggan = $this->model;
            $pelanggan->nama_pelanggan  = strtoupper($props['nama_pelanggan']);
            $pelanggan->alamat          = strtoupper($props['alamat']);
            $pelanggan->no_telp         = strtoupper($props['no_telp']);
            $pelanggan->email           = strtoupper($props['email']);
            $pelanggan->status          = strtoupper($props['status']);
            $pelanggan->created_id      = $this->returnAuthUser()->id;
            $pelanggan->save();

            /* WRITE LOG */
            $this->newValues = $this->model::find($pelanggan->id);
            $logParameters = [
                'status'        => 'success',
                'module'        => $this->moduleName,
                'event'         => 'created',
                'description'   => 'Membuat pelanggan baru [ '.$pelanggan->id.' - '.$pelanggan->nama_pelanggan.' ]',
                'user_id'       => $this->returnAuthUser()->id ?? null,
                'old_values'    => $this->oldValues,
                'new_values'    => $this->newValues
            ];
            $this->writeActivityLog($logParameters);

            $pelanggan = PelangganResource::make($pelanggan);
            return $pelanggan;
        } catch (Exception $ex) {
            /* WRITE LOG */
            $logParameters = [
                'status'        => 'error',
                'module'        => $this->moduleName,
                'event'         => 'created',
                'description'   => 'Gagal membuat pelanggan [ Pesan: '.$ex->getMessage().' ]',
                'user_id'       => $this->returnAuthUser()->id ?? null,
                'old_values'    => $this->oldValues,
                'new_values'    => $this->newValues
            ];
            $this->writeActivityLog($logParameters);

            throw $ex;
        }
    }

    /* UPDATE PELANGGAN */
    public function updatePelanggan($props, $id){
        try {
            $this->oldValues = $this->model::find($id);
            $pelanggan = $this->model::find($id);
            if ($pelanggan) {
                $pelanggan->nama_pelanggan  = strtoupper($props['nama_pelanggan']);
                $pelanggan->alamat          = strtoupper($props['alamat']);
                $pelanggan->no_telp         = strtoupper($props['no_telp']);
                $pelanggan->email           = strtoupper($props['email']);
                $pelanggan->status          = strtoupper($props['status']);
                $pelanggan->updated_id      = $this->returnAuthUser()->id;
                $pelanggan->update();

                /* WRITE LOG */
                $this->newValues = $this->model::find($pelanggan->id);
                $logParameters = [
                    'status'        => 'success',
                    'module'        => $this->moduleName,
                    'event'         => 'updated',
                    'description'   => 'Memperbaharui pelanggan [ '.$pelanggan->id.' - '.$pelanggan->nama_pelanggan.' ]',
                    'user_id'       => $this->returnAuthUser()->id ?? null,
                    'old_values'    => $this->oldValues,
                    'new_values'    => $this->newValues
                ];
                $this->writeActivityLog($logParameters);

                $pelanggan = PelangganResource::make($pelanggan);
                return $pelanggan;
            } else {
                throw new Exception('Catatan tidak ditemukan!');
            }
        } catch (Exception $ex) {
            /* WRITE LOG */
            $logParameters = [
                'status'        => 'error',
                'module'        => $this->moduleName,
                'event'         => 'updated',
                'description'   => 'Gagal memperbaharui pelanggan [ Pesan: '.$ex->getMessage().' ]',
                'user_id'       => $this->returnAuthUser()->id ?? null,
                'old_values'    => $this->oldValues,
                'new_values'    => $this->newValues
            ];
            $this->writeActivityLog($logParameters);

            throw $ex;
        }
    }

    /* DESTROY PELANGGAN */
    public function destroyPelanggan($id){
        try {
            $this->oldValues = $this->model::find($id);
            $pelanggan = $this->model::find($id);

            if ($pelanggan) {
                $pelanggan->delete();

                /* WRITE LOG */
                $logParameters = [
                    'status'        => 'success',
                    'module'        => $this->moduleName,
                    'event'         => 'deleted',
                    'description'   => 'Menghapus pelanggan [ '.$this->oldValues->id.' - '. $this->oldValues->nama_pelanggan.' ]',
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
                'description'   => 'Gagal menghapus pelanggan [ Pesan: '.$ex->getMessage().' ]',
                'user_id'       => $this->returnAuthUser()->id ?? null,
                'old_values'    => $this->oldValues,
                'new_values'    => $this->newValues
            ];
            $this->writeActivityLog($logParameters);

            throw $ex;
        }
    }

    /* DESTROY SELECTED / MULTIPLE PELANGGAN */
    public function destroyMultiplePelanggan($props){
        try {
            $this->oldValues = $this->model::whereIn('id', $props)->get();
            $pelanggan = $this->model::whereIn('id', $props);

            if ($pelanggan->count() > 0) {
                $pelanggan->delete();

                /* WRITE LOG */
                $logParameters = [
                    'status'        => 'success',
                    'module'        => $this->moduleName,
                    'event'         => 'deleted',
                    'description'   => 'Menghapus pelanggan [ '.$this->oldValues.' ]',
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
                'description'   => 'Gagal menghapus pelanggan [ Pesan: '.$ex->getMessage().' ]',
                'user_id'       => $this->returnAuthUser()->id ?? null,
                'old_values'    => $this->oldValues,
                'new_values'    => $this->newValues
            ];
            $this->writeActivityLog($logParameters);

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
            $pelanggan = PelangganResource::collection($datas);

            return $pelanggan;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /* FETCH ALL PELANGGAN FOR OPTIONS */
    public function fetchDataOptions($props){
        try {
            /* GET DATA WITH FILTER AS A MODEL */
            $datas = $this->dataFilterPagination($this->model, $props, null);

            /* RETRIEVE ALL ROW, CONVERT TO ARRAY AND FORMAT AS RESOURCE */
            $pelanggan = $datas->select('id', 'nama_pelanggan', 'alamat')->get();

            return $pelanggan;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /* FETCH PELANGGAN BY NAME */
    /* INTERNAL USED (USED BY ANOTHER SERVICES) */
    public function fetchByName($name){
        $data = $this->model::where('nama_pelanggan', '=', $name)->first();
        if ($data) {
            return $data;
        }
        return null;
    }
}
