<?php
namespace App\Http\Services\v1\DataMaster\Produk;

use Exception;
use App\Models\PaketProduk;
use Illuminate\Support\Str;
use App\Http\Services\v1\BaseServices;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\v1\DataMaster\Produk\PaketProdukResource;

class PaketProdukServices extends BaseServices
{
    /* PRIVATE VARIABLE */
    private $model;
    private $moduleName;
    private $oldValues;
    private $newValues;

    public function __construct()
    {
        $this->model = new PaketProduk();
        $this->moduleName = 'Paket Produk';
    }

    /* FETCH ALL PAKET PRODUK */
    public function fetchAll($props){
        try {
            /* GET DATA WITH FILTER AS A MODEL */
            $datas = $this->dataFilterPagination($this->model, $props, null);

            /* RETRIEVE ALL ROW, CONVERT TO ARRAY AND FORMAT AS RESOURCE */
            $datas = $datas->get();
            $paketProduk = PaketProdukResource::collection($datas);

            return $paketProduk;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /* FETCH ALL PAKET PRODUK LIMIT */
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
            $datas = PaketProdukResource::collection($datas);
            $paketProduk = [
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

            return $paketProduk;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /* FETCH PAKET PRODUK BY ID */
    public function fetchById($id){
        try {
            $paketProduk = $this->model::find($id);
            if ($paketProduk) {
                $paketProduk = PaketProdukResource::make($paketProduk);
                return $paketProduk;
            }

            throw new Exception('Catatan tidak ditemukan!');
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /* CREATE NEW PAKET PRODUK */
    public function createPaketProduk($props){
        try {
            $imageName = null;
            $imagePath = storage_path("app/public/images/");
            $imageBinary = $props->file('gambar');

            /* TRY TO UPLOAD IMAGE FIRST */
            if (!empty($props->file('gambar'))) {
                /* DECLARE NEW IMAGE VARIABLE */
                $image = $props->file('gambar');
                $newName = Str::slug($props['nama_paket_produk']).'.' . $image->getClientOriginalExtension();
                if (Storage::exists($newName)) {
                    $newName = Str::slug($props['nama_paket_produk']).'-'. rand() . '.' . $image->getClientOriginalExtension();
                }

                $uploadImage = $this->returnUploadImage($imagePath, $newName, $imageBinary);
                if ($uploadImage['status'] == 'success') {
                    $imageName = $uploadImage['filename'];
                }
            }

            $paketProduk = $this->model;
            $paketProduk->kategori_produk_id    = $props['kategori_produk'];
            $paketProduk->nama_paket_produk     = strtoupper($props['nama_paket_produk']);
            $paketProduk->warna                 = strtoupper($props['warna']);
            $paketProduk->satuan                = strtoupper($props['satuan']);
            $paketProduk->gambar                = $imageName;
            $paketProduk->created_id            = $this->returnAuthUser()->id;
            $paketProduk->save();

            /* WRITE LOG */
            $this->newValues = $this->model::find($paketProduk->id);
            $logParameters = [
                'status'        => 'success',
                'module'        => $this->moduleName,
                'event'         => 'created',
                'description'   => 'Membuat paket produk baru [ '.$paketProduk->id.' - '.$paketProduk->nama_paket_produk.' ]',
                'user_id'       => $this->returnAuthUser()->id ?? null,
                'old_values'    => $this->oldValues,
                'new_values'    => $this->newValues
            ];
            $this->writeActivityLog($logParameters);

            $paketProduk = PaketProdukResource::make($paketProduk);
            return $paketProduk;
        } catch (Exception $ex) {
            /* WRITE LOG */
            $logParameters = [
                'status'        => 'error',
                'module'        => $this->moduleName,
                'event'         => 'created',
                'description'   => 'Gagal membuat paket produk [ '.$ex->getMessage().' ]',
                'user_id'       => $this->returnAuthUser()->id ?? null,
                'old_values'    => $this->oldValues,
                'new_values'    => $this->newValues
            ];
            $this->writeActivityLog($logParameters);

            throw $ex;
        }
    }

    /* UPDATE PAKET PRODUK */
    public function updatePaketProduk($props, $id){
        try {
            $this->oldValues = $this->model::find($id);
            $paketProduk = $this->model::find($id);
            if ($paketProduk) {
                $imageName = $paketProduk->gambar;
                $imagePath = storage_path("app/public/images/");
                $imageBinary = $props->file('gambar');

                /* TRY TO UPLOAD IMAGE */
                if (!empty($props->file('gambar'))) {
                    // IF CURRENT IMAGE IS NOT EMPTY, DELETE CURRENT IMAGE
                    if ($paketProduk->gambar != null) {
                        $this->returnDeleteFile($imagePath, $imageName);
                    }

                    /* DECLARE NEW IMAGE VARIABLE */
                    $gambar = $props->file('gambar');
                    $newName = Str::slug($props['nama_paket_produk']).'.' . $gambar->getClientOriginalExtension();
                    if (Storage::exists($newName)) {
                        $newName = Str::slug($props['nama_paket_produk']).'-' . rand() . '.' . $gambar->getClientOriginalExtension();
                    }

                    $uploadImage = $this->returnUploadImage($imagePath, $newName, $imageBinary);
                    if ($uploadImage['status'] == 'success') {
                        $imageName = $uploadImage['filename'];
                    }
                }

                /* UPDATE PAKET PRODUK */
                $paketProduk->kategori_produk_id    = $props['kategori_produk'];
                $paketProduk->nama_paket_produk     = strtoupper($props['nama_paket_produk']);
                $paketProduk->warna                 = strtoupper($props['warna']);
                $paketProduk->satuan                = strtoupper($props['satuan']);
                $paketProduk->gambar                = $imageName;
                $paketProduk->updated_id            = $this->returnAuthUser()->id;
                $paketProduk->update();

                /* WRITE LOG */
                $this->newValues = $this->model::find($paketProduk->id);
                $logParameters = [
                    'status'        => 'success',
                    'module'        => $this->moduleName,
                    'event'         => 'updated',
                    'description'   => 'Memperbaharui paket produk [ '.$paketProduk->id.' - '.$paketProduk->nama_paket_produk.' ]',
                    'user_id'       => $this->returnAuthUser()->id ?? null,
                    'old_values'    => $this->oldValues,
                    'new_values'    => $this->newValues
                ];
                $this->writeActivityLog($logParameters);

                $paketProduk = PaketProdukResource::make($paketProduk);
                return $paketProduk;
            } else {
                throw new Exception('Catatan tidak ditemukan!');
            }
        } catch (Exception $ex) {
            /* WRITE LOG */
            $logParameters = [
                'status'        => 'error',
                'module'        => $this->moduleName,
                'event'         => 'updated',
                'description'   => 'Gagal memperbaharui paket produk [ Pesan: '.$ex->getMessage().' ]',
                'user_id'       => $this->returnAuthUser()->id ?? null,
                'old_values'    => $this->oldValues,
                'new_values'    => $this->newValues
            ];
            $this->writeActivityLog($logParameters);

            throw $ex;
        }
    }

    /* DESTROY PAKET PRODUK */
    public function destroyPaketProduk($id){
        try {
            $this->oldValues = $this->model::find($id);
            $paketProduk = $this->model::find($id);

            if ($paketProduk) {
                $paketProduk->delete();

                /* WRITE LOG */
                $logParameters = [
                    'status'        => 'success',
                    'module'        => $this->moduleName,
                    'event'         => 'deleted',
                    'description'   => 'Menghapus paket produk [ '.$this->oldValues->id.' - '. $this->oldValues->nama_paket_produk.' ]',
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
                'description'   => 'Gagal menghapus paket produk [ Pesan: '.$ex->getMessage().' ]',
                'user_id'       => $this->returnAuthUser()->id ?? null,
                'old_values'    => $this->oldValues,
                'new_values'    => $this->newValues
            ];
            $this->writeActivityLog($logParameters);

            throw $ex;
        }
    }

    /* DESTROY SELECTED / MULTIPLE PAKET PRODUK */
    public function destroyMultiplePaketProduk($props){
        try {
            $this->oldValues = $this->model::whereIn('id', $props)->get();
            $paketProduk = $this->model::whereIn('id', $props);

            if ($paketProduk->count() > 0) {
                $paketProduk->delete();

                /* WRITE LOG */
                $logParameters = [
                    'status'        => 'success',
                    'module'        => $this->moduleName,
                    'event'         => 'deleted',
                    'description'   => 'Menghapus paket produk [ '.$this->oldValues.' ]',
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
                'description'   => 'Gagal menghapus paket produk [ Pesan: '.$ex->getMessage().' ]',
                'user_id'       => $this->returnAuthUser()->id ?? null,
                'old_values'    => $this->oldValues,
                'new_values'    => $this->newValues
            ];
            $this->writeActivityLog($logParameters);

            throw $ex;
        }
    }

    /* FETCH ALL PAKET PRODUK FOR OPTIONS */
    public function fetchDataOptions($props){
        try {
            /* GET DATA WITH FILTER AS A MODEL */
            $datas = $this->dataFilterPagination($this->model, $props, null);

            /* RETRIEVE ALL ROW, CONVERT TO ARRAY AND FORMAT AS RESOURCE */
            $paketProduk = $datas->select('id', 'nama_paket_produk', 'warna', 'satuan')->get();

            return $paketProduk;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /* FETCH PAKET PRODUK BY NAME */
    /* INTERNAL USED (USED BY ANOTHER SERVICES) */
    public function fetchByName($name){
        $data = $this->model::where('nama_paket_produk', '=', $name)->first();
        if ($data) {
            return $data;
        }
        return null;
    }
}
