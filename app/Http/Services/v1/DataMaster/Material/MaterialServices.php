<?php
namespace App\Http\Services\v1\DataMaster\Material;

use Exception;
use Illuminate\Support\Facades\Storage;
use App\Http\Services\v1\BaseServices;
use App\Http\Resources\v1\DataMaster\Material\MaterialResource;
use App\Models\Material;

class MaterialServices extends BaseServices
{
    /* PRIVATE VARIABLE */
    private $model;
    private $moduleName;
    private $oldValues;
    private $newValues;

    public function __construct()
    {
        $this->model = new Material();
        $this->moduleName = 'Material';
    }

    /* FETCH ALL MATERIAL */
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
            $material = MaterialResource::collection($datas);

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
            $datas = MaterialResource::collection($datas);
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
                $material = MaterialResource::make($material);
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
            $imageName = null;
            $imagePath = storage_path("app/public/images/");
            $imageBinary = $props->file('gambar');

            /* TRY TO UPLOAD IMAGE FIRST */
            if (!empty($props->file('gambar'))) {
                /* DECLARE NEW IMAGE VARIABLE */
                $image = $props->file('gambar');
                $newName = $props['kode'].'.' . $image->getClientOriginalExtension();
                if (Storage::exists($newName)) {
                    $newName = $props['kode'].'-'. rand() . '.' . $image->getClientOriginalExtension();
                }

                $uploadImage = $this->returnUploadImage($imagePath, $newName, $imageBinary);
                if ($uploadImage['status'] == 'success') {
                    $imageName = $uploadImage['filename'];
                }
            }

            /* STORE MATERIAL */
            $material = new $this->model;
            $material->kode                 = strtoupper($props['kode']);
            $material->nama_material        = strtoupper($props['nama_material']);
            $material->panjang              = strtoupper($props['panjang']);
            $material->satuan               = strtoupper($props['satuan']);
            $material->warna                = strtoupper($props['warna']);
            $material->gambar               = $imageName;
            $material->harga_beli_terakhir  = $props['harga_beli_terakhir'];
            $material->harga_beli_konversi  = $props['harga_beli_konversi'];
            $material->harga_jual           = $props['harga_jual'];
            $material->status               = $props['status'];
            $material->created_id           = $this->returnAuthUser()->id;
            $material->save();

            /* WRITE LOG */
            $this->newValues = $this->model::find($material->id);
            $logParameters = [
                'status'        => 'success',
                'module'        => $this->moduleName,
                'event'         => 'created',
                'description'   => 'Membuat material baru [ '.$material->id.' - '.$material->nama_material.' ]',
                'user_id'       => $this->returnAuthUser()->id ?? null,
                'old_values'    => $this->oldValues,
                'new_values'    => $this->newValues
            ];
            $this->writeActivityLog($logParameters);

            $material = MaterialResource::make($material);
            return $material;
        } catch (Exception $ex) {
            /* WRITE LOG */
            $logParameters = [
                'status'        => 'error',
                'module'        => $this->moduleName,
                'event'         => 'created',
                'description'   => 'Gagal membuat material [ Pesan: '.$ex->getMessage().' ]',
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
                $imageName = $material->gambar;
                $imagePath = storage_path("app/public/images/");
                $imageBinary = $props->file('gambar');

                /* TRY TO UPLOAD IMAGE */
                if (!empty($props->file('gambar'))) {
                    // IF CURRENT IMAGE IS NOT EMPTY, DELETE CURRENT IMAGE
                    if ($material->gambar != null) {
                        $this->returnDeleteFile($imagePath, $imageName);
                    }

                    /* DECLARE NEW IMAGE VARIABLE */
                    $gambar = $props->file('gambar');
                    $newName = $props['kode'].'.' . $gambar->getClientOriginalExtension();
                    if (Storage::exists($newName)) {
                        $newName = $props['kode'].'-' . rand() . '.' . $gambar->getClientOriginalExtension();
                    }

                    $uploadImage = $this->returnUploadImage($imagePath, $newName, $imageBinary);
                    if ($uploadImage['status'] == 'success') {
                        $imageName = $uploadImage['filename'];
                    }
                }

                /* UPDATE MATERIAL */
                $material->kode                 = strtoupper($props['kode']);
                $material->nama_material        = strtoupper($props['nama_material']);
                $material->panjang              = strtoupper($props['panjang']);
                $material->satuan               = strtoupper($props['satuan']);
                $material->warna                = strtoupper($props['warna']);
                $material->gambar               = $imageName;
                $material->harga_beli_terakhir  = $props['harga_beli_terakhir'];
                $material->harga_beli_konversi  = $props['harga_beli_konversi'];
                $material->harga_jual           = $props['harga_jual'];
                $material->status               = $props['status'];
                $material->updated_id           = $this->returnAuthUser()->id;
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

                $material = MaterialResource::make($material);
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

    /* FETCH EXPORT DATA */
    public function fetchExportData($props){
        try {
            /* GET DATA WITH FILTER AS A MODEL */
            $datas = $this->dataFilterPagination($this->model, $props, null);

            /* RETRIEVE ALL ROW, CONVERT TO ARRAY AND FORMAT AS RESOURCE */
            $datas = $datas->get();
            $material = MaterialResource::collection($datas);

            return $material;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /* FETCH ALL MATERIAL FOR OPTIONS */
    public function fetchDataOptions($props){
        try {
            /* GET DATA WITH FILTER AS A MODEL */
            $datas = $this->dataFilterPagination($this->model, $props, null);

            /* RETRIEVE ALL ROW, CONVERT TO ARRAY AND FORMAT AS RESOURCE */
            $material = $datas->get();

            return $material;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /* FETCH MATERIAL BY NAME */
    /* INTERNAL USED (USED BY ANOTHER SERVICES) */
    public function fetchByName($name){
        $data = $this->model::where('nama_material', '=', $name)->first();
        if ($data) {
            return $data;
        }
        return null;
    }
}
