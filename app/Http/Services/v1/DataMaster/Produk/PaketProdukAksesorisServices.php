<?php
namespace App\Http\Services\v1\DataMaster\Produk;

use Exception;
use App\Models\PaketProdukAksesoris;
use App\Http\Services\v1\BaseServices;
use App\Http\Resources\v1\DataMaster\Material\PaketAksesorisResource;

class PaketProdukAksesorisServices extends BaseServices
{
    /* PRIVATE VARIABLE */
    private $model;
    private $moduleName;
    private $oldValues;
    private $newValues;

    public function __construct()
    {
        $this->model = new PaketProdukAksesoris();
        $this->moduleName = 'Paket Aksesoris';
    }

    /* FETCH ALL PAKET AKSESORIS */
    public function fetchAll($props){
        try {
            /* GET DATA WITH FILTER AS A MODEL */
            $datas = $this->dataFilterPagination($this->model, $props, null);

            /* ADD FILTER BY PAKET PRODUK IF AVAILABLE */
            if (isset($props['paket_produk_id'])) {
                $datas->where('paket_produk_id', '=', $props['paket_produk_id']);
            }

            /* RETRIEVE ALL ROW, CONVERT TO ARRAY AND FORMAT AS RESOURCE */
            $datas = $datas->get();
            $material = PaketAksesorisResource::collection($datas);

            return $material;
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

            /* ADD FILTER BY PAKET PRODUK IF AVAILABLE */
            if (isset($props['paket_produk_id'])) {
                $getFilterData->where('paket_produk_id', '=', $props['paket_produk_id']);
            }
            $totalFiltered = $getFilterData->count();

            /* GET DATA WITH FILTER AS A MODEL */
            $datas = $this->dataFilter($this->model, $props, null);

            /* ADD FILTER BY PAKET PRODUK IF AVAILABLE */
            if (isset($props['paket_produk_id'])) {
                $datas->where('paket_produk_id', '=', $props['paket_produk_id']);
            }

            /* RETRIEVE ALL ROW, CONVERT TO ARRAY AND FORMAT AS RESOURCE */
            $datas = $datas->get();
            $datas = PaketAksesorisResource::collection($datas);
            $material = [
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

            return $material;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /* FETCH PAKET AKSESORIS BY ID */
    public function fetchById($id){
        try {
            $material = $this->model::find($id);
            if ($material) {
                $material = PaketAksesorisResource::make($material);
                return $material;
            }

            throw new Exception('Catatan tidak ditemukan!');
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /* CREATE NEW PAKET AKSESORIS */
    public function createPaket($props){
        try {
            $material = $this->model;
            $material->paket_produk_id      = $props['paket_produk_id'];
            $material->paket_aksesoris_id   = $props['paket_aksesoris'];
            $material->nama_paket           = strtoupper($props['nama_paket']);
            $material->minimal_lebar        = strtoupper($props['minimal_lebar']);
            $material->maksimal_lebar       = strtoupper($props['maksimal_lebar']);
            $material->minimal_tinggi       = strtoupper($props['minimal_tinggi']);
            $material->maksimal_tinggi      = strtoupper($props['maksimal_tinggi']);
            $material->jumlah_daun          = strtoupper($props['jumlah_daun']);
            $material->created_id           = $this->returnAuthUser()->id;
            $material->save();

            /* WRITE LOG */
            $this->newValues = $this->model::find($material->id);
            $logParameters = [
                'status'        => 'success',
                'module'        => $this->moduleName,
                'event'         => 'created',
                'description'   => 'Menambahkan paket aksesoris baru [ '.$material->id.' - '.$material->nama_paket.' ]',
                'user_id'       => $this->returnAuthUser()->id ?? null,
                'old_values'    => $this->oldValues,
                'new_values'    => $this->newValues
            ];
            $this->writeActivityLog($logParameters);

            $material = PaketAksesorisResource::make($material);
            return $material;
        } catch (Exception $ex) {
            /* WRITE LOG */
            $logParameters = [
                'status'        => 'error',
                'module'        => $this->moduleName,
                'event'         => 'created',
                'description'   => 'Gagal menambahkan paket aksesoris [ '.$ex->getMessage().' ]',
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
            $material = $this->model::find($id);
            if ($material) {
                $material->paket_aksesoris_id   = $props['paket_aksesoris'];
                $material->nama_paket           = strtoupper($props['nama_paket']);
                $material->minimal_lebar        = strtoupper($props['minimal_lebar']);
                $material->maksimal_lebar       = strtoupper($props['maksimal_lebar']);
                $material->minimal_tinggi       = strtoupper($props['minimal_tinggi']);
                $material->maksimal_tinggi      = strtoupper($props['maksimal_tinggi']);
                $material->jumlah_daun          = strtoupper($props['jumlah_daun']);
                $material->updated_id           = $this->returnAuthUser()->id;
                $material->update();

                /* WRITE LOG */
                $this->newValues = $this->model::find($material->id);
                $logParameters = [
                    'status'        => 'success',
                    'module'        => $this->moduleName,
                    'event'         => 'updated',
                    'description'   => 'Memperbaharui paket aksesoris [ '.$material->id.' - '.$material->nama_paket.' ]',
                    'user_id'       => $this->returnAuthUser()->id ?? null,
                    'old_values'    => $this->oldValues,
                    'new_values'    => $this->newValues
                ];
                $this->writeActivityLog($logParameters);

                $material = PaketAksesorisResource::make($material);
                return $material;
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
            $material = $this->model::find($id);

            if ($material) {
                $material->delete();

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
            $material = $this->model::whereIn('id', $props);

            if ($material->count() > 0) {
                $material->delete();

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
            $material = PaketAksesorisResource::collection($datas);

            return $material;
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
            $material = $datas->select('id', 'nama_paket', 'alamat')->get();

            return $material;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
}
