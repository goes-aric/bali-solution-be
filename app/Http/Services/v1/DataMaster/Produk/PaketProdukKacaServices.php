<?php
namespace App\Http\Services\v1\DataMaster\Produk;

use Exception;
use App\Http\Services\v1\BaseServices;
use App\Models\PaketProdukKaca;

class PaketProdukKacaServices extends BaseServices
{
    /* PRIVATE VARIABLE */
    private $model;
    private $moduleName;
    private $oldValues;
    private $newValues;

    public function __construct()
    {
        $this->model = new PaketProdukKaca();
        $this->moduleName = 'Paket Produk';
    }

    /* FETCH ALL MATERIAL */
    public function fetchAll($props){
        try {
            /* GET DATA WITH FILTER AS A MODEL */
            $datas = $this->dataFilterPagination($this->model, $props, null);

            /* ADD FILTER BY PAKET PRODUK IF AVAILABLE */
            if (isset($props['paket_produk_id'])) {
                $datas->where('paket_produk_id', '=', $props['paket_produk_id']);
            }

            /* ADD FILTER BY TIPE IF AVAILABLE */
            if (isset($props['tipe'])) {
                $datas->where('tipe', '=', $props['tipe']);
            }

            /* RETRIEVE ALL ROW, CONVERT TO ARRAY AND FORMAT AS RESOURCE */
            $material = $datas->get();

            return $material;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /* FETCH ALL MATERIAL LIMIT */
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

            /* ADD FILTER BY TIPE IF AVAILABLE */
            if (isset($props['tipe'])) {
                $getFilterData->where('tipe', '=', $props['tipe']);
            }
            $totalFiltered = $getFilterData->count();

            /* GET DATA WITH FILTER AS A MODEL */
            $datas = $this->dataFilter($this->model, $props, null);

            /* ADD FILTER BY PAKET PRODUK IF AVAILABLE */
            if (isset($props['paket_produk_id'])) {
                $datas->where('paket_produk_id', '=', $props['paket_produk_id']);
            }

            /* ADD FILTER BY TIPE IF AVAILABLE */
            if (isset($props['tipe'])) {
                $datas->where('tipe', '=', $props['tipe']);
            }

            /* RETRIEVE ALL ROW, CONVERT TO ARRAY AND FORMAT AS RESOURCE */
            $datas = $datas->get();
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

    /* FETCH MATERIAL BY ID */
    public function fetchById($id){
        try {
            $material = $this->model::find($id);
            if ($material) {
                return $material;
            }

            throw new Exception('Catatan tidak ditemukan!');
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /* CREATE NEW MATERIAL */
    public function createMaterial($props){
        try {
            /* STORE MATERIAL */
            $material = new $this->model;
            $material->paket_produk_id  = $props['paket_produk_id'];
            $material->material_id      = $props['material'];
            $material->kode             = strtoupper($props['kode']);
            $material->nama_material    = strtoupper($props['nama_material']);
            $material->panjang          = strtoupper($props['panjang']);
            $material->lebar            = strtoupper($props['lebar']);
            $material->tebal            = strtoupper($props['tebal']);
            $material->satuan           = strtoupper($props['satuan']);
            $material->tipe             = $props['tipe'];
            $material->created_id       = $this->returnAuthUser()->id;
            $material->save();

            /* WRITE LOG */
            $this->newValues = $this->model::find($material->id);
            $logParameters = [
                'status'        => 'success',
                'module'        => $this->moduleName,
                'event'         => 'created',
                'description'   => 'Menambahkan material baru [ '.$material->id.' - '.$material->nama_material.' ]',
                'user_id'       => $this->returnAuthUser()->id ?? null,
                'old_values'    => $this->oldValues,
                'new_values'    => $this->newValues
            ];
            $this->writeActivityLog($logParameters);

            return $material;
        } catch (Exception $ex) {
            /* WRITE LOG */
            $logParameters = [
                'status'        => 'error',
                'module'        => $this->moduleName,
                'event'         => 'created',
                'description'   => 'Gagal menambahkan material [ Pesan: '.$ex->getMessage().' ]',
                'user_id'       => $this->returnAuthUser()->id ?? null,
                'old_values'    => $this->oldValues,
                'new_values'    => $this->newValues
            ];
            $this->writeActivityLog($logParameters);

            throw $ex;
        }
    }

    /* UPDATE MATERIAL */
    public function updateMaterial($props, $id){
        try {
            $this->oldValues = $this->model::find($id);
            $material = $this->model::find($id);
            if ($material) {
                /* UPDATE MATERIAL */
                $material->material_id      = $props['material'];
                $material->kode             = strtoupper($props['kode']);
                $material->nama_material    = strtoupper($props['nama_material']);
                $material->panjang          = strtoupper($props['panjang']);
                $material->lebar            = strtoupper($props['lebar']);
                $material->tebal            = strtoupper($props['tebal']);
                $material->satuan           = strtoupper($props['satuan']);
                $material->tipe             = $props['tipe'];
                $material->updated_id       = $this->returnAuthUser()->id;
                $material->update();

                /* WRITE LOG */
                $this->newValues = $this->model::find($material->id);
                $logParameters = [
                    'status'        => 'success',
                    'module'        => $this->moduleName,
                    'event'         => 'updated',
                    'description'   => 'Memperbaharui material [ '.$material->id.' - '.$material->nama_material.' ]',
                    'user_id'       => $this->returnAuthUser()->id ?? null,
                    'old_values'    => $this->oldValues,
                    'new_values'    => $this->newValues
                ];
                $this->writeActivityLog($logParameters);

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
                'description'   => 'Gagal memperbaharui material [ Pesan: '.$ex->getMessage().' ]',
                'user_id'       => $this->returnAuthUser()->id ?? null,
                'old_values'    => $this->oldValues,
                'new_values'    => $this->newValues
            ];
            $this->writeActivityLog($logParameters);

            throw $ex;
        }
    }

    /* DESTROY MATERIAL */
    public function destroyMaterial($id){
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
                    'description'   => 'Menghapus material [ '.$this->oldValues->id.' - '. $this->oldValues->nama_material.' ]',
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
                'description'   => 'Gagal menghapus material [ Pesan: '.$ex->getMessage().' ]',
                'user_id'       => $this->returnAuthUser()->id ?? null,
                'old_values'    => $this->oldValues,
                'new_values'    => $this->newValues
            ];
            $this->writeActivityLog($logParameters);

            throw $ex;
        }
    }

    /* DESTROY SELECTED / MULTIPLE MATERIAL */
    public function destroyMultipleMaterial($props){
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
                    'description'   => 'Menghapus material [ '.$this->oldValues.' ]',
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
                'description'   => 'Gagal menghapus material [ Pesan: '.$ex->getMessage().' ]',
                'user_id'       => $this->returnAuthUser()->id ?? null,
                'old_values'    => $this->oldValues,
                'new_values'    => $this->newValues
            ];
            $this->writeActivityLog($logParameters);

            throw $ex;
        }
    }
}
