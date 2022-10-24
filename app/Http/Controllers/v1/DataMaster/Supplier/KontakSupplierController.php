<?php
namespace App\Http\Controllers\v1\DataMaster\Supplier;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\v1\BaseController;
use App\Http\Services\v1\DataMaster\Supplier\KontakSupplierServices;

class KontakSupplierController extends BaseController
{
    private $kontakServices;

    public function __construct(KontakSupplierServices $kontakServices)
    {
        $this->kontakServices = $kontakServices;
    }

    public function list(Request $request, $id)
    {
        try {
            $props = $this->getBaseQueryParams($request, []);
            $contacts = $this->kontakServices->fetchAll($props, $id);

            return $this->returnResponse('success', self::HTTP_OK, 'Daftar kontak supplier', $contacts);
        } catch (Exception $ex) {
            return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
        }
    }

    public function index(Request $request, $id)
    {
        try {
            $props = $this->getBaseQueryParams($request, []);
            $contacts = $this->kontakServices->fetchLimit($props, $id);

            return $this->returnResponse('success', self::HTTP_OK, 'Daftar kontak supplier', $contacts);
        } catch (Exception $ex) {
            return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
        }
    }

    public function store(Request $request)
    {
        try {
            $rules = [
                'supplier_id'   => 'required',
                'kontak_person' => 'required',
                'email'         => 'required|email',
                'no_telp'       => 'required',
            ];
            $validator = $this->returnValidator($request->all(), $rules);
            if ($validator->fails()) {
                return $this->returnResponse('error', self::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
            }

            $contact = $this->kontakServices->createContact($request);
            return $this->returnResponse('success', self::HTTP_OK, 'Kontak supplier berhasil dibuat', $contact);
        } catch (Exception $ex) {
            return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
        }
    }

    public function show($id)
    {
        try {
            $contact = $this->kontakServices->fetchById($id);
            return $this->returnResponse('success', self::HTTP_OK, 'Detail kontak supplier', $contact);
        } catch (Exception $ex) {
            return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $rules = [
                'supplier_id'   => 'required',
                'kontak_person' => 'required',
                'email'         => 'required|email',
                'no_telp'       => 'required',
            ];
            $validator = $this->returnValidator($request->all(), $rules);
            if ($validator->fails()) {
                return $this->returnResponse('error', self::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
            }

            $contact = $this->kontakServices->updateContact($request, $id);
            return $this->returnResponse('success', self::HTTP_OK, 'Kontak supplier berhasil diperbaharui', $contact);
        } catch (Exception $ex) {
            return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
        }
    }

    public function destroy($id)
    {
        try {
            $contact = $this->kontakServices->destroyContact($id);
            return $this->returnResponse('success', self::HTTP_OK, 'Catatan berhasil dihapus!', $contact);
        } catch (Exception $ex) {
            return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
        }
    }

    public function destroyMultiple(Request $request)
    {
        try {
            $props = $request->data;
            $contacts = $this->kontakServices->destroyMultipleContact($props);

            return $this->returnResponse('success', self::HTTP_OK, 'Catatan berhasil dihapus!', $contacts);
        } catch (Exception $ex) {
            return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
        }
    }
}
