<?php
namespace App\Http\Controllers\v1;

use Exception;
use App\Http\Controllers\v1\BaseController;
use App\Http\Services\v1\MiscellaneousServices;

class MiscellaneousController extends BaseController
{
    private $miscellaneousServices;

    public function __construct(MiscellaneousServices $miscellaneousServices)
    {
        $this->miscellaneousServices = $miscellaneousServices;
    }

    /* RETURN UNIT LENGTH (SATUAN PANJANG) OPTIONS */
    public function fetchUnitLengthOptions()
    {
        try {
            $unitLength = $this->miscellaneousServices->fetchUnitLengthOptions();

            return $this->returnResponse('success', self::HTTP_OK, 'Daftar satuan panjang', $unitLength);
        } catch (Exception $ex) {
            return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
        }
    }

    /* RETURN UNIT (SATUAN) OPTIONS */
    public function fetchUnitOptions()
    {
        try {
            $unit = $this->miscellaneousServices->fetchUnitOptions();

            return $this->returnResponse('success', self::HTTP_OK, 'Daftar satuan', $unit);
        } catch (Exception $ex) {
            return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
        }
    }
}
