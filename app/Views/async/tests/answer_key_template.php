<?php

use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

$helper = new Sample();
if ($helper->isCli()) {
    $helper->log('This example should only be run from a Web Browser' . PHP_EOL);

    return;
}

// Create new Spreadsheet object
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Adding first header row
$sheet->setCellValueByColumnAndRow('1', '1', 'QUESTION NUMBER');
$sheet->setCellValueByColumnAndRow('2', '1', 'CORRECT ANSWER');
$sheet->setCellValueByColumnAndRow('3', '1', 'QUESTION TYPE');
$sheet->setCellValueByColumnAndRow('4', '1', 'FORMAT OF CORRECT ANSWER');

// For autosizing (cell automatic expansion) 
// https://stackoverflow.com/questions/52070754/center-all-text-in-phpspreadsheet-and-make-the-cells-expand-to-fill-up-with-cont
$sheet->getColumnDimension('A')->setAutoSize(true);
$sheet->getColumnDimension('B')->setAutoSize(true);
$sheet->getColumnDimension('C')->setAutoSize(true);
$sheet->getColumnDimension('D')->setAutoSize(true);

//Styling the first row
$styleArray = [
    'font' => [
        'bold' => true,
        'color' => [
            'argb' => 'FFFFFFFF',
        ]
    ],
    'fill' => [
        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
        'startColor' => [
            'argb' => 'FFFF0000',
        ]
    ]
];
$sheet->getStyle('A1:D1')->applyFromArray($styleArray);

// Looping through the question numbers and generating all columns based on it
if (!empty($question_numbers)) :
    for ($i = 0; $i < count($question_numbers); $i++) {
        //Adding Question Number in the 1st column of the excel
        $sheet->setCellValueByColumnAndRow('1', $i + 2, $question_numbers[$i]);
        //Adding CORRECT ANSWER in the 2nd column of the excel
        $sheet->setCellValueByColumnAndRow('2', $i + 2, $correct_answers[$i]);
        //Adding Question TYPE in the 3rd column of the excel
        $sheet->setCellValueByColumnAndRow('3', $i + 2, $question_type[$i]);

        //Adding FORMAT OF CORRECT ANSWER in the 4th column of the excel
        if ($question_type[$i] == "SINGLE") {

            if ($correct_anwser_format == 'A B C D') {
                $sheet->setCellValueByColumnAndRow('4', $i + 2, 'A');
            } else {
                $sheet->setCellValueByColumnAndRow('4', $i + 2, 'option1');
            }
        }
        if ($question_type[$i] == "PASSAGE_MULTIPLE") {
            if ($correct_anwser_format == 'A B C D') {
                $sheet->setCellValueByColumnAndRow('4', $i + 2, 'A,B,D');
            } else {
                $sheet->setCellValueByColumnAndRow('4', $i + 2, 'option1,option2,option4');
            }
        }
        if ($question_type[$i] == "MATCH") {
            $sheet->setCellValueByColumnAndRow('4', $i + 2, 'A-P,B-R,S-T');
        }
        if ($question_type[$i] == "MULTIPLE") {
            if ($correct_anwser_format == 'A B C D') {
                $sheet->setCellValueByColumnAndRow('4', $i + 2, 'A,C');
            } else {
                $sheet->setCellValueByColumnAndRow('4', $i + 2, 'option1,option3');
            }
        }
        if ($question_type[$i] == "NUMBER") {
            $sheet->setCellValueByColumnAndRow('4', $i + 2, '723');
        }
    }
endif;


// Rename worksheet
$spreadsheet->getActiveSheet()->setTitle('answer key template -');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Xlsx)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="answer key template -.xlsx"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header('Pragma: public'); // HTTP/1.0

$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$writer->save('php://output');
exit;
