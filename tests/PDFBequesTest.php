<?php

use PHPUnit\Framework\TestCase;
use Beques\PDFBeques;

final class PDFBequesTest extends TestCase
{
    public function test_it_can_read_fields_from_pdf(): void
    {
	$pdfbeques = new PDFBeques('./tests/files/test.pdf');
        $pdfbeques->flatten();
        $this->assertEquals($pdfbeques->getFieldValue('Nom'),'NOM0');
        $this->assertEquals($pdfbeques->getFieldValue('Cognoms'),'COGNOMS1');
        $this->assertEquals($pdfbeques->getFieldValue('DNI'),'DNI2');
        $this->assertEquals($pdfbeques->getFieldValue('Date'),'1/1/2010');
	$this->assertEquals($pdfbeques->getFieldValue('IDALU'),'IDALU4');
    }

    public function test_it_convert_fields_to_excel(): void
    {
	$pdfbeques = new PDFBeques('./tests/files/test.pdf');
        $pdfbeques->flatten();
        $pdfbeques->writeExcel('./tests/files/test.xlsx.tmp');

        $reader1 = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $data1 = $reader1->load('./tests/files/test.xlsx.tmp');
        
        $reader2 = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $data2 = $reader2->load('./tests/files/test.xlsx');

        foreach (range('A', 'E') as $col) {
            foreach (range(1, 2) as $row) {
                $this->assertEquals(
                    $data1->getActiveSheet()->getCell($col.$row)->getValue(), 
                    $data2->getActiveSheet()->getCell($col.$row)->getValue());
            }
        }
    }
}

