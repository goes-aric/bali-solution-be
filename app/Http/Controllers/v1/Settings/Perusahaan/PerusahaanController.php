<?php
namespace App\Http\Controllers\v1\Settings\Perusahaan;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\v1\BaseController;
use App\Http\Services\v1\Settings\Perusahaan\PerusahaanServices;

class PerusahaanController extends BaseController
{
    private $perusahaanServices;
    private $moduleName;

    public function __construct(PerusahaanServices $perusahaanServices)
    {
        $this->perusahaanServices = $perusahaanServices;
        $this->moduleName = 'Perusahaan';
    }
    public function save(Request $request)
    {
        // if ($this->checkPermissions($this->moduleName, 'save') == true) {
            try {
                $rules = [
                    'nama_perusahaan'   => 'required',
                    'alamat'	        => 'nullable',
                    'nomor_legalitas'	=> 'nullable',
                    'no_telp'		    => 'nullable',
                    'website'           => 'nullable|url',
                    'email'             => 'nullable|email',
                    'logo'              => 'nullable|mimes:jpeg,jpg,png|max:2048',
                ];
                $validator = $this->returnValidator($request->all(), $rules);
                if ($validator->fails()) {
                    return $this->returnResponse('error', self::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
                }

                $data = $this->perusahaanServices->savePerusahaan($request);
                return $this->returnResponse('success', self::HTTP_OK, 'Profil perusahaan berhasil disimpan', $data);
            } catch (Exception $ex) {
                return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
            }
        // }

        // return $this->returnNoPermissionResponse();
    }

    public function show($id)
    {
        // if ($this->checkPermissions($this->moduleName, 'view') == true) {
            try {
                $data = $this->perusahaanServices->fetchById($id);
                return $this->returnResponse('success', self::HTTP_OK, 'Detail profil perusahaan', $data);
            } catch (Exception $ex) {
                return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
            }
        // }

        // return $this->returnNoPermissionResponse();
    }

    public function destroy($id)
    {
        try {
            $data = $this->perusahaanServices->destroyPerusahaan($id);
            return $this->returnResponse('success', self::HTTP_OK, 'Catatan berhasil dihapus!', $data);
        } catch (Exception $ex) {
            return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
        }
    }
}
