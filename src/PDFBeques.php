<?php

namespace Beques;

require_once './vendor/autoload.php';

use mikehaertl\pdftk\Pdf;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PDFBeques
{
    public $file;
    public $data;

    public function __construct($file)
    {
        $this->file = $file;
        $this->flatten();
    }

    public function flatten()
    {
        $pdf = new Pdf($this->file);
        $this->data = array_reduce($pdf->getDataFields()->getArrayCopy(), function ($acc, $field) {
            $acc[$field['FieldName']] = $field;

            return $acc;
        }, []);
    }

    public function getFieldValue($field)
    {
        return $this->data[$field]['FieldValue'];
    }

    public function getHeaders()
    {
        $headers = [];
        foreach ($this->data as $row) {
            array_push($headers, $row['FieldName']);
        }

        return $headers;
    }

    public function writeExcel($file)
    {
        //headers
        $heading = $this->getHeaders();
        $rowNumberH = 1;
        $colH = 'A';
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        foreach ($heading as $h) {
            $spreadsheet->setActiveSheetIndex(0)->setCellValue($colH.$rowNumberH, $h);
            $spreadsheet->setActiveSheetIndex(0)->getStyle($colH.$rowNumberH)->getFont()->setBold(true);
            $colH++;
        }
        $spreadsheet->setActiveSheetIndex(0)->setAutoFilter('A1:E1'); //TODO: dynamic
        //content

        $rowNumberH = 2;
        $colH = 'A';

        foreach ($this->data as $row) {
            $spreadsheet->setActiveSheetIndex(0)->setCellValue($colH.$rowNumberH, $row['FieldValue']);
            $colH++;
        }
        //write document
        $writer = new Xlsx($spreadsheet);
        $writer->save($file);
    }
}
