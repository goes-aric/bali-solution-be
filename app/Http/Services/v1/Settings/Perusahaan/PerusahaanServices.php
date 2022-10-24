<?php
namespace App\Http\Services\v1\Settings\Perusahaan;

use Exception;
use App\Models\Perusahaan;
use App\Http\Services\v1\BaseServices;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\v1\Settings\Perusahaan\PerusahaanResource;

class PerusahaanServices extends BaseServices
{
    /* PRIVATE VARIABLE */
    private $model;
    private $moduleName;
    private $oldValues;
    private $newValues;

    public function __construct()
    {
        $this->model =  new Perusahaan();
        $this->moduleName = 'Perusahaan';
    }

    /* FETCH PERUSAHAAN BY ID */
    public function fetchById($id){
        try {
            $data = $this->model::find($id);
            if ($data) {
                $data = PerusahaanResource::make($data);
                return $data;
            }

            throw new Exception('Catatan tidak ditemukan!');
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /* CREATE OR UPDATE PERUSAHAAN */
    public function savePerusahaan($props){
        try {
            $data = $this->model::first();
            if ($data) {
                $this->oldValues = $this->model::first();

                $imageName = $data->logo;
                $imagePath = storage_path("app/public/images/");
                $imageBinary = $props->file('logo');

                /* TRY TO UPLOAD IMAGE */
                if (!empty($props->file('logo'))) {
                    // IF CURRENT IMAGE IS NOT EMPTY, DELETE CURRENT IMAGE
                    if ($data->logo != null) {
                        $this->returnDeleteFile($imagePath, $imageName);
                    }

                    /* DECLARE NEW IMAGE VARIABLE */
                    $logo = $props->file('logo');
                    $newName = 'logo.' . $logo->getClientOriginalExtension();
                    if (Storage::exists($newName)) {
                        $newName = 'logo-' . rand() . '.' . $logo->getClientOriginalExtension();
                    }

                    $uploadImage = $this->returnUploadImage($imagePath, $newName, $imageBinary);
                    if ($uploadImage['status'] == 'success') {
                        $imageName = $uploadImage['filename'];
                    }
                }

                /* UPDATE PERUSAHAAN */
                $data->nama_perusahaan  = $props['nama_perusahaan'];
                $data->alamat           = $props['alamat'];
                $data->nomor_legalitas  = $props['nomor_legalitas'];
                $data->no_telp          = $props['no_telp'];
                $data->website          = $props['website'];
                $data->email            = $props['email'];
                $data->logo             = $imageName;
                $data->updated_id       = $this->returnAuthUser()->id;
                $data->update();

                /* WRITE LOG */
                $this->newValues = $this->model::find($data->id);
                $logParameters = [
                    'status'        => 'success',
                    'module'        => $this->moduleName,
                    'event'         => 'updated',
                    'description'   => 'Perbaharui perusahaan [ '.$data->id.' ]',
                    'user_id'       => $this->returnAuthUser()->id ?? null,
                    'old_values'    => $this->oldValues,
                    'new_values'    => $this->newValues
                ];
                $this->writeActivityLog($logParameters);

                $data = PerusahaanResource::make($data);
                return $data;
            } else {
                $imageName = null;
                $imagePath = storage_path("app/public/images/");
                $imageBinary = $props->file('logo');

                /* TRY TO UPLOAD IMAGE FIRST */
                if (!empty($props->file('logo'))) {
                    /* DECLARE NEW IMAGE VARIABLE */
                    $image = $props->file('logo');
                    $newName = 'logo.' . $image->getClientOriginalExtension();
                    if (Storage::exists($newName)) {
                        $newName = 'logo-'. rand() . '.' . $image->getClientOriginalExtension();
                    }

                    $uploadImage = $this->returnUploadImage($imagePath, $newName, $imageBinary);
                    if ($uploadImage['status'] == 'success') {
                        $imageName = $uploadImage['filename'];
                    }
                }

                /* STORE PERUSAHAAN */
                $create = $this->model;
                $create->nama_perusahaan    = $props['nama_perusahaan'];
                $create->alamat             = $props['alamat'];
                $create->nomor_legalitas    = $props['nomor_legalitas'];
                $create->no_telp            = $props['no_telp'];
                $create->website            = $props['website'];
                $create->email              = $props['email'];
                $create->logo               = $imageName;
                $create->created_id         = $this->returnAuthUser()->id;
                $create->save();

                /* WRITE LOG */
                $this->newValues = $this->model::find($create->id);
                $logParameters = [
                    'status'        => 'success',
                    'module'        => $this->moduleName,
                    'event'         => 'created',
                    'description'   => 'Buat perusahaan baru [ '.$create->id.' ]',
                    'user_id'       => $this->returnAuthUser()->id ?? null,
                    'old_values'    => $this->oldValues,
                    'new_values'    => $this->newValues
                ];
                $this->writeActivityLog($logParameters);

                $create = PerusahaanResource::make($create);
                return $create;
            }
        } catch (Exception $ex) {
            /* WRITE LOG */
            $logParameters = [
                'status'        => 'error',
                'module'        => $this->moduleName,
                'event'         => 'updated',
                'description'   => 'Kesalahan menyimpan perusahaan [ Pesan: '.$ex->getMessage().' ]',
                'user_id'       => $this->returnAuthUser()->id ?? null,
                'old_values'    => $this->oldValues,
                'new_values'    => $this->newValues
            ];
            $this->writeActivityLog($logParameters);

            throw $ex;
        }
    }

    /* DESTROY PERUSAHAAN */
    public function destroyPerusahaan($id){
        try {
            $this->oldValues = $this->model::find($id);
            $data = $this->model::find($id);

            if ($data) {
                $data->delete();

                /* WRITE LOG */
                $logParameters = [
                    'status'        => 'success',
                    'module'        => $this->moduleName,
                    'event'         => 'deleted',
                    'description'   => 'Menghapus perusahaan [ '.$this->oldValues->id.' ]',
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
                'description'   => 'Gagal menghapus perusahaan [ Pesan: '.$ex->getMessage().' ]',
                'user_id'       => $this->returnAuthUser()->id ?? null,
                'old_values'    => $this->oldValues,
                'new_values'    => $this->newValues
            ];
            $this->writeActivityLog($logParameters);

            return $ex;
        }
    }
}
