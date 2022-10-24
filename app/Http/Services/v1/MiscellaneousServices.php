<?php
namespace App\Http\Services\v1;

use App\Http\Services\v1\BaseServices;

class MiscellaneousServices extends BaseServices
{
    /* RETURN UNIT LENGTH (SATUAN PANJANG) */
    public function fetchUnitLengthOptions(){
        $unitLength = ["MM", "CM", "M"];
        return $unitLength;
    }

    /* RETURN UNIT (SATUAN) */
    public function fetchUnitOptions(){
        $unit = ["UNIT", "PASANG", "BATANG", "BUAH", "LEMBAR"];
        return $unit;
    }
}
