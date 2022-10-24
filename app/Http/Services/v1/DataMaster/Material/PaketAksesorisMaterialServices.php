<?php
namespace App\http\Services\v1\DataMaster\Material;

use App\Models\PaketAksesorisMaterial;
use App\Http\Services\v1\BaseServices;
use App\Http\Resources\v1\DataMaster\Material\PaketAksesorisMaterialResource;
use Exception;

class PaketAksesorisMaterialServices extends BaseServices
{
    /* PRIVATE VARIABLE */
    private $model;
    private $moduleName;
    private $oldValues;
    private $newValues;

    public function __construct()
    {
        $this->model = new PaketAksesorisMaterial;
        $this->moduleName = 'Paket Aksesoris';
    }

    /* FETCH ALL MATERIAL */
    public function fetchAll($props, $id){
        try {
            /* GET DATA WITH FILTER AS A MODEL */
            $datas = $this->dataFilterPagination($this->model, $props, null)->where('paket_aksesoris_id', '=', $id);

            /* RETRIEVE ALL ROW, CONVERT TO ARRAY AND FORMAT AS RESOURCE */
            $datas = $datas->get();
            $materials = PaketAksesorisMaterialResource::collection($datas);

            return $materials;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /* FETCH ALL MATERIAL LIMIT */
    public function fetchLimit($props, $id){
        try {
            /* GET DATA WITH FILTER FOR PAGINATION AS A MODEL */
            $getAllData = $this->dataFilterPagination($this->model, $props, null)->where('paket_aksesoris_id', '=', $id);

            /* GET DATA WITH FILTER AS A MODEL */
            $datas = $this->dataFilter($this->model, $props, null)->where('paket_aksesoris_id', '=', $id);

            /* RETRIEVE ALL ROW, CONVERT TO ARRAY AND FORMAT AS RESOURCE */
            $datas = $datas->get();
            $datas = PaketAksesorisMaterialResource::collection($datas);
            $materials = [
                "total" => $getAllData->count(),
                "per_page" => $props['take'],
                "current_page" => $props['skip'] == 0 ? 1 : ($props['skip'] + 1),
                "last_page" => ceil($getAllData->count() / $props['take']),
                "from" => $props['skip'] != 0 ? ($props['skip'] * $props['take']) + 1 : 1,
                "to" => ($props['skip'] * $props['take']) + $datas->count(),
                "show" => [
                    ["number" => 25, "name" => "25"], ["number" => 50, "name" => "50"], ["number" => 100, "name" => "100"], ["number" => $getAllData->count(), "name" => "ALL"]
                ],
                "data" => $datas
            ];

            return $materials;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /* FETCH MATERIAL BY ID */
    public function fetchById($id){
        try {
            $material = $this->model::find($id);
            if ($material) {
                $material = PaketAksesorisMaterialResource::make($material);
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
            $material = $this->model;
            $material->paket_aksesoris_id   = $props['paket_aksesoris_id'];
            $material->aksesoris_id         = $props['aksesoris_id'];
            $material->kode                 = $props['kode'];
            $material->nama_material        = $props['nama_material'];
            $material->tipe                 = $props['tipe'];
            $material->warna                = $props['warna'];
            $material->satuan               = $props['satuan'];
            $material->qty                  = $props['qty'];
            $material->created_id           = $this->returnAuthUser()->id;
            $material->save();

            /* WRITE LOG */
            $this->newValues = $this->model::find($material->id);
            $logParameters = [
                'status'        => 'success',
                'module'        => $this->moduleName,
                'event'         => 'created',
                'description'   => 'Menambahkan material baru [ '.$material->id.' - '.$material->kode.' ]',
                'user_id'       => $this->returnAuthUser()->id ?? null,
                'old_values'    => $this->oldValues,
                'new_values'    => $this->newValues
            ];
            $this->writeActivityLog($logParameters);

            $material = PaketAksesorisMaterialResource::make($material);
            return $material;
        } catch (Exception $ex) {
            /* WRITE LOG */
            $logParameters = [
                'status'        => 'error',
                'module'        => $this->moduleName,
                'event'         => 'created',
                'description'   => 'Gagal menambahkan material [ '.$ex->getMessage().' ]',
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
                $material->aksesoris_id     = $props['aksesoris_id'];
                $material->kode             = $props['kode'];
                $material->nama_material    = $props['nama_material'];
                $material->tipe             = $props['tipe'];
                $material->warna            = $props['warna'];
                $material->satuan           = $props['satuan'];
                $material->qty              = $props['qty'];
                $material->updated_id       = $this->returnAuthUser()->id;
                $material->update();

                /* WRITE LOG */
                $this->newValues = $this->model::find($material->id);
                $logParameters = [
                    'status'        => 'success',
                    'module'        => $this->moduleName,
                    'event'         => 'updated',
                    'description'   => 'Memperbaharui material [ '.$material->id.' - '.$material->kode.' ]',
                    'user_id'       => $this->returnAuthUser()->id ?? null,
                    'old_values'    => $this->oldValues,
                    'new_values'    => $this->newValues
                ];
                $this->writeActivityLog($logParameters);

                $material = PaketAksesorisMaterialResource::make($material);
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
                    'description'   => 'Menghapus material [ '.$this->oldValues->id.' - '. $this->oldValues->kode.' ]',
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
            $materials = $this->model::whereIn('id', $props);

            if ($materials->count() > 0) {
                $materials->delete();

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
