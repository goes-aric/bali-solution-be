<?php

namespace App\Jobs\Pelanggan;

use File;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Http\Services\v1\BaseServices;
use Illuminate\Support\Facades\Http;
use Log;

class ExportDraftPelanggan extends BaseServices implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /* PRIVATE VARIABLE */
    private $props;
    private $user;

    public function __construct($props)
    {
        $this->props = $props;
        $this->user = $this->returnAuthUser();
    }

    public function handle()
    {
        $this->exportDraft($this->props);
    }

    /* EXPORT MATERIAL DRAFT TO EXCEL FILE */
    public function exportDraft($props){
        try {
            /* PARSE PROPS */
            $broadcastUrl   = $props['url'];
            $key            = $props['key'];

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

            /* DECLARE VARIABLES */
            $fileName = 'Draft-Import-Supplier';

            /* SET SHEET WIDTH */
            $sheet->getColumnDimension('A')->setWidth(10);
            $sheet->getColumnDimension('B')->setWidth(15);
            $sheet->getColumnDimension('C')->setWidth(50);
            $sheet->getColumnDimension('D')->setWidth(15);
            $sheet->getColumnDimension('E')->setWidth(25);
            $sheet->getColumnDimension('F')->setWidth(15);
            $sheet->getColumnDimension('G')->setWidth(15);

            /* SET DATA HEADER */
            $sheet->setCellValue('A1', 'NAMA');
            $sheet->setCellValue('B1', 'ALAMAT');
            $sheet->setCellValue('C1', 'NO_TELP');
            $sheet->setCellValue('D1', 'EMAIL');
            $sheet->setCellValue('E1', 'STATUS');

            /* APPLY STYLE ARRAY */
            $sheet->getStyle('A1:E1')->applyFromArray($styleHeaderArray);
            $sheet->getStyle('A1:E2')->applyFromArray($styleBorderArray);

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
