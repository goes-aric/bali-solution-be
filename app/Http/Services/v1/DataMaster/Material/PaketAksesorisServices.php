<?php
namespace App\Http\Services\v1\DataMaster\Material;

use Exception;
use App\Http\Services\v1\BaseServices;
use App\Http\Resources\v1\DataMaster\Material\PaketAksesorisResource;
use App\Models\PaketAksesoris;

class PaketAksesorisServices extends BaseServices
{
    /* PRIVATE VARIABLE */
    private $model;
    private $moduleName;
    private $oldValues;
    private $newValues;

    public function __construct()
    {
        $this->model = new PaketAksesoris();
        $this->moduleName = 'Paket Aksesoris';
    }

    /* FETCH ALL PAKET AKSESORIS */
    public function fetchAll($props){
        try {
            /* GET DATA WITH FILTER AS A MODEL */
            $datas = $this->dataFilterPagination($this->model, $props, null);

            /* RETRIEVE ALL ROW, CONVERT TO ARRAY AND FORMAT AS RESOURCE */
            $datas = $datas->get();
            $paket = PaketAksesorisResource::collection($datas);

            return $paket;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /* FETCH ALL PAKET AKSESORIS LIMIT */
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
            $datas = PaketAksesorisResource::collection($datas);
            $paket = [
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

            return $paket;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /* FETCH PAKET AKSESORIS BY ID */
    public function fetchById($id){
        try {
            $paket = $this->model::find($id);
            if ($paket) {
                $paket = PaketAksesorisResource::make($paket);
                return $paket;
            }

            throw new Exception('Catatan tidak ditemukan!');
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /* CREATE NEW PAKET AKSESORIS */
    public function createPaket($props){
        try {
            $paket = $this->model;
            $paket->nama_paket      = strtoupper($props['nama_paket']);
            $paket->keterangan      = strtoupper($props['keterangan']);
            $paket->minimal_lebar   = strtoupper($props['minimal_lebar']);
            $paket->maksimal_lebar  = strtoupper($props['maksimal_lebar']);
            $paket->minimal_tinggi  = strtoupper($props['minimal_tinggi']);
            $paket->maksimal_tinggi = strtoupper($props['maksimal_tinggi']);
            $paket->jumlah_daun     = strtoupper($props['jumlah_daun']);
            $paket->created_id      = $this->returnAuthUser()->id;
            $paket->save();

            /* WRITE LOG */
            $this->newValues = $this->model::find($paket->id);
            $logParameters = [
                'status'        => 'success',
                'module'        => $this->moduleName,
                'event'         => 'created',
                'description'   => 'Membuat paket aksesoris baru [ '.$paket->id.' - '.$paket->nama_paket.' ]',
                'user_id'       => $this->returnAuthUser()->id ?? null,
                'old_values'    => $this->oldValues,
                'new_values'    => $this->newValues
            ];
            $this->writeActivityLog($logParameters);

            $paket = PaketAksesorisResource::make($paket);
            return $paket;
        } catch (Exception $ex) {
            /* WRITE LOG */
            $logParameters = [
                'status'        => 'error',
                'module'        => $this->moduleName,
                'event'         => 'created',
                'description'   => 'Gagal membuat paket aksesoris [ '.$ex->getMessage().' ]',
                'user_id'       => $this->returnAuthUser()->id ?? null,
                'old_values'    => $this->oldValues,
                'new_values'    => $this->newValues
            ];
            $this->writeActivityLog($logParameters);

            throw $ex;
        }
    }

    /* UPDATE PAKET PAKET AKSESORIS */
    public function updatePaket($props, $id){
        try {
            $this->oldValues = $this->model::find($id);
            $paket = $this->model::find($id);
            if ($paket) {
                $paket->nama_paket      = strtoupper($props['nama_paket']);
                $paket->keterangan      = strtoupper($props['keterangan']);
                $paket->minimal_lebar   = strtoupper($props['minimal_lebar']);
                $paket->maksimal_lebar  = strtoupper($props['maksimal_lebar']);
                $paket->minimal_tinggi  = strtoupper($props['minimal_tinggi']);
                $paket->maksimal_tinggi = strtoupper($props['maksimal_tinggi']);
                $paket->jumlah_daun     = strtoupper($props['jumlah_daun']);
                $paket->updated_id      = $this->returnAuthUser()->id;
                $paket->update();

                /* WRITE LOG */
                $this->newValues = $this->model::find($paket->id);
                $logParameters = [
                    'status'        => 'success',
                    'module'        => $this->moduleName,
                    'event'         => 'updated',
                    'description'   => 'Memperbaharui paket aksesoris [ '.$paket->id.' - '.$paket->nama_paket.' ]',
                    'user_id'       => $this->returnAuthUser()->id ?? null,
                    'old_values'    => $this->oldValues,
                    'new_values'    => $this->newValues
                ];
                $this->writeActivityLog($logParameters);

                $paket = PaketAksesorisResource::make($paket);
                return $paket;
            } else {
                throw new Exception('Catatan tidak ditemukan!');
            }
        } catch (Exception $ex) {
            /* WRITE LOG */
            $logParameters = [
                'status'        => 'error',
                'module'        => $this->moduleName,
                'event'         => 'updated',
                'description'   => 'Gagal memperbaharui paket aksesoris [ Pesan: '.$ex->getMessage().' ]',
                'user_id'       => $this->returnAuthUser()->id ?? null,
                'old_values'    => $this->oldValues,
                'new_values'    => $this->newValues
            ];
            $this->writeActivityLog($logParameters);

            throw $ex;
        }
    }

    /* DESTROY PAKET AKSESORIS */
    public function destroyPaket($id){
        try {
            $this->oldValues = $this->model::find($id);
            $paket = $this->model::find($id);

            if ($paket) {
                $paket->delete();

                /* WRITE LOG */
                $logParameters = [
                    'status'        => 'success',
                    'module'        => $this->moduleName,
                    'event'         => 'deleted',
                    'description'   => 'Menghapus paket aksesoris [ '.$this->oldValues->id.' - '. $this->oldValues->nama_paket.' ]',
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
                'description'   => 'Gagal menghapus paket aksesoris [ Pesan: '.$ex->getMessage().' ]',
                'user_id'       => $this->returnAuthUser()->id ?? null,
                'old_values'    => $this->oldValues,
                'new_values'    => $this->newValues
            ];
            $this->writeActivityLog($logParameters);

            throw $ex;
        }
    }

    /* DESTROY SELECTED / MULTIPLE PAKET AKSESORIS */
    public function destroyMultiplePaket($props){
        try {
            $this->oldValues = $this->model::whereIn('id', $props)->get();
            $paket = $this->model::whereIn('id', $props);

            if ($paket->count() > 0) {
                $paket->delete();

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

    /* FETCH EXPORT DATA */
    public function fetchExportData($props){
        try {
            /* GET DATA WITH FILTER AS A MODEL */
            $datas = $this->dataFilterPagination($this->model, $props, null);

            /* RETRIEVE ALL ROW, CONVERT TO ARRAY AND FORMAT AS RESOURCE */
            $datas = $datas->get();
            $paket = PaketAksesorisResource::collection($datas);

            return $paket;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /* FETCH ALL PAKET AKSESORIS FOR OPTIONS */
    public function fetchDataOptions($props){
        try {
            /* GET DATA WITH FILTER AS A MODEL */
            $datas = $this->dataFilterPagination($this->model, $props, null);

            /* RETRIEVE ALL ROW, CONVERT TO ARRAY AND FORMAT AS RESOURCE */
            $paket = $datas->select('id', 'nama_paket', 'alamat')->get();

            return $paket;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
}
