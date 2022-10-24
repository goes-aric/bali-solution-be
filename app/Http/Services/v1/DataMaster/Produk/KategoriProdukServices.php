<?php
namespace App\Http\Services\v1\DataMaster\Produk;

use Exception;
use App\Models\KategoriProduk;
use App\Http\Services\v1\BaseServices;
use App\Http\Resources\v1\DataMaster\Produk\KategoriProdukResource;

class KategoriProdukServices extends BaseServices
{
    /* PRIVATE VARIABLE */
    private $model;
    private $moduleName;
    private $oldValues;
    private $newValues;

    public function __construct()
    {
        $this->model = new KategoriProduk;
        $this->moduleName = 'Kategori Produk';
    }

    /* FETCH ALL KATEGORI PRODUK */
    public function fetchAll($props){
        try {
            /* GET DATA WITH FILTER AS A MODEL */
            $datas = $this->dataFilterPagination($this->model, $props, null);

            /* RETRIEVE ALL ROW, CONVERT TO ARRAY AND FORMAT AS RESOURCE */
            $datas = $datas->get();
            $kategori = KategoriProdukResource::collection($datas);

            return $kategori;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /* FETCH ALL KATEGORI PRODUK LIMIT */
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
            $datas = KategoriProdukResource::collection($datas);
            $kategori = [
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

            return $kategori;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /* FETCH KATEGORI PRODUK BY ID */
    public function fetchById($id){
        try {
            $kategori = $this->model::find($id);
            if ($kategori) {
                $kategori = KategoriProdukResource::make($kategori);
                return $kategori;
            }

            throw new Exception('Catatan tidak ditemukan!');
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /* CREATE NEW KATEGORI PRODUK */
    public function createKategori($props){
        try {
            $kategori = $this->model;
            $kategori->nama_kategori    = strtoupper($props['nama_kategori']);
            $kategori->keterangan       = strtoupper($props['keterangan']);
            $kategori->status           = strtoupper($props['status']);
            $kategori->created_id       = $this->returnAuthUser()->id;
            $kategori->save();

            /* WRITE LOG */
            $this->newValues = $this->model::find($kategori->id);
            $logParameters = [
                'status'        => 'success',
                'module'        => $this->moduleName,
                'event'         => 'created',
                'description'   => 'Membuat kategori produk baru [ '.$kategori->id.' - '.$kategori->nama_kategori.' ]',
                'user_id'       => $this->returnAuthUser()->id ?? null,
                'old_values'    => $this->oldValues,
                'new_values'    => $this->newValues
            ];
            $this->writeActivityLog($logParameters);

            $kategori = KategoriProdukResource::make($kategori);
            return $kategori;
        } catch (Exception $ex) {
            /* WRITE LOG */
            $logParameters = [
                'status'        => 'error',
                'module'        => $this->moduleName,
                'event'         => 'created',
                'description'   => 'Gagal membuat kategori produk [ '.$ex->getMessage().' ]',
                'user_id'       => $this->returnAuthUser()->id ?? null,
                'old_values'    => $this->oldValues,
                'new_values'    => $this->newValues
            ];
            $this->writeActivityLog($logParameters);

            throw $ex;
        }
    }

    /* UPDATE KATEGORI PRODUK */
    public function updateKategori($props, $id){
        try {
            $this->oldValues = $this->model::find($id);
            $kategori = $this->model::find($id);
            if ($kategori) {
                $kategori->nama_kategori    = strtoupper($props['nama_kategori']);
                $kategori->keterangan       = strtoupper($props['keterangan']);
                $kategori->status           = strtoupper($props['status']);
                $kategori->updated_id       = $this->returnAuthUser()->id;
                $kategori->update();

                /* WRITE LOG */
                $this->newValues = $this->model::find($kategori->id);
                $logParameters = [
                    'status'        => 'success',
                    'module'        => $this->moduleName,
                    'event'         => 'updated',
                    'description'   => 'Memperbaharui kategori produk [ '.$kategori->id.' - '.$kategori->nama_kategori.' ]',
                    'user_id'       => $this->returnAuthUser()->id ?? null,
                    'old_values'    => $this->oldValues,
                    'new_values'    => $this->newValues
                ];
                $this->writeActivityLog($logParameters);

                $kategori = KategoriProdukResource::make($kategori);
                return $kategori;
            } else {
                throw new Exception('Catatan tidak ditemukan!');
            }
        } catch (Exception $ex) {
            /* WRITE LOG */
            $logParameters = [
                'status'        => 'error',
                'module'        => $this->moduleName,
                'event'         => 'updated',
                'description'   => 'Gagal memperbaharui kategori produk [ Pesan: '.$ex->getMessage().' ]',
                'user_id'       => $this->returnAuthUser()->id ?? null,
                'old_values'    => $this->oldValues,
                'new_values'    => $this->newValues
            ];
            $this->writeActivityLog($logParameters);

            throw $ex;
        }
    }

    /* DESTROY KATEGORI PRODUK */
    public function destroyKategori($id){
        try {
            $this->oldValues = $this->model::find($id);
            $kategori = $this->model::find($id);

            if ($kategori) {
                $kategori->delete();

                /* WRITE LOG */
                $logParameters = [
                    'status'        => 'success',
                    'module'        => $this->moduleName,
                    'event'         => 'deleted',
                    'description'   => 'Menghapus kategori produk [ '.$this->oldValues->id.' - '. $this->oldValues->nama_kategori.' ]',
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
                'description'   => 'Gagal menghapus kategori produk [ Pesan: '.$ex->getMessage().' ]',
                'user_id'       => $this->returnAuthUser()->id ?? null,
                'old_values'    => $this->oldValues,
                'new_values'    => $this->newValues
            ];
            $this->writeActivityLog($logParameters);

            throw $ex;
        }
    }

    /* DESTROY SELECTED / MULTIPLE KATEGORI PRODUK */
    public function destroyMultipleKategori($props){
        try {
            $this->oldValues = $this->model::whereIn('id', $props)->get();
            $kategori = $this->model::whereIn('id', $props);

            if ($kategori->count() > 0) {
                $kategori->delete();

                /* WRITE LOG */
                $logParameters = [
                    'status'        => 'success',
                    'module'        => $this->moduleName,
                    'event'         => 'deleted',
                    'description'   => 'Menghapus kategori produk [ '.$this->oldValues.' ]',
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
                'description'   => 'Gagal menghapus kategori produk [ Pesan: '.$ex->getMessage().' ]',
                'user_id'       => $this->returnAuthUser()->id ?? null,
                'old_values'    => $this->oldValues,
                'new_values'    => $this->newValues
            ];
            $this->writeActivityLog($logParameters);

            throw $ex;
        }
    }

    /* FETCH ALL KATEGORI PRODUK FOR OPTIONS */
    public function fetchDataOptions($props){
        try {
            /* GET DATA WITH FILTER AS A MODEL */
            $datas = $this->dataFilterPagination($this->model, $props, null);

            /* RETRIEVE ALL ROW, CONVERT TO ARRAY AND FORMAT AS RESOURCE */
            $kategori = $datas->select('id', 'nama_kategori', 'keterangan')->get();

            return $kategori;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /* FETCH KATEGORI PRODUK BY NAME */
    /* INTERNAL USED (USED BY ANOTHER SERVICES) */
    public function fetchByName($name){
        $data = $this->model::where('nama_kategori', '=', $name)->first();
        if ($data) {
            return $data;
        }
        return null;
    }
}
