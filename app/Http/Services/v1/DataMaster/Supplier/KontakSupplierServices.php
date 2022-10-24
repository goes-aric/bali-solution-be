<?php
namespace App\http\Services\v1\DataMaster\Supplier;

use App\Models\KontakSupplier;
use App\Http\Services\v1\BaseServices;
use App\Http\Resources\v1\DataMaster\Supplier\KontakSupplierResource;
use Exception;

class KontakSupplierServices extends BaseServices
{
    /* PRIVATE VARIABLE */
    private $model;
    private $moduleName;
    private $oldValues;
    private $newValues;

    public function __construct()
    {
        $this->model = new KontakSupplier;
        $this->moduleName = 'Supplier';
    }

    /* FETCH ALL CONTACT */
    public function fetchAll($props, $id){
        try {
            /* GET DATA WITH FILTER AS A MODEL */
            $datas = $this->dataFilterPagination($this->model, $props, null)->where('supplier_id', '=', $id);

            /* RETRIEVE ALL ROW, CONVERT TO ARRAY AND FORMAT AS RESOURCE */
            $datas = $datas->get();
            $contacts = KontakSupplierResource::collection($datas);

            return $contacts;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /* FETCH ALL CONTACT LIMIT */
    public function fetchLimit($props, $id){
        try {
            /* GET DATA WITH FILTER FOR PAGINATION AS A MODEL */
            $getAllData = $this->dataFilterPagination($this->model, $props, null)->where('supplier_id', '=', $id);

            /* GET DATA WITH FILTER AS A MODEL */
            $datas = $this->dataFilter($this->model, $props, null)->where('supplier_id', '=', $id);

            /* RETRIEVE ALL ROW, CONVERT TO ARRAY AND FORMAT AS RESOURCE */
            $datas = $datas->get();
            $datas = KontakSupplierResource::collection($datas);
            $contacts = [
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

            return $contacts;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /* FETCH CONTACT BY ID */
    public function fetchById($id){
        try {
            $contact = $this->model::find($id);
            if ($contact) {
                $contact = KontakSupplierResource::make($contact);
                return $contact;
            }

            throw new Exception('Catatan tidak ditemukan!');
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /* CREATE NEW CONTACT */
    public function createContact($props){
        try {
            $contact = $this->model;
            $contact->supplier_id   = $props['supplier_id'];
            $contact->kontak_person = strtoupper($props['kontak_person']);
            $contact->email         = $props['email'];
            $contact->no_telp       = $props['no_telp'];
            $contact->created_id    = $this->returnAuthUser()->id;
            $contact->save();

            /* WRITE LOG */
            $this->newValues = $this->model::find($contact->id);
            $logParameters = [
                'status'        => 'success',
                'module'        => $this->moduleName,
                'event'         => 'created',
                'description'   => 'Menambahkan kontak baru [ '.$contact->id.' - '.$contact->kontak_person.' ]',
                'user_id'       => $this->returnAuthUser()->id ?? null,
                'old_values'    => $this->oldValues,
                'new_values'    => $this->newValues
            ];
            $this->writeActivityLog($logParameters);

            $contact = KontakSupplierResource::make($contact);
            return $contact;
        } catch (Exception $ex) {
            /* WRITE LOG */
            $logParameters = [
                'status'        => 'error',
                'module'        => $this->moduleName,
                'event'         => 'created',
                'description'   => 'Gagal membuat kontak [ '.$ex->getMessage().' ]',
                'user_id'       => $this->returnAuthUser()->id ?? null,
                'old_values'    => $this->oldValues,
                'new_values'    => $this->newValues
            ];
            $this->writeActivityLog($logParameters);

            throw $ex;
        }
    }

    /* UPDATE CONTACT */
    public function updateContact($props, $id){
        try {
            $this->oldValues = $this->model::find($id);
            $contact = $this->model::find($id);
            if ($contact) {
                $contact->supplier_id   = $props['supplier_id'];
                $contact->kontak_person = strtoupper($props['kontak_person']);
                $contact->email         = $props['email'];
                $contact->no_telp       = $props['no_telp'];
                $contact->updated_id    = $this->returnAuthUser()->id;
                $contact->update();

                /* WRITE LOG */
                $this->newValues = $this->model::find($contact->id);
                $logParameters = [
                    'status'        => 'success',
                    'module'        => $this->moduleName,
                    'event'         => 'updated',
                    'description'   => 'Memperbaharui kontak [ '.$contact->id.' - '.$contact->kontak_person.' ]',
                    'user_id'       => $this->returnAuthUser()->id ?? null,
                    'old_values'    => $this->oldValues,
                    'new_values'    => $this->newValues
                ];
                $this->writeActivityLog($logParameters);

                $contact = KontakSupplierResource::make($contact);
                return $contact;
            } else {
                throw new Exception('Catatan tidak ditemukan!');
            }
        } catch (Exception $ex) {
            /* WRITE LOG */
            $logParameters = [
                'status'        => 'error',
                'module'        => $this->moduleName,
                'event'         => 'updated',
                'description'   => 'Gagal memperbaharui kontak [ Pesan: '.$ex->getMessage().' ]',
                'user_id'       => $this->returnAuthUser()->id ?? null,
                'old_values'    => $this->oldValues,
                'new_values'    => $this->newValues
            ];
            $this->writeActivityLog($logParameters);

            throw $ex;
        }
    }

    /* DESTROY CONTACT */
    public function destroyContact($id){
        try {
            $this->oldValues = $this->model::find($id);
            $contact = $this->model::find($id);

            if ($contact) {
                $contact->delete();

                /* WRITE LOG */
                $logParameters = [
                    'status'        => 'success',
                    'module'        => $this->moduleName,
                    'event'         => 'deleted',
                    'description'   => 'Menghapus kontak [ '.$this->oldValues->id.' - '. $this->oldValues->kontak_person.' ]',
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
                'description'   => 'Gagal menghapus kontak [ Pesan: '.$ex->getMessage().' ]',
                'user_id'       => $this->returnAuthUser()->id ?? null,
                'old_values'    => $this->oldValues,
                'new_values'    => $this->newValues
            ];
            $this->writeActivityLog($logParameters);

            throw $ex;
        }
    }

    /* DESTROY SELECTED / MULTIPLE CONTACT */
    public function destroyMultipleContact($props){
        try {
            $this->oldValues = $this->model::whereIn('id', $props)->get();
            $contacts = $this->model::whereIn('id', $props);

            if ($contacts->count() > 0) {
                $contacts->delete();

                /* WRITE LOG */
                $logParameters = [
                    'status'        => 'success',
                    'module'        => $this->moduleName,
                    'event'         => 'deleted',
                    'description'   => 'Menghapus kontak [ '.$this->oldValues.' ]',
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
                'description'   => 'Gagal menghapus kontak [ Pesan: '.$ex->getMessage().' ]',
                'user_id'       => $this->returnAuthUser()->id ?? null,
                'old_values'    => $this->oldValues,
                'new_values'    => $this->newValues
            ];
            $this->writeActivityLog($logParameters);

            throw $ex;
        }
    }
}
