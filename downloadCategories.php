<?php

require 'vendor/autoload.php';

$language ="da";

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

#
# Open database connection -----------------------------------------------------
#
require 'PrepareDatabaseOperation.php';

// Create new Spreadsheet object
$spreadsheet = new Spreadsheet();
//$sheet = $spreadsheet->setActiveSheetIndex(0);

// Set document properties
$spreadsheet->getProperties()->setCreator('Maarten Balliauw')
    ->setLastModifiedBy('Maarten Balliauw')
    ->setTitle('Office 2007 XLSX Test Document')
    ->setSubject('Office 2007 XLSX Test Document')
    ->setDescription('Test document for Office 2007 XLSX, generated using PHP classes.')
    ->setKeywords('office 2007 openxml php')
    ->setCategory('Test result file');

$sqlQuery = 'SELECT CASE sub.altLabels WHEN NULL THEN sub.prefferredLabel WHEN "" THEN sub.prefferredLabel ELSE CONCAT(sub.prefferredLabel, "/", sub.altLabels) END childLabels, "Added" connection, sup.prefferredLabel parentLabel, sup.grp parentGrp FROM `kompetence` sub LEFT JOIN ( kompetence_kategorisering kat JOIN kompetence sup ON kat.superkompetence=sup.conceptUri )  ON sub.conceptUri=kat.subkompetence where CASE sub.altLabels WHEN NULL THEN sub.prefferredLabel WHEN "" THEN sub.prefferredLabel ELSE CONCAT(sub.prefferredLabel, "/", sub.altLabels) END is not null';

/* Select queries return a resultset */
$sheet = $spreadsheet->setActiveSheetIndex(0);
$i = 1;
$sheet->setCellValueByColumnAndRow(1, $i, "childLabels");
$sheet->setCellValueByColumnAndRow(2, $i, "connection");
$sheet->setCellValueByColumnAndRow(3, $i, "parentLabel");
$sheet->setCellValueByColumnAndRow(4, $i, "parentGrp");
dbExecute($sqlQuery);
if ($GLOBALS["mysqliresult"]) {
    while($row = $GLOBALS["mysqliresult"]->fetch_object()) {
    	       $i++;
	       $sheet->setCellValueByColumnAndRow(1, $i, $row->childLabels);
	       $sheet->setCellValueByColumnAndRow(2, $i, $row->connection);
	       $sheet->setCellValueByColumnAndRow(3, $i, $row->parentLabel);
	       $sheet->setCellValueByColumnAndRow(4, $i, $row->parentGrp);
    }
}
$sheet->setAutoFilter('A1:D'.$i);
$sheet->getColumnDimension('A')->setWidth(40);
$sheet->getColumnDimension('B')->setWidth(15);
$sheet->getColumnDimension('C')->setWidth(30);
$sheet->getColumnDimension('D')->setWidth(15);


#
# Close database connection ----------------------------------------------------
#
require 'FinalizeDatabaseOperation.php';

// Redirect output to a client s web browser (Xlsx)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="competence_categories.xlsx"');
header('Cache-Control: max-age=0');

$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$writer->save('php://output');
exit;
