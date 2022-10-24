<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BaseImport implements ToArray, WithHeadingRow
{
    use Importable;
    private $data;

    public function __construct()
    {
        $this->data = [];
    }

    public function array(array $rows)
    {
        $this->data = $rows;
    }

    public function getArray(): array
    {
        return $this->data;
    }
}
