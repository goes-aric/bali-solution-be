<?php
namespace App\Http\Services\v1\Settings\TipePenyesuaian;

use Exception;
use App\Models\TipePenyesuaian;
use App\Http\Services\v1\BaseServices;
use App\Http\Resources\v1\Settings\TipePenyesuaian\TipePenyesuaianResource;

class TipePenyesuaianServices extends BaseServices
{
    /* PRIVATE VARIABLE */
    private $model;
    private $moduleName;
    private $oldValues;
    private $newValues;

    public function __construct()
    {
        $this->model = new TipePenyesuaian;
        $this->moduleName = 'Tipe Penyesuaian';
    }

    /* FETCH ALL TIPE PENYESUAIAN */
    public function fetchAll($props){
        try {
            /* GET DATA WITH FILTER AS A MODEL */
            $datas = $this->dataFilterPagination($this->model, $props, null);

            /* RETRIEVE ALL ROW, CONVERT TO ARRAY AND FORMAT AS RESOURCE */
            $datas = $datas->get();
            $tipePenyesuaian = TipePenyesuaianResource::collection($datas);

            return $tipePenyesuaian;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /* FETCH ALL TIPE PENYESUAIAN LIMIT */
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
            $datas = TipePenyesuaianResource::collection($datas);
            $tipePenyesuaian = [
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

            return $tipePenyesuaian;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /* FETCH TIPE PENYESUAIAN BY ID */
    public function fetchById($id){
        try {
            $tipePenyesuaian = $this->model::find($id);
            if ($tipePenyesuaian) {
                $tipePenyesuaian = TipePenyesuaianResource::make($tipePenyesuaian);
                return $tipePenyesuaian;
            }

            throw new Exception('Catatan tidak ditemukan!');
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /* CREATE NEW TIPE PENYESUAIAN */
    public function createTipePenyesuaian($props){
        try {
            $tipePenyesuaian = $this->model;
            $tipePenyesuaian->deskripsi     = strtoupper($props['deskripsi']);
            $tipePenyesuaian->posisi        = $props['posisi'];
            $tipePenyesuaian->created_id    = $this->returnAuthUser()->id;
            $tipePenyesuaian->save();

            /* WRITE LOG */
            $this->newValues = $this->model::find($tipePenyesuaian->id);
            $logParameters = [
                'status'        => 'success',
                'module'        => $this->moduleName,
                'event'         => 'created',
                'description'   => 'Membuat tipe penyesuaian baru [ '.$tipePenyesuaian->id.' - '.$tipePenyesuaian->deskripsi.' ]',
                'user_id'       => $this->returnAuthUser()->id ?? null,
                'old_values'    => $this->oldValues,
                'new_values'    => $this->newValues
            ];
            $this->writeActivityLog($logParameters);

            $tipePenyesuaian = TipePenyesuaianResource::make($tipePenyesuaian);
            return $tipePenyesuaian;
        } catch (Exception $ex) {
            /* WRITE LOG */
            $logParameters = [
                'status'        => 'error',
                'module'        => $this->moduleName,
                'event'         => 'created',
                'description'   => 'Gagal membuat tipe penyesuaian [ '.$ex->getMessage().' ]',
                'user_id'       => $this->returnAuthUser()->id ?? null,
                'old_values'    => $this->oldValues,
                'new_values'    => $this->newValues
            ];
            $this->writeActivityLog($logParameters);

            throw $ex;
        }
    }

    /* UPDATE TIPE PENYESUAIAN */
    public function updateTipePenyesuaian($props, $id){
        try {
            $this->oldValues = $this->model::find($id);
            $tipePenyesuaian = $this->model::find($id);
            if ($tipePenyesuaian) {
                $tipePenyesuaian->deskripsi     = strtoupper($props['deskripsi']);
                $tipePenyesuaian->posisi        = $props['posisi'];
                $tipePenyesuaian->updated_id    = $this->returnAuthUser()->id;
                $tipePenyesuaian->update();

                /* WRITE LOG */
                $this->newValues = $this->model::find($tipePenyesuaian->id);
                $logParameters = [
                    'status'        => 'success',
                    'module'        => $this->moduleName,
                    'event'         => 'updated',
                    'description'   => 'Memperbaharui tipe penyesuaian [ '.$tipePenyesuaian->id.' - '.$tipePenyesuaian->deskripsi.' ]',
                    'user_id'       => $this->returnAuthUser()->id ?? null,
                    'old_values'    => $this->oldValues,
                    'new_values'    => $this->newValues
                ];
                $this->writeActivityLog($logParameters);

                $tipePenyesuaian = TipePenyesuaianResource::make($tipePenyesuaian);
                return $tipePenyesuaian;
            } else {
                throw new Exception('Catatan tidak ditemukan!');
            }
        } catch (Exception $ex) {
            /* WRITE LOG */
            $logParameters = [
                'status'        => 'error',
                'module'        => $this->moduleName,
                'event'         => 'updated',
                'description'   => 'Gagal memperbaharui tipe penyesuaian [ Pesan: '.$ex->getMessage().' ]',
                'user_id'       => $this->returnAuthUser()->id ?? null,
                'old_values'    => $this->oldValues,
                'new_values'    => $this->newValues
            ];
            $this->writeActivityLog($logParameters);

            throw $ex;
        }
    }

    /* DESTROY TIPE PENYESUAIAN */
    public function destroyTipePenyesuaian($id){
        try {
            $this->oldValues = $this->model::find($id);
            $tipePenyesuaian = $this->model::find($id);

            if ($tipePenyesuaian) {
                $tipePenyesuaian->delete();

                /* WRITE LOG */
                $logParameters = [
                    'status'        => 'success',
                    'module'        => $this->moduleName,
                    'event'         => 'deleted',
                    'description'   => 'Menghapus tipe penyesuaian [ '.$this->oldValues->id.' - '. $this->oldValues->deskripsi.' ]',
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
                'description'   => 'Gagal menghapus tipe penyesuaian [ Pesan: '.$ex->getMessage().' ]',
                'user_id'       => $this->returnAuthUser()->id ?? null,
                'old_values'    => $this->oldValues,
                'new_values'    => $this->newValues
            ];
            $this->writeActivityLog($logParameters);

            throw $ex;
        }
    }

    /* DESTROY SELECTED / MULTIPLE TIPE PENYESUAIAN */
    public function destroyMultipleTipePenyesuaian($props){
        try {
            $this->oldValues = $this->model::whereIn('id', $props)->get();
            $tipePenyesuaian = $this->model::whereIn('id', $props);

            if ($tipePenyesuaian->count() > 0) {
                $tipePenyesuaian->delete();

                /* WRITE LOG */
                $logParameters = [
                    'status'        => 'success',
                    'module'        => $this->moduleName,
                    'event'         => 'deleted',
                    'description'   => 'Menghapus tipe penyesuaian [ '.$this->oldValues.' ]',
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
                'description'   => 'Gagal menghapus tipe penyesuaian [ Pesan: '.$ex->getMessage().' ]',
                'user_id'       => $this->returnAuthUser()->id ?? null,
                'old_values'    => $this->oldValues,
                'new_values'    => $this->newValues
            ];
            $this->writeActivityLog($logParameters);

            throw $ex;
        }
    }

    /* FETCH ALL TIPE PENYESUAIAN FOR OPTIONS */
    public function fetchDataOptions($props){
        try {
            /* GET DATA WITH FILTER AS A MODEL */
            $datas = $this->dataFilterPagination($this->model, $props, null);

            /* RETRIEVE ALL ROW, CONVERT TO ARRAY AND FORMAT AS RESOURCE */
            $tipePenyesuaian = $datas->select('id', 'deskripsi', 'posisi')->get();

            return $tipePenyesuaian;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
}
