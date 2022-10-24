<?php

namespace App\Jobs\Supplier;

use Log;
use File;
use Exception;
use Illuminate\Bus\Queueable;
use App\Http\Services\v1\BaseServices;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Http;

class ExportSupplier extends BaseServices implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /* PRIVATE VARIABLE */
    private $props;
    private $carbon;
    private $user;

    public function __construct($props)
    {
        $this->props = $props;
        $this->carbon = $this->returnCarbon();
        $this->user = $this->returnAuthUser();
    }

    public function handle()
    {
        $this->export($this->props);
    }

    /* EXPORT MATERIAL TO EXCEL FILE */
    public function export($props){
        try {
            /* PARSE PROPS */
            $broadcastUrl   = $props['url'];
            $key            = $props['key'];
            $suppliers      = $props['data'];
            $params         = $props['props'];

            /* CREATE NEW SPREADSHEET FILE */
            $spreadsheet = new Spreadsheet;
            $sheet = $spreadsheet->getActiveSheet();

            /* DECLARE STYLE ARRAY */
            $styleHeaderArray = [
                'font' => [
                    'bold' => true,
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ];
            $styleBorderArray = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ];
            $styleBoldArray = [
                'font' => [
                    'bold' => true,
                ],
            ];
            $styleCenterArray = [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
            ];
            $styleRightArray = [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
                ],
            ];

            /* DECLARE VARIABLES */
            $now = $this->carbon::now();
            $formatedDate = $now->format('F d, Y');
            $fileName = 'Suppliers';

            /* SET SHEET WIDTH */
            $sheet->getColumnDimension('A')->setWidth(5);
            $sheet->getColumnDimension('B')->setWidth(25);
            $sheet->getColumnDimension('C')->setWidth(40);
            $sheet->getColumnDimension('D')->setWidth(15);
            $sheet->getColumnDimension('E')->setWidth(10);

            /* SET DATA HEADER */
            $sheet->setCellValue('A1', 'Data Supplier');
            $sheet->setCellValue('E1', 'Diterbitkan : '.$formatedDate);
            $sheet->setCellValue('E2', 'Oleh : '.$this->user->name);

            $sheet->setCellValue('A4', 'No');
            $sheet->setCellValue('B4', 'Nama');
            $sheet->setCellValue('C4', 'Alamat');
            $sheet->setCellValue('D4', 'Kota');
            $sheet->setCellValue('E4', 'Kode Pos');

            /* FILL DATA */
            $no = 1;
            $row = 5;
            foreach ($suppliers as $supplier) {
                $sheet->setCellValue('A'.$row, $no);
                $sheet->setCellValue('B'.$row, $supplier->nama_supplier);
                $sheet->setCellValue('C'.$row, $supplier->alamat);
                $sheet->setCellValue('D'.$row, $supplier->kota);
                $sheet->setCellValue('E'.$row, $supplier->kode_pos);
                $no++;
                $row++;
            }

            /* APPLY STYLE ARRAY */
            $sheet->getStyle('A1:E1')->applyFromArray($styleBoldArray);
            $sheet->getStyle('A4:E4')->applyFromArray($styleHeaderArray);
            $sheet->getStyle('A4:E'.($row-1))->applyFromArray($styleBorderArray);

            /* DEFINE STORE LOCATION */
            $fileLocation = storage_path("app/public/exports");

            /* CHECK PATH IS EXISTS OR NOT */
            /* IF DOES NOT EXISTS, CREATE DIRECTORY */
            if (!File::isDirectory($fileLocation)) {
                File::makeDirectory($fileLocation, 0777, true, true);
            }

            /* WRITE EXCEL FILE AND STORE TO PUBLIC */
            $writer = new Xlsx($spreadsheet);
            $writer->save($fileLocation.'/'.$fileName.'.xlsx');

            Log::debug('Please wait for the file you requested is being prepared');

            Http::post($broadcastUrl, [
                'to'        => $this->user->id,
                'key'       => $key,
                'return'    =>  [
                    'filename'  => $fileName.'.xlsx',
                    'url'       => url('storage/exports').'/'.$fileName.'.xlsx'
                ]
            ]);
        } catch (Exception $ex) {
            Log::debug($ex);
        }
    }
}
